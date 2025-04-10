<?php

namespace App\Controller;

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

#[Route('/group-message')]
class GroupMessageController extends AbstractController
{
    #[Route('/{id}', name: 'app_group_message_index', methods: ['GET', 'POST'])]
    public function index(
        WorkGroup $workGroup,
        Request $request,
        GroupMessageRepository $groupMessageRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        NotificationService $notificationService
    ): Response {
        $query = $groupMessageRepository->createQueryBuilder('gm')
            ->andWhere('gm.workGroup = :groupId')
            ->setParameter('groupId', $workGroup->getId())
            ->orderBy('gm.createdAt', 'ASC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $groupMessage = new GroupMessage();
        $form = $this->createForm(GroupMessageType::class, $groupMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupMessage->setAuthor($this->getUser());
            $groupMessage->setWorkGroup($workGroup);

            $content = $groupMessage->getContent();

            preg_match_all('/@(\w+)/', $content, $mentions);
            $mentionedUsers = [];
            if (!empty($mentions[1])) {
                $userRepository = $entityManager->getRepository('App:User');
                foreach ($mentions[1] as $username) {
                    $user = $userRepository->findOneBy(['fullName' => $username]);
                    if ($user) {
                        $mentionedUsers[] = $user;
                    }
                }
            }

            $entityManager->persist($groupMessage);
            $entityManager->flush();

            foreach ($mentionedUsers as $mentionedUser) {
                $notificationService->createNotification(
                    'mention',
                    sprintf('%s vous a mentionné dans le groupe "%s".', $this->getUser()->getFullName(), $workGroup->getName()),
                    $mentionedUser
                );
            }

            $notificationService->notifyWorkGroupMembers(
                $workGroup,
                sprintf('%s a posté un nouveau message dans le groupe "%s".', $this->getUser()->getFullName(), $workGroup->getName())
            );

            return $this->redirectToRoute('app_group_message_index', ['id' => $workGroup->getId()]);
        }

        return $this->render('group_message/index.html.twig', [
            'workGroup' => $workGroup,
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupMessage $groupMessage, EntityManagerInterface $entityManager): Response
    {
        if ($groupMessage->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(GroupMessageType::class, $groupMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Message mis à jour avec succès.');
            return $this->redirectToRoute('app_group_message_index', ['id' => $groupMessage->getWorkGroup()->getId()]);
        }

        return $this->render('group_message/edit.html.twig', [
            'form' => $form->createView(),
            'groupMessage' => $groupMessage,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_group_message_delete', methods: ['POST'])]
    public function delete(Request $request, GroupMessage $groupMessage, EntityManagerInterface $entityManager): Response
    {
        if ($groupMessage->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $groupMessage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupMessage);
            $entityManager->flush();
            $this->addFlash('success', 'Message supprimé avec succès.');
        }

        return $this->redirectToRoute('app_group_message_index', ['id' => $groupMessage->getWorkGroup()->getId()]);
    }
}
