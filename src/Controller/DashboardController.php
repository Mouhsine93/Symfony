<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        // Vérifie si l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user instanceof Utilisateur || !$user->isJetonValide()) {
            // Redirige vers la page de connexion si l'utilisateur n'est pas authentifié ou si le jeton a expiré
            return new RedirectResponse($this->generateUrl('app_login', [
                'error' => 'Votre session a expiré. Veuillez vous reconnecter.'
            ]));
        }

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
            'data' => $data, // Passe les données à la vue
        ]);
    }
}
