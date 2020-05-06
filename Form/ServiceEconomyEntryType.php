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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ServiceEconomyEntryType.
 */
class ServiceEconomyEntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
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
                'required' => true,
            ])
            ->add('periodFrom', null, [
                'label' => 'economy_entry.form.service.period_from',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'js-economy-entry-period-from d-none',
                ],
            ])
            ->add('periodTo', null, [
                'label' => 'economy_entry.form.service.period_to',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'js-economy-entry-period-to d-none',
                ],
            ])
            ->add('amountPeriod', ChoiceType::class, [
                'label' => 'economy_entry.form.service.amount_period',
                'choices' => EconomyEntryAmountPeriodEnumType::getChoices(),
                'help' => 'economy_entry.form.service.amount_period_help',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'economy_entry.form.service.amount',
                'help' => 'economy_entry.form.service.amount_help',
                'currency' => 'DKK',
                'grouping' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceEconomyEntry::class,
        ]);
    }
}
