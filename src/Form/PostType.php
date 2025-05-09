<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\WorkGroup;
use App\Repository\WorkGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bundle\SecurityBundle\Security; // ✅ CORRECT ici (au lieu de Component\Security\Core\Security)

class PostType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la publication'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu'
            ])
            ->add('workGroup', EntityType::class, [
                'class' => WorkGroup::class,
                'choice_label' => 'name',
                'label' => 'Groupe de travail (facultatif)',
                'required' => false,
                'query_builder' => function (WorkGroupRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('g')
                        ->leftJoin('g.userLinks', 'link')
                        ->where('g.type = :public')
                        ->orWhere('link.user = :user')
                        ->setParameter('public', 'public')
                        ->setParameter('user', $user);
                }
            ])
            ->add('attachments', FileType::class, [
                'label' => 'Fichiers joints',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'application/pdf',
                                ],
                            ])
                        ]
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
