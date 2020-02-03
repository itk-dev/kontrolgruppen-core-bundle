<?php

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ForwardedToAuthority;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForwardedToAuthorityType
 */
class ForwardedToAuthorityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'service.form.name',
            ])
            ->add('processTypes', null, [
                'label' => 'forwarded_to_authority.form.process_types',
                'by_reference' => false,
                'attr' => ['class' => 'select2'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ForwardedToAuthority::class,
        ]);
    }
}
