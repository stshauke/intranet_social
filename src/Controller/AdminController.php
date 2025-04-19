<?php

namespace App\Controller;

// Importation des classes nécessaires
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\UserProfileType;

// Définition de la route principale pour le contrôleur d'administration
#[Route('/admin')]
class AdminController extends AbstractController
{
    // Route pour afficher le tableau de bord d'administration
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(UserRepository $userRepository): Response
    {
        // Récupère tous les utilisateurs depuis la base de données
        $users = $userRepository->findAll();

        // Rend la vue du tableau de bord avec la liste des utilisateurs
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    // Route pour éditer un utilisateur via un formulaire
    #[Route('/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(
        Request $request,                      // Requête HTTP
        User $user,                            // Utilisateur à modifier (injection automatique)
        EntityManagerInterface $entityManager, // Pour interagir avec la base de données
        SluggerInterface $slugger              // Pour sécuriser le nom des fichiers
    ): Response {
        // Création du formulaire de profil utilisateur
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request); // Traitement de la requête

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère le fichier d'image de profil
            $profileImageFile = $form->get('profileImage')->getData();

            // Si un fichier a été téléchargé
            if ($profileImageFile) {
                // Génère un nom de fichier sécurisé et unique
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                // Déplace le fichier vers le répertoire de destination
                $profileImageFile->move(
                    $this->getParameter('profiles_directory'),
                    $newFilename
                );

                // Met à jour l'image de profil de l'utilisateur
                $user->setProfileImage($newFilename);
            }

            // Enregistre les modifications dans la base de données
            $entityManager->flush();

            // Affiche un message de succès
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');

            // Redirige vers le tableau de bord admin
            return $this->redirectToRoute('admin_dashboard');
        }

        // Rend la vue d'édition avec le formulaire
        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    // Route pour supprimer un utilisateur (requête POST uniquement)
    #[Route('/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifie la validité du token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Supprime l'utilisateur et enregistre les changements
            $entityManager->remove($user);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        // Redirige vers le tableau de bord admin
        return $this->redirectToRoute('admin_dashboard');
    }
}
