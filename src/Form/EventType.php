<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\School;
use App\Repository\ProductRepository;
use App\Repository\SchoolRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name',TextType::class,[
                'label' => 'forms.name'
            ])
            ->add('event_datetime',DateTimeType::class,[
                'widget' => "single_text",
                'label' => 'forms.event.date'
            ])
            ->add('price',MoneyType::class,[
                'currency' => "DZD",
                'label' => 'forms.price'
            ])
            ->add('deadline_date',DateType::class,[
                'label' => 'forms.event.deadline',
                'widget' => "single_text"
            ])
            ->add('description',TextareaType::class,[
                "label" => 'forms.description'
            ])
            ->add('reservation_places',IntegerType::class,[
                'label' => 'forms.event.reservation_places'
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'forms.event.state',
                'choices'  => array_flip(Event::STATE),
            ])
            ->add('product',EntityType::class,[
                'class' => Product::class,
                'label' => 'forms.product',
                'choice_label' => 'name',
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },

            ])
            ->add('school',EntityType::class,[
                'class' => School::class,
                'label' => 'forms.school',
                'choice_label' => 'name',
                'query_builder' => function (SchoolRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                }
            ]);;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'translation_domain' => 'forms'
        ]);
    }
}
