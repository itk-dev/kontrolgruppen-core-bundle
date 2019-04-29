<?php

namespace Kontrolgruppen\CoreBundle\Filter;

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

class ProcessFilterType extends AbstractType
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
        $builder->add('processType', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->processTypeRepository->findAll(), function ($carry, $processType) {
                $carry[$processType->getName()] = $processType->getId();
                return $carry;
            }, []),
            'label' => 'process.form.process_type',
            'label_attr' => array('class'=>'sr-only'),
            'placeholder' => $this->translator->trans('process.table.filter.show_all_types'),
            'attr'=> array('class'=>'form-control-sm ml-auto mr-3')
        ]);

        $builder->add('processStatus', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->processStatusRepository->findAll(), function ($carry, $processStatus) {
                $carry[$processStatus->getName()] = $processStatus->getId();
                return $carry;
            }, []),
            'label' => 'process.form.process_status',
            'label_attr' => array('class'=>'sr-only'),
            'placeholder' => $this->translator->trans('process.table.filter.show_all_status'),
            'attr'=> array('class'=>'form-control-sm mr-3')
        ]);

        $builder->add('caseWorker', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->userRepository->findAll(), function ($carry, $caseWorker) {
                $carry[$caseWorker->getUsername()] = $caseWorker->getId();
                return $carry;
            }, []),
            'label' => 'process.form.case_worker',
            'label_attr' => array('class'=>'sr-only'),
            'placeholder' => $this->translator->trans('process.table.filter.show_all_case_worker'),
            'attr'=> array('class'=>'form-control-sm')
        ]);
    }

    public function getBlockPrefix()
    {
        return 'process_filter';
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
