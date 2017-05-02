<?php

namespace AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TransactionsType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder)
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
      ->add('transaction_type', EntityType::class, array(
        'label'     => 'Transaction Type',
        'class'     => 'AccountBundle:TransactionType',
        'choice_label'  => 'name',
      ))
      ->add('accountId')
      ->add('save', SubmitType::class)
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AccountBundle\Entity\Transactions'
    ));
  }
}
