<?php

namespace AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Repository\CategoriesRepository;

class TransactionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories','entity',array(
                'class'=>'CategoriesBundle:Categories',
                'query_builder' => function (CategoriesRepository $categories) {
                    return $categories->findCategories();
                },
                'property' => 'name'
            ))
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
