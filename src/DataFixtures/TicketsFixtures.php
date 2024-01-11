<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Categories;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Tickets;
use App\Entity\Comments;
use App\Entity\Status;
use App\Entity\Technologies;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TicketsFixtures extends Fixture implements DependentFixtureInterface
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
    private function getCommentsIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des commentaires
        $commentIds = $manager->getRepository(Comments::class)->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $commentIds = array_column($commentIds, 'id');

        return $commentIds;
    }

    private function getCategoriesIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des catégories
        $categoryIds = $manager->getRepository(Categories::class)->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $categoryIds = array_column($categoryIds, 'id');

        return $categoryIds;
    }

    private function getTechnologiesIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des technologies
        $technologyIds = $manager->getRepository(Technologies::class)->createQueryBuilder('t')
            ->select('t.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $technologyIds = array_column($technologyIds, 'id');

        return $technologyIds;
    }

    private function getStatusIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des statuts
        $statusIds = $manager->getRepository(Status::class)->createQueryBuilder('s')
            ->select('s.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $statusIds = array_column($statusIds, 'id');

        return $statusIds;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Récupérer les IDs des statuts, des utilisateurs, des catégories, des technologies
        $userIds = $this->getUsersIds($manager);
        $statusIds = $this->getStatusIds($manager);
        $categoryIds = $this->getCategoriesIds($manager);
        $technologyIds = $this->getTechnologiesIds($manager);
        $commentIds = $this->getCommentsIds($manager);

        // Ajout de 20 tickets
        for ($i = 1; $i <= 20; $i++) {
            $ticket = new Tickets();
            $ticket->setTitle($faker->sentence);
            $ticket->setDescription($faker->paragraph);
            $ticket->setCreatedAt($faker->dateTimeThisYear);
            $ticket->setUpdatedAt($faker->dateTimeThisYear);

            // Associer un utilisateur au hasard
            $randomUserId = $userIds[array_rand($userIds)];
            $user = $manager->getRepository(User::class)->find($randomUserId);
            $ticket->setUser($user);

            // Associer une catégorie au hasard
            $randomCategoryId = $categoryIds[array_rand($categoryIds)];
            $category = $manager->getRepository(Categories::class)->find($randomCategoryId);
            $ticket->setCategorie($category);

            // Associer une technologie au hasard
            $randomTechnologyId = $technologyIds[array_rand($technologyIds)];
            $technology = $manager->getRepository(Technologies::class)->find($randomTechnologyId);
            $ticket->setTechnologie($technology);

            // Associer un statut au hasard
            $randomStatusId = $statusIds[array_rand($statusIds)];
            $status = $manager->getRepository(Status::class)->find($randomStatusId);
            $ticket->setStatus($status);
            // Associer un utilisateur au hasard selon le statut
            if ($status && in_array($status->getLabel(), ['Résolut', 'Archivé'])) {
                $randomUserId = $userIds[array_rand($userIds)];
                $user = $manager->getRepository(User::class)->find($randomUserId);
                // Vérifier si l'utilisateur existe avant de l'associer au ticket
                if ($user) {
                    $ticket->setSolvedBy($user);
                }
            }


            // Associer plusieurs commentaire aléatoirement
            $numComments = mt_rand(1, 5); // Choose the maximum number of comments per ticket
            for ($j = 1; $j <= $numComments; $j++) {
                $randomCommentId = $commentIds[array_rand($commentIds)];
                $comment = $manager->getRepository(Comments::class)->find($randomCommentId);
                $ticket->addComment($comment);
            }

            $manager->persist($ticket);
        }

        $manager->flush();
    }



    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CommentsFixtures::class, // Assuming you have a CommentsFixtures class
        ];
    }
}
