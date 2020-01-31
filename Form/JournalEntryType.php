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
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class JournalEntryType.
 */
class JournalEntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'journal_entry.form.title',
            ])
            ->add('body', CKEditorType::class, [
                'label' => 'journal_entry.form.body',
                'config' => ['toolbar' => 'editor'],
            ])
            ->add('type', null, [
                'label' => 'journal_entry.form.type',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JournalEntry::class,
        ]);
    }
}
