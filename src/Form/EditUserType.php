<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $object = $options['data'] ?? null;
        $isEdit = $object && $object->getId();
        $builder
            ->add('email', EmailType::class,[
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Please enter an email adress'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Editor' => 'ROLE_EDITOR',
                    'Admin' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'User picture',
                'label_attr' => [
                    'class' => 'form-label mt-3 '
                ],
                'required' => !$isEdit , // image is required only if the form is used for create
            ])
            -> add('Submit', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'button-13'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
