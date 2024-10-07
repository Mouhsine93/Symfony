<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UsersAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if ($user instanceof Utilisateur) {
            // Vérification si le jeton est valide
            if ($user->isJetonValide()) {
                
                // Authentification réussie
                if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
                    return new RedirectResponse($targetPath);
                }
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            } else {
                // Authentification échouée, jeton invalide
                // Rediriger vers la page de connexion
                return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
            }

            // Gérer jeton seulement lors de la connexion
            $newToken = bin2hex(random_bytes(16));
            $user->setJeton($newToken);
            
            // Date d'expiration (30 minutes à partir de maintenant)
            $expirationDate = new \DateTime();
            $expirationDate->modify('+30 minutes');
            $user->setJetonExpireAt($expirationDate);
            
            // Récupère l'EntityManager pour persister les changements
            $this->entityManager->flush(); // Persiste les modifications
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login', [
            'error' => 'Votre jeton a expiré. Veuillez vous reconnecter.'
        ]));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
