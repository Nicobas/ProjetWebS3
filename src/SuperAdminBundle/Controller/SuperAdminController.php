<?php

namespace SuperAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SuperAdminController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('SuperAdminBundle::index.html.twig', array(
        ));
    }
}