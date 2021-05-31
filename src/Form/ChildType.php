<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('schoolar_level',ChoiceType::class, [
                'choices' => array_flip(Child::SCHOOLAR_LEVEL),
                'choice_translation_domain' => "types",
                'label' => 'forms.schoolarlevel',
                'required' => false,
                'group_by' => function ($choice, $key, $value) {
                    if (strpos($key, "section")) {
                        return "levels.categories.section";
                    } elseif (strpos($key, "primary")) {
                        return "levels.categories.primary";
                    } elseif (strpos($key, "secondary")) {
                        return "levels.categories.secondary";
                    } else {
                        return "levels.categories.high";
                    }
                }
            ])
            ->add('school',EntityType::class,[
                'required' => true,
                'class' => User::class,
                'label' => 'forms.relatedschool',
                'choice_label' => 'name',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.type = 0');
                },
            ])
            ->add('birthday_date', DateType::class, [
                'widget' => "single_text",
                'label' => 'forms.birthday'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
        ]);
    }
}
