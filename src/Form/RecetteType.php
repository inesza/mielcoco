<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use App\Entity\Categorie; 
use App\Entity\Composition; 

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', Type\TextType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Nom de la recette"
                ],
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Ce champ ne peut pas être vide"]),
                    new Constraints\Length([ 
                        "min" => 2, 
                        "max" => 150, 
                        "minMessage" => "Le nom de la recette doit être compris entre 2 et 150 caractères", 
                        "maxMessage" => "Le nom de la recette doit être compris entre 2 et 150 caractères"])
                ]
            ])
            ->add('description', Type\TextType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Description courte"
                ]
            ])
            ->add('instructions', Type\TextareaType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Instructions"
                ]
            ])
            ->add('photo', Type\FileType::class, [
                "mapped" => false,
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Ajouter une photo"
                ]
            ])
            ->add('niveau', ChoiceType::class, [
                "expanded" => true,
                "attr" => ["class" => "form-check"],
                'choices'  => [
                    'Débutant' => "debutant",
                    'Intermédiaire' => "intermediaire",
                    'Confirmé' => "confirme"
                ]
            ])
            ->add('categorie', EntityType::class, [ 
                "mapped" => false,
                "class" => Categorie::class, 
                "expanded" => true,
                "multiple" => true,
                "attr" => ["class" => "form-check"],
                "choice_label" => function(Categorie $categorie){
                    return $categorie->getNom();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
