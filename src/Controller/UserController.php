<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Form\MessageType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response {
        $searchTerm = $request->query->get('search');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($searchTerm) {
            $queryBuilder
                ->where('u.fullName LIKE :search OR u.email LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6 // Nombre d’utilisateurs par page
        );

        return $this->render('user/index.html.twig', [
            'users' => $pagination,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/{id}', name: 'app_user_profile', methods: ['GET'])]
    public function profile(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileImageFile = $form->get('profileImage')->getData();

            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                $profileImageFile->move(
                    $this->getParameter('profiles_directory'),
                    $newFilename
                );

                $user->setProfileImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/message-modal', name: 'app_user_message_modal', methods: ['GET', 'POST'])]
    public function messageModal(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $message = new Message();
        $message->setSender($this->getUser());
        $message->setRecipient($user);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setIsRead(false);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->render('message/_modal.html.twig', [
            'form' => $form->createView(),
            'recipient' => $user,
        ]);
    }
}
