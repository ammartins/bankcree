<?php

namespace TransactionsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionsController extends Controller
{
    /**
     * This is the landing dashboard for all UserRepository
     *
     * @Route("/", name="dashboard")
     */
    public function dashAction()
    {
         return $this
            ->redirectToRoute(
                'main_dashboard',
                array(
                    'year' => date('Y'),
                    'month' => date('m'),
                )
            );
    }

    /**
     * @Route("/transactions/edit/{year}/{month}/{id}", name="edit")
     *
     * @param int     $year
     * @param int     $month
     * @param int     $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($year, $month, $id, Request $request)
    {
        // Get Transaction
        $em = $this->getDoctrine()->getManager();
        $transaction = $this->get('account.account_repository')->find($id);

        // Generate Form for Edit
        $form = $this->createForm(
            TransactionsType::class,
            $transaction,
            array (
            'entity_manager' => $em
            )
        );
        $form->handleRequest($request);

        // If the form is being submitted and it is valid lets save this
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('account.account_service')->save($transaction);
            $this->addFlash('notice', 'Transaction was successfully updated.');

            return $this->redirectToRoute(
                'main_dashboard',
                array(
                'year' => $year,
                'month' => $month
                ),
                301
            );
        }
    }
}
