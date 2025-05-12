<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNotificationPreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Liste des types à proposer
        $types = [
    'message' => 'Messages privés',
    'new_post' => 'Nouvelles publications',
    'new_comment' => 'Nouveaux commentaires',
    'new_like' => 'Mentions "J\'aime"',
    'mention' => 'Mentions dans un message',
];


        foreach ($types as $type => $label) {
            $builder->add($type, CheckboxType::class, [
                'label' => $label,
                'required' => false,
                'mapped' => false, // car on met à jour les entités manuellement
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
