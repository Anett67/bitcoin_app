<?php

namespace App\Form;

use App\Entity\Crypto;
use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('crypto', EntityType::class, [
                'label' => false,
                'class' => Crypto::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', TypeIntegerType::class ,[
                'label' => false,
                'attr' => ['placeholder' => "Quantité"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
