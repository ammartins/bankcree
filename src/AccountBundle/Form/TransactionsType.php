<?php

namespace AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// Feeding form select with data from another Entity
// use Doctrine\ORM\EntityRepository;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use CategoriesBundle\Entity\Categories;

class TransactionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder
        //     ->add('transactionHash')
        //     ->add('createAt', 'datetime', array(
        //         'widget'        => 'single_text',
        //         'format'        => 'yyyy-MM-dd',
        //         'with_minutes'  => false,
        //         'with_seconds'  => false,
        //         'disabled'      => true,
        //     ))
        //     ->add('startsaldo')
        //     ->add('endsaldo')
        //     ->add('amount')
        //     ->add('description')
        //     ->add('name', 'entity', array(
        //         'label'     => 'Transaction Type',
        //         'class'     => 'CategoriesBundle:Categories',
        //         'choice_label'  => 'name',
        //     ))
        //     ->add('accountId')
        //     ->add('save', SubmitType::class);

        $builder->add('motherFocker', 'entity', array(new Categories()));

            // $builder->add('name', EntityType::class, array(
            //     'class' => 'CategoriesBundle:Categories',
            //     'query_builder' => function ($er) {
            //         // dump(get_class($er));
            //         return $er->createQueryBuilder('u')
            //             ->orderBy('u.id', 'ASC');
            //     },
            //     'choice_label' => 'name',
            // ));

            // dump($builder);
            // exit;
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
