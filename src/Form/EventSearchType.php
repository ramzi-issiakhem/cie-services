<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventSearch;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product',EntityType::class,[
                'class' => Product::class,
                'label' => 'forms.product',
                'required' => false,
                'choice_label' => 'name',
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                }
                ])
            ->add('state',ChoiceType::class, [
                'label' => 'forms.event.state',
                'required' => false,
                'choices'  => array_flip(Event::STATE),
            ])
            ->add('event_datetime',DateTimeType::class,[
                'required' => false,
                'widget' => "single_text",
                'label' => 'forms.event.date'
            ])
            ->add('deadline_date',DateType::class,[
                'required' => false,
                'label' => 'forms.event.deadline',
                'widget' => "single_text"
            ])
            ->add('school',EntityType::class,[
                'class' => User::class,
                'required' => false,
                'label' => 'forms.school',
                'choice_label' => 'name',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC')->andWhere('u.type = 0');
                }
            ])/*->add('sort',ChoiceType::class,[
                'label' => 'forms.sort',
                'choices' => [
                    'forms.ascendant' => 'ASC',
                    'forms.descendant' => 'DESC',
                    ]
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventSearch::class,
            'translation_domain' => 'forms',
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
            return '';
    }

}
