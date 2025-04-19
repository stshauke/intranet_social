<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation de l'entité liée au formulaire
use App\Entity\Post;

// Importation des types de champs pour le formulaire
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

// Configuration des options du formulaire
use Symfony\Component\OptionsResolver\OptionsResolver;

// Contraintes de validation pour les fichiers uploadés
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;

// Déclaration de la classe PostType utilisée pour créer ou modifier un post
class PostType extends AbstractType
{
    // Méthode de construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ de saisie pour le titre de la publication
            ->add('title', TextType::class, [
                'label' => 'Titre de la publication',
                'attr' => ['placeholder' => 'Saisir le titre de votre publication'],
            ])
            // Champ de saisie pour le contenu (zone de texte multilignes)
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la publication',
                'attr' => ['rows' => 5, 'placeholder' => 'Exprimez-vous ici...'],
            ])
            // Champ de téléchargement de fichiers (images ou PDF)
            ->add('attachments', FileType::class, [
                'label' => 'Ajouter des fichiers ou images',
                'mapped' => false, // Ce champ n’est pas lié directement à l’entité Post
                'multiple' => true, // Autorise le téléchargement de plusieurs fichiers
                'required' => false, // Champ optionnel
                'constraints' => [
                    // Appliquer les contraintes à tous les fichiers uploadés
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M', // Taille maximale par fichier
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
                // Attribut HTML : accepte uniquement certains types de fichiers côté client
                'attr' => [
                    'accept' => 'image/*,application/pdf'
                ]
            ])
        ;
    }

    // Configuration du formulaire : association avec l'entité Post
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class, // L'entité à laquelle le formulaire est lié
        ]);
    }
}
