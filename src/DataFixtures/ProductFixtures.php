<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $shoes = new Category();
        $shoes->setName('Shoes');
        $shoes->setSlug('shoes');
        $manager->persist($shoes);

        $clothing = new Category();
        $clothing->setName('Clothing');
        $clothing->setSlug('clothing');
        $manager->persist($clothing);

        $electronics = new Category();
        $electronics->setName('Electronics');
        $electronics->setSlug('electronics');
        $manager->persist($electronics);

        $featuredProducts = [
            [
                'name' => 'Air Force',
                'description' => 'Classic basketball silhouette reimagined for modern streets.',
                'price' => 175.00,
                'stock' => 24,
                'image' => '/images/air.png',
            ],
            [
                'name' => 'Jordan',
                'description' => 'High-energy design with premium cushioning and bold style.',
                'price' => 149.00,
                'stock' => 20,
                'image' => '/images/jordan.png',
            ],
            [
                'name' => 'Blazer',
                'description' => 'Retro court silhouette with a modern streetwear twist.',
                'price' => 119.00,
                'stock' => 18,
                'image' => '/images/blazer.png',
            ],
            [
                'name' => 'Crater',
                'description' => 'Lightweight build with a futuristic, sustainable vibe.',
                'price' => 109.00,
                'stock' => 16,
                'image' => '/images/crater.png',
            ],
            [
                'name' => 'Hippie',
                'description' => 'Everyday comfort with a soft, minimal look.',
                'price' => 200.00,
                'stock' => 12,
                'image' => '/images/hippie.png',
            ],
        ];

        foreach ($featuredProducts as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setStock($data['stock']);
            $product->setImageUrl($data['image']);
            $product->setCategory($shoes);
            $manager->persist($product);
        }

        $product1 = new Product();
        $product1->setName('Nike Air Max');
        $product1->setDescription('Classic Nike sneakers, very comfortable.');
        $product1->setPrice(120.99);
        $product1->setStock(50);
        $product1->setImageUrl('/images/nike.avif');
        $product1->setCategory($shoes);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Limited SB High');
        $product2->setDescription('Limited edition high-top with vibrant colorwork.');
        $product2->setPrice(159.99);
        $product2->setStock(18);
        $product2->setImageUrl('/images/sneakerslimited.png');
        $product2->setCategory($shoes);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Limited SB Duo');
        $product3->setDescription('Artist-inspired pair for collectors and daily wear.');
        $product3->setPrice(179.99);
        $product3->setStock(12);
        $product3->setImageUrl('/images/sneakerslimited2.png');
        $product3->setCategory($shoes);
        $manager->persist($product3);

        $manager->flush();
    }
}
