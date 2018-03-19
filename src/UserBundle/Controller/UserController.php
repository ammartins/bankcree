<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
// Sessions
use Symfony\Component\HttpFoundation\Session\Session;
// Forms
use UserBundle\Form\UserType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
     * @Route("/user/profile", name="profile")
     */
    public function profileAction(Request $request)
    {
        // Generate Form for Edit
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $form = $this->createForm(
            UserType::class,
            $user
        );
        $form->handleRequest($request);

        // If the form is being submitted and it is valid lets save this
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'User was successfully updated.');

            return $this->redirectToRoute('profile', array(), 301);
        }

        return $this->render(
            'UserBundle:User:edit.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
