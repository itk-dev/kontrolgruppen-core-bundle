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
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProcessCompleteType extends AbstractType
{
    protected $serviceRepository;

    /**
     * ProcessCompleteType constructor.
     */
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('processStatus', null, [
                'label' => 'process.complete.status',
                'required' => true,
            ])
            ->add('policeReport', ChoiceType::class, [
                'label' => 'process.form.police_report',
                'required' => true,
                'choices' => [
                    null => null,
                    'common.boolean.yes' => true,
                    'common.boolean.no' => false,
                ],
            ])
            ->add('courtDecision', ChoiceType::class, [
                'label' => 'process.form.court_decision',
                'required' => false,
                'choices' => [
                    null => null,
                    'court_decision.true' => true,
                    'court_decision.false' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }
}
