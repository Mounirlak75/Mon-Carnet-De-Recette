<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        // Rend la vue Twig 'user/index.html.twig' et peut également passer des données à cette vue si nécessaire
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController', // Exemple de donnée passée à la vue (non utilisée ici)
        ]);
    }
}