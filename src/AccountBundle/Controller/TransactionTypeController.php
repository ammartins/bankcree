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
   * @Route("/type/show/{currentMonth}/{id}", name="type_show")
   */
  public function showAction($currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:TransactionType')->find($id);

    return $this->render('AccountBundle:TransactionType:show.html.twig',
      array(
        'transaction'   => $transaction,
        'currentMonth'  => $currentMonth,
      )
    );
  }

  /**
   * @Route("/type/new/{currentMonth}", name="type_new")
   *
   * @param int $currentMonth
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function newAction($currentMonth, Request $request)
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
        return $this->redirectToRoute('home', array('currentMonth' => $currentMonth), 301);
    }

    return $this->render('AccountBundle:TransactionType:edit.html.twig',
      array(
        'transactionType' => $transaction,
        'form'            => $form->createView(),
        'currentMonth'    => $currentMonth,
      )
    );
  }

  /**
   * @Route("/type/edit/{currentMonth}/{id}", name="type_edit")
   *
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction($currentMonth, $id, Request $request)
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
        return $this->redirectToRoute('home', array('currentMonth' => $currentMonth), 301);
    }

    return $this->render('AccountBundle:TransactionType:edit.html.twig',
      array(
        'transactionType' => $transaction,
        'form'            => $form->createView(),
        'currentMonth'    => $currentMonth,
      )
    );
  }

  /**
   * @Route("/type/delete/{currentMonth}/{id}", name="delete_type")
   *
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   */
  public function deleteAction($currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:TransactionType')->find($id);
    $em->remove($transaction);
    $em->flush();

    return $this->redirectToRoute('home', array('currentMonth' => $currentMonth), 301);
  }
}
