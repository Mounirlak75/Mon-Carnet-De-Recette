<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Categorie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Recette1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez le titre de la recette',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez la recette',
                ],
            ])
            ->add('temps_de_preparation', ChoiceType::class, [
                'choices' => [
                    'Court' => 'court',
                    'Moyen' => 'moyen',
                    'Long' => 'long',
                ],
                'label' => 'Temps de préparation',
                'placeholder' => 'Choisissez une durée',
            ])
            ->add('temps_de_cuisson', ChoiceType::class, [
                'choices' => [
                    'Court' => 'court',
                    'Moyen' => 'moyen',
                    'Long' => 'long',
                ],
                'label' => 'Temps de cuisson',
                'placeholder' => 'Choisissez une durée',
            ])
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    'Facile' => 'facile',
                    'Moyen' => 'moyen',
                    'Difficile' => 'difficile',
                ],
                'label' => 'difficulte',
                'placeholder' => 'Choisissez une difficulté',
            ])
            ->add('ingredient', TextareaType::class, [
                'label' => 'Ingrédient',
                'attr' => [
                    'placeholder' => 'Listez les ingrédients',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Utilisateur',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégorie',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
