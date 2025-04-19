<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation des entités nécessaires pour le formulaire
use App\Entity\User;
use App\Entity\WorkGroup;

// Importation des types de champs utilisés dans le formulaire
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Déclaration du formulaire WorkGroupType pour la création/modification d’un groupe de travail
class WorkGroupType extends AbstractType
{
    // Construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom du groupe
            ->add('name', TextType::class, [
                'label' => 'Nom du groupe',
            ])
            // Champ pour la description du groupe
            ->add('description', TextType::class, [
                'label' => 'Description du groupe',
            ])
            // Champ de choix pour définir si le groupe est privé ou public
            ->add('isPrivate', ChoiceType::class, [
                'label' => 'Type de groupe',
                'choices' => [
                    'Public' => false,
                    'Privé' => true,
                ],
            ])
            // Sélection multiple des membres à ajouter au groupe
            ->add('members', EntityType::class, [
                'class' => User::class, // Entité liée au champ
                'choice_label' => 'fullName', // Ce qui est affiché dans la liste
                'multiple' => true, // Autorise la sélection de plusieurs utilisateurs
                'expanded' => false, // ✅ Affichage sous forme de liste déroulante
                'mapped' => false, // Champ non lié directement à l’entité, géré manuellement
                'label' => 'Membres du groupe',
                'attr' => [
                    'class' => 'select2', // Classe utilisée pour activer un sélecteur JS (type Select2)
                ],
            ]);
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkGroup::class, // Entité liée au formulaire
        ]);
    }
}
