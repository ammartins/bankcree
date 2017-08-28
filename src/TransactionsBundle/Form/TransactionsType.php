<?php

namespace TransactionsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Repository\CategoriesRepository;

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

        $parent = [NULL];

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
            ->add(
                'categories',
                ChoiceType::class,
                array(
                    'choices' => $parent
                )
            )
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TransactionsBundle\Entity\Transactions'
        ));

        $resolver->setRequired('entity_manager');
    }
}
