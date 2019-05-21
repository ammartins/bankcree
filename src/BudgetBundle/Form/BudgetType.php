<?php

namespace BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BudgetType extends AbstractType
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

        $parent = [];

        foreach ($parents as $par) {
            if (!$par->getParent()) {
                $parent[$par->getName()][] = [];
                $parent[$par->getName()][$par->getId()] = $par->getName();
                continue;
            }
            $parent[$par->getParent()->getName()][$par->getId()] = $par->getName();
        }

        ksort($parent);

        $builder
            ->add('name')
            ->add('budgetLimit')
            ->add('annually')
            ->add(
                'name',
                ChoiceType::class,
                array(
                    'choices' => $parent,
                    'required' => false
                )
            )
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'BudgetBundle\Entity\Budget',
            'allow_extra_fields' => true
            )
        );

        $resolver->setRequired('entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'budgetbundle_budget';
    }
}
