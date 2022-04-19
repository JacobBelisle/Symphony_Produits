<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('POST')
            ->add(
                'produit',
                TextType::class,
                [
                    'label' => 'Produit',
                    'required' => true
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description', 'required' => true
                ]
            )
            ->add(
                'prix',
                NumberType::class,
                [
                    'label' => 'Prix',
                    'constraints' => [new Positive],
                    'required' => true
                ]
            )
            ->add(
                'id_categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                    'choice_label' => 'categorie',
                    'label' => 'Catégorie',
                    'multiple' => false,
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class
        ]);
    }
}
