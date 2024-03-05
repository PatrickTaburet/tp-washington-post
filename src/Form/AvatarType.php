<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //define if the form is used for create or for update ($object + getid)

        $object = $options['data'] ?? null;
        $isEdit = $object && $object->getId();

        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'User picture',
                'label_attr' => [
                    'class' => 'form-label mt-3 '
                ],
                'required' => !$isEdit , // image is required only if the form is used for create
            ])
            // ->add('imageName')
            // ->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avatar::class,
        ]);
    }
    public function getBlockPrefix()
    {
        return 'app_avatar';
    }
}
