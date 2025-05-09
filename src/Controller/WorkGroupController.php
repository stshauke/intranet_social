<?php

namespace App\Controller;

use App\Entity\UserWorkGroup;
use App\Entity\WorkGroup;
use App\Form\WorkGroupType;
use App\Repository\UserWorkGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/workgroup')]
class WorkGroupController extends AbstractController
{
    #[Route('/', name: 'workgroup_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $groups = $em->getRepository(WorkGroup::class)->findVisibleToUser($this->getUser());

        return $this->render('workgroup/list.html.twig', [
            'workGroups' => $groups,
        ]);
    }

    #[Route('/new', name: 'workgroup_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $group = new WorkGroup();
        $form = $this->createForm(WorkGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $creator = $this->getUser();
            $group->setCreator($creator);

            $em->persist($group);

            // Le créateur est automatiquement membre
            $userLink = new UserWorkGroup();
            $userLink->setUser($creator);
            $userLink->setWorkGroup($group);
            $userLink->setIsAdmin(true);
            $em->persist($userLink);

            // Ajouter les modérateurs
            foreach ($form->get('moderators')->getData() as $moderator) {
                $group->addModerator($moderator);

                // Lier aussi comme membre
                $link = new UserWorkGroup();
                $link->setUser($moderator);
                $link->setWorkGroup($group);
                $link->setIsAdmin(false);
                $em->persist($link);
            }

            $em->flush();

            $this->addFlash('success', 'Groupe créé avec succès.');
            return $this->redirectToRoute('workgroup_list');
        }

        return $this->render('workgroup/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'workgroup_show')]
    public function show(WorkGroup $workGroup, UserWorkGroupRepository $uwgRepo): Response
    {
        $user = $this->getUser();
        $isMember = $uwgRepo->isMember($user, $workGroup);

        if ($workGroup->getType() === 'private' && !$isMember) {
            throw $this->createAccessDeniedException('Accès réservé aux membres du groupe.');
        }

        if ($workGroup->getType() === 'secret' && !$isMember) {
            throw $this->createNotFoundException('Groupe introuvable.');
        }

        $userLinks = $uwgRepo->findBy(['workGroup' => $workGroup]);

        return $this->render('workgroup/show.html.twig', [
            'workGroup' => $workGroup,
            'userLinks' => $userLinks,
        ]);
    }

    #[Route('/{id}/edit', name: 'workgroup_edit')]
    public function edit(Request $request, WorkGroup $workGroup, EntityManagerInterface $em, UserWorkGroupRepository $uwgRepo): Response
    {
        $user = $this->getUser();

        if ($user !== $workGroup->getCreator() && !$workGroup->getModerators()->contains($user)) {
            throw $this->createAccessDeniedException("Seuls le créateur ou un modérateur peuvent modifier ce groupe.");
        }

        $form = $this->createForm(WorkGroupType::class, $workGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Nettoyer les anciens liens membres
            $oldLinks = $uwgRepo->findBy(['workGroup' => $workGroup]);
            foreach ($oldLinks as $link) {
                $em->remove($link);
            }

            // Ajouter à nouveau le créateur
            $creatorLink = new UserWorkGroup();
            $creatorLink->setUser($workGroup->getCreator());
            $creatorLink->setWorkGroup($workGroup);
            $creatorLink->setIsAdmin(true);
            $em->persist($creatorLink);

            // Modérateurs = membres aussi
            $workGroup->getModerators()->clear();
            foreach ($form->get('moderators')->getData() as $moderator) {
                $workGroup->addModerator($moderator);

                $link = new UserWorkGroup();
                $link->setUser($moderator);
                $link->setWorkGroup($workGroup);
                $link->setIsAdmin(false);
                $em->persist($link);
            }

            $em->flush();

            $this->addFlash('success', 'Groupe mis à jour.');
            return $this->redirectToRoute('workgroup_show', ['id' => $workGroup->getId()]);
        }

        return $this->render('workgroup/edit.html.twig', [
            'form' => $form->createView(),
            'workGroup' => $workGroup,
        ]);
    }

    #[Route('/{id}/delete', name: 'workgroup_delete', methods: ['POST'])]
    public function delete(Request $request, WorkGroup $workGroup, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workGroup->getId(), $request->request->get('_token'))) {
            $em->remove($workGroup);
            $em->flush();
            $this->addFlash('success', 'Groupe supprimé.');
        }

        return $this->redirectToRoute('workgroup_list');
    }
}
