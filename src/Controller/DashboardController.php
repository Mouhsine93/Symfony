<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]

    public function index(): Response
    {
        // Données statiques pour l'exemple
        $data = [
            'nombreCommandes' => 80,
            'nombreVentes' => 180,
            'nombreArticles' => 90,
            'chiffreAffaires' => 20000,
            'pourcentageCommandes' => 5,
            'pourcentageVentes' => 8,
            'pourcentageArticles' => 3,
            'pourcentageCA' => 7,
            'ventesRecentes' => [
                ['date' => '02 Août 2024', 'client' => 'Francois Jacques', 'article' => 'Redmi 9', 'prix' => 800],
                ['date' => '02 Août 2024', 'client' => 'Francois Jacques', 'article' => 'HP dek', 'prix' => 1500],
                // Ajoutez les autres ventes ici
            ]
        ];
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
