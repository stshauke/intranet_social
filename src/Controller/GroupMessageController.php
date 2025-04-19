<?php

namespace App\Controller;

// Importation des entités, formulaires, repositories, services et composants nécessaires
use App\Entity\GroupMessage;
use App\Entity\WorkGroup;
use App\Form\GroupMessageType;
use App\Repository\GroupMessageRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la route de base pour toutes les méthodes de ce contrôleur
#[Route('/group-message')]
class GroupMessageController extends AbstractController
{
    // Affiche les messages du groupe de travail et permet d'en poster un nouveau
    #[Route('/{id}', name: 'app_group_message_index', methods: ['GET', 'POST'])]
    public function index(
        WorkGroup $workGroup,                                     // Groupe de travail courant (résolu via l'ID)
        Request $request,                                         // Requête HTTP
        GroupMessageRepository $groupMessageRepository,           // Repository pour accéder aux messages
        EntityManagerInterface $entityManager,                    // Accès à la BDD
        PaginatorInterface $paginator,                            // Service de pagination
        NotificationService $notificationService                  // Service de notification
    ): Response {
        // Création de la requête pour récupérer les messages du groupe, triés par date croissante
        $query = $groupMessageRepository->createQueryBuilder('gm')
            ->andWhere('gm.workGroup = :groupId')
            ->setParameter('groupId', $workGroup->getId())
            ->orderBy('gm.createdAt', 'ASC')
            ->getQuery();

        // Application de la pagination sur la requête
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Récupère le numéro de page depuis l'URL
            10                                  // Nombre d'éléments par page
        );

        // Création du formulaire de message
        $groupMessage = new GroupMessage();
        $form = $this->createForm(GroupMessageType::class, $groupMessage);
        $form->handleRequest($request);

        // Traitement du formulaire s’il est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Attribution de l’auteur et du groupe au message
            $groupMessage->setAuthor($this->getUser());
            $groupMessage->setWorkGroup($workGroup);

            // Analyse du contenu pour détecter les mentions
            $content = $groupMessage->getContent();
            preg_match_all('/@(\w+)/', $content, $mentions); // Extraction des pseudos mentionnés
            $mentionedUsers = [];

            // Recherche des utilisateurs mentionnés dans la base de données
            if (!empty($mentions[1])) {
                $userRepository = $entityManager->getRepository('App:User');
                foreach ($mentions[1] as $username) {
                    $user = $userRepository->findOneBy(['fullName' => $username]);
                    if ($user) {
                        $mentionedUsers[] = $user;
                    }
                }
            }

            // Persistance du message en base
            $entityManager->persist($groupMessage);
            $entityManager->flush();

            // Envoi d'une notification à chaque utilisateur mentionné
            foreach ($mentionedUsers as $mentionedUser) {
                $notificationService->createNotification(
                    'mention',
                    sprintf('%s vous a mentionné dans le groupe "%s".', $this->getUser()->getFullName(), $workGroup->getName()),
                    $mentionedUser
                );
            }

            // Notification à tous les membres du groupe qu’un nouveau message a été posté
            $notificationService->notifyWorkGroupMembers(
                $workGroup,
                sprintf('%s a posté un nouveau message dans le groupe "%s".', $this->getUser()->getFullName(), $workGroup->getName())
            );

            // Redirection pour éviter la double soumission du formulaire
            return $this->redirectToRoute('app_group_message_index', ['id' => $workGroup->getId()]);
        }

        // Affichage de la vue avec les messages paginés et le formulaire
        return $this->render('group_message/index.html.twig', [
            'workGroup' => $workGroup,
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour modifier un message de groupe existant
    #[Route('/{id}/edit', name: 'app_group_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupMessage $groupMessage, EntityManagerInterface $entityManager): Response
    {
        // Seul l’auteur ou un admin peut éditer un message
        if ($groupMessage->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Création et gestion du formulaire d’édition
        $form = $this->createForm(GroupMessageType::class, $groupMessage);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on enregistre
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Message mis à jour avec succès.');
            return $this->redirectToRoute('app_group_message_index', ['id' => $groupMessage->getWorkGroup()->getId()]);
        }

        // Affichage du formulaire d’édition
        return $this->render('group_message/edit.html.twig', [
            'form' => $form->createView(),
            'groupMessage' => $groupMessage,
        ]);
    }

    // Méthode pour supprimer un message de groupe
    #[Route('/{id}/delete', name: 'app_group_message_delete', methods: ['POST'])]
    public function delete(Request $request, GroupMessage $groupMessage, EntityManagerInterface $entityManager): Response
    {
        // Vérifie les permissions : auteur ou admin
        if ($groupMessage->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Vérifie la validité du token CSRF
        if ($this->isCsrfTokenValid('delete' . $groupMessage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupMessage); // Suppression du message
            $entityManager->flush();               // Application en base
            $this->addFlash('success', 'Message supprimé avec succès.');
        }

        // Redirection vers la vue des messages du groupe
        return $this->redirectToRoute('app_group_message_index', ['id' => $groupMessage->getWorkGroup()->getId()]);
    }
}
