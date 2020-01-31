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
use Kontrolgruppen\CoreBundle\Entity\BaseConclusion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseConclusionType.
 */
class BaseConclusionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conclusion', CKEditorType::class, [
                'label' => 'conclusion.form.base.conclusion',
                'label_attr' => ['class' => 'sr-only'],
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
            'data_class' => BaseConclusion::class,
        ]);
    }
}
