<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation de l'entité User utilisée par le formulaire
use App\Entity\User;

// Importation des types de champs nécessaires pour le formulaire
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

// Configuration des options du formulaire
use Symfony\Component\OptionsResolver\OptionsResolver;

// Contraintes de validation sur les fichiers
use Symfony\Component\Validator\Constraints\File;

// Formulaire d’inscription d’un nouvel utilisateur
class RegistrationFormType extends AbstractType
{
    // Construction des champs du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom complet
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['placeholder' => 'Votre nom complet'],
            ])
            // Champ pour l'adresse email
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'exemple@entreprise.com'],
            ])
            // Champ pour le mot de passe (non mappé car il sera encodé manuellement)
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // Important : ce champ n'est pas lié directement à l'entité
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Choisissez un mot de passe'
                ],
                'required' => true,
            ])
            // Champ pour l’upload de la photo de profil
            ->add('profileImage', FileType::class, [
                'label' => 'Photo de profil (JPEG ou PNG)',
                'mapped' => false, // ✅ important : ce champ est géré manuellement dans le contrôleur
                'required' => false, // Ce champ est optionnel
                'constraints' => [
                    new File([
                        'maxSize' => '2M', // Taille maximale autorisée
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou WebP)',
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*' // Accepte uniquement les images côté client
                ]
            ])
            // Champ pour la biographie de l'utilisateur
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Parlez un peu de vous...',
                    'rows' => 4,
                ],
            ])
        ;
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // L'entité liée au formulaire
        ]);
    }
}
