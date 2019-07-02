<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'label' => 'client.form.first_name',
            ])
            ->add('lastName', null, [
                'label' => 'client.form.last_name',
            ])
            ->add('address', null, [
                'label' => 'client.form.address',
            ])
            ->add('postalCode', null, [
                'label' => 'client.form.postal_code',
            ])
            ->add('city', null, [
                'label' => 'client.form.city',
            ])
            ->add('telephone', null, [
                'label' => 'client.form.telephone',
            ])
            ->add('numberOfChildren', null, [
                'label' => 'client.form.number_of_children',
            ])
            ->add('carRegistrationNumber', null, [
                'label' => 'client.form.car_registration_number',
            ])
            ->add('selfEmployed', null, [
                'label' => 'client.form.self_employed',
            ])
            ->add('worksInMajorCompany', null, [
                'label' => 'client.form.works_in_major_company',
            ])
            ->add('notEmployed', null, [
                'label' => 'client.form.not_employed',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
