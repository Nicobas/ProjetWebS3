<?php

namespace TripBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TripBundle\Entity\Trip;
use TripBundle\Entity\TripParticipant;
use TripBundle\Form\TripType;

class AdminController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $trips = $currentUser->getAdministrableTrips();

        return $this->render('TripBundle:Admin:admin_index.html.twig', array(
            'trips' => $trips,
        ));
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
            return $this->redirect($this->generateUrl('trip_admin'));
        }

        return $this->render('TripBundle:Admin:trip_edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function restrictionsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $participants = $em->getRepository('TripBundle:TripParticipant')->getTripsParticipantsWithUsersRestrictions($trip);

        return $this->render('TripBundle:Admin:trip_edit.html.twig', array(
            'tripParticipants' => $participants,
        ));
    }

    public function usersAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $participants = $em->getRepository('TripBundle:TripParticipant')->getTripsParticipantsWithUsersRestrictions($trip);

        return $this->render('TripBundle:Admin:user_manager.html.twig', array(
            'tripParticipants' => $participants,
        ));
    }
}