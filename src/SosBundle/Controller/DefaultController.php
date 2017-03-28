<?php

namespace SosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SosBundle\Entity\Etablissement;
use SosBundle\Entity\Secteur;
use SosBundle\Service\Matching;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('SosBundle:Default:index.html.twig');
    }

    /**
     * @Route("/search")
     */
    public function searchAction(Request $request)
    {	

    	$data = array();

    	// Validation ville
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "ville" ) {

    		$data['ville'] = $request->get('ville');

    		$doctrine = $this->getDoctrine();
	        $em = $doctrine->getManager();

	        // Si google ne trouve pas les coordonnées de la ville écrite on la cherche dans la BDD
    		if ($data['ville']['latitude'] == "" || $data['ville']['longitude'] == "") {
    			
    			$em = $this->getDoctrine()->getManager();
    			$qb = $em->createQueryBuilder();

    			$result = $qb->select('v')
                  ->from('SosBundle:Ville','v')
                  ->where('v.libelle LIKE :libelle')
                  ->setParameter('libelle', $data['ville']['libelle'])
                  ->getQuery()
                  ->getOneOrNullResult();
		     
                if ($result == null) {
                	return $this->render('SosBundle:Search:ville.html.twig', array('error' => 'La ville ne fait pas partie de notre base de donnée.'));
                }else{
                	$data['ville']['libelle'] = $result->getLibelle();
                	$data['ville']['latitude'] = $result->getLatitude();
                	$data['ville']['longitude'] = $result->getLongitude();
                }

    		}

	        $repo = $em->getRepository("SosBundle:Etablissement");
	        $etablissements = $repo->findAll();

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

	        dump($data);
    		return $this->render('SosBundle:Search:classification.html.twig', array('etablissements' => $etablissements, 'data' => $data));	
    	}

    	// Validation classification
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "classification" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');

    		$doctrine = $this->getDoctrine();
	        $em = $doctrine->getManager();
	        $repo = $em->getRepository("SosBundle:Secteur");
	        $secteurs = $repo->findAll();

	        $data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

    		dump($data);
    		return $this->render('SosBundle:Search:secteur.html.twig', array('secteurs' => $secteurs, 'data' => $data));	
    	}

   		// Validation secteur activité
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "secteur" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');

    		dump($data);

    		$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			if ($data['secteur_activite'] == 1) {

				$em = $this->getDoctrine()->getManager();
				$qb = $em->createQueryBuilder();
				$postes = $qb->select('p')
	              ->from('SosBundle:PosteRecherche','p')
	              ->where('p.secteur = :secteur_activite')
	              ->setParameter('secteur_activite', $data['secteur_activite'])
	              ->getQuery()
	              ->getResult();

				return $this->render('SosBundle:Search:poste_hotellerie.html.twig', array('postes' => $postes, 'data' => $data));	  	

			}else if($data['secteur_activite'] == 2){

		 		$doctrine = $this->getDoctrine();
		        $em = $doctrine->getManager();
		        $repo = $em->getRepository("SosBundle:Service");
		        $services = $repo->findAll();

				return $this->render('SosBundle:Search:service.html.twig', array('services' => $services, 'data' => $data));	  	

			}
    		
    	}

   		// Validation service restauration
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "service" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['service_activite'] = $request->get('service_activite');

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			$em = $this->getDoctrine()->getManager();
			$qb = $em->createQueryBuilder();
			$postes = $qb->select('p')
              ->from('SosBundle:PosteRecherche','p')
              ->where('p.secteur = :secteur_activite')
              ->andWhere('p.service = :service_activite')
              ->setParameters(array('secteur_activite' => $data['secteur_activite'], 'service_activite' => $data['service_activite']))
              ->getQuery()
              ->getResult();

    		dump($data);
			return $this->render('SosBundle:Search:poste_restauration.html.twig', array('postes' => $postes, 'data' => $data));	  	
    	}

   		// Validation poste
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "poste_hotellerie" || $request->get('form') == "poste_restauration" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');

    		if ($request->get('form') == "poste_restauration") {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

    		$doctrine = $this->getDoctrine();
	        $em = $doctrine->getManager();
	        $repo = $em->getRepository("SosBundle:Contrat");
	        $contrats = $repo->findAll();

    		dump($data);
    		return $this->render('SosBundle:Search:contrat.html.twig', array('contrats' => $contrats, 'data' => $data));	
    	}

   		// Validation contrat
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "contrat" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');
    		$data['contrat'] = $request->get('contrat');

    		// Si on est dans la restauration : on ajoute le type de restauration
    		if (null !== $request->get('service_activite')) {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			$em = $this->getDoctrine()->getManager();
        	$repo = $em->getRepository("SosBundle:Contrat");
	        $contrat = $repo->find($data['contrat']);
			$contrat_duree = $contrat->getDuree();

    		dump($data);
    		return $this->render('SosBundle:Search:contrat_duree.html.twig', array('contrat_duree' => $contrat_duree, 'data' => $data));	

		}

   		// Validation duree contrat
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "duree" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');
    		$data['contrat'] = $request->get('contrat');
    		$data['contrat_duree'] = $request->get('contrat_duree');

    		// Si on est dans la restauration : on ajoute le type de restauration
    		if (null !== $request->get('service_activite')) {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			$em = $this->getDoctrine()->getManager();
        	$repo = $em->getRepository("SosBundle:CursusScolaire");
	        $cursus_scolaire = $repo->findAll();


			dump($data);    		
    		return $this->render('SosBundle:Search:cursus_scolaire.html.twig', array('cursus_scolaire' => $cursus_scolaire, 'data' => $data));	

		}

   		// Validation cursus scolaire
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "cursus" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');
    		$data['contrat'] = $request->get('contrat');
    		$data['contrat_duree'] = $request->get('contrat_duree');
    		$data['cursus_scolaire'] = $request->get('cursus_scolaire');

    		// Si on est dans la restauration : on ajoute le type de restauration
    		if (null !== $request->get('service_activite')) {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			$em = $this->getDoctrine()->getManager();
        	$repo = $em->getRepository("SosBundle:Anglais");
	        $niveau_anglais = $repo->findAll();

			dump($data);    		
    		return $this->render('SosBundle:Search:anglais.html.twig', array('niveau_anglais' => $niveau_anglais, 'data' => $data));	

		}

   		// Validation cursus scolaire
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "anglais" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');
    		$data['contrat'] = $request->get('contrat');
    		$data['contrat_duree'] = $request->get('contrat_duree');
    		$data['cursus_scolaire'] = $request->get('cursus_scolaire');
    		$data['niveau_anglais'] = $request->get('niveau_anglais');

    		// Si on est dans la restauration : on ajoute le type de restauration
    		if (null !== $request->get('service_activite')) {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['match_employe'] = $this->get('sos.matching')->getNumberOfEmploye($data, $request->get('form'));

			dump($data);    		
    		return $this->render('SosBundle:Search:date_debut.hml.twig', array('data' => $data));	

		}


   		// Validation cursus scolaire
    	if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "date" ) {

			$data['ville'] = $request->get('ville');
    		$data['classification'] = $request->get('classification');
    		$data['secteur_activite'] = $request->get('secteur_activite');
    		$data['poste'] = $request->get('poste');
    		$data['contrat'] = $request->get('contrat');
    		$data['contrat_duree'] = $request->get('contrat_duree');
    		$data['cursus_scolaire'] = $request->get('cursus_scolaire');
    		$data['niveau_anglais'] = $request->get('niveau_anglais');
    		$data['date_debut'] = $request->get('date_debut');

    		// Si on est dans la restauration : on ajoute le type de restauration
    		if (null !== $request->get('service_activite')) {
    			$data['service_activite'] = $request->get('service_activite');
    		}

			$data['employes'] = $this->get('sos.matching')->getEmploye($data);

    		foreach ($data['employes']  as $employee){
    		    $dateNaisssance = $employee->getDateNaissance();
    		    $today = new \DateTime('NOW');
                $age= $today->diff($dateNaisssance);

                $employee->age=(int)($age->days/365);
            }

			dump($data);    		
    		return $this->render('SosBundle:Search:resultat.html.twig', array('data' => $data));

		}

        return $this->render('SosBundle:Search:ville.html.twig');
    }


}