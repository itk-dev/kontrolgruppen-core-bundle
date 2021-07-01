<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\IncomeType;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IncomeTypeType.
 */
class IncomeTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $builder
            ->add('clientTypes', ClientTypesType::class, [
                'label' => 'process_type.form.client_types',
                //'disabled' => null !== $data && null !== $data->getClientType(),
            ]);

        $builder
            ->add('name', null, [
                'label' => 'income_type.form.name',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IncomeType::class,
        ]);
    }
}
