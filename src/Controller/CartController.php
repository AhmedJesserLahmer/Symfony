<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_index')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $items = [];
        $total = 0.0;

        if ($cart !== []) {
            $products = $productRepository->findBy(['id' => array_keys($cart)]);
            $productsById = [];

            foreach ($products as $product) {
                $productsById[$product->getId()] = $product;
            }

            foreach ($cart as $productId => $quantity) {
                if (!isset($productsById[$productId])) {
                    continue;
                }

                $product = $productsById[$productId];
                $unitPrice = (float) $product->getPrice();
                $lineTotal = $unitPrice * $quantity;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'lineTotal' => $lineTotal,
                ];

                $total += $lineTotal;
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, SessionInterface $session): Response
    {
        if (!$this->isCsrfTokenValid('cart_add' . $product->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_cart_index');
        }

        $cart = $session->get('cart', []);
        $productId = $product->getId();

        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        $session->set('cart', $cart);

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?? $this->generateUrl('app_cart_index'));
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Product $product, Request $request, SessionInterface $session): Response
    {
        if (!$this->isCsrfTokenValid('cart_remove' . $product->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_cart_index');
        }

        $cart = $session->get('cart', []);
        $productId = $product->getId();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(Request $request, SessionInterface $session): Response
    {
        if (!$this->isCsrfTokenValid('cart_clear', (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_cart_index');
        }

        $session->remove('cart');

        return $this->redirectToRoute('app_cart_index');
    }
}
