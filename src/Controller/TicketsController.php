<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Tickets;
use App\Form\CommentsType;
use App\Form\TakeTicketType;
use App\Form\TicketsType;
use App\Repository\StatusRepository;
use App\Repository\TicketsRepository;
use App\Service\PictureService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/tickets')]
class TicketsController extends AbstractController
{
    public function __construct(
        private StatusRepository $statusRepository,
        private TicketsRepository $ticketsRepository,
        private PictureService $pictureService
    ) {
    }

    #[Route('/', name: 'app_tickets_index', methods: ['GET'])]
    public function index(TicketsRepository $ticketsRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur actuellement connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger si l'utilisateur n'est pas connecté
        }

        // Modifier la requête pour obtenir uniquement les tickets de l'utilisateur connecté
        $tickets = $entityManagerInterface->getRepository(Tickets::class)->findBy(['user' => $user]);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'app_tickets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Tickets();

        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichier = $this->pictureService->add($image, '');

                $img = new Images();

                $img->setPath($fichier);
                $ticket->addImage($img);
            }

            $ticket->setStatus($this->statusRepository->findOneBy([]));
            $ticket->setUser($this->getUser());
            $ticket->setCreatedAt(new DateTime());
            $ticket->setUpdatedAt(new DateTime());


            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tickets_show', methods: ['GET', 'POST', 'PUT'])]
    public function show(Tickets $ticket, $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        $formButton = $this->createForm(TakeTicketType::class, $ticket);
        $formButton->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichier = $this->pictureService->add($image, '');

                $img = new Images();

                $img->setPath($fichier);
                $comment->addImage($img);
            }

            $comment->setCreatedAt(new DateTime);
            $comment->setupdateAt(new DateTime);


            $comment->setUser($this->getUser());

            $comment->setTicket($this->ticketsRepository->find($id));

            $ticket->setStatus($this->statusRepository->findOneBy(['id' => 3]));

            $entityManager->persist($comment);

            $entityManager->flush();

            return $this->redirectToRoute('app_tickets_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        if ($formButton->isSubmitted() && $formButton->isValid()) {

            $ticket->setSolvedBy($this->getUser());



            $entityManager->flush();

            return $this->redirectToRoute('app_tickets_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }


        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'formButton' => $formButton->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tickets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tickets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tickets_delete', methods: ['POST'])]
    public function delete(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tickets_index', [], Response::HTTP_SEE_OTHER);
    }
}
