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
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProcessGroupType.
 */
class ProcessGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'process_group.name',
            ])
            ->add('primaryProcess', EntityType::class, [
                'class' => Process::class,
                'choice_label' => 'caseNumber',
                'label' => 'process_group.primary_process',
            ])
        ;

        $formModifier = function (FormInterface $form, Process $primaryProcess) {
            $form->add('processes', EntityType::class, [
                'class' => Process::class,
                'choice_label' => 'caseNumber',
                'multiple' => true,
                'label' => 'process_group.processes',
                'attr' => [
                    'class' => 'select2',
                ],
                'query_builder' => function (ProcessRepository $processRepository) use ($primaryProcess) {
                    return $processRepository->createQueryBuilder('p')
                        ->where('p.caseNumber != :primaryProcessCaseNumber')
                        ->setParameter(':primaryProcessCaseNumber', $primaryProcess->getCaseNumber())
                        ;
                },
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var ProcessGroup $data */
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getPrimaryProcess());
            }
        );

        $builder->get('primaryProcess')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                /** @var Process $data */
                $data = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $data);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessGroup::class,
        ]);
    }
}
