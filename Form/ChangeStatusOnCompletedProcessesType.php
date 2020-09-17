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
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangeStatusOnCompletedProcessesType.
 */
class ChangeStatusOnCompletedProcessesType extends AbstractType
{
    private $processStatusRepository;

    /**
     * ChangeStatusOnCompletedProcessesType constructor.
     *
     * @param ProcessStatusRepository $processStatusRepository
     */
    public function __construct(ProcessStatusRepository $processStatusRepository)
    {
        $this->processStatusRepository = $processStatusRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processStatus', EntityType::class, [
                'label' => 'change_status_on_completed_processes.form.status_label',
                'class' => ProcessStatus::class,
                'choices' => $this->processStatusRepository->findBy(['isCompletingStatus' => true]),
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'change_status_on_completed_processes.form.submit_label',
            ])
        ;
    }
}
