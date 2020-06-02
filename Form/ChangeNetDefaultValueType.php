<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangeNetDefaultValueType.
 */
class ChangeNetDefaultValueType extends AbstractType
{
    private $serviceRepository;

    /**
     * ChangeNetDefaultValueType constructor.
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
            ->add('service', EntityType::class, [
                'label' => 'change_net_default_value.form.service_label',
                'class' => Service::class,
                'choices' => $this->serviceRepository->findAll(),
                'required' => true,
            ])
            ->add('value', PercentType::class, [
                'label' => 'change_net_default_value.form.value_label',
                'required' => 'true',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'change_net_default_value.form.submit_label',
            ])
        ;
    }
}
