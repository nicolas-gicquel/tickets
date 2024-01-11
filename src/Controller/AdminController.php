<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\TicketsRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private CategoriesRepository $categoriesRepository,
        private TicketsRepository $ticketsRepository
        ){}

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        $tickets = $this->ticketsRepository->findAll();
        $categories = $this->categoriesRepository->findAll();
        foreach ($users as $user) {
            $ticketCount = $this->ticketsRepository->findTicketSolvedByUserId($user->getId());
            $user->setTicketSolved(Count($ticketCount));
        }
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'categories' => $categories,
            'tickets' => $tickets,
            'user' => $user
        ]);
    }
}
