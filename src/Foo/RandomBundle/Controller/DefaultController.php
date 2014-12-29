<?php

namespace Foo\RandomBundle\Controller;

use Foo\RandomBundle\Utility\Random;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends FOSRestController
{
    const PARAM_BYTES = 'bytes';

    const PARAM_METHOD = 'generator';

    public function randomAction(Request $request)
    {
        $allowed_params = array(
          $this::PARAM_BYTES,
          $this::PARAM_METHOD,
        );
        $params = $request->query->all();
        $disallowed_params = array_diff(array_keys($params), $allowed_params);
        if (count($disallowed_params)) {
          throw new \Exception('Unrecognised parameters: ' . implode(',', $disallowed_params));
        }

        // Get the random generator service.
        $random = $this->get('foo_random.random');

        // Use 'bytes' param if set.
        $bytes = $request->get($this::PARAM_BYTES);
        if (isset($bytes)) {
          $random->setBytes($bytes);
        }

        // Determine which method to call.
        $method = $request->get($this::PARAM_METHOD);
        if (!isset($method)) {
          $method = $random::DEFAULT_METHOD;
        }
        // Throw an error if the method set as a parameter is not valid.
        if (!in_array($method, $random->getMethods())) {
          throw new \Exception('Invalid generator type: ' . $method);
        }

        $view = $this->view()
          ->setData($random->{$method}())
          ->setFormat('json')
          ->setHeader('Access-Control-Allow-Origin', '*');

        return $this->handleView($view);
    }
}
