<?php

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
            ->add('cpr', null, [
                'label' => 'client.form.cpr',
            ])
            ->add('firstName', null, [
                'label' => 'client.form.firstName',
            ])
            ->add('lastName', null, [
                'label' => 'client.form.lastName',
            ])
            ->add('address', null, [
                'label' => 'client.form.address',
            ])
            ->add('postalCode', null, [
                'label' => 'client.form.postalCode',
            ])
            ->add('city', null, [
                'label' => 'client.form.city',
            ])
            ->add('telephone', null, [
                'label' => 'client.form.telephone',
            ])
            ->add('numberOfChildren', null, [
                'label' => 'client.form.numberOfChildren',
            ])
            ->add('carRegistrationNumber', null, [
                'label' => 'client.form.carRegistrationNumber',
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
