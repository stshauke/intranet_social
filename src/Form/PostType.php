<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la publication',
                'attr' => ['placeholder' => 'Saisir le titre de votre publication'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la publication',
                'attr' => ['rows' => 5, 'placeholder' => 'Exprimez-vous ici...'],
            ])
            ->add('attachments', FileType::class, [
                'label' => 'Ajouter des fichiers ou images',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'application/pdf',
                                    'image/webp',
                                ],
                                'mimeTypesMessage' => 'Merci d\'uploader des fichiers valides (JPEG, PNG, WebP ou PDF)',
                            ])
                        ],
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*,application/pdf'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
