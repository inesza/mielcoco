<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints;

use App\Entity\User;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('prenom', Type\TextType::class, [
            "attr" => [
                "placeholder" => "Prénom",
            ],
            "label" => false,
            "constraints" => [
                new Constraints\NotBlank([ "message" => "Veuillez renseigner votre prénom"]),
                new Constraints\Length([ 
                    "min" => 2, 
                    "max" => 100, 
                    "minMessage" => "Votre prénom doit faire entre 2 et 100 caractères", 
                    "maxMessage" => "Votre prénom doit faire entre 2 et 100 caractères"])
            ]
        ])
            ->add('nom', Type\TextType::class, [
                "attr" => [
                    "placeholder" => "Nom"
                ],
                "label" => false,
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Veuillez renseigner votre nom"]),
                    new Constraints\Length([ 
                        "min" => 2, 
                        "max" => 100, 
                        "minMessage" => "Votre nom doit faire entre 2 et 100 caractères", 
                        "maxMessage" => "Votre nom doit faire entre 2 et 100 caractères"])
                ]
            ])
            ->add('adresse', Type\TextType::class, [
                "attr" => [
                    "placeholder" => "Adresse (numéro et nom de rue)"
                ],
                "label" => false,
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Veuillez renseigner votre adresse"]),
                    new Constraints\Length([ 
                        "min" => 2, 
                        "minMessage" => "Veuillez renseigner votre adresse"])
                ]
            ])
            ->add('cp', Type\TextType::class, [
                "attr" => [
                    "placeholder" => "Code postal"
                ],
                "label" => false,
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Veuillez renseigner votre code postal"])
                ]
            ])
            ->add('ville', Type\TextType::class, [
                "attr" => [
                    "placeholder" => "Ville"
                ],
                "label" => false,
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Veuillez renseigner votre ville"]),
                    new Constraints\Length([ 
                        "min" => 2, 
                        "minMessage" => "Veuillez renseigner votre adresse"])
                ]
            ])
            ->add('telephone', Type\TextType::class, [
                "attr" => [
                    "placeholder" => "Téléphone"
                ],
                "label" => false,
                "constraints" => [
                    new Constraints\NotBlank([ "message" => "Veuillez renseigner votre numéro de téléphone"]),
                    new Constraints\Length([ 
                        "min" => 10, 
                        "max" => 10,
                        "minMessage" => "Saisissez un numéro de téléphone à 10 chiffres"])
                ]
            ])
        ; 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
