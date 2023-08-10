<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//importación para la creación del formulario
use Symfony\Component\Form\Extension\Core\Type\TextType;
//asociar a la entidad
use App\Entity\PersonaEntity;
//boton
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class PersonEntityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('correo', TextType::class, ['label' => 'Email'])
            ->add('telefono', TextType::class, ['label' => 'Telefono'])
            ->add('pais', ChoiceType::class,[
                'choices'=>[
                    'Seleccione una opción' => false,
                    'España' => 1,
                    'Alemania' => 2,
                    'Francia' => 3,
                    'Australia' => 4,
                    'Austria' => 5
                    
                ],
                'choice_attr' => [
                    'Seleccione una opción' => [
                        'disabled'=> true, 
                        'selected'=> true
                    ]
                ],
                
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //asociar a la entidad
            'data_class'=> PersonaEntity::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',

        ]);
    }
}
