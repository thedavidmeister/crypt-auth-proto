<?php

namespace Foo\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FooPersonaBundle:Default:index.html.twig', array('name' => $name));
    }
}
