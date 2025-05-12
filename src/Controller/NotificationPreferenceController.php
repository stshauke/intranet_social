<?php

namespace App\Controller;

use App\Entity\NotificationPreference;
use App\Form\UserNotificationPreferencesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications/preferences')]
class NotificationPreferenceController extends AbstractController
{
    #[Route('/', name: 'app_notification_preferences')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Types pris en charge
        $types = ['message', 'new_post', 'new_comment', 'new_like', 'mention'];
        $preferences = [];

        // Initialisation ou récupération des préférences
        foreach ($types as $type) {
            $preference = $user->getNotificationPreferenceForType($type);

            if (!$preference) {
                $preference = new NotificationPreference();
                $preference->setUser($user);
                $preference->setType($type);
                $preference->setEnabled(true); // par défaut
                $em->persist($preference);
                $user->addNotificationPreference($preference);
            }

            $preferences[$type] = $preference;
        }

        // Création du formulaire
        $form = $this->createForm(UserNotificationPreferencesType::class);

        // Pré-remplissage du formulaire avec les préférences
        foreach ($preferences as $type => $preference) {
            if ($form->has($type)) {
                $form->get($type)->setData($preference->isEnabled());
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($preferences as $type => $preference) {
                if ($form->has($type)) {
                    $enabled = $form->get($type)->getData();
                    $preference->setEnabled($enabled);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Préférences mises à jour.');
            return $this->redirectToRoute('app_notification_preferences');
        }

        return $this->render('notification/preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
