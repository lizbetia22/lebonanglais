<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Picture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('author')
            ->add('email')
            ->add('price')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('pictures', EntityType::class, [
            'class' => Picture::class,
            'choice_label' => 'path',
            'multiple' => true,
            'expanded' => true,
            'by_reference' => false,
        ]);
//            ->add('picture', FileType::class, [
//                'mapped' => false,
//                'required' => false,
//                'multiple' => true,
//            ]);
//            ->add('imageFile', VichImageType::class, [
//            'required' => false,
//            'allow_delete' => true,
//            'download_uri' => true,
//        ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $advert = $event->getData();
            $form = $event->getForm();

            if (!$advert || null === $advert->getId()) {
                $form->remove('createdAt');
                $form->remove('publishedAt');
                $form->remove('state');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}