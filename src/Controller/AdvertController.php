<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Picture;
use App\Form\AdvertEdit;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/advert')]
class AdvertController extends AbstractController
{
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
            /** @var UploadedFile|null $pictureFile */
            $pictureFiles = $form->get('picture')->getData();

            foreach ($pictureFiles as $pictureFile){
                if ($pictureFile instanceof UploadedFile) {
                    $newFilename = uniqid() . '.' . $pictureFile->guessExtension();
                    try {
                        $pictureFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $picture = new Picture();
                    $picture->setPath($newFilename);

                    $advert->addPicture($picture);
                }
        }

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
