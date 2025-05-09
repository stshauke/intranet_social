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
            ->add('type', ChoiceType::class, [
                'label' => 'Type de groupe',
                'choices' => [
                    'Public' => 'public',
                    'Privé' => 'private',
                    'Secret' => 'secret',
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('moderators', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'required' => false,
                'label' => 'Modérateurs',
                'attr' => ['class' => 'select2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkGroup::class,
        ]);
    }
}
