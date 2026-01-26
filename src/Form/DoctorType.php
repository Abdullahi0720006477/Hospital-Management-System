<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Doctor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select']
            ])
            ->add('specialization', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('experience', NumberType::class, [
                'label' => 'Experience (Years)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('consultationFee', MoneyType::class, [
                'currency' => 'USD',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctor::class,
        ]);
    }
}
