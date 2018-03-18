<?php

namespace ImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/importer", name="importer")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('ImporterBundle:Imported')->findAll();

        return $this
            ->render(
                'ImporterBundle:Default:index.html.twig',
                array(
                    'data' => $data,
                )
            );
    }

    /**
     * @Route("/importAll")
     */
    public function importAllAction()
    {
        $importer = $this->get('importer.import');
        $importer->importFiles();

        return $this->redirectToRoute('importer');
    }
}
