<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message')]
    public function index(MessageRepository $messageRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $receivedMessages = $messageRepository->findBy(['recipient' => $user], ['createdAt' => 'DESC']);
        $sentMessages = $messageRepository->findBy(['sender' => $user], ['createdAt' => 'DESC']);
        $users = $userRepository->findAll();

        return $this->render('message/index.html.twig', [
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'users' => $users,
        ]);
    }

    #[Route('/send/{id}', name: 'app_message_send', methods: ['GET', 'POST'])]
    public function send(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): Response {
        $sender = $this->getUser();

        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($user);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setIsRead(false);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush(); // ✅ flush AVANT la notification

            // ✅ Créer une notification pour le vrai destinataire
            $notificationService->createNotification(
                'message',
                sprintf('%s vous a envoyé un message.', $sender->getFullName()),
                $message->getRecipient(), // très important
                null,
                null,
                $message
            );

            $this->addFlash('success', 'Message envoyé avec succès.');
            return $this->redirectToRoute('app_message');
        }

        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
            'recipient' => $user,
        ]);
    }

    #[Route('/read/{id}', name: 'app_message_read', methods: ['GET'])]
    public function read(Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (
            $message->getRecipient() !== $user &&
            $message->getSender() !== $user &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas lire ce message.');
        }

        if (!$message->isRead() && $message->getRecipient() === $user) {
            $message->setIsRead(true);
            $entityManager->flush();
        }

        return $this->render('message/read.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($message->getSender() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce message.');
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Message mis à jour avec succès.');
            return $this->redirectToRoute('app_message_read', ['id' => $message->getId()]);
        }

        return $this->render('message/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($message->getSender() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce message.');
        }

        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message supprimé avec succès.');
        }

        return $this->redirectToRoute('app_message');
    }
}
