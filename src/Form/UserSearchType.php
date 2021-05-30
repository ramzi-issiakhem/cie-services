<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserSearch;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('roles',ChoiceType::class,[
                'required' => false,
                'choices' => User::ROLES,
                'choice_translation_domain' => "types",
            ])
            ->add('scholar_level',ChoiceType::class, [
                'choices' => array_flip(User::SCHOOLAR_LEVEL),
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
            ->add('type',ChoiceType::class,[
                'required' => false,
                'choice_translation_domain' => "types",
                'choices' => [
                    "users.type.professional" => 0,
                    "users.type.personnel" => 1
                ]
            ])
            ->add('related_school',EntityType::class,[
                'required' => false,
                'class' => User::class,
                'label' => 'forms.relatedschool',
                'choice_label' => 'name',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.type = 0');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSearch::class,
            'translations_domain' => 'admin',
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
