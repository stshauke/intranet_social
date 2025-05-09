<?php

namespace App\Form;

use App\Entity\GroupInvitation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];
        $workGroup = $options['work_group'];

        $builder
            ->add('invitedUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => 'Utilisateur à inviter',
                'query_builder' => function (UserRepository $ur) use ($currentUser, $workGroup) {
                    return $ur->createQueryBuilder('u')
                        ->where('u != :currentUser')
                        ->setParameter('currentUser', $currentUser);
                },
                'attr' => ['class' => 'form-control select2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupInvitation::class,
            'current_user' => null,
            'work_group' => null,
        ]);
    }
}
