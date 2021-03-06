<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType as FileTypeClass;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\File as FileEnum;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\HttpFoundation\File\File as FileComponent;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('file', FileTypeClass::class, [
                'label' => 'Media File',
                'required' => false,
                'mapped' => false,
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Music' => FileEnum::MUSIC_TYPE,
                    'Video' => FileEnum::VIDEO_TYPE,
                    'Photo' => FileEnum::PHOTO_TYPE,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => File::class,
            ]
        );
    }
}
