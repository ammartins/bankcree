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
    public function budgetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $budgets = $em->getRepository('BudgetBundle:Budget')->findAll();
        $budgetStatus = $em->getRepository('BudgetBundle:Budget')->findBudgets();

        $result = [];
        foreach ($budgets as $budget) {
            $result[$budget->getName()] = [
                "name" => $budget->getName(),
                "limit" => $budget->getBudgetLimit(),
                "amount" => 0,
                "id" => $budget->getId()
            ];
            foreach ($budgetStatus as $budgetst) {
                if ($budgetst["name"] === $budget->getName()) {
                    $result[$budget->getName()]["amount"] += $budgetst["amount"]*-1;
                    continue;
                }
                if ($budgetst['parent']
                ) {
                    $parentCategory = $em
                        ->getRepository('CategoriesBundle:Categories')
                        ->findById($budgetst['parent']);
                    // TODO try to not have if inside of if
                    if ($parentCategory[0]->getName() === $budget->getName()) {
                        $result[$parentCategory[0]->getName()]["amount"] += $budgetst["amount"]*-1;
                        continue;
                    }
                }
            }
        }

        return $this->render(
            'BudgetBundle:Budget:budgetIndex.html.twig',
            array(
                'budgets' => $result,
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

        $form = $this->createForm(
            BudgetType::class,
            $budget,
            array(
                'entity_manager' => $em
            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $em->getRepository('CategoriesBundle:Categories')
                ->findBy(array('id' => $form->getData()->getName()));

            $budget->setName($name[0]->getName());

            $userId = $this->get('security.context')->getToken()->getUser()->getId();
            $budget->setAccountId($userId);

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

    /**
     * @Route("/show/budget", name="budgets_show")
     */
    public function showAction()
    {
    }

    /**
     * @Route("/edit/budget", name="budgets_edit")
     */
    public function editAction()
    {
    }

    /**
     * @Route("/budget/delete/{id}", name="budgets_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $budget  = $em->getRepository('BudgetBundle:Budget')->find($id);
        $em->remove($budget);
        $em->flush();

        return $this->redirectToRoute(
            'budget',
            array(),
            301
        );
    }
}
