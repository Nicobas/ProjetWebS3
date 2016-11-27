<?php

namespace TripBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use SuperAdminBundle\Form\EmailTripRolesType;
use SuperAdminBundle\FormClasses\EmailFormClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TripBundle\Entity\RestrictionTrip;
use TripBundle\Entity\Trip;
use TripBundle\Entity\TripParticipant;
use TripBundle\Form\EditCommentParticipantType;
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
            throw new NotFoundHttpException("Le voyage d'id " . $id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
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
            throw new NotFoundHttpException("Le voyage d'id " . $id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
        }

        $restrictions = $trip->getRestrictions();

        $emailFormClass = new EmailFormClass();

        $form = $this->get('form.factory')->create(EmailTripRolesType::class, $emailFormClass);

        if ($form->handleRequest($request)->isValid()) {
            $restriction = new RestrictionTrip();
            $restriction->setTrip($trip);
            $restriction->setEmail($emailFormClass->getEmail());
            $restriction->setInscrit(false);
            $em->persist($restriction);
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', "La restriction a été ajouté.");
        }

        return $this->render('TripBundle:Admin:trip_restrictions.html.twig', array(
            'trip' => $trip,
            'form' => $form->createView(),
        ));
    }

    public function usersAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id " . $id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
        }

        $participants = $em->getRepository('TripBundle:TripParticipant')->getTripsParticipantsWithUsersRestrictions($trip);

        return $this->render('TripBundle:Admin:user_manager.html.twig', array(
            'tripParticipants' => $participants,
        ));
    }

    public function userDesinscriptionAction($trip_id, $user_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TripBundle:Trip')->find($trip_id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id " . $trip_id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
        }

        $user = $em->getRepository('UserBundle:User')->find($user_id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id " . $user_id . " n'existe pas.");
        }

        $oldTripParticipant = $em->getRepository('TripBundle:TripParticipant')->getUserParticipant($user, $trip);

        if ($oldTripParticipant != null) {
            $em->remove($oldTripParticipant);
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', "Vous avez désinscrit l'utilisateur de ce voyage");

            return $this->redirect($this->generateUrl('trip_admin_users', array('id' => $trip_id)));
        } else {
            $request->getSession()->getFlashBag()->add('error', "L'utilisateur d'id " . $user_id . " n'est pas inscrit à ce voyage");

            return $this->redirect($this->generateUrl('trip_admin_users', array('id' => $trip_id)));
        }
    }

    public function userEditAction($trip_id, $user_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($trip_id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id " . $trip_id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
        }

        $user = $em->getRepository('UserBundle:User')->find($user_id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id " . $user_id . " n'existe pas.");
        }

        $tripParticipant = $em->getRepository('TripBundle:TripParticipant')->getUserParticipant($user, $trip);

        $form = $this->get('form.factory')->create(EditCommentParticipantType::class, $tripParticipant);

        if ($form->handleRequest($request)->isValid()) {
            $em->flush();

            $request->getSession()->getFlashBag()->add('valid', 'Les modifications ont été effectuées.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('trip_admin_users', array('id' => $trip_id)));
        }

        return $this->render('TripBundle:Admin:admin_user_edit_comment.html.twig', array(
            'tripParticipant' => $tripParticipant,
            'form' => $form->createView(),
        ));
    }

    public function restrictionDeleteAction($trip_id, $rest_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('TripBundle:Trip')->find($trip_id);

        if (null === $trip) {
            throw new NotFoundHttpException("Le voyage d'id " . $trip_id . " n'existe pas.");
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$trip->getAdmins()->contains($currentUser)) {
            throw new UnauthorizedHttpException("Vous n'êtes pas admin de ce voyage.");
        }

        $restriction = $em->getRepository('TripBundle:RestrictionTrip')->find($rest_id);

        if (null === $restriction) {
            throw new NotFoundHttpException("La restriction d'id " . $rest_id . " n'existe pas.");
        }

        if ($restriction->getTrip() !== $trip) {
            throw new NotFoundHttpException("La restriction d'id " . $rest_id . " n'est pas associée au voyage d'id " . $trip_id . ".");
        }

        $em->remove($restriction);
        $em->flush();

        $request->getSession()->getFlashBag()->add('valid', "Vous avez retirer cet email des restrictions");

        return $this->redirect($this->generateUrl('trip_admin_restrictions', array('id' => $trip_id)));
    }
}