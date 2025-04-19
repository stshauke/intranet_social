<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation de l'entité GroupMessage liée à ce formulaire
use App\Entity\GroupMessage;

// Importation des types de champs et classes de base pour les formulaires Symfony
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Déclaration du formulaire GroupMessageType, utilisé pour écrire un message de groupe
class GroupMessageType extends AbstractType
{
    // Construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ "content" de type Textarea (zone de texte multilignes)
            ->add('content', TextareaType::class, [
                'label' => false, // Pas de label affiché dans le formulaire
                'attr' => [
                    'placeholder' => 'Votre message...', // Texte d’aide pour l’utilisateur
                    'rows' => 6, // ✅ Zone de saisie plus haute par défaut
                    'style' => 'min-height: 150px;', // ✅ Hauteur minimum via style CSS en ligne
                ],
            ]);
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // L'entité à laquelle le formulaire est lié : GroupMessage
            'data_class' => GroupMessage::class,
        ]);
    }
}
