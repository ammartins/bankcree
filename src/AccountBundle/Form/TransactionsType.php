<?php

namespace AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TransactionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionHash')
            ->add('createAt', 'datetime', array(
                'widget'        => 'single_text',
                'format'        => 'yyyy-MM-dd',
                'with_minutes'  => false,
                'with_seconds'  => false,
                'disabled'      => true,
            ))
            ->add('startsaldo')
            ->add('endsaldo')
            ->add('amount')
            ->add('description')
            ->add('name', 'entity', array(
                'label'     => 'Transaction Type',
                'class'     => 'CategoriesBundle:Categories',
                'choice_label'  => 'name',
            ))
            ->add('accountId')
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AccountBundle\Entity\Transactions'
        ));
    }
}
