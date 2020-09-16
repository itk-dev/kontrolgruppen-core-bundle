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
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ProcessCompleteType.
 */
class ProcessCompleteType extends AbstractType
{
    protected $serviceRepository;

    /**
     * ProcessCompleteType constructor.
     *
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processStatus', EntityType::class, [
                'class' => ProcessStatus::class,
                'constraints' => [new NotBlank()],
                'required' => true,
                'choices' => $options['available_statuses'],
            ])
            ->add('policeReport', ChoiceType::class, [
                'label' => 'process.form.police_report',
                'required' => false,
                'choices' => [
                    'common.boolean.yes' => true,
                    'common.boolean.no' => false,
                ],
            ])
            ->add('courtDecision', ChoiceType::class, [
                'label' => 'process.form.court_decision',
                'required' => false,
                'choices' => [
                    'court_decision.true' => true,
                    'court_decision.false' => false,
                ],
            ])
            ->add('performedCompanyCheck', ChoiceType::class, [
                'label' => 'process.form.perforned_company_check',
                'required' => false,
                'choices' => [
                    'common.boolean.yes' => true,
                    'common.boolean.no' => false,
                ],
            ])
            ->add('forwardedToAuthorities', null, [
                'label' => 'service.form.forwarded_to_authorities',
                'by_reference' => false,
                'attr' => ['class' => 'select2'],
                'help' => 'process.form.complete.forwarded_to_authorities_help',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
            'available_statuses' => [],
        ]);
    }
}
