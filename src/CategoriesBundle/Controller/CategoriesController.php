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

        $results = array();
        $transactionsResult = array();

        $matchService = $this->container->get('transactions.match');

        foreach ($openTransactions as $openTransaction) {
            $matches = $matchService->match(
                $transactions,
                $openTransaction,
                $category->getId()
            );

            $categoryName = $category->getName();

            if (!array_key_exists($categoryName, $results)) {
                $results[$categoryName] = 0;
            }

            if (count($matches[0])) {
                $results[$categoryName] += 1;
                $transactionsResult[] = $openTransaction;
            }

            if ($results[$categoryName] == 0) {
                unset($results[$categoryName]);
            }
        }

        return $this->render(
            'CategoriesBundle:Categories:show.html.twig',
            array(
            'category' => $category,
            'transactions' => $transactionsResult,
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
            ->getMatchTransactions();

        $results = array();
        $macthingCategories = array();
        $transactionsResult = array();

        if ($toBeSave->getCategories() == null) {
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

                if ($results[$categoryName] == 0) {
                    unset($results[$categoryName]);
                }
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
                'main_dashboard',
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
        $category  = new Categories();

        $form = $this->createForm(
            CategoriesType::class,
            $category,
            array(
            'entity_manager' => $em
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Setting User
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $category->setAccountId($user->getId());

            if ($form->getData()->getParent() == 0) {
                $category->setParent(null);
            }

            if ($form->getData()->getParent() > 0) {
                $parent = $em
                    ->getRepository('CategoriesBundle:Categories')
                    ->findBy(array('id' => $form->getData()->getParent()));
                $category->setParent($parent[0]);
            }

            $em->persist($category);
            $em->flush();
            $this->addFlash('notice', 'Transaction was successfully created.');

            if ($request->query->get('month') && $request->query->get('year')) {
                return $this->redirectToRoute(
                    'match',
                    array(
                        'year' => $request->query->get('year'),
                        'month' => $request->query->get('month'),
                        'id' => $request->query->get('id'),
                    ),
                    301
                );
            }

            return $this->redirectToRoute(
                'categories',
                array(),
                301
            );
        }

        return $this->render(
            'CategoriesBundle:Categories:edit.html.twig',
            array(
            'Categories' => $category,
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
     * @Route("/delete/categories/{id}", name="categories_delete")
     *
     * @param int     $id
     * @param Request $request
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category  = $em
            ->getRepository('CategoriesBundle:Categories')
            ->find($id);

        $transactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => $id));

        foreach ($transactions as $transaction) {
            $transaction->setCategories(null);
        }

        $em->remove($category);
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
        }

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        // set a HTTP response header
        $response->headers->set('Content-Type', 'text/html');
        // print the HTTP headers followed by the content
        $response->send();

        return $response;
    }
}
