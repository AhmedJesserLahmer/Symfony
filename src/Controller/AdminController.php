<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
    ): Response
    {
        $products = $productRepository->findBy([], ['id' => 'DESC']);
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $users = $userRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'users' => $users,
            'totalStock' => array_sum(array_map(static fn (Product $product): int => $product->getStock() ?? 0, $products)),
            'lowStockCount' => count(array_filter($products, static fn (Product $product): bool => ($product->getStock() ?? 0) <= 5)),
        ]);
    }

    #[Route('/admin/products/create', name: 'app_admin_product_create', methods: ['POST'])]
    public function createProduct(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_product_create', (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'Product creation was refused.');

            return $this->redirectToRoute('app_admin');
        }

        $product = new Product();
        $this->fillProductFromRequest($product, $request, $categoryRepository);

        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('admin_success', 'Product created.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/products/{id}/update', name: 'app_admin_product_update', methods: ['POST'])]
    public function updateProduct(Product $product, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_product_update'.$product->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'Product update was refused.');

            return $this->redirectToRoute('app_admin');
        }

        $this->fillProductFromRequest($product, $request, $categoryRepository);
        $entityManager->flush();

        $this->addFlash('admin_success', 'Product updated.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/products/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_product_delete'.$product->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'Product deletion was refused.');

            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('admin_success', 'Product deleted.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/categories/create', name: 'app_admin_category_create', methods: ['POST'])]
    public function createCategory(Request $request, SluggerInterface $slugger, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_category_create', (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'Category creation was refused.');

            return $this->redirectToRoute('app_admin');
        }

        $name = trim((string) $request->request->get('name'));

        if ($name === '') {
            $this->addFlash('admin_error', 'Category name is required.');

            return $this->redirectToRoute('app_admin');
        }

        $slug = strtolower((string) $slugger->slug($name));

        if ($categoryRepository->findOneBy(['slug' => $slug]) !== null) {
            $this->addFlash('admin_error', 'This category already exists.');

            return $this->redirectToRoute('app_admin');
        }

        $category = new Category();
        $category->setName($name);
        $category->setSlug($slug);

        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash('admin_success', 'Category created.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_category_delete'.$category->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'Category deletion was refused.');

            return $this->redirectToRoute('app_admin');
        }

        foreach ($category->getProducts() as $product) {
            $product->setCategory(null);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('admin_success', 'Category deleted.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/users/{id}/toggle-admin', name: 'app_admin_user_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('admin_error', 'You cannot change your own admin role here.');

            return $this->redirectToRoute('app_admin');
        }

        if (!$this->isCsrfTokenValid('admin_user_toggle'.$user->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('admin_error', 'User role update was refused.');

            return $this->redirectToRoute('app_admin');
        }

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $roles = array_values(array_diff($roles, ['ROLE_ADMIN']));
            $this->addFlash('admin_success', 'Admin role removed.');
        } else {
            $roles[] = 'ROLE_ADMIN';
            $this->addFlash('admin_success', 'Admin role added.');
        }

        $user->setRoles(array_values(array_unique($roles)));
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }

    private function fillProductFromRequest(Product $product, Request $request, CategoryRepository $categoryRepository): void
    {
        $product->setName(trim((string) $request->request->get('name')));
        $product->setDescription(trim((string) $request->request->get('description')));
        $product->setPrice(number_format(max(0, (float) $request->request->get('price')), 2, '.', ''));
        $product->setStock(max(0, $request->request->getInt('stock')));
        $product->setImageUrl(trim((string) $request->request->get('imageUrl')));

        $categoryId = $request->request->getInt('category');
        $product->setCategory($categoryId > 0 ? $categoryRepository->find($categoryId) : null);
    }
}
