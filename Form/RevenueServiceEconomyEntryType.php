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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class RevenueServiceEconomyEntryType.
 */
class RevenueServiceEconomyEntryType extends AbstractType
{
    private $authorizationChecker;

    /**
     * RevenueServiceEconomyEntryType constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ServiceEconomyEntry $serviceEconomyEntry */
        $serviceEconomyEntry = $options['data'];

        $builder
            ->add('service', null, [
                'label' => 'economy_entry.form.service.service',
                'help' => 'economy_entry.form.service.service_help',
                'attr' => [
                    'readonly' => true,
                    'class' => 'readonly',
                ],
            ])
            ->add('futureSavingsPeriodFrom', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM-yy',
                'required' => false,
                'attr' => [
                    'class' => 'd-none future-savings-period-from',
                ],
            ])
            ->add('futureSavingsPeriodTo', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM-yy',
                'required' => false,
                'attr' => [
                    'class' => 'd-none future-savings-period-to',
                ],
            ])
            ->add('futureSavingsAmount', MoneyType::class, [
                'label' => 'economy_entry.form.service.future_savings_amount',
                'help' => 'economy_entry.form.service.future_savings_amount_help',
                'currency' => 'DKK',
                'grouping' => true,
                'required' => false,
                'disabled' => !$this->authorizationChecker->isGranted('edit', $serviceEconomyEntry->getProcess()),
                'attr' => [
                    'class' => 'future-savings-amount',
                ],
            ])
            ->add('repaymentPeriodFrom', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM-yy',
                'required' => false,
                'attr' => [
                    'class' => 'd-none repayment-period-from',
                ],
            ])
            ->add('repaymentPeriodTo', null, [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM-yy',
                'required' => false,
                'attr' => [
                    'class' => 'd-none repayment-period-to',
                ],
            ])
            ->add('repaymentAmount', MoneyType::class, [
                'label' => 'economy_entry.form.service.repayment_amount',
                'help' => 'economy_entry.form.service.repayment_amount_help',
                'currency' => 'DKK',
                'grouping' => true,
                'required' => false,
                'disabled' => !$this->authorizationChecker->isGranted('edit', $serviceEconomyEntry->getProcess()),
                'attr' => [
                    'class' => 'repayment-amount',
                ],
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
