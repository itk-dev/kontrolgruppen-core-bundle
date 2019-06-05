<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTypesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessTypeType extends AbstractType
{
    private $dispatcher;

    /**
     * ProcessTypeType constructor.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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
            ])
            ->add('services', null, [
                'label' => 'process_type.form.services',
            ])
            ->add('hideInDashboard', null, [
                'label' => 'process_type.form.hide_in_dashboard',
            ])
            ->add('conclusionClass', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'process_type.form.conclusion_class',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessType::class,
        ]);
    }
}
