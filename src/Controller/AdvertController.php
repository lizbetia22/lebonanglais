<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertEdit;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvertController extends AbstractController
{
    public function __construct(WorkflowInterface $advertWorkflow)
    {
        $this->advertWorkflow = $advertWorkflow;
    }

    #[Route('/admin/advert', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository): Response
    {
        return $this->render('advert/index.html.twig', [
            'adverts' => $advertRepository->findAll(),
        ]);
    }

    #[Route('/advert/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/admin/{id}/{transition}', name: 'app_advert_state', methods: ['GET', 'POST'])]
    public function updateState(Request $request, Advert $advert, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $workflow = $this->advertWorkflow;
        $transition = $request->attributes->get('transition');

        if ($workflow->can($advert, $transition)) {
            $workflow->apply($advert, $transition);

            if ($transition === 'publish') {
                $advert->setPublishedAt(new \DateTime());
                (new MailerController)->sendEmail($mailer, $advert->getEmail());
            } elseif ($transition === 'reject_publish') {
                $advert->setPublishedAt(null);
            }

            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Status was changed successfully.');

            return $this->redirectToRoute('app_advert_index');
        }

        $this->addFlash('error', 'Transition not allowed.');

        return $this->redirectToRoute('app_advert_index');
    }

    #[Route('/admin/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertEdit::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

}
