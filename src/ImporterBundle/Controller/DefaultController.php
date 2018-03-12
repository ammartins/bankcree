<?php

namespace ImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ImporterBundle\Entity\Imported;
use Symfony\Component\Finder\Finder;

class DefaultController extends Controller
{
  /**
   * @Route("/importer")
   */
  public function indexAction()
  {
    $finder = new Finder();
    // Maybe I should make this a bit more secure :P
    $finder->files()->in('../data')->name('*.TAB');;

    foreach ($finder as $file) {
      $path = explode("/", $file->getPath());
      dump($path[count($path)-1]);
      dump(count($path));
      $import = new Imported();
      $import->setFileName($file->getBasename());
      $import->setImportedAt(date('d-M-Y'));
      dump($import);
      // dump(get_class_methods($import));
      // dump(get_class_methods($file));
      // dump($file->getPath());
      // exit;
      // dump($file->getRelativePathname());
    }
    exit;
    return $this->render('ImporterBundle:Default:index.html.twig');
  }
}
