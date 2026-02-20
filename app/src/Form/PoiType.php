<?php

namespace App\Form;

use App\Entity\Poi;
use App\Entity\City;
use App\Entity\PoiCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'ID Externe (Datatourisme)'
            ])

            ->add('name', TextType::class, [
                'label' => 'Nom du lieu'
            ])

            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'required' => false,
                'scale' => 8,
            ])

            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => false,
                'scale' => 8,
            ])

            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
            ])

            ->add('category', EntityType::class, [
                'class' => PoiCategory::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
            ])

            ->add('contacts', TextareaType::class, [
                'label' => 'Contacts',
                'required' => false,
            ])

            ->add('classements', TextareaType::class, [
                'label' => 'Classements',
                'required' => false,
            ])

            ->add('updatedAt', DateTimeType::class, [
                'label' => 'Date de mise à jour',
                'required' => false,
                'widget' => 'single_text',
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poi::class,
        ]);
    }
}
