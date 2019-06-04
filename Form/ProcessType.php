<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processType', null, [
                'label' => 'process.form.process_type',
            ])
            ->add('caseWorker', null, [
                'label' => 'process.form.case_worker',
            ])
            ->add('clientCPR', null, [
                'label' => 'process.form.client_cpr',
                'attr' => [
                    'class' => 'js-input-cpr',
                ],
            ])
            ->add('channel', null, [
                'label' => 'process.form.channel',
            ])
            ->add('service', null, [
                'label' => 'process.form.service',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }
}
