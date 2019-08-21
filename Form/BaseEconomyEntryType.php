<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\BaseEconomyEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseEconomyEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', HiddenType::class)
            ->add('process', null, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add('account', null, [
                'label' => 'economy_entry.form.base.account',
                'help' => 'economy_entry.form.base.account_help',
            ])
            ->add('text', null, [
                'label' => 'economy_entry.form.base.text',
                'help' => 'economy_entry.form.base.text_help',
            ])
            ->add('date', null, [
                'label' => 'economy_entry.form.base.date',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM yyyy HH:mm',
                'attr' => ['class' => 'js-datepicker'],
                'help' => 'economy_entry.form.base.date_help',
            ])
            ->add('amount', null, [
                'label' => 'economy_entry.form.base.amount',
                'help' => 'economy_entry.form.base.amount_help',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BaseEconomyEntry::class,
        ]);
    }
}
