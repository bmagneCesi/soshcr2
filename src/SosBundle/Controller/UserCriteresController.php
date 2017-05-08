<?php

namespace SosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SosBundle\Entity\UserCritere;
use SosBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;


class UserCriteresController extends Controller
{
   /**
     * @Route("/usercritere")
     */
    public function indexAction(Request $request)
    { 

        return $this->render('SosBundle:UserCriteres:index.html.twig'); 

    }

   /**
     * @Route("/usercritere/step1")
     */
    public function step1Action(Request $request)
    { 

        $em = $this->getDoctrine()->getManager();

        $repoContrats = $em->getRepository("SosBundle:Contrat");
        $contrats = $repoContrats->findAll();

        $repoCursus = $em->getRepository("SosBundle:CursusScolaire");
        $cursusScolaire = $repoCursus->findAll();

        return $this->render('SosBundle:UserCriteres:step1.html.twig', array('contrats' => $contrats, 'cursusScolaire' => $cursusScolaire)); 

    }

    /**
     * @Route("/usercriteres/step2")
     */
    public function step2Action(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getManager();

      // Validation contrat
      if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_1" )
      {
        
        $contrats = $request->get('data');

        foreach ($contrats as $key => $value) {

          if (isset($value['cursus']) && isset($value['duree']))
          {
            $data['contrats'][] = array(
              'id' => $key,
              'duree' => $value['duree'],
              'cursus' => $value['cursus']
            ); 
          }
          else if (!isset($value['duree']) && isset($value['cursus']))
          {
            $data['contrats'][] = array(
              'id' => $key,
              'cursus' => $value['cursus']
            ); 
          }
          elseif (isset($value['duree']) && !isset($value['cursus'])) {
            $data['contrats'][] = array(
              'id' => $key,
              'duree' => $value['duree']
            ); 
          }
          
        }

        // store into the session
        $session = $request->getSession();
        $session->set('contrats', $data['contrats']);

        $repoEtablissement = $em->getRepository('SosBundle:Etablissement');
        $etablissements = $repoEtablissement->findAll();

        
        return $this->render('SosBundle:UserCriteres:step2.html.twig', array('etablissements' => $etablissements)); 

      }
      else
      {
        return $this->redirectToRoute('usercriteres_'.$request->get('form'));
      }
    }

    /**
     * @Route("/usercriteres/step3")
     */
    public function step3Action(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getManager();

      // Validation contrat
      if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_2" )
      {
        
        $data['etablissements'] = $request->get('etablissements');
        // store into the session
        $session = $request->getSession();
        $session->set('etablissements', $data['etablissements']);

        return $this->render('SosBundle:UserCriteres:step3.html.twig'); 

      }
      else
      {
        return $this->redirectToRoute('usercriteres_'.$request->get('form'));
      }
    }

    /**
     * @Route("/usercriteres/step4")
     */
    public function step4Action(Request $request)
    {

        $data = array();
        $em = $this->getDoctrine()->getManager();

      // Validation contrat
      if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_3" )
      {
        
        $data['etablissements'] = $request->get('etablissements');
        // store into the session
        $session = $request->getSession();
        $session->set('etablissements', $data['etablissements']);

        return $this->render('SosBundle:UserCriteres:step4.html.twig'); 

      }
      else
      {
        return $this->redirectToRoute('usercriteres_'.$request->get('form'));
      }
    }

}