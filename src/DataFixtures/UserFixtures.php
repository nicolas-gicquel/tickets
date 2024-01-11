<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture 
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {
    }


    public function load(ObjectManager $manager): void
    {
        $admin = new User;
        $admin->setEmail('aouda@test.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('Jeanjean');
        $admin->setFirstName('Jean');
        $admin->setLastName('Martin');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $manager->persist($admin);

        $faker = Factory::create('fr_FR');

        for ($usr = 1; $usr <= 13; $usr++) {
            $user = new User;
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setFirstName($faker->firstName);
            $user->setPseudo($user->getFirstName().$faker->randomNumber(3));
            $user->setLastName($faker->lastName);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'password')
            );
            $manager->persist($user);
            $user->setDiscordPath("discord ".$faker->lastName);
        }

        $manager->flush();
    }
        
}
