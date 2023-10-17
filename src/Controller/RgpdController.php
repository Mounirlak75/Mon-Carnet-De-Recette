<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RgpdController extends AbstractController
{
    #[Route('/rgpd', name: 'app_rgpd')]
    public function index(): Response
    {
        // Rend la vue Twig 'rgpd/index.html.twig' avec le nom du contrôleur
        return $this->render('rgpd/index.html.twig', [
            'controller_name' => 'RgpdController',
        ]);
    }
}
