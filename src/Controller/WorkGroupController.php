<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkGroup;
use App\Form\WorkGroupType;
use App\Repository\WorkGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/group')]
class WorkGroupController extends AbstractController
{
    #[Route('/', name: 'app_work_group', methods: ['GET'])]
    public function index(WorkGroupRepository $workGroupRepository): Response
    {
        return $this->render('work_group/index.html.twig', [
            'work_groups' => $workGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_work_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workGroup = new WorkGroup();
        $form = $this->createForm(WorkGroupType::class, $workGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créateur du groupe
            $workGroup->setCreator($this->getUser());

            // Récupérer les membres sélectionnés dans le formulaire
            $members = $form->get('members')->getData();
            foreach ($members as $member) {
                $workGroup->addMember($member);
            }

            $entityManager->persist($workGroup);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe créé avec succès.');

            return $this->redirectToRoute('app_work_group');
        }

        return $this->render('work_group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_work_group_show', methods: ['GET'])]
    public function show(WorkGroup $workGroup): Response
    {
        return $this->render('work_group/show.html.twig', [
            'work_group' => $workGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_work_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkGroup $workGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkGroupType::class, $workGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vider les anciens membres du groupe
            foreach ($workGroup->getMembers() as $member) {
                $workGroup->removeMember($member);
            }

            // Ajouter les nouveaux membres sélectionnés
            $members = $form->get('members')->getData();
            foreach ($members as $member) {
                $workGroup->addMember($member);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Groupe mis à jour avec succès.');

            return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
        }

        return $this->render('work_group/edit.html.twig', [
            'form' => $form->createView(),
            'work_group' => $workGroup,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_work_group_delete', methods: ['POST'])]
    public function delete(Request $request, WorkGroup $workGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workGroup->getId(), $request->request->get('_token'))) {
            // ✅ Nettoyer la table de liaison user_work_group
            $connection = $entityManager->getConnection();
            $connection->executeStatement('DELETE FROM user_work_group WHERE work_group_id = :id', [
                'id' => $workGroup->getId(),
            ]);

            // ✅ Supprimer les posts liés
            foreach ($workGroup->getPosts() as $post) {
                $entityManager->remove($post);
            }

            // ✅ Supprimer les messages du forum de groupe
            foreach ($workGroup->getMessages() as $message) {
                $entityManager->remove($message);
            }

            // ✅ Supprimer le groupe
            $entityManager->remove($workGroup);
            $entityManager->flush();

            $this->addFlash('success', 'Le groupe a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_work_group');
    }
}
