<?php

namespace Foo\RandomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($bytes)
    {
        return $this->render('FooRandomBundle:Default:index.html.twig', array('bytes' => $bytes));
    }
}
