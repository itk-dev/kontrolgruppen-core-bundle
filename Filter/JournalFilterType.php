<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Filter;

use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class JournalFilterType.
 */
class JournalFilterType extends AbstractType
{
    private $processTypeRepository;
    private $processStatusRepository;
    private $userRepository;
    private $security;
    private $translator;

    /**
     * ProcessFilterType constructor.
     *
     * @param ProcessTypeRepository   $processTypeRepository
     * @param ProcessStatusRepository $processStatusRepository
     * @param UserRepository          $userRepository
     * @param Security                $security
     * @param TranslatorInterface     $translator
     */
    public function __construct(ProcessTypeRepository $processTypeRepository, ProcessStatusRepository $processStatusRepository, UserRepository $userRepository, Security $security, TranslatorInterface $translator)
    {
        $this->processTypeRepository = $processTypeRepository;
        $this->processStatusRepository = $processStatusRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Filters\ChoiceFilterType::class, [
            'label' => 'process.form.process_type',
            'label_attr' => ['class' => 'sr-only'],
            'placeholder' => $this->translator->trans('journal_entry.table.filter.all_entry'),
            'choices' => JournalEntryEnumType::getChoices(),
            'attr' => ['class' => 'mr-3 form-control-sm'],
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'journal_filter';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'validation_groups' => ['filtering'],
            ]
        );
    }
}
