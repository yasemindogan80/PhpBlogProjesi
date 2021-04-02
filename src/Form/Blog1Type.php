<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class Blog1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title')
            ->add('keywords')
            ->add('description')

            ->add('image', FileType::class, [
                'label' => 'Blog Main Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',  //all image types
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ])
            ->add('star', ChoiceType::class,[
                'choices'=> [
                    '1 Star' => '1',
                    '2 Star' => '2',
                    '3 Star' => '3',
                    '4 Star' => '4',
                    '5 Star' => '5',
                ],
            ])
            ->add('address')
            ->add('phone')
            ->add('fax')
            ->add('email')

            ->add('city', ChoiceType::class,[
                'choices' => [
                    'Ankara' => 'Ankara',
                    'İstanbul' => 'İstanbul',
                    'Antalya' => 'Antalya',
                ]
            ])
            ->add('country',ChoiceType::class,[
                'choices' => [
                    'Turkiye' => 'Turkiye',
                    'Spain' => 'Spain',
                    'Greece' => 'Greece',
                    'Russia' => 'Russia',
                    'France' => 'France',
                ]
            ])
            ->add('location')
            ->add('detail', CKEditorType::class,array(
                'config' => array(
                    'uiColor'=>'#ffffff',
                ),
            ))


            ->add('created_at')
            ->add('updated_at')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
