<?php

namespace App\Form;

use App\Entity\SystemSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SystemSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hospitalName', TextType::class, [
                'label' => 'Hospital Name',
                'attr' => ['placeholder' => 'e.g. MediCore Hospital'],
            ])
            ->add('dashboardTitle', TextType::class, [
                'label' => 'Dashboard Title',
                'attr' => ['placeholder' => 'e.g. Hospital OS v2.0'],
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'System Contact Email',
                'required' => false,
            ])
            ->add('contactPhone', TelType::class, [
                'label' => 'System Contact Phone',
                'required' => false,
            ])
            ->add('themeColor', ColorType::class, [
                'label' => 'Primary Theme Color',
                'required' => false,
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'System Logo (Image file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPG, PNG, SVG)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SystemSettings::class,
        ]);
    }
}
