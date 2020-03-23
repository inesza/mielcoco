<?php

namespace App\Form;

use App\Entity\Composition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use App\Entity\Produit; 

class CompositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('produit', EntityType::class, [ 
                "class" => Produit::class, 
                "choice_label" => function(Produit $produit){
                    return $produit->getNom() . " (" . $produit->getUnite() . ")";
                }
            ])
            ->add('quantite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Composition::class,
        ]);
    }
}
