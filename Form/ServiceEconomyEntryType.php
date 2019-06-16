<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceEconomyEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', HiddenType::class)
            ->add('process', HiddenType::class)
            ->add('service', null, [
                'label' => 'economy_entry.form.service.service',
            ])
            ->add('periodFrom', HiddenType::class, [
                'label' => 'economy_entry.form.service.period_from',
                'attr' => [
                    'class' => 'js-monthpicker-from',
                ],
            ])
            ->add('periodTo', HiddenType::class, [
                'label' => 'economy_entry.form.service.period_to',
                'attr' => [
                    'class' => 'js-monthpicker-to',
                ],
            ])
            ->add('amountPeriod', ChoiceType::class, [
                'label' => 'economy_entry.form.service.amount_period',
                'choices' => [
                    'Pr. month' => 1,
                    'Pr. third month' => '3',
                    'Pr. half year' => '6',
                    'Pr. year' => 12,
                ],
            ])
            ->add('amount', null, [
                'label' => 'economy_entry.form.service.amount',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceEconomyEntry::class,
        ]);
    }
}
