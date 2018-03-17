<?php

namespace ImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/importer")
     */
    public function indexAction()
    {
        // $importer = $this->get('importer.import');
        // $importer->importFiles();
        // exit;
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
}
