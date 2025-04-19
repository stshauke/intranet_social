<?php

// Déclaration du namespace du formulaire
namespace App\Form;

// Importation de l'entité liée au formulaire
use App\Entity\Message;
use App\Entity\User;

// Importation du type EntityType pour lier une entité dans le formulaire
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

// Importation des composants de base du formulaire Symfony
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Déclaration de la classe MessageType, utilisée pour créer ou envoyer un message privé
class MessageType extends AbstractType
{
    // Construction du formulaire avec les champs nécessaires
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ de sélection du destinataire (type EntityType)
            ->add('recipient', EntityType::class, [
                'class' => User::class, // L'entité associée à ce champ
                'choice_label' => 'fullName', // Le champ utilisé pour l'affichage dans la liste déroulante
                'label' => 'Destinataire', // Label du champ
                'attr' => [
                    'class' => 'select2', // Classe CSS pour styliser ou activer un plugin JS
                    'data-placeholder' => 'Sélectionnez un utilisateur', // Texte d'aide affiché
                ],
            ])
            // Champ de saisie du contenu du message (zone de texte)
            ->add('content', TextareaType::class, [
                'label' => 'Message', // Label du champ
                'attr' => [
                    'placeholder' => 'Écrivez votre message ici...', // Texte d’aide à la saisie
                    'rows' => 4 // Nombre de lignes visibles par défaut
                ],
            ])
        ;
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class, // Le formulaire est lié à l'entité Message
        ]);
    }
}
