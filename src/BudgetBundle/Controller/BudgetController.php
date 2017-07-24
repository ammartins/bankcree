<?php

namespace BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// Entity Budget
use BudgetBundle\Entity\Budget;
use BudgetBundle\Form\BudgetType;

class BudgetController extends Controller
{

    /**
     * @param Request $request
     *
     * @Route("/budget", name="budget")
     */
    public function budgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $budgets = $em->getRepository('BudgetBundle:Budget')->findAll();

        return $this->render(
            'BudgetBundle:Budget:budgetIndex.html.twig',
            array(
                'budgets' => $budgets,
            )
        );
    }

    /**
     * This is the landing dashboard for all UserRepository
     *
     * @param Request $request
     *
     * @Route("/new/budget", name="budget_new")
     */
    public function newBudgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $budget = new Budget();

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budget->setName($budget->getName()->getName());

            $em->persist($budget);
            $em->flush();

            $this->addFlash('notice', 'Budget created with success.');

            return $this->redirectToRoute('budget', array(), 301);
        }

        return $this->render(
            'BudgetBundle:Budget:budget.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
