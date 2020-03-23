<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('client', IntegerType::class )
            // ->add('montant', TextType::class, [ "attr" => [ "readonly" => true, "class" => "form-control-plaintext" ] ])
            //->add('date', DateType::class, [ "widget" => "single_text", "attr" => [ "disabled" => true ], "input" => "datetime", "required" => false ])
            ->add('etat', ChoiceType::class, [
                "attr" => ["class" => "form-check-inline"],
                'choices'  => [
                    'En cours de traitement' => "traitement",
                    'En cours de livraison' => "livraison",
                    'Commande livrée' => "expedie"
                    ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
