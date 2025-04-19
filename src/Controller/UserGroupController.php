<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des classes nécessaires de Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Contrôleur de base Symfony
use Symfony\Component\HttpFoundation\Response;                    // Représente une réponse HTTP
use Symfony\Component\Routing\Attribute\Route;                    // Permet de définir les routes via les attributs

// Déclaration du contrôleur final (non extensible) UserGroupController
final class UserGroupController extends AbstractController
{
    // Définition de la route "/user/group" avec le nom "app_user_group"
    #[Route('/user/group', name: 'app_user_group')]
    public function index(): Response
    {
        // Affiche la vue Twig "user_group/index.html.twig" avec une variable "controller_name"
        return $this->render('user_group/index.html.twig', [
            'controller_name' => 'UserGroupController',
        ]);
    }
}
