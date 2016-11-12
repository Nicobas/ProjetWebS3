<?php

namespace SuperAdminBundle\Controller;

use SuperAdminBundle\Form\EmailTripRolesType;
use TripBundle\Form\TripType;
use SuperAdminBundle\FormClasses\EmailFormClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TripBundle\Entity\Trip;

class TripManagerController extends Controller
{
    public function indexAction()
    {
        $trips = $this->getDoctrine()->getManager()->getRepository('TripBundle:Trip')->findAll();

        return $this->render('SuperAdminBundle:TripManager:trip_manager.html.twig', array(
            'trips' => $trips,
        ));
    }

    public function addAction(Request $request)
    {
        $trip = new Trip();

        $form = $this->get('form.factory')->create(TripType::class, $trip);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trip);
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', 'Le voyage à été ajouté.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('superadmin_trips'));
        }

        return $this->render('TripBundle:Admin:trip_edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $em->remove($trip);

        $em->flush();

        $request->getSession()->getFlashBag()->add('valid', "Le voyage à été supprimé");

        return $this->redirect($this->generateUrl('superadmin_trips'));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create(TripType::class, $trip);

        if ($form->handleRequest($request)->isValid()) {
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', 'Le voyage à été modifié.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('superadmin_trips'));
        }

        return $this->render('TripBundle:Admin:trip_edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function rolesAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->getTripWithAdmins($id);

        $emailFormClass = new EmailFormClass();

        $form = $this->get('form.factory')->create(EmailTripRolesType::class, $emailFormClass);

        if ($form->handleRequest($request)->isValid()) {
            $user = $em->getRepository('UserBundle:User')->findOneByEmail($emailFormClass->getEmail());

            if (null != $user)
            {
                if ($trip->getAdmins()->contains($user)) {
                    $request->getSession()->getFlashBag()->add('warning', "L'utilisateur est déja admin");
                }
                else {
                    $trip->addAdmin($user);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('valid', "L'admin à été ajouté.");
                }
            }
            else {
                $request->getSession()->getFlashBag()->add('error', "Cet email n'est pas liée à un utilisateur");
            }
        }

        return $this->render('SuperAdminBundle:TripManager:trip_roles.html.twig', array(
            'trip' => $trip,
            'form' => $form->createView(),
        ));
    }

    public function deleteAdminAction($trip_id, $admin_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->getTripWithAdmins($trip_id);
        $admin = $em->getRepository('UserBundle:User')->find($admin_id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$trip_id." n'existe pas.");
        }

        if (null === $admin) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$admin_id." n'existe pas.");
        }

        if ($trip->getAdmins()->contains($admin)) {
            $trip->removeAdmin($admin);
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', "L'admin à été retiré");
        }

        return $this->redirect($this->generateUrl('superadmin_trip_roles', array('id' => $trip_id)));
    }

}