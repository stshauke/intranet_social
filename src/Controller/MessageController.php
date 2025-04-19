<?php

// Déclaration du namespace
namespace App\Controller;

// Importation des entités, formulaires, repositories et composants nécessaires
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Déclaration du contrôleur pour la gestion des messages privés
#[Route('/message')]
class MessageController extends AbstractController
{
    // Route principale d'affichage des messages reçus et envoyés
    #[Route('/', name: 'app_message')]
    public function index(MessageRepository $messageRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser(); // Récupération de l'utilisateur connecté

        // Récupération des messages reçus (triés du plus récent au plus ancien)
        $receivedMessages = $messageRepository->findBy(['recipient' => $user], ['createdAt' => 'DESC']);

        // Récupération des messages envoyés (triés du plus récent au plus ancien)
        $sentMessages = $messageRepository->findBy(['sender' => $user], ['createdAt' => 'DESC']);

        // Liste de tous les utilisateurs (destinataires possibles)
        $users = $userRepository->findAll();

        // Affichage de la vue avec les données nécessaires
        return $this->render('message/index.html.twig', [
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'users' => $users,
        ]);
    }

    // Route pour envoyer un message à un utilisateur
    #[Route('/send/{id}', name: 'app_message_send', methods: ['GET', 'POST'])]
    public function send(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de message
        $message = new Message();
        $message->setSender($this->getUser());                 // L'expéditeur est l'utilisateur connecté
        $message->setRecipient($user);                         // Le destinataire est celui passé dans l'URL
        $message->setCreatedAt(new \DateTimeImmutable());      // Date d'envoi
        $message->setIsRead(false);                            // Statut : non lu par défaut

        // Création et traitement du formulaire d'envoi
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        // Si le formulaire est valide, on sauvegarde le message
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            // Notification de succès
            $this->addFlash('success', 'Message envoyé avec succès.');

            // Redirection vers la liste des messages
            return $this->redirectToRoute('app_message');
        }

        // Affichage du formulaire d'envoi
        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
            'recipient' => $user,
        ]);
    }

    // Route pour lire un message (GET uniquement)
    #[Route('/read/{id}', name: 'app_message_read', methods: ['GET'])]
    public function read(Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Utilisateur actuel

        // ✅ Vérifie les autorisations : seul le destinataire, l'expéditeur ou un admin peut lire
        if (
            $message->getRecipient() !== $user &&
            $message->getSender() !== $user &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas lire ce message.');
        }

        // ✅ Si le message n'est pas lu et que c'est le destinataire, on le marque comme lu
        if (!$message->isRead() && $message->getRecipient() === $user) {
            $message->setIsRead(true);
            $entityManager->flush();
        }

        // Affichage du message dans la vue
        return $this->render('message/read.html.twig', [
            'message' => $message,
        ]);
    }

    // Route pour modifier un message existant
    #[Route('/edit/{id}', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // ✅ Seul l’expéditeur du message ou un admin peut le modifier
        if ($message->getSender() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce message.');
        }

        // Création et traitement du formulaire d’édition
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        // Enregistrement des modifications si formulaire valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Message mis à jour avec succès.');

            return $this->redirectToRoute('app_message_read', ['id' => $message->getId()]);
        }

        // Affichage du formulaire d'édition
        return $this->render('message/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    // Route pour supprimer un message via un formulaire (POST)
    #[Route('/delete/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // ✅ Seul l’expéditeur ou un admin peut supprimer un message
        if ($message->getSender() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce message.');
        }

        // Vérifie le token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message supprimé avec succès.');
        }

        // Redirection vers la boîte de réception
        return $this->redirectToRoute('app_message');
    }
}
