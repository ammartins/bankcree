<?php

namespace TransactionsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Repository\CategoriesRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TransactionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $parents = $em
            ->getRepository('CategoriesBundle:Categories')
            ->findBy(
                array(),
                array(
                    'name' => 'ASC'
                )
            );

        // If categories is set the all add to form explodes ... SAD
        $categoryId = null;
        if ($options['data']->getCategories()) {
            $categoryId = $options['data']->getCategories()->getId();
        }
        $options['data']->setCategories('null');

        foreach ($parents as $par) {
            if (!$par->getParent()) {
                $parent[$par->getName()][] = [];
                $parent[$par->getName()][$par->getId()] = $par->getName();
                continue;
            }
            $parent[$par->getParent()->getName()][$par->getId()] = $par->getName();
        }

        $builder->add(
            'categories',
            ChoiceType::class,
            array(
                'choices' => $parent
            )
        );

        $builder
            ->add('transactionHash')
            ->add('startsaldo')
            ->add('endsaldo')
            ->add('amount')
            ->add('description')
            ->add('categoryId', HiddenType::class, ['data' => $categoryId, "mapped" => false])
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'TransactionsBundle\Entity\Transactions'
            )
        );

        $resolver->setRequired('entity_manager');
    }
}
