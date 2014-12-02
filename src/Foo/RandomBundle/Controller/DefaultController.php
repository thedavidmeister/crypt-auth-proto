<?php

namespace Foo\RandomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Foo\RandomBundle\Utility\Random;
use FOS\RestBundle\Controller\FOSRestController;

class DefaultController extends FOSRestController
{
    public function indexAction($bytes)
    {
        $random = Random::urandomBase64($bytes);
        $view = $this->view()
          ->setData($random)
          ->setFormat('json')
          ->setHeader('Access-Control-Allow-Origin', '*');

        return $this->handleView($view);
    }
}
