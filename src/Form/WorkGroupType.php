<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WorkGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du groupe',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description du groupe',
            ])
            ->add('isPrivate', ChoiceType::class, [
                'label' => 'Type de groupe',
                'choices' => [
                    'Public' => false,
                    'Privé' => true,
                ],
            ])
            ->add('members', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'expanded' => false, // ✅ Liste déroulante propre
                'mapped' => false,
                'label' => 'Membres du groupe',
                'attr' => [
                    'class' => 'select2',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkGroup::class,
        ]);
    }
}
