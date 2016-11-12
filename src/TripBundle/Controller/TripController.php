<?php

namespace TripBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use SuperAdminBundle\Form\EmailTripRolesType;
use SuperAdminBundle\Form\TripType;
use SuperAdminBundle\FormClasses\EmailFormClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TripBundle\Entity\Trip;
use TripBundle\Entity\TripParticipant;

class TripController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $tripParticipants = $em->getRepository('TripBundle:TripParticipant')->getUserTripParticipantsWithTrips($currentUser);
        $allowedTrips = $em->getRepository('TripBundle:Trip')->getUserTripsWithRestrictions($currentUser);

        $trips = new ArrayCollection(array_map(create_function('$o', 'return $o->getTrip();'), $tripParticipants));
        $allowedTripNotUse = new ArrayCollection();

        foreach ($allowedTrips as $t)
        {
            if (!$trips->contains($t)) {
                $allowedTripNotUse->add($t);
            }
        }

        return $this->render('TripBundle:Trip:trip_index.html.twig', array(
            'user_trips' => $tripParticipants,
            'allowed_trips' => $allowedTripNotUse,
        ));
    }

    public function detailAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $tripParticipant = $em->getRepository('TripBundle:TripParticipant')->getUserParticipant($currentUser, $id);

        if ($tripParticipant === null) {
        $request->getSession()->getFlashBag()->add('error', "Vous n'êtes pas inscrit à ce voyage");

        return $this->redirect($this->generateUrl('trip_homepage'));
    }

        return $this->render('TripBundle:Trip:trip_detail.html.twig', array(
            'tripPart' => $tripParticipant,
        ));
    }

    public function inscriptionAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $oldTripParticipant = $em->getRepository('TripBundle:TripParticipant')->getUserParticipant($currentUser, $trip);

        if ($oldTripParticipant != null) {
            $request->getSession()->getFlashBag()->add('warning', "Vous êtes déjà inscrit à ce voyage");

            return $this->redirect($this->generateUrl('trip_homepage'));
        }

        $isTripAllowed = $em->getRepository('TripBundle:Trip')->isTripAllowed($currentUser, $trip);

        if ($isTripAllowed) {
            $tripParticipant = new TripParticipant();
            $em->persist($tripParticipant);

            $tripParticipant->setTrip($trip);
            $tripParticipant->setParticipant($currentUser);

            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', "Vous êtes inscrit à ce voyage");

            return $this->redirect($this->generateUrl('trip_detail', array('id' => $id)));
        }
        else {
            $request->getSession()->getFlashBag()->add('error', "Vous n'avez pas accès à ce voyage");

            return $this->redirect($this->generateUrl('trip_homepage'));
        }
    }

    public function desinscriptionAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id ".$id." n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $oldTripParticipant = $em->getRepository('TripBundle:TripParticipant')->getUserParticipant($currentUser, $trip);

        if ($oldTripParticipant != null) {
            $em->remove($oldTripParticipant);
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', "Vous avez été désinscrit de ce voyage");

            return $this->redirect($this->generateUrl('trip_homepage'));
        }
        else {
            $request->getSession()->getFlashBag()->add('error', "Vous n'êtes pas inscrit à ce voyage");

            return $this->redirect($this->generateUrl('trip_homepage'));
        }
    }
}