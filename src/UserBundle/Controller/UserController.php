<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
// Sessions
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render(
            'UserBundle:Security:login.html.twig',
            array(
            'error' => $error,
            )
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        //clear the token, cancel session and redirect
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        return $this->redirect($this->generateUrl('login'));
    }

    /**
     * @Route("/User/profile", name="profile")
     */
    public function profileAction()
    {
        // Generate Form for Edit
        $em = $this->getDoctrine()->getManager();
        $transaction = $this->get('account.account_repository')->find($id);
        
        $form = $this->createForm(
            UserType::class,
            $transaction,
            array (
                'entity_manager' => $em
            )
        );
        $form->handleRequest($request);
    }
}
