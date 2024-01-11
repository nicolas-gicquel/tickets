<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class StatusFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createCategory('Nouveau', $manager);
        $this->createCategory('En cours', $manager);
        $this->createCategory('Résolut', $manager);
        $this->createCategory('Archivé', $manager);

        $manager->flush();
    }
    public function createCategory(string $label, ObjectManager $manager)
    {
        $category = new Status();
        $category->setLabel($label);
        $category->setColor('red');
        $category->setSlug($this->slugger->slug($category->getLabel())->lower());
        $manager->persist($category);

        $manager->flush();
    }
}
