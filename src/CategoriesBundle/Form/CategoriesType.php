<?php

namespace CategoriesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategoriesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
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

        $parent = [NULL];

        foreach ($parents as $par) {
            if ($par->getParent()) {
                continue;
            }
            $parent[$par->getId()] = $par->getName();
        }

        $builder
            ->add('name')
            ->add('recurring')
            ->add('parent', ChoiceType::class, array(
                'choices' => $parent
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

        $resolver->setRequired('entity_manager');
    }
}
