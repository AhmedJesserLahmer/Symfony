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

        $product1 = new Product();
        $product1->setName('Nike Air Max');
        $product1->setDescription('Classic Nike sneakers, very comfortable.');
        $product1->setPrice(120.99);
        $product1->setStock(50);
        $product1->setImageUrl('/images/nike.avif');
        $product1->setCategory($shoes);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Adidas Hoodie');
        $product2->setDescription('Warm and stylish hoodie for everyday wear.');
        $product2->setPrice(59.99);
        $product2->setStock(30);
        $product2->setImageUrl('/images/adidas.avif');
        $product2->setCategory($clothing);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Sony Headphones');
        $product3->setDescription('Noise cancelling wireless headphones.');
        $product3->setPrice(199.99);
        $product3->setStock(15);
        $product3->setImageUrl('/images/sony.webp');
        $product3->setCategory($electronics);
        $manager->persist($product3);

        $manager->flush();
    }
}
