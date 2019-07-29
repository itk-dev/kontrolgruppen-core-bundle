<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'process_status.form.name',
            ])
            ->add('processTypes', null, [
                'label' => 'process_status.form.process_types',
                'by_reference' => false,
                'attr' => ['class' => 'select2'],
            ])
            ->add('isForwardToAnotherAuthority', null, [
                'label' => 'process_status.form.is_forward_to_another_authority',
                'help' => 'process_status.form.is_forward_to_another_authority_help',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessStatus::class,
        ]);
    }
}
