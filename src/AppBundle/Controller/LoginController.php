<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    /**
     * @Route("/app/1/{foo}", name="foo")
     * @Method({"GET"})
     */
    public function showAction($foo)
    {
        return $this->render('default/foo.html.twig', array('foo' => $foo));
    }
}
