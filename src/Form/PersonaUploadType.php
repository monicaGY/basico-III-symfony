<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\PersonaEntityUpload;

use Symfony\Component\Form\Extension\Core\Type\FileType;
class PersonaUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', TextType::class, ['label' => 'Nombre', 'required' => false])
        ->add('correo', TextType::class, ['label' => 'Email', 'required' => false])
        ->add('telefono', TextType::class, ['label' => 'Telefono', 'required' => false])
        ->add('pais', ChoiceType::class,[
            'choices'=>[
                'España' => 1,
                'Alemania' => 2,
                'Francia' => 3,
                'Australia' => 4,
                'Austria' => 5
                
            ],
            'placeholder' => 'Seleccione una opción'
            
        ])
        ->add('foto', FileType::class, ['mapped' => true])
        ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => PersonaEntityUpload::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}
