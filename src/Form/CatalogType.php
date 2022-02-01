<?php

namespace App\Form;

use App\Entity\Catalog;
use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                ->add('function',ChoiceType::class,[
                    'label' => "forms.choice.function",
                    "choices" => Contact::Functions,
                    'choice_translation_domain' => 'types'
                ])
                ->add('email',EmailType::class,[
                    'label' => 'forms.email',
                    'required' => true
                ])

                ->add('name',TextType::class,[
                    'label' => 'forms.catalog.name',
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
