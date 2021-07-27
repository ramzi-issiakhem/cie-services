<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
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
                'label' => 'forms.name.fr'
            ])
            ->add('nameAr',TextType::class,[
                'label' => 'forms.name.ar'
            ])
            ->add('event_datetime',DateTimeType::class,[
                'widget' => "single_text",
                'label' => 'forms.event.date'
            ])
            ->add('price',MoneyType::class,[
                'currency' => "DZD",
                'label' => 'forms.price',
                "help" => "prix d'une place"
            ])
            ->add('deadline_date',DateType::class,[
                'label' => 'forms.event.deadline',
                'widget' => "single_text"
            ])
            ->add('descriptionFr',TextareaType::class,[
                "label" => 'forms.description.fr'
            ])
            ->add('descriptionAr',TextareaType::class,[
                "label" => 'forms.description.ar'
            ])
            ->add('descriptionEn',TextareaType::class,[
                "label" => 'forms.description.en'
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
                'class' => User::class,
                'label' => 'forms.school',
                'choice_label' => 'name',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC')
                        ->andWhere('u.type = 0');
                }
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'translation_domain' => 'forms'
        ]);
    }
}
