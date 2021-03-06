<?php
namespace SosBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SosBundle\Entity\UserCritere;
use SosBundle\Entity\User;
use SosBundle\Entity\PosteCritere;
use SosBundle\Entity\ContratCritere;
use SosBundle\Entity\Anglais;
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
        return $this->render('SosBundle:UserCriteres:step1.html.twig', array('contrats' => $contrats));
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
                        'contrat' => $key,
                        'duree' => $value['duree'],
                        'cursus' => $value['cursus']
                    );
                }
                else if (!isset($value['duree']) && isset($value['cursus']))
                {
                    $data['contrats'][] = array(
                        'contrat' => $key,
                        'cursus' => $value['cursus']
                    );
                }
                elseif (isset($value['duree']) && !isset($value['cursus'])) {
                    $data['contrats'][] = array(
                        'contrat' => $key,
                        'duree' => $value['duree']
                    );
                }
                else
                {
                    $data['contrats'][] = array(
                        'contrat' => $key,
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
        $em = $this->getDoctrine()->getManager();
        // Validation contrat
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_2" )
        {

            // store into the session
            $session = $request->getSession();
            $session->set('etablissements', $request->get('etablissements'));
            
            
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

            $geocoder = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBvtLKkt2MEgUKvIat-0wBT4Hg4wQ9HkqQ&address=%s&sensor=false';
            $query = sprintf($geocoder, urlencode($_POST['ville']));
            $result = json_decode(file_get_contents($query));
            
            if ($result == NULL || $result == "" ||  $result->status == "ZERO_RESULTS" || $result->status == "INVALID_REQUEST"  || $result->status == "REQUEST_DENIED" ){
                return $this->render('SosBundle:UserCriteres:step3.html.twig', array('error' => 'ville'));
            }else{
                $json = $result->results[0];
                // store into the session
                $session = $request->getSession();
                $session->set('ville', $json->formatted_address);
                $session->set('latitude', $json->geometry->location->lat);
                $session->set('longitude', $json->geometry->location->lng);
                $session->set('rayon_emploi', $request->get('rayon_emploi'));

            }

            $repoFormations = $em->getRepository('SosBundle:Formation');
            $formations = $repoFormations->findAll();
            $repoAnglais = $em->getRepository('SosBundle:Anglais');
            $niveauAnglais = $repoAnglais->findAll();
            
            
            
            
            
            return $this->render('SosBundle:UserCriteres:step4.html.twig', array('formations' => $formations, 'niveauAnglais' => $niveauAnglais));
        }
        else
        {
            return $this->redirectToRoute('usercriteres_'.$request->get('form'));
        }
    }
    /**
     * @Route("/usercriteres/step5")
     */
    public function step5Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Validation contrat
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_4" )
        {

            // store into the session
            $session = $request->getSession();
            $session->set('formation', $request->get('formation'));
            $session->set('anglais', $request->get('anglais'));
            $postesRepository = $em->getRepository('SosBundle:PosteRecherche');
            $postes = $postesRepository->findAll();
            
            
            
            
            
            
            
            $repoExperiences = $em->getRepository('SosBundle:Experience');
            $experiences = $repoExperiences->findAll();
            return $this->render('SosBundle:UserCriteres:step5.html.twig', array('postes' => $postes, 'experiences' => $experiences));
        }
        else
        {
            return $this->redirectToRoute('usercriteres_'.$request->get('form'));
        }
    }

    /**
     * @Route("/usercriteres/step8")
     */
    public function step8Action(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getManager();
        // Validation contrat
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_5" )
        {
            $postes = $request->get('data');

            foreach ($postes as $key => $value) {
                if (isset($value['poste']) && !empty($value['poste']) && isset($value['experience']) && !empty($value['experience']))
                {
                    $data['postes'][] = array(
                        'poste' => $key,
                        'experience' => $value['experience']
                    );
                }
            }
            $session = $request->getSession();
            $session->set('postes', $data['postes']);
            
            
            
            
            
            
            
            
            return $this->render('SosBundle:UserCriteres:step8.html.twig');
        }
        else
        {
            return $this->redirectToRoute('usercriteres_'.$request->get('form'));
        }
    }

    /**
     * @Route("/usercriteres/edit/disponibilite")
     */
    public function disponibiliteModificationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $criteres = $user->getCriteres();
        $dispos = array();
        foreach ($criteres as $key => $value) {
            $dispos = json_decode($value->getDisponibilites());
        }
        $dispos = array_unique($dispos);
        
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_8" )
        {
            $dispos = $request->get('disponibilite');
            if (!empty($dispos))
            {
                foreach ($criteres as $key => $value) {
                    $value->setDisponibilites(json_encode($dispos));
                    $em->persist($value);
                }
                $em->flush();
            }
            
            return $this->redirectToRoute('dashboard');

        }
        return $this->render('SosBundle:UserCriteres:disponibilite_modification.html.twig', array('disponibilites' => $dispos));
    }

    /**
     * @Route("/usercriteres/edit")
     */
    public function userCriteresModificationAction(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getManager();
        // Validation contrat
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_8" || $request->get('form') == "step_5")
        {

            $postes = $request->get('data');

            foreach ($postes as $key => $value) {
                if (isset($value['poste']) && !empty($value['poste']) && isset($value['experience']) && !empty($value['experience']))
                {
                    $data['postes'][] = array(
                        'poste' => $key,
                        'experience' => $value['experience']
                    );
                }
            }

            $session = $request->getSession();
            $session->set('disponibilite', $request->get('disponibilite'));
            $resultat = array(
                'contrats' => $session->get('contrats'),
                'etablissements' => $session->get('etablissements'),
                'ville' => $session->get('ville'),
                'latitude' => $session->get('latitude'),
                'longitude' => $session->get('longitude'),
                'rayon_emploi' => $session->get('rayon_emploi'),
                'formations' => $session->get('formation'),
                'anglais' => $session->get('anglais'),
                'score' => 0,
                'postes' => $data['postes'],
                'disponibilites' => $session->get('disponibilite'),
            );

            $repoAnglais = $em->getRepository('SosBundle:Anglais');
            $repoContrat = $em->getRepository('SosBundle:Contrat');
            $repoCursus = $em->getRepository('SosBundle:CursusScolaire');
            $repoDuree = $em->getRepository('SosBundle:TypeContrat');
            $repoPoste = $em->getRepository('SosBundle:PosteRecherche');
            $repoExperience = $em->getRepository('SosBundle:Experience');
            $repoFormation = $em->getRepository('SosBundle:Formation');
            $repoEtablissement = $em->getRepository('SosBundle:Etablissement');

            //****SCORE*****

            $anglais = $em->getRepository('SosBundle:Anglais')->find($resultat['anglais']);
            $pointsAnglais = $anglais->getPoints();

            $pointsTotal=0;
            foreach ($resultat['postes'] as $k => $poste) {
                $pointsExperience = $repoExperience->find(($poste['experience']))->getPoints();
                $pointsPoste = $repoPoste->find($poste['poste'])->getCoefficient();
                $pointsTotal = $pointsTotal+($pointsExperience*$pointsPoste);
            }
            $pointsTotal = $pointsTotal + $pointsAnglais;

            $userCriteres = $this->getUser()->getCriteres();
            $dateDispoOld = $userCriteres[0]->getDisponibilites();

            $alreadyCriteresRepo = $em->getRepository('SosBundle:UserCritere');
            $alreadyCriteres = $alreadyCriteresRepo->createQueryBuilder('uc')
                ->where('uc.user = :user')
                ->setParameter('user', $this->getUser()->getId())
                ->getQuery()
                ->getResult();
            if (!empty($alreadyCriteres)) {
                foreach ($alreadyCriteres as $key => $value) {
                    $em->remove($value);
                }
            }

            //*********************
            foreach ($resultat['contrats'] as $key => $contrat)
            {
                foreach ($resultat['postes'] as $k => $poste)
                {
                    $usercritere = new UserCritere();
                    $usercritere->setUser($this->getUser());
                    $usercritere->setContrat($repoContrat->find($contrat['contrat']));
                    if (isset($contrat['duree']))
                    {
                        foreach ($contrat['duree'] as $key => $value)
                        {
                            $duree = $repoDuree->find($value);
                            $usercritere->addDuree($duree);
                        }
                    }
                    if (isset($contrat['cursus']))
                    {
                        foreach ($contrat['cursus'] as $key => $value)
                        {
                            $cursus = $repoCursus->find($value);
                            $usercritere->addCursus($cursus);
                        }
                    }
                    $usercritere->setPoste($repoPoste->find($poste['poste']));
                    $usercritere->setExperience($repoExperience->find($poste['experience']));
                    $usercritere->setVille($resultat['ville']);
                    $usercritere->setLatitude($resultat['latitude']);
                    $usercritere->setLatitude($resultat['latitude']);
                    $usercritere->setLongitude($resultat['longitude']);
                    $usercritere->setRayonEmploi($resultat['rayon_emploi']);
                    $usercritere->setNiveauAnglais($repoAnglais->find($resultat['anglais']));
                    $usercritere->setScore($pointsTotal);
                    $usercritere->setDisponibilites($dateDispoOld);
                    
                    foreach ($resultat['formations'] as $key => $value)
                    {
                        $formation = $repoFormation->find($value['id']);
                        $usercritere->addFormation($formation);
                    }
                    foreach ($resultat['etablissements'] as $key => $value)
                    {
                        $etablissement = $repoEtablissement->find($value['id']);
                        $usercritere->addEtablissement($etablissement);
                    }
                    $em->persist($usercritere);
                }
                $em->flush();
            }      

            return $this->render('SosBundle:UserCriteres:step9.html.twig', array('contratCritere' => $usercritere));
        }
        else
        {
            return $this->redirectToRoute('usercriteres_'.$request->get('form'));
        }
    }
    /**
     * @Route("/usercriteres/step9")
     */
    public function step9Action(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getManager();
        // Validation contrat
        if ($request->isMethod('POST') && null !== $request->get('form') && $request->get('form') == "step_8" || $request->get('form') == "step_5")
        {

            $session = $request->getSession();
            $session->set('disponibilite', $request->get('disponibilite'));
            $resultat = array(
                'contrats' => $session->get('contrats'),
                'etablissements' => $session->get('etablissements'),
                'ville' => $session->get('ville'),
                'latitude' => $session->get('latitude'),
                'longitude' => $session->get('longitude'),
                'rayon_emploi' => $session->get('rayon_emploi'),
                'formations' => $session->get('formation'),
                'anglais' => $session->get('anglais'),
                'score' => 0,
                'postes' => $session->get('postes'),
                'disponibilites' => $session->get('disponibilite'),
            );

            $repoAnglais = $em->getRepository('SosBundle:Anglais');
            $repoContrat = $em->getRepository('SosBundle:Contrat');
            $repoCursus = $em->getRepository('SosBundle:CursusScolaire');
            $repoDuree = $em->getRepository('SosBundle:TypeContrat');
            $repoPoste = $em->getRepository('SosBundle:PosteRecherche');
            $repoExperience = $em->getRepository('SosBundle:Experience');
            $repoFormation = $em->getRepository('SosBundle:Formation');
            $repoEtablissement = $em->getRepository('SosBundle:Etablissement');

            //****SCORE*****

            $anglais = $em->getRepository('SosBundle:Anglais')->find($resultat['anglais']);
            $pointsAnglais = $anglais->getPoints();

            $pointsTotal=0;
            foreach ($resultat['postes'] as $k => $poste) {
                $pointsExperience = $repoExperience->find(($poste['experience']))->getPoints();
                $pointsPoste = $repoPoste->find($poste['poste'])->getCoefficient();
                $pointsTotal = $pointsTotal+($pointsExperience*$pointsPoste);
            }
            $pointsTotal = $pointsTotal + $pointsAnglais;
            //*********************

            foreach ($resultat['contrats'] as $key => $contrat)
            {
                foreach ($resultat['postes'] as $k => $poste)
                {
                    $usercritere = new UserCritere();
                    $usercritere->setUser($this->getUser());
                    $usercritere->setContrat($repoContrat->find($contrat['contrat']));
                    if (isset($contrat['duree']))
                    {
                        foreach ($contrat['duree'] as $key => $value)
                        {
                            $duree = $repoDuree->find($value);
                            $usercritere->addDuree($duree);
                        }
                    }
                    if (isset($contrat['cursus']))
                    {
                        foreach ($contrat['cursus'] as $key => $value)
                        {
                            $cursus = $repoCursus->find($value);
                            $usercritere->addCursus($cursus);
                        }
                    }
                    $usercritere->setPoste($repoPoste->find($poste['poste']));
                    $usercritere->setExperience($repoExperience->find($poste['experience']));
                    $usercritere->setVille($resultat['ville']);
                    $usercritere->setLatitude($resultat['latitude']);
                    $usercritere->setLatitude($resultat['latitude']);
                    $usercritere->setLongitude($resultat['longitude']);
                    $usercritere->setRayonEmploi($resultat['rayon_emploi']);
                    $usercritere->setNiveauAnglais($repoAnglais->find($resultat['anglais']));
                    $usercritere->setScore($pointsTotal);

                    if (!empty($resultat['disponibilites']))
                    {
                        $usercritere->setDisponibilites(json_encode($resultat['disponibilites']));
                    }
                    
                    foreach ($resultat['formations'] as $key => $value)
                    {
                        $formation = $repoFormation->find($value['id']);
                        $usercritere->addFormation($formation);
                    }
                    foreach ($resultat['etablissements'] as $key => $value)
                    {
                        $etablissement = $repoEtablissement->find($value['id']);
                        $usercritere->addEtablissement($etablissement);
                    }
                    $em->persist($usercritere);
                    $em->flush();
                }
            }
            return $this->render('SosBundle:UserCriteres:step9.html.twig', array('contratCritere' => $usercritere));
        }
        else
        {
            return $this->redirectToRoute('usercriteres_'.$request->get('form'));
        }
    }
}