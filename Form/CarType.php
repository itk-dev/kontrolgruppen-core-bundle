<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kontrolgruppen\CoreBundle\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarType.
 */
class CarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registrationNumber', null, [
                'label' => 'client.form.car.registration_number',
            ])
            ->add('sharedOwnership', null, [
                'label' => 'client.form.car.shared_ownership',
            ])
            ->add('notes', CKEditorType::class, [
                'label' => 'client.form.car.notes',
                'config' => ['toolbar' => 'editor'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
