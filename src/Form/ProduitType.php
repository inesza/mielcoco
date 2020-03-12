<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', Type\TextType::class, [
                "attr" => ["placeholder" => "Nom du produit"],
                "label" => "Nom du produit",
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Ce champ ne peut pas être vide"]),
                    new Constraints\Length([ 
                        "min" => 2, 
                        "max" => 150, 
                        "minMessage" => "Le nom du produit doit être compris entre 2 et 150 caractères", 
                        "maxMessage" => "Le nom du produit doit être compris entre 2 et 150 caractères"])
                ]
            ])
            ->add('photo', Type\FileType::class, ["mapped" => false])
            ->add('unite')
            ->add('prix_unitaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
