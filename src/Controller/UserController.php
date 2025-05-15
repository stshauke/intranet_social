<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des entités
use App\Entity\Message;
use App\Entity\User;

// Importation des formulaires
use App\Form\UserProfileType;
use App\Form\MessageType;

// Importation des repositories
use App\Repository\UserRepository;

// Importation des services et composants nécessaires
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse; // ✅ Ajouté
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// Définition de la route principale pour ce contrôleur
#[Route('/user')]
class UserController extends AbstractController
{
    // Route pour afficher la liste des utilisateurs avec pagination et recherche
    #[Route('/', name: 'app_user')]
    public function index(
        Request $request,                        // Requête HTTP contenant les paramètres (dont "search")
        UserRepository $userRepository,          // Repository pour accéder aux utilisateurs
        PaginatorInterface $paginator            // Service de pagination
    ): Response {
        $searchTerm = $request->query->get('search'); // Récupération du terme de recherche

        // Création de la requête de base
        $queryBuilder = $userRepository->createQueryBuilder('u');

        // Si un terme de recherche est présent, on filtre les résultats
        if ($searchTerm) {
            $queryBuilder
                ->where('u.fullName LIKE :search OR u.email LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }

        // Application de la pagination à la requête
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6 // Nombre d’utilisateurs par page
        );

        // Affichage de la vue avec les utilisateurs filtrés/paginés
        return $this->render('user/index.html.twig', [
            'users' => $pagination,
            'searchTerm' => $searchTerm,
        ]);
    }

    // Route pour afficher le profil d'un utilisateur
    #[Route('/{id}', name: 'app_user_profile', methods: ['GET'])]
    public function profile(User $user): Response
    {
        // Affichage du profil utilisateur
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    // Route pour permettre à l'utilisateur de modifier son profil
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,                         // Requête HTTP
        User $user,                               // Utilisateur à modifier
        EntityManagerInterface $entityManager,    // Pour la sauvegarde en base
        SluggerInterface $slugger                 // Pour sécuriser les noms de fichiers
    ): Response {
        // Vérifie que l'utilisateur connecté ne modifie que son propre profil
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        // Création et traitement du formulaire de profil
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l’image de profil si un fichier a été envoyé
            $profileImageFile = $form->get('profileImage')->getData();

            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                // Déplacement du fichier dans le répertoire prévu
                $profileImageFile->move(
                    $this->getParameter('profiles_directory'),
                    $newFilename
                );

                // Mise à jour de l’utilisateur avec le nom du fichier
                $user->setProfileImage($newFilename);
            }

            // Enregistrement des changements en base
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Profil mis à jour avec succès.');

            // Redirection vers la page du profil
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        // Affichage du formulaire de modification
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    // Route pour envoyer un message via un modal (AJAX ou formulaire dans une boîte)
    #[Route('/{id}/message-modal', name: 'app_user_message_modal', methods: ['GET', 'POST'])]
    public function messageModal(
        Request $request,                         // Requête HTTP
        User $user,                               // Destinataire du message
        EntityManagerInterface $entityManager     // Gestion de la base de données
    ): Response {
        $message = new Message();                // Création d'un nouveau message
        $message->setSender($this->getUser());   // L’expéditeur est l’utilisateur connecté
        $message->setRecipient($user);           // Le destinataire est passé en paramètre
        $message->setCreatedAt(new \DateTimeImmutable()); // Date d'envoi
        $message->setIsRead(false);              // Statut non lu par défaut

        // Création et traitement du formulaire
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        // Si le formulaire est valide, on enregistre le message
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            // Réponse JSON pour signaler le succès (utilisée en AJAX)
            return $this->json(['success' => true]);
        }

        // Rendu du formulaire dans un fragment Twig (_modal.html.twig)
        return $this->render('message/_modal.html.twig', [
            'form' => $form->createView(),
            'recipient' => $user,
        ]);
    }

    // ✅ Route de recherche pour l'autocomplétion des mentions
    #[Route('/mention/search', name: 'mention_search', methods: ['GET'])]
    public function mentionSearch(Request $request, UserRepository $userRepo): JsonResponse
    {
        $term = $request->query->get('q', '');
        $users = $userRepo->createQueryBuilder('u')
            ->where('u.username LIKE :term')
            ->setParameter('term', $term . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $results = array_map(fn(User $u) => [
            'id' => $u->getId(),
            'value' => '@' . $u->getUsername(),
        ], $users);

        return new JsonResponse($results);
    }
}
