<?php

namespace App\DataFixtures;

use App\Entity\Tickets;
use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createCategory('Back-end', $manager);
        $this->createCategory('Front-end', $manager);
        
        $manager->flush();
    }
    public function createCategory(string $label, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setLabel($label);
        // $category->setSlug($this->slugger->slug($category->getLabel())->lower());
        $manager->persist($category);
    }
}
