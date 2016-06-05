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

// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    $serializer   = $this->get('jms_serializer');
    $toBeSave     = $em->getRepository('AccountBundle:Transactions')->find($id);
    $transaction  = $em->getRepository('AccountBundle:Transactions')->getMatchTransactions($id);

    $results = array();
    $transactionDescription = preg_split('/[\s\/\*]/', $transaction['transaction'][0]['description']);
    foreach ( $transaction['data'] as $item )
    {
      $itemDescription = $item['description'];
      $itemDescription = preg_replace('!\s+!', ' ', $itemDescription);
      $itemDescription = preg_split('/[\s\/\*]/', $itemDescription);
      $score = 0;
      $special = 0;
      foreach ( $itemDescription as $item1)
      {
        if (
          $item1 == 'TRTP' || $item1 == 'IBAN' || $item1 == 'BIC' ||
          $item1 == 'NAME' || $item1 == 'EREF' || $item1 == 'SEPA' ||
          $item1 == 'REMI' || $item1 == 'CSID' || $item1 == 'Incasso' ||
          $item1 == 'MARF' || $item1 == '' || $item1 == 'algemeen' ||
          $item1 == 'doorlopend' || $item1 == 'IBAN:' ||
          $item1 == 'Overboeking' || $item1 == 'INGBNL2A' || $item1 == 'BIC:' ||
          $item1 == 'Omschrijving:' || $item1 == 'SEPA'
        )
        {
          $special += 1;
          continue;
        }
        if ( in_array($item1, $transactionDescription) )
        {
          $score += 1;
        }
        if ( $score > (count($itemDescription)-$special)/2 ) {
            $item['percentage'] = round((($score*100)/(count($itemDescription)-$special)), 0);
            $results[] = $item;
            $score = 0;
            $special = 0;
            continue;
        }
      }
    }

    $form = $this->createFormBuilder($toBeSave)
              ->add('transaction_type', EntityType::class, array(
                    'label'     => 'Transaction Type',
                    'class'     => 'AccountBundle:TransactionType',
                    'choice_label'  => 'name',
              ))->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $em->persist($toBeSave);
        $em->flush();
        $this->addFlash('notice', 'Transaction was successfully updated.');
        return $this->redirectToRoute('home',
          array(
            'currentYear'   => $currentYear,
            'currentMonth' => $currentMonth
          ),301);
    } elseif ($form->isSubmitted() && !$form->isValid()) {
        $this->addFlash('notice', 'Transaction was not updated.');
    }

    // Even more hugly code :P
    $type = array();
    foreach ( $results as $result )
    {
      if ( array_key_exists($result['name'], $type) ) {
        $type[$result['name']] += 1;
      } else {
        $type[$result['name']] = 1;
      }
    }

    $type = $serializer->serialize($type, 'json');

    return $this->render('AccountBundle:tools:matchTransaction.html.twig',
      array(
        'type'          => $type,
        'form'          => $form->createView(),
        'transactions'  => $results,
        'transaction'   => $transaction['transaction'][0],
        'currentYear'   => $currentYear,
        'currentMonth'  => $currentMonth
      )
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
