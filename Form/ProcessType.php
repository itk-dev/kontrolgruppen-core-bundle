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
use Kontrolgruppen\CoreBundle\Entity\ProcessType as ProcessTypeEntity;
use Kontrolgruppen\CoreBundle\Form\Process\ClientCompanyType;
use Kontrolgruppen\CoreBundle\Form\Process\ClientPersonType;
use Kontrolgruppen\CoreBundle\Repository\ChannelRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessType.
 */
class ProcessType extends AbstractType
{
    protected $serviceRepository;
    protected $channelRepository;
    protected $router;
    protected $translator;

    /**
     * ProcessType constructor.
     *
     * @param ServiceRepository   $serviceRepository
     * @param ChannelRepository   $channelRepository
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(ServiceRepository $serviceRepository, ChannelRepository $channelRepository, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->serviceRepository = $serviceRepository;
        $this->channelRepository = $channelRepository;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processType', null, [
                'label' => 'process.form.process_type',
            ])
            ->add('clientType', ChoiceType::class, [
                'label' => 'process.form.client_type',
                'attr' => [
                    'class' => 'js-input-client-type',
                ],
                'choices' => [
                    $this->translator->trans('process.form.client_type.empty') => '',
                    $this->translator->trans('process.form.client_type.company') => 'company',
                    $this->translator->trans('process.form.client_type.person') => 'person',
                ],
                'required' => true,
                'mapped' => false,
            ])
            ->add('company', ClientCompanyType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'client-company',
                    'data-client-type' => 'company',
                ],
                'mapped' => false,
            ])
            ->add('person', ClientPersonType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'client-person',
                    'data-client-type' => 'person',
                ],
                'mapped' => false,
            ])
            ->add('caseWorker', null, [
                'label' => 'process.form.case_worker',
            ])
            ->add('reason', null, [
                'label' => 'process.form.reason',
            ])
            ->add('service', null, [
                'label' => 'process.form.service',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('channel', null, [
                'label' => 'process.form.channel',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
        ;

        $formModifier = function (FormInterface $form, ProcessTypeEntity $processType = null) {
            if (null !== $processType) {
                $choices = $this->getServiceChoices($processType);

                $form->remove('service');
                $form->add('service', ChoiceType::class, [
                    'label' => 'process.form.service',
                    'choices' => $choices,
                    'choice_label' => function ($choice, $key, $value) {
                        return $choice->getName();
                    },
                ]);

                $choices = $this->getChannelChoices($processType);

                $form->remove('channel');
                $form->add('channel', ChoiceType::class, [
                    'label' => 'process.form.channel',
                    'choices' => $choices,
                    'choice_label' => function ($choice, $key, $value) {
                        return $choice->getName();
                    },
                ]);
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getProcessType());
            }
        );

        $builder->get('processType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $processType = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $processType);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }

    /**
     * @param ProcessTypeEntity $processType
     *
     * @return mixed
     */
    private function getServiceChoices(ProcessTypeEntity $processType)
    {
        return $this->serviceRepository->getByProcessType($processType);
    }

    /**
     * @param ProcessTypeEntity $processType
     *
     * @return mixed
     */
    private function getChannelChoices(ProcessTypeEntity $processType)
    {
        return $this->channelRepository->getByProcessType($processType);
    }
}
