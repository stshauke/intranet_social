<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation de l'entité liée au formulaire
use App\Entity\Comment;

// Importation des composants de base du système de formulaire de Symfony
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Déclaration de la classe CommentType, utilisée pour créer ou modifier un commentaire
class CommentType extends AbstractType
{
    // Construction du formulaire (ajout des champs)
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajout du champ 'content' de type zone de texte
            ->add('content', TextareaType::class, [
                'label' => false, // Pas de label affiché
                'attr' => [
                    'placeholder' => 'Écrivez votre commentaire...', // Texte d’aide à la saisie
                    'rows' => 3 // Nombre de lignes visibles par défaut
                ]
            ]);
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class, // Le formulaire sera mappé à l’entité Comment
        ]);
    }
}
