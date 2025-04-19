<?php

// Déclaration du namespace
namespace App\Controller;

// Importation des classes nécessaires
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// Déclaration du contrôleur responsable de l'inscription des utilisateurs
class RegistrationController extends AbstractController
{
    // Route pour accéder au formulaire d'inscription
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,                                   // Objet représentant la requête HTTP
        UserPasswordHasherInterface $userPasswordHasher,   // Service pour hasher les mots de passe
        EntityManagerInterface $entityManager,              // Interface pour les opérations base de données
        SluggerInterface $slugger                           // Service pour sécuriser le nom de fichier
    ): Response {
        // Création d'un nouvel utilisateur vide
        $user = new User();

        // Création du formulaire d'inscription lié à l'entité User
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request); // Traitement de la requête

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe avant stockage
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Attribution du rôle utilisateur par défaut
            $user->setRoles(['ROLE_USER']);

            // ✅ Gestion de l'upload de la photo de profil
            $profileImageFile = $form->get('profileImage')->getData();

            // Si une image de profil a été envoyée
            if ($profileImageFile) {
                // Génération d'un nom de fichier sécurisé
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                // Déplacement du fichier dans le dossier configuré
                try {
                    $profileImageFile->move(
                        $this->getParameter('profiles_directory'),
                        $newFilename
                    );
                    // Enregistrement du nom de fichier dans l'entité
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    // Gestion des erreurs de téléchargement
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo de profil.');
                }
            }

            // Sauvegarde de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');

            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire dans la vue Twig
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
