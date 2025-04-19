<?php

// Déclaration du namespace
namespace App\Controller;

// Importation des entités utilisées
use App\Entity\User;
use App\Entity\WorkGroup;

// Importation des formulaires et repositories
use App\Form\WorkGroupType;
use App\Repository\WorkGroupRepository;

// Importation des services nécessaires
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la route de base pour toutes les routes de ce contrôleur
#[Route('/work/group')]
class WorkGroupController extends AbstractController
{
    // Route pour afficher la liste de tous les groupes de travail
    #[Route('/', name: 'app_work_group', methods: ['GET'])]
    public function index(WorkGroupRepository $workGroupRepository): Response
    {
        // Rendu de la vue avec tous les groupes récupérés depuis le repository
        return $this->render('work_group/index.html.twig', [
            'work_groups' => $workGroupRepository->findAll(),
        ]);
    }

    // Route pour créer un nouveau groupe de travail
    #[Route('/new', name: 'app_work_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workGroup = new WorkGroup(); // Création d'un nouvel objet WorkGroup

        // Création du formulaire lié au groupe
        $form = $this->createForm(WorkGroupType::class, $workGroup);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Attribution de l'utilisateur connecté comme créateur du groupe
            $workGroup->setCreator($this->getUser());

            // Récupération des membres sélectionnés dans le formulaire
            $members = $form->get('members')->getData();
            foreach ($members as $member) {
                $workGroup->addMember($member); // Ajout des membres au groupe
            }

            // Sauvegarde du groupe
            $entityManager->persist($workGroup);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Groupe créé avec succès.');

            // Redirection vers la liste des groupes
            return $this->redirectToRoute('app_work_group');
        }

        // Affichage du formulaire de création
        return $this->render('work_group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher un groupe spécifique
    #[Route('/{id}', name: 'app_work_group_show', methods: ['GET'])]
    public function show(WorkGroup $workGroup): Response
    {
        // Affichage du détail du groupe
        return $this->render('work_group/show.html.twig', [
            'work_group' => $workGroup,
        ]);
    }

    // Route pour modifier un groupe existant
    #[Route('/{id}/edit', name: 'app_work_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkGroup $workGroup, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de modification
        $form = $this->createForm(WorkGroupType::class, $workGroup);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Suppression des membres précédents
            foreach ($workGroup->getMembers() as $member) {
                $workGroup->removeMember($member);
            }

            // Ajout des nouveaux membres sélectionnés
            $members = $form->get('members')->getData();
            foreach ($members as $member) {
                $workGroup->addMember($member);
            }

            // Mise à jour en base
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Groupe mis à jour avec succès.');

            // Redirection vers la page du groupe
            return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
        }

        // Affichage du formulaire de modification
        return $this->render('work_group/edit.html.twig', [
            'form' => $form->createView(),
            'work_group' => $workGroup,
        ]);
    }

    // Route pour supprimer un groupe de travail
    #[Route('/{id}/delete', name: 'app_work_group_delete', methods: ['POST'])]
    public function delete(Request $request, WorkGroup $workGroup, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $workGroup->getId(), $request->request->get('_token'))) {

            // ✅ Nettoyage manuel de la table pivot user_work_group
            $connection = $entityManager->getConnection();
            $connection->executeStatement('DELETE FROM user_work_group WHERE work_group_id = :id', [
                'id' => $workGroup->getId(),
            ]);

            // ✅ Suppression des publications liées au groupe
            foreach ($workGroup->getPosts() as $post) {
                $entityManager->remove($post);
            }

            // ✅ Suppression des messages liés au forum du groupe
            foreach ($workGroup->getMessages() as $message) {
                $entityManager->remove($message);
            }

            // ✅ Suppression du groupe lui-même
            $entityManager->remove($workGroup);
            $entityManager->flush();

            // Message flash de confirmation
            $this->addFlash('success', 'Le groupe a été supprimé avec succès.');
        }

        // Redirection vers la liste des groupes
        return $this->redirectToRoute('app_work_group');
    }
}
