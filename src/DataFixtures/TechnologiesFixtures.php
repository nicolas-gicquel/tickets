<?php

namespace App\DataFixtures;

use App\Entity\Technologies;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class TechnologiesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $this->createCategory('React', $manager);
        $this->createCategory('Vue.js', $manager);
        $this->createCategory('Node.js', $manager);
        $this->createCategory('Laravel', $manager);
        $this->createCategory('Angular', $manager);
        $this->createCategory('Express.js', $manager);
        $this->createCategory('Django', $manager);
        $this->createCategory('Ruby on Rails', $manager);
        $this->createCategory('Spring Boot', $manager);
        $this->createCategory('ASP.NET', $manager);
        $this->createCategory('Flask', $manager);
        $this->createCategory('Symfony', $manager);
        $this->createCategory('Meteor', $manager);
        $this->createCategory('Ember.js', $manager);
        $this->createCategory('Backbone.js', $manager);
        $this->createCategory('Fluttter', $manager);
        $this->createCategory('Xamarin', $manager);
        $this->createCategory('Swift', $manager);
        $this->createCategory('Kotlin', $manager);
        $this->createCategory('Cordova', $manager);;

        $manager->flush();
    }
    public function createCategory(string $label, ObjectManager $manager)
    {
        $category = new Technologies();
        $category->setLabel($label);
        $category->setSlug($this->slugger->slug($category->getLabel())->lower());
        $manager->persist($category);
    }
}
