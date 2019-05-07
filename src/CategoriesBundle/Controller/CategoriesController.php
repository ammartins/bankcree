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

        $categories  = $em
            ->getRepository('CategoriesBundle:Categories')
            ->findBy(array(), array('name' => 'ASC'));

        $organizedC = [];

        foreach ($categories as $category) {
            if (!is_null($category->getParent())) {
                continue;
            }

            $organizedC[$category->getId()][] = $category;

            foreach ($categories as $categor) {
                if ($categor->getId() === $category->getId()
                    || is_null($categor->getParent())
                ) {
                    continue;
                }

                if ($categor->getParent()->getId() === $category->getId()) {
                    $organizedC[$category->getId()][] = $categor;
                }
            }
        }

        return $this
        ->render(
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

        $category = $em->getRepository('CategoriesBundle:Categories')->find($id);

        /**
         * All transactions that were already matched against given category
         */
        $transactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => $id));

        /**
         * All transactions that don't have a matching category yet
         */
        $openTransactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => null ));

        $results[$id] = array();

        $matchService = $this->container->get('transactions.match');

        foreach ($transactions as $match) {
            if ($match->getCategories()) {
                $matches = $matchService->matchToClean($match, $openTransactions);
                if (count($matches) > 0) {
                    foreach ($matches as $matchR) {
                        $categorieId = $match->getCategories()->getId();
                        $results[$categorieId][$matchR->getId()] = $matchR;
                    }
                }
                continue;
            }
        }

        $tranId = $category->getId();

        return $this->render(
            'CategoriesBundle:Categories:show.html.twig',
            array(
            'category' => $category,
            'transactions' => $results[$tranId] ? $results[$tranId] : [],
            'match' => $transactions,
            )
        );
    }

    /**
     * @Route("/match/{year}/{month}/{id}", name="match")
     *
     * @param int     $year
     * @param int     $month
     * @param int     $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchAction($year, $month, $id, Request $request)
    {
        // dump($id);
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->get('jms_serializer');
        $matchService = $this->get('transactions.match');

        // Element to be matched.
        $toBeSave = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->find($id);

        /*
         * All transactions that were matched with $id
         * also the transaction to be compared against.
         */
        $transactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->getMatchTransactions($id);

        $results = array();
        $macthingCategories = array();
        $transactionsResult = array();

        foreach ($transactions as $item) {
            $item = $em
                ->getRepository('TransactionsBundle:Transactions')
                ->findOneById($item['id']);

            $categoryName = $item->getCategories()->getName();

            if (!array_key_exists($categoryName, $results)) {
                $results[$categoryName] = 0;
            }

            $macthingCategories = $matchService
                ->match(
                    array($item),
                    $toBeSave,
                    $item->getCategories()->getId()
                );

            if (in_array($categoryName, $macthingCategories[0])) {
                $results[$categoryName] += 1;
                $transactionsResult[] = $macthingCategories[1];
                continue;
            }
            unset($results[$categoryName]);
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
            $this->addFlash('notice', 'Transaction was not updated.');
        }

        $results = $serializer->serialize($results, 'json');

        return $this->render(
            'CategoriesBundle:Categories:match.html.twig',
            array(
            'type' => $results,
            'form' => $form->createView(),
            'transaction' => $toBeSave,
            'transactions' => $transactionsResult,
            'year' => $year,
            'month' => $month
            )
        );
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
                $transaction->setParent(null);
            }

            if ($form->getData()->getParent() > 0) {
                $parent = $em
                    ->getRepository('CategoriesBundle:Categories')
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
     * @param int     $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $transaction  = $em
            ->getRepository('CategoriesBundle:Categories')
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
                $transaction->setParent(null);
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
     * @param int     $id
     * @param Request $request
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction  = $em
            ->getRepository('CategoriesBundle:Categories')
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

            $type = $em->getRepository('CategoriesBundle:Categories')->find($type);

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
