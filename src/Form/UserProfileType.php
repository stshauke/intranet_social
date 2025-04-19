<?php

// Déclaration du namespace pour le formulaire
namespace App\Form;

// Importation des classes nécessaires
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;

// Définition du formulaire UserProfileType pour modifier un profil utilisateur
class UserProfileType extends AbstractType
{
    // Construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom complet
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['placeholder' => 'Nom complet de l\'utilisateur'],
            ])
            // Champ pour l’adresse email
            ->add('email', TextType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['placeholder' => 'Adresse e-mail'],
            ])
            // Champ optionnel pour la biographie de l'utilisateur
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Parlez un peu de l\'utilisateur...',
                    'rows' => 4
                ],
            ])
            // Champ pour l’upload de l’image de profil
            ->add('profileImage', FileType::class, [
                'label' => 'Photo de profil (JPEG, PNG, WEBP)',
                'mapped' => false, // Champ non lié à l'entité, géré manuellement
                'required' => false, // Ce champ est optionnel
                'constraints' => [
                    new File([
                        'maxSize' => '2M', // Taille maximale autorisée
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, WebP)',
                    ])
                ],
            ])
            // Champ pour choisir les rôles de l'utilisateur
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true, // ✅ Affiche les choix sous forme de cases à cocher
                'multiple' => true, // Autorise plusieurs choix
                'required' => true,
            ]);
    }

    // Configuration du formulaire : liaison avec l'entité User
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // L'entité associée à ce formulaire
        ]);
    }
}
