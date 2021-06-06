<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class, [
                'label' => 'forms.email'
            ])
            ->add('logo',FileType::class,[
                'label' => 'forms.users.logo',
                'data_class' => null,

            ])

            ->add('mobile_phone', NumberType::class, [
                'label' => 'forms.mobilephone'
            ])
                ->add('name', TextType::class, [
                    'label' => 'forms.username'
                ])
            ->add('mobile_phone',TelType::class,[
                'label' => 'forms.telephone'
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => "forms"

        ]);
    }
}
