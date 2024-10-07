<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user instanceof Utilisateur) {
            $newToken = bin2hex(random_bytes(16));
            $user->setJeton($newToken);
    
            // Définir la date d'expiration (par exemple, 30 minutes à partir de maintenant)
            $expirationDate = new \DateTime();
            $expirationDate->modify('+30 minutes');
            $user->setJetonExpireAt($expirationDate);
            
            // Déboguer pour voir les valeurs
            dump($user->getJeton()); // Vérifiez le jeton
            dump($user->getJetonExpireAt()); // Vérifiez la date d'expiration
            
            $entityManager->flush(); // Persiste les modifications
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

    }
}
