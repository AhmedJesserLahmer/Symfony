<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InfoController extends AbstractController
{
    private const PAGES = [
        'company' => [
            'title' => 'Company',
            'kicker' => 'About us',
            'body' => 'Sneakers Store is a student-built sneaker shop focused on clean style, simple shopping, and fresh releases.',
        ],
        'contact' => [
            'title' => 'Contact',
            'kicker' => 'Talk to us',
            'body' => 'Need help with an order or a product? Send us a message through feedback and our team will answer as soon as possible.',
        ],
        'careers' => [
            'title' => 'Careers',
            'kicker' => 'Join the team',
            'body' => 'We are always looking for motivated people who love sneakers, web projects, and customer experience.',
        ],
        'affiliates' => [
            'title' => 'Affiliates',
            'kicker' => 'Partners',
            'body' => 'Creators, clubs, and campus groups can partner with us for sneaker campaigns and special offers.',
        ],
        'stores' => [
            'title' => 'Stores',
            'kicker' => 'Find us',
            'body' => 'Our store locator is coming soon. For now, you can shop online and follow our updates on social media.',
        ],
        'support' => [
            'title' => 'Support',
            'kicker' => 'Help center',
            'body' => 'For order help, product questions, or technical issues, use the feedback form and describe your request.',
        ],
        'refund' => [
            'title' => 'Refund',
            'kicker' => 'Returns',
            'body' => 'Refund requests are accepted within 14 days when the product is unused and returned in its original condition.',
        ],
        'faq' => [
            'title' => 'FAQ',
            'kicker' => 'Quick answers',
            'body' => 'You can browse products, add items to your cart, apply promo codes, and send feedback from the site.',
        ],
        'stories' => [
            'title' => 'Stories',
            'kicker' => 'Sneaker news',
            'body' => 'Stories will feature drops, styling ideas, and behind-the-scenes updates from the Sneakers Store project.',
        ],
    ];

    #[Route('/info/{slug}', name: 'app_info_page')]
    public function page(string $slug): Response
    {
        if (!isset(self::PAGES[$slug])) {
            throw $this->createNotFoundException('Page not found.');
        }

        return $this->render('info/page.html.twig', [
            'page' => self::PAGES[$slug],
        ]);
    }

    #[Route('/newsletter', name: 'app_newsletter_join', methods: ['POST'])]
    public function newsletter(Request $request): RedirectResponse
    {
        $email = trim((string) $request->request->get('email'));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('footer_error', 'Please enter a valid email address.');
        } else {
            $this->addFlash('footer_success', 'Thanks for joining our newsletter.');
        }

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_products'));
    }

    #[Route('/products/go/{name}', name: 'app_product_go')]
    public function product(string $name, ProductRepository $productRepository): RedirectResponse
    {
        $productName = str_replace('-', ' ', $name);
        $fallbackName = preg_replace('/^Air\s+/i', '', $productName);
        $product = $productRepository->findOneBy(['name' => $productName])
            ?? ($fallbackName ? $productRepository->findOneBy(['name' => $fallbackName]) : null);

        if ($product === null) {
            return $this->redirectToRoute('app_products');
        }

        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }
}
