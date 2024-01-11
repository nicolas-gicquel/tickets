<?php

namespace App\Controller;

use App\Entity\Status;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(StatusRepository $statusRepository, TicketsRepository $ticketsRepository): Response
    {
        $status = $statusRepository->findAll();
        $tickets = $ticketsRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'status' => $status,
            'tickets' => $tickets
        ]);
    }
    #[Route('/stats', name: 'app_stats')]
    public function stats(TicketsRepository $ticketsRepository,EntityManagerInterface $em): Response
    {
    // Fetch the current date and time
    $today = new \DateTime();
    $today->setTime(0, 0);

    $firstDayOfWeek = clone $today;
    $firstDayOfWeek->modify('Monday this week');

    $firstDayOfMonth = clone $today;
    $firstDayOfMonth->modify('first day of this month');

    // Define the status IDs
    $statusNew = 1;
    $statusInProgress = 2;
    $statusClosed = 3;

    // The functions to get the number of tickets for each status and period
    $getStatusCount = function ($statusId, $date) use ($em) {
        $qb = $em->createQueryBuilder();
        return $qb->select('count(t.id)')
            ->from('App\Entity\Tickets', 't')
            ->where('t.status = :status')
            ->andWhere('t.createdAt >= :date')
            ->setParameter('status', $statusId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    };
    $getMaxResolvedTickets = function ($date) use ($em, $statusClosed) {
        $qb = $em->createQueryBuilder();
        return $qb->select('u.pseudo, COUNT(t.id) as ticketCount')
            ->from('App\Entity\Tickets', 't')
            ->join('t.user', 'u')
            ->where('t.status = :status')
            ->andWhere('t.updatedAt >= :date')
            ->setParameter('status', $statusClosed)
            ->setParameter('date', $date)
            ->groupBy('u.id')
            ->orderBy('ticketCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    };

    // Get the number of tickets for each status and period
    $newTicketsToday = $getStatusCount($statusNew, $today);
    $inProgressTicketsToday = $getStatusCount($statusInProgress, $today);
    $closedTicketsToday = $getStatusCount($statusClosed, $today);

    $newTicketsThisWeek = $getStatusCount($statusNew, $firstDayOfWeek);
    $inProgressTicketsThisWeek = $getStatusCount($statusInProgress, $firstDayOfWeek);
    $closedTicketsThisWeek = $getStatusCount($statusClosed, $firstDayOfWeek);

    $newTicketsThisMonth = $getStatusCount($statusNew, $firstDayOfMonth);
    $inProgressTicketsThisMonth = $getStatusCount($statusInProgress, $firstDayOfMonth);
    $closedTicketsThisMonth = $getStatusCount($statusClosed, $firstDayOfMonth);

    $maxResolvedToday = $getMaxResolvedTickets($today);
    $maxResolvedThisWeek = $getMaxResolvedTickets($firstDayOfWeek);
    $maxResolvedThisMonth = $getMaxResolvedTickets($firstDayOfMonth);

    // Send the data to the view
    return $this->render('main/stats.html.twig', [
        'newTicketsToday' => $newTicketsToday,
        'inProgressTicketsToday' => $inProgressTicketsToday,
        'closedTicketsToday' => $closedTicketsToday,
        'newTicketsThisWeek' => $newTicketsThisWeek,
        'inProgressTicketsThisWeek' => $inProgressTicketsThisWeek,
        'closedTicketsThisWeek' => $closedTicketsThisWeek,
        'newTicketsThisMonth' => $newTicketsThisMonth,
        'inProgressTicketsThisMonth' => $inProgressTicketsThisMonth,
        'closedTicketsThisMonth' => $closedTicketsThisMonth,
        'maxResolvedToday' => $maxResolvedToday,
        'maxResolvedThisWeek' => $maxResolvedThisWeek,
        'maxResolvedThisMonth' => $maxResolvedThisMonth,
    ]);
    }
}
