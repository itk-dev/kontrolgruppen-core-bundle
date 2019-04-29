<?php

namespace Kontrolgruppen\CoreBundle\Filter;

use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param ProcessTypeRepository $processTypeRepository
     * @param ProcessStatusRepository $processStatusRepository
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(
        ProcessTypeRepository $processTypeRepository,
        ProcessStatusRepository $processStatusRepository,
        UserRepository $userRepository,
        Security $security,
        TranslatorInterface $translator
    ) {
        $this->processTypeRepository = $processTypeRepository;
        $this->processStatusRepository = $processStatusRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Filters\ChoiceFilterType::class, [
            'label' => 'process.form.process_type',
            'label_attr' => array('class'=>'sr-only'),
            'placeholder' => $this->translator->trans('journal_entry.table.filter.all_entry'),
            'choices' => JournalEntryEnumType::getChoices(),
            'attr'=> array('class'=>'mr-3 form-control-sm')
        ]);
    }

    public function getBlockPrefix()
    {
        return 'journal_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'validation_groups' => array('filtering'),
            ]
        );
    }
}
