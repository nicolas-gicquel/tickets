<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Images;
use App\Entity\Tickets;
use App\Entity\Comments;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Ticket; // Assurez-vous d'importer la classe Ticket si ce n'est pas déjà fait

class ImagesFixtures extends Fixture implements DependentFixtureInterface
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
    private function getCommentIds(ObjectManager $manager): array
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

    private function getTicketIds(ObjectManager $manager): array
    {
        // Récupérer tous les IDs des tickets
        $ticketIds = $manager->getRepository(Tickets::class)->createQueryBuilder('t')
            ->select('t.id')
            ->getQuery()
            ->getScalarResult();

        // Transformer le résultat en un tableau simple d'IDs
        $ticketIds = array_column($ticketIds, 'id');

        return $ticketIds;
    }

   
    public function load(ObjectManager $manager): void
    {
       
        $commentIds = $this->getCommentIds($manager);
        $ticketIds = $this->getTicketIds($manager);
        $userIds = $this->getUsersIds($manager);

        $totalImages = 40;
        $userImageLimit = count($userIds);
        $commentImageLimit = ($totalImages - $userImageLimit) / 2;

        for ($i = 1; $i <= $totalImages; $i++) {
            $image = new Images();

            // Définir le chemin de l'image en utilisant le chemin de base et le numéro d'itération
            $imagePath = "snow.jpg";
            $image->setPath($imagePath);

            // Attribuer les images équitablement entre les utilisateurs et les commentaires
           if ($i <= $userImageLimit + $commentImageLimit) {
                $randomCommentId = $commentIds[array_rand($commentIds)];
                $comment = $manager->getRepository(Comments::class)->find($randomCommentId);
                $image->setComment($comment);
            } else {
                $randomTicketId = $ticketIds[array_rand($ticketIds)];
                $ticket = $manager->getRepository(Tickets::class)->find($randomTicketId);
                $image->setTicket($ticket);
            }

            $manager->persist($image);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [

            TicketsFixtures::class, // Assuming you have a CommentsFixtures class
            CommentsFixtures::class, // Assuming you have a CommentsFixtures class
        ];
    }
}
