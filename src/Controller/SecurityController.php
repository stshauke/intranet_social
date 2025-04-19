<?php

// Déclaration du namespace
namespace App\Controller;

// Importation des classes nécessaires
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Contrôleur de base Symfony
use Symfony\Component\HttpFoundation\Response;                   // Classe pour représenter une réponse HTTP
use Symfony\Component\Routing\Attribute\Route;                   // Annotation pour les routes
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; // Utilitaire pour la gestion de l'authentification

// Déclaration du contrôleur responsable de la sécurité (login/logout)
class SecurityController extends AbstractController
{
    // Route pour afficher le formulaire de connexion
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Affichage du formulaire de connexion avec :
        // - le dernier nom d'utilisateur saisi
        // - l'éventuelle erreur d'authentification
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    // Route pour la déconnexion de l'utilisateur
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode ne sera jamais exécutée :
        // Symfony intercepte la route et gère la déconnexion automatiquement via firewall
        throw new \LogicException('Logout is handled by Symfony security.');
    }
}
