<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_products');
    }

    #[Route('/products', name: 'app_products')]
    public function index(Request $request, ProductRepository $repository, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->query->getInt('category', 0);
        $selectedCategory = $categoryId ? $categoryRepository->find($categoryId) : null;
        $minPriceRaw = $request->query->get('min');
        $maxPriceRaw = $request->query->get('max');
        $priceRange = (string) $request->query->get('price', '');
        $minPrice = is_numeric($minPriceRaw) ? (float) $minPriceRaw : null;
        $maxPrice = is_numeric($maxPriceRaw) ? (float) $maxPriceRaw : null;

        if ($priceRange !== '' && $minPrice === null && $maxPrice === null) {
            switch ($priceRange) {
                case '0-120':
                    $minPrice = 0.0;
                    $maxPrice = 120.0;
                    break;
                case '120-160':
                    $minPrice = 120.0;
                    $maxPrice = 160.0;
                    break;
                case '160-200':
                    $minPrice = 160.0;
                    $maxPrice = 200.0;
                    break;
                case '200+':
                    $minPrice = 200.0;
                    $maxPrice = null;
                    break;
                default:
                    $priceRange = '';
                    break;
            }
        }

        $products = $repository->findByFilters($selectedCategory, $minPrice, $maxPrice);
        $categories = $categoryRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'priceRange' => $priceRange,
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
