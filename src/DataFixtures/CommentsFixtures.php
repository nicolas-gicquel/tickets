<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Comments;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentsFixtures extends Fixture implements DependentFixtureInterface
{

    private function getUsersIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des utilisateurs
        $userIds = $manager->getRepository(User::class)->createQueryBuilder('u')
            ->select('u.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $userIds = array_column($userIds, 'id');

        return $userIds;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $userIds = $this->getUsersIds($manager);
        for ($cmt = 1; $cmt <= 30; $cmt++) {
            $randomDays = rand(1, 90); // 90 jours correspondent à 3 mois
            $randomDate = new \DateTime();
            $randomDate->sub(new \DateInterval("P{$randomDays}D")); // Soustraire un nombre aléatoire de jours



            // Définition de la date générée pour le commentaire
            $commentDate = $randomDate->format('Y-m-d H:i:s');

            $comment = new Comments();

            // Associer un utilisateur au hasard
            $randomUserId = $userIds[array_rand($userIds)];
            $user = $manager->getRepository(User::class)->find($randomUserId);
            $comment->setUser($user);

            $comment->setCreatedAt(new \DateTime($commentDate)); // Assurez-vous que votre entité Comments a une méthode setCreatedAt

            // Générer une date pour updateAt entre 1 jour et 4 jours après la création
            $updateAt = new \DateTime($commentDate);
            $updateAt->add(new \DateInterval("P" . rand(1, 4) . "D"));
            $comment->setUpdateAt($updateAt);

            $comment->setContent($faker->text());

            $manager->persist($comment);

            // Créez une référence à chaque entité Comment avec le format "comment_i"
            $this->addReference("comment_$cmt", $comment);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
