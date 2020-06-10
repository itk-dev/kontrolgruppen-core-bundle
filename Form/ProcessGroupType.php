<?php

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('primaryProcess', EntityType::class, [
                'class' => Process::class,
                'choice_label' => 'caseNumber',
                'label' => 'process_group.primary_process',
            ])
            ->add('processes', EntityType::class, [
                'class' => Process::class,
                'choice_label' => 'caseNumber',
                'multiple' => true,
                'label' => 'process_group.processes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessGroup::class,
        ]);
    }
}
