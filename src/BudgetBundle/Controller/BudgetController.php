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
     * This is the landing dashboard for all UserRepository
     *
     * @param Request $request
     *
     * @Route("/budget", name="budget")
     */
    public function budgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $budget = new Budget();

        $form = $this->createForm(BudgetType::class, $budget);
        // $form->add('transaction_type', EntityType::class, array(
        //     'label' => 'Transaction Type',
        //     'class' => 'CategoriesBundle:Categories',
        //     'choice_label' => 'name',
        // ));
        // dump($form);
        // exit;
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // Setting User
        //     $user = $this->get('security.token_storage')->getToken()->getUser();
        //     $transaction->setAccountId($user->getId());
        //
        //     $em->persist($transaction);
        //     $em->flush();
        //     $this->addFlash('notice', 'Transaction was successfully created.');
        //
        //     return $this->redirectToRoute(
        //         'home',
        //         array(
        //             'currentYear'   => $currentYear,
        //             'currentMonth'  => $currentMonth
        //         ),
        //         301
        //     );
        // }



        return $this->render(
            'BudgetBundle:Budget:budget.html.twig',
            array(
                'form' => $form->createView()
            )
        );

        // $em = $this->getDoctrine()->getManager();
        //
        // $budget = new Budget();
        //
        // $budget->setName('Home');
        // $budget->setGoal(1200);
        // $Categories = $em->getRepository('CategoriesBundle:Categories')->findAll();
        // $Categories = $Categories[0];
        // dump($Categories);
        // $budget->setCategoriess(array($Categories));
        //
        // $em->persist($budget);
        // $em->flush();
        //
        // dump($budget);
        //
        // $data = $em->getRepository('AccountBundle:Budget')
        //     ->findAll();
        //
        // dump($data);
        // exit;
    }
}
