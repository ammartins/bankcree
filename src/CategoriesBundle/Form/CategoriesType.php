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
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        $parents = $em
            ->getRepository('CategoriesBundle:Categories')
            ->findAllParents();
            // );

        $parent = [null];

        // dump($parents);
        // exit;

        foreach ($parents as $par) {
            if ($par->getParent()) {
                continue;
            }
            $parent[$par->getId()] = $par->getName();
        }

        dump($parent);
        exit;

        if ($options['data']->getId()) {
            $builder->add(
                'parent',
                ChoiceType::class,
                array(
                    'choices' => $parent
                )
            );
        }

        $builder
            ->add('name')
            ->add('recurring')
            ->add('savings')
            ->add('companyLogo')
            ->add('save', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'allow_extra_fields' => true,
            'data_class' => 'CategoriesBundle\Entity\Categories'
            )
        );

        $resolver->setRequired('entity_manager');
    }
}
