<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'forms.name.fr'
            ])
            ->add('nameAr',TextType::class,[
                'label' => 'forms.name.ar'
            ])
            ->add('descriptionAr',TextareaType::class,[
                "label" => 'forms.description.ar'
            ])
            ->add('descriptionEn',TextareaType::class,[
                "label" => 'forms.description.en'
            ])
            ->add('descriptionFr',TextareaType::class,[
                "label" => 'forms.description.fr'
            ])
            ->add('logo',FileType::class,[
                'required' => true,
                'multiple' => false,
                'mapped' => true,
                "label" => 'forms.logo'

            ])
            ->add('cover_image',FileType::class,[
                'required' => true,
                'multiple' => false,
                'mapped' => true,
                "label" => 'forms.coverimage'

            ])
            ->add('images',FileType::class,[
                'required' => true,
                'mapped' => false,
                'multiple' => true,
                "label" => 'forms.images'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'forms'
        ]);
    }
}
