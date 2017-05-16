<?php

namespace SosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
	/**
     * @Route("/mesrecommandations")
     */
    public function mesrecommandationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $recommandations = $em->getRepository('SosBundle:Recommandation')->findBy(array('user' => 2, 'valide' => 1));
        $nbreco = count($recommandations);
        return $this->render('SosBundle:User:mesrecommandations.html.twig', array("recommandations" => $recommandations, "nbreco"=>$nbreco));
    }

    /**
     * @Route("/user/{id}", requirements={"id" = "\d+"})
     */
    public function userAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SosBundle:User')->find($id);
        dump($user);
        return $this->render('SosBundle:User:userProfil.html.twig', array("user" => $user));
    }

}
