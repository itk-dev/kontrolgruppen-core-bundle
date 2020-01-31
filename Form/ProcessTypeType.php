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
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTypesEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProcessTypeType.
 */
class ProcessTypeType extends AbstractType
{
    private $dispatcher;

    /**
     * ProcessTypeType constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = new GetConclusionTypesEvent();
        $this->dispatcher->dispatch(GetConclusionTypesEvent::NAME, $event);

        $conclusionTypes = $event->getTypes();

        $choices = [];

        foreach ($conclusionTypes as $conclusionType) {
            $choices[$conclusionType['name']] = $conclusionType['class'];
        }

        $builder
            ->add('name', null, [
                'label' => 'process_type.form.name',
            ])
            ->add('processStatuses', null, [
                'label' => 'process_type.form.process_statuses',
                'attr' => ['class' => 'select2'],
            ])
            ->add('services', null, [
                'label' => 'process_type.form.services',
                'attr' => ['class' => 'select2'],
            ])
            ->add('channels', null, [
                'label' => 'process_type.form.channels',
                'attr' => ['class' => 'select2'],
            ])
            ->add('hideInDashboard', null, [
                'label' => 'process_type.form.hide_in_dashboard',
            ])
            ->add('conclusionClass', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'process_type.form.conclusion_class',
            ])
            ->add('defaultProcessStatus', EntityType::class, [
                'class' => ProcessStatus::class,
                'choice_label' => 'name',
                'label' => 'process_type.form.default_process_status',
                'help' => 'process_type.form.default_process_status_help',
            ])
            ->add('defaultProcessStatusOnEmptyCaseWorker', EntityType::class, [
                'class' => ProcessStatus::class,
                'choice_label' => 'name',
                'label' => 'process_type.form.default_process_status_on_empty_case_worker',
                'help' => 'process_type.form.default_process_status_on_empty_case_worker_help',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessType::class,
        ]);
    }
}
