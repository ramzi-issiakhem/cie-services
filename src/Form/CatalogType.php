<?php

namespace App\Form;

use App\Entity\Catalog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email',EmailType::class,[
                    'label' => 'forms.email',
                    'required' => true
                ])
                ->add('familyName',TextType::class,[
                    'label' => 'forms.familyName',
                    'required' => true
                ])
                ->add('firstName',TextType::class,[
                    'label' => 'forms.firstName',
                    'required' => true
                ])
                ->add('buisenessName',TextType::class,[
                    'label' => 'forms.buisenessName',
                    'required' => true
                ])
                ->add('mobilePhone',NumberType::class,[
                    'label' => "forms.telephone",
                    'required' => true
                ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Catalog::class,
            'translation_domain' => "forms"
        ]);
    }
}
