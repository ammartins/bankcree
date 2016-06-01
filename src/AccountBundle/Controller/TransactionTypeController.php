<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use AccountBundle\Entity\TransactionType;
use AccountBundle\Form\TransactionTypeType;

class TransactionTypeController extends Controller
{
  /**
   * @Route("/type/show/{currentYear}/{currentMonth}/{id}", name="type_show")
   * @param int $currentYear
   * @param int $currentMonth
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function showAction($currentYear ,$currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:TransactionType')->find($id);

    return $this->render('AccountBundle:TransactionType:show.html.twig',
      array(
        'transaction'   => $transaction,
        'currentMonth'  => $currentMonth,
        'currentYear'   => $currentYear,
      )
    );
  }

  /**
   * @Route("/match/{currentYear}/{currentMonth}/{id}", name="match")
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function matchAction($currentYear, $currentMonth, $id, Request $request) {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:Transactions')->getMatchTransactions($id);

    return $this->render('AccountBundle:tools:matchTransaction.html.twig',
      array()
    );
  }

  /**
   * @Route("/type/new/{currentYear}/{currentMonth}", name="type_new")
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function newAction($currentYear, $currentMonth, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = new TransactionType();
    $form         = $this->createForm(TransactionTypeType::class, $transaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $em->persist($transaction);
        $em->flush();
        $this->addFlash('notice', 'Transaction was successfully created.');
        return $this->redirectToRoute('home', array(
          'currentYear'   => $currentYear,
          'currentMonth'  => $currentMonth
        ),301);
    }

    return $this->render('AccountBundle:TransactionType:edit.html.twig',
      array(
        'transactionType' => $transaction,
        'form'            => $form->createView(),
        'currentMonth'    => $currentMonth,
        'currentYear'     => $currentYear,
      )
    );
  }

  /**
   * @Route("/type/edit/{currentYear}/{currentMonth}/{id}", name="type_edit")
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction($currentYear, $currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:TransactionType')->find($id);
    $form         = $this->createForm(TransactionTypeType::class, $transaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $em->persist($transaction);
        $em->flush();
        $this->addFlash('notice', 'Transaction was successfully updated.');
        return $this->redirectToRoute('home', array(
          'currentYear'   => $currentYear,
          'currentMonth'  => $currentMonth
        ),301);
    }

    return $this->render('AccountBundle:TransactionType:edit.html.twig',
      array(
        'transactionType' => $transaction,
        'form'            => $form->createView(),
        'currentMonth'    => $currentMonth,
        'currentYear'     => $currentYear,
      )
    );
  }

  /**
   * @Route("/type/delete/{currentYear}/{currentMonth}/{id}", name="delete_type")
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   */
  public function deleteAction($currentYear, $currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:TransactionType')->find($id);
    $em->remove($transaction);
    $em->flush();

    return $this->redirectToRoute('home',
      array(
        'currentMonth'  => $currentMonth,
        'currentYear'   => $currentYear,
      ),
      301
    );
  }
}
