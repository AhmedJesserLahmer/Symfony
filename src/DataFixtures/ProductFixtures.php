<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setName('Nike Air Max');
        $product1->setDescription('Classic Nike sneakers, very comfortable.');
        $product1->setPrice(120.99);
        $product1->setStock(50);
        $product1->setImageUrl('/images/nike.avif');        
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Adidas Hoodie');
        $product2->setDescription('Warm and stylish hoodie for everyday wear.');
        $product2->setPrice(59.99);
        $product2->setStock(30);
        $product2->setImageUrl('/images/adidas.avif');
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Sony Headphones');
        $product3->setDescription('Noise cancelling wireless headphones.');
        $product3->setPrice(199.99);
        $product3->setStock(15);
        $product3->setImageUrl('/images/sony.webp');
        $manager->persist($product3);

        $manager->flush();
    }
}