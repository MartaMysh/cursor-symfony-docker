<?php

namespace App\Form;

use App\Entity\Data;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', ChoiceType::class, [
                'choices' => [
                    'Pen' => 'Pen',
                    'Pencil' => 'Pencil',
                ],
                'placeholder' => 'Wybierz produkt',
                'label' => 'Produkt'
            ])
            ->add('color', ChoiceType::class, [
                'choices' => [
                    'Blue' => 'Blue',
                    'Red' => 'Red',
                    'Black' => 'Black',
                ],
                'placeholder' => 'Wybierz kolor',
                'label' => 'Kolor',
                'required' => false,
            ])
            ->add('amount', IntegerType::class, [
                'label' => 'Ilość sprzedanych',
                'attr' => [
                    'min' => 1,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Data::class,
        ]);
    }
}
