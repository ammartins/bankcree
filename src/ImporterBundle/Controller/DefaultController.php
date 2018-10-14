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
        $user = $this->get('security.context')->getToken();
        $userId = $user->getUser()->getId();

        $data = $em
            ->getRepository('ImporterBundle:Imported')
            ->getImported($userId);

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
        $user = $this->get('security.context')->getToken();
        $userId = $user->getUser()->getId();

        $importer = $this->get('importer.import');
        $importFrom = $this->container->getParameter('data_folder');

        $importer->importFiles($importFrom, $userId);

        return $this->redirectToRoute('importer');
    }

    /**
     * @Route("/imoport/file/", name="importFile")
     */
    public function importFileAction($filename)
    {
        $importer = $this->get('importer.import');
        $importFrom = $this->container->getParameter('data_folder');
        $importer->importFiles($importFrom, $filename);

        return $this->redirectToRoute('importer');
    }
}
