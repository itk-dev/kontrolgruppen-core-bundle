<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeightedConclusionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('argumentsFor', null, [
                'label' => 'conclusion.form.weighted.arguments_for',
            ])
            ->add('argumentsAgainst', null, [
                'label' => 'conclusion.form.weighted.arguments_against',
            ])
            ->add('conclusion', null, [
                'label' => 'conclusion.form.weighted.conclusion',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WeightedConclusion::class,
        ]);
    }
}
