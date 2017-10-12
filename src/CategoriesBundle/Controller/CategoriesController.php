<?php

namespace CategoriesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Form\CategoriesType;

// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// For Ajax response
use Symfony\Component\HttpFoundation\Response;

// Transactions For Match Form
use TransactionsBundle\Entity\Transactions;
use TransactionsBundle\Form\TransactionsType;

class CategoriesController extends Controller
{

    /**
     * @Route("/categories", name="categories")
     */
    public function categoriesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories  = $em->getRepository('CategoriesBundle:Categories')
            ->findBy(array(), array('name' => 'ASC'));

        $organizedC = [];
        foreach ($categories as $category) {
            if (!is_null($category->getParent())) {
                continue;
            }
            $organizedC[$category->getId()][] = $category;
            foreach ($categories as $categor) {
                if ($categor->getId() === $category->getId()) {
                    continue;
                }
                if (is_null($categor->getParent())) {
                    continue;
                }
                if ($categor->getParent()->getId() === $category->getId()) {
                    $organizedC[$category->getId()][] = $categor;
                }
            }
        }

        ksort($organizedC);
        return $this->render(
            'CategoriesBundle:Categories:categories.html.twig',
            array(
                'categories' => $organizedC,
            )
        );
    }

    /**
     * @Route("/categories/show/{id}", name="categories_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $transaction = $em->getRepository('CategoriesBundle:Categories')
            ->find($id);

        $possibleMatch = $em->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => $id));

        $toMatch = $em->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => null ));
        $results[$id] = array();

        foreach ($possibleMatch as $match) {
            if ($match->getCategories()) {
                $matches =  $this->match($match, $toMatch);
                if (count($matches) > 0) {
                    foreach ($matches as $matchR) {
                        $categorieId = $match->getCategories()->getId();
                        $results[$categorieId][$matchR->getId()] = $matchR;
                    }
                } else {
                    continue;
                }
            }
        }

        $tranId = $transaction->getId();

        return $this->render(
            'CategoriesBundle:Categories:show.html.twig',
            array(
                'transaction' => $transaction,
                'transactions' => $results[$tranId] ? $results[$tranId] : [],
                'matchAgainst' => $possibleMatch,
            )
        );
    }

    /**
     * @Route("/match/{year}/{month}/{id}", name="match")
     *
     * @param int $year
     * @param int $month
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchAction($year, $month, $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->get('jms_serializer');
        $matchService = $this->get('transactions.match');

        $toBeSave = $em->getRepository('TransactionsBundle:Transactions')
            ->find($id);
        $transaction = $em->getRepository('TransactionsBundle:Transactions')
            ->getMatchTransactions($id);

        $results = array();
        $transacDescription = $transaction['transaction'][0]['description'];
        $transacDescription = $matchService->cleanUp($transacDescription);

        foreach ($transaction['data'] as $item) {
            $score = 0;

            $itemDescription = $matchService->cleanUp($item['description']);

            foreach ($itemDescription as $value) {
                if (in_array($value, $transacDescription)) {
                    $score += 1;
                }
            }

            if ($score > (count($itemDescription))/2) {
                $item['percentage'] = round(
                    (($score*100)/(count($transacDescription))),
                    0
                );

                $results[] = $item;
                $score = 0;
                continue;
            }
        }

        $form = $this->createForm(
            TransactionsType::class,
            $toBeSave,
            array(
                'entity_manager' => $em
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryId = $form->getData()->getCategories();
            $category = $em
                ->getRepository('CategoriesBundle:Categories')
                ->findById($categoryId);
            $toBeSave->setCategories($category[0]);

            $em->persist($toBeSave);
            $em->flush();

            $this->addFlash('notice', 'Transaction was successfully updated.');

            return $this->redirectToRoute(
                'home',
                array(
                    'year' => $year,
                    'month' => $month
                ),
                301
            );
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            // $this->addFlash('notice', $form->getError());
            $this->addFlash('notice', 'Transaction was not updated.');
        }

        // Even more hugly code :P
        $type = array();
        foreach ($results as $result) {
            if (array_key_exists($result['name'], $type)) {
                $type[$result['name']] += 1;
                continue;
            }
            $type[$result['name']] = 1;
        }

        $type = $serializer->serialize($type, 'json');

        return $this->render(
            'CategoriesBundle:Categories:match.html.twig',
            array(
                'type' => $type,
                'form' => $form->createView(),
                'transactions' => $results,
                'transaction' => $transaction['transaction'][0],
                'year' => $year,
                'month' => $month
            )
        );
    }

    // TODO OMG PLEASE REMOVE THIS CODE FROM HERE
    public function match($toBeSave, $transaction)
    {
        $results = array();
        $categorieDesc = preg_split(
            '/[\s\/\*]/',
            $toBeSave->getDescription()
        );
        foreach ($transaction as $item) {
            if ($item->getCategories()) {
                continue;
            }

            $score = 0;
            $special = 0;

            $itemDescription = $item->getDescription();
            $itemDescription = preg_replace('!\s+!', ' ', $itemDescription);
            $itemDescription = preg_split('/[\s\/\*]/', $itemDescription);

            foreach ($itemDescription as $key => $item1) {
                if ($item1 == 'TRTP' || $item1 == 'IBAN' ||
                    $item1 == 'BIC' || $item1 == 'NAME' ||
                    $item1 == 'EREF' || $item1 == 'SEPA' ||
                    $item1 == 'REMI' || $item1 == 'CSID' ||
                    $item1 == 'Incasso' || $item1 == 'MARF' ||
                    $item1 == '' || $item1 == 'algemeen' ||
                    $item1 == 'doorlopend' || $item1 == 'IBAN:' ||
                    $item1 == 'Overboeking' || $item1 == 'INGBNL2A' ||
                    $item1 == 'BIC:' || $item1 == 'Omschrijving:' ||
                    $item1 == 'SEPA' || $item1 == 'OVERBOEKING' ||
                    $item1 == 'BEA' || $item1 == 'NOTPROVIDED' ||
                    $item1 == 'Naam:'
                ) {
                    $special += 1;
                    $itemDescription[$key] = NULL;
                    continue;
                }
                if (in_array($item1, $categorieDesc)) {
                    $score += 1;
                }
                if ($score > (count($itemDescription)-$special)/2) {
                    $results[$item->getId()] = $item;
                    $score = 0;
                    $special = 0;
                    continue;
                }
            }
        }
        // Even more hugly code :P
        return $results;
    }


    /**
     * @Route("/categories/new/", name="categories_new")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction  = new Categories();

        $form = $this->createForm(
            CategoriesType::class,
            $transaction,
            array(
                'entity_manager' => $em
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Setting User
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $transaction->setAccountId($user->getId());

            if ($form->getData()->getParent() == 0) {
                $transaction->setParent(NULL);
            }

            if ($form->getData()->getParent() > 0) {
                $parent = $em->getRepository('CategoriesBundle:Categories')
                    ->findBy(array('id' => $form->getData()->getParent()));
                $transaction->setParent($parent[0]);
            }

            $em->persist($transaction);
            $em->flush();
            $this->addFlash('notice', 'Transaction was successfully created.');

            return $this->redirectToRoute(
                'categories',
                array(),
                301
            );
        }

        return $this->render(
            'CategoriesBundle:Categories:edit.html.twig',
            array(
                'Categories' => $transaction,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/categories/edit/{id}", name="categories_edit")
     *
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction  = $em->getRepository('CategoriesBundle:Categories')
            ->find($id);

        $form = $this->createForm(
            CategoriesType::class,
            $transaction,
            array(
                'entity_manager' => $em
            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->getParent() === 0) {
                  $transaction->setParent(NULL);
            }

            if ($form->getData()->getCompanyLogo()) {
                $transaction->setCompanyLogo($form->getData()->getCompanyLogo());
            }

            $em->persist($transaction);
            $em->flush();
            $this->addFlash('notice', 'Transaction was successfully updated.');

            return $this->redirectToRoute(
                'categories',
                array(),
                301
            );
        }

        return $this->render(
            'CategoriesBundle:Categories:edit.html.twig',
            array(
                'Categories' => $transaction,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/categories/delete/{id}", name="categories_delete")
     *
     * @param int $id
     * @param Request $request
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction  = $em->getRepository('CategoriesBundle:Categories')
            ->find($id);
        $em->remove($transaction);
        $em->flush();

        return $this->redirectToRoute(
            'categories',
            array(),
            301
        );
    }

    /**
     * @Route("/categories/matching", defaults={"_format"="xml"}, name="matching")
     */
    public function matchingAction(Request $request)
    {
        $response = new Response();
        if ($request->isXmlHttpRequest()) {
            $em =  $this->getDoctrine()->getManager();
            $update = $request->request->get('selected');
            $type = $request->request->get('type');
            $type = $em->getRepository('CategoriesBundle:Categories')
                ->find($type);

            $response->setStatusCode(Response::HTTP_OK);

            // set a HTTP response header
            $response->headers->set('Content-Type', 'text/html');
            // print the HTTP headers followed by the content
            $response->send();

            // TODO update all the IDs with type
            foreach ($update as $id) {
                $element = $em->getRepository('TransactionsBundle:Transactions')
                    ->find($id);
                $element->setCategories($type);
                $em->flush();
            }

            return $response;
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            // set a HTTP response header
            $response->headers->set('Content-Type', 'text/html');
            // print the HTTP headers followed by the content
            $response->send();

            return $response;
        }
    }
}
