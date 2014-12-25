<?php

namespace Foo\RandomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Foo\RandomBundle\Utility\Random;
use FOS\RestBundle\Controller\FOSRestController;

class DefaultController extends FOSRestController
{
    public function indexAction($bytes)
    {
        $random = $this->get('foo_random.random');

        $view = $this->view()
          ->setData($random->urandomBase64($bytes))
          ->setFormat('json')
          ->setHeader('Access-Control-Allow-Origin', '*');

        return $this->handleView($view);
    }
}
