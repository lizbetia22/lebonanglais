<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(AdvertRepository $advertRepository): Response
    {
        $publishedAdverts = $advertRepository->findAllPublished();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'publishedAdverts' => $publishedAdverts,
        ]);
    }
}
