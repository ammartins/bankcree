<?php

namespace CategoriesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CategoriesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('recurring')
            ->add('parent', 'entity', array(
                'label' => 'Transaction Type',
                'class' => 'CategoriesBundle:Categories',
                'choice_label' => 'name',
            ))
            ->add('isParent', CheckboxType::class, array(
                'label' => 'Parent category?',
                'required' => false,
            ))
            ->add('save', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'data_class' => 'CategoriesBundle\Entity\Categories'
        ));
    }
}
