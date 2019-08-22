<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryAmountPeriodEnumType;
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
            ->add('process', null, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add('service', null, [
                'label' => 'economy_entry.form.service.service',
                'help' => 'economy_entry.form.service.service_help',
            ])
            ->add('periodFrom', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM/yyyy',
                'attr' => [
                    'class' => 'js-monthpicker-from d-none',
                ],
            ])
            ->add('periodTo', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM/yyyy',
                'attr' => [
                    'class' => 'js-monthpicker-to d-none',
                ],
            ])
            ->add('amountPeriod', ChoiceType::class, [
                'label' => 'economy_entry.form.service.amount_period',
                'choices' => EconomyEntryAmountPeriodEnumType::getChoices(),
                'help' => 'economy_entry.form.service.amount_period_help',
            ])
            ->add('amount', null, [
                'label' => 'economy_entry.form.service.amount',
                'help' => 'economy_entry.form.service.amount_help',
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
