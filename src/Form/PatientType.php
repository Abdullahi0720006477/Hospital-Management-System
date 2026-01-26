<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date of Birth',
                'attr' => ['class' => 'form-control']
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Phone Number',
                'attr' => ['class' => 'form-control']
            ])
            ->add('address', TextareaType::class, [
                'required' => false,
                'label' => 'Address',
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('bloodGroup', ChoiceType::class, [
                'choices' => [
                    'A+' => 'A+', 'A-' => 'A-',
                    'B+' => 'B+', 'B-' => 'B-',
                    'AB+' => 'AB+', 'AB-' => 'AB-',
                    'O+' => 'O+', 'O-' => 'O-',
                ],
                'placeholder' => 'Select Blood Group',
                'required' => false,
                'label' => 'Blood Group',
                'attr' => ['class' => 'form-select']
            ])
            ->add('allergies', TextareaType::class, [
                'required' => false,
                'label' => 'Allergies',
                'attr' => ['class' => 'form-control', 'rows' => 2]
            ])
            ->add('emergencyContactName', TextType::class, [
                'required' => false,
                'label' => 'Emergency Contact Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('emergencyContactPhone', TelType::class, [
                'required' => false,
                'label' => 'Emergency Contact Phone',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
