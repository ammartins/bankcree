<?php

namespace ImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/import", name="importer")
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
     * @Route("/import/all", name="importAll")
     */
    public function importAllAction()
    {
        $importer = $this->get('importer.import');
        $importer->importFiles();

        return $this->redirectToRoute('importer');
    }

    /**
     * @Route("/imoport/file/", name="importFile")
     */
    public function importFileAction($filename)
    {
        $importer = $this->get('importer.import');
        $importer->importFiles();

        return $this->redirectToRoute('importer');
    }
}
