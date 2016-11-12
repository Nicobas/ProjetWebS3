<?php

namespace SuperAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserManagerController extends Controller
{
    public function indexAction()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->findAll();

        return $this->render('SuperAdminBundle:UserManager:user_manager.html.twig', array(
            'users' => $users,
        ));
    }

    public function lockAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        $user->setLocked(!$user->isLocked());

        $em->flush();

        $request->getSession()->getFlashBag()->add('valid', "Le statut d'activation de l'utilisateur à changé");

        return $this->redirect($this->generateUrl('superadmin_users'));
    }
}