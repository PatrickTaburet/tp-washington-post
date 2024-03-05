<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //define if the form is used for create or for update ($object + getid)
        $object = $options['data'] ?? null;
        $isEdit = $object && $object->getId();
        $builder
            ->add('title')
            ->add('content')
            ->add('imageFile', VichImageType::class, [
                'label' => 'Article picture',
                'label_attr' => [
                    'class' => 'form-label mt-3 '
                ],
                'required' => !$isEdit , // image is required only if the form is used for create
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);

    }
    public function getBlockPrefix()
    {
        return 'app_article';
    }
}
