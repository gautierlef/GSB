<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use App\Form\RenseignerType;
use App\Form\LigneFraisForfaitType;
use App\Form\LigneFraisHorsForfaitType;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\FraisForfait;


class VisiteurController extends AbstractController
{
    /**
     * @Route("/visiteur", name="visiteur")
     */
    public function index()
    {
        return $this->render('visiteur/index.html.twig');
    }
    
    /**
     * @Route("/consulter", name="consulter")
     */
    public function consulter(SessionInterface $session)
    {
        $fichefrais = $this->getDoctrine()->getManager()->getRepository(\App\Entity\FicheFrais::class)->findBy(['idVisiteur' => $session->get('id')]);;
        return $this->render('visiteur/consulter.html.twig',array('fichefrais'=>$fichefrais));
    }
    
    /**
     * @Route("/renseigner", name="renseigner")
     */
    function creerFicheFrais(Request $query, SessionInterface $session) {
        $renseigner = new Fichefrais();
        $form = $this->createForm(RenseignerType::class, $renseigner);
        $form->handleRequest($query);
        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $lignesFraisForfait = array();
        $form2 = $this->createForm(LigneFraisHorsForfaitType::class, $ligneFraisHorsForfait, array('id' => $session->get('id')));
        $fiches = $this->getFiches();
        $form2->handleRequest($query);
        $form3 = $this->createForm(LigneFraisForfaitType::class, $lignesFraisForfait, array('id' => $session->get('id')));
        $form3->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $visiteurs = $this->getVisiteurs();
                    foreach ($visiteurs as $visiteur) {
                        if ($session->get('id') == $visiteur->getId()) {
                            $renseigner->setIdVisiteur($visiteur);   
                        }
                    }
                    $fiches = $this->getFiches();
                    foreach ($fiches as $fiche) {
                        if ($fiche->getIdVisiteur()->getId() == $session->get('id') && $renseigner->getMois() == $fiche->getMois()) {
                            return $this->render('visiteur/renseigner.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView(), 'form3' => $form3->createView(), 'error' => 1));
                        }
                    }
                    $etats = $this->getEtat();
                    foreach ($etats as $etat) {
                        if ($etat->getId() == 'CR') {
                            $renseigner->setIdEtat($etat);
                        }
                    }
                    $renseigner->setMontantValide(0);
                    $renseigner->setDateModif(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($renseigner);
                    $em->flush();
                    $this->creerLigneFrais($renseigner->getId());
                    return $this->redirectToRoute('renseigner', array('id' => $renseigner->getIdvisiteur()));
                }
            }
            if ($form3->isSubmitted()) {
                if ($form3->isValid()) {
                    $fiches = $this->getFiches();
                    foreach ($fiches as $fiche) {
                        if ($fiche->getIdVisiteur()->getId() == $session->get('id')) {
                            $ficheFrais = $fiche;
                        }
                    }
                    $lignesFraisForfait = $this->getLignesFraisForfait($ficheFrais->getMois(), $ficheFrais->getIdVisiteur()->getId());
                    $em = $this->getDoctrine()->getManager();
                    foreach($lignesFraisForfait as $ligne) {
                        if ($ligne->getIdFraisForfait()->getId() == 'REP') {
                            $ligne->setQuantite($form3['quantiteRepas']->getData());
                            $em->persist($ligne);
                        }
                    }
                    foreach($lignesFraisForfait as $ligne) {
                        if ($ligne->getIdFraisForfait()->getId() == 'NUI') {
                            $ligne->setQuantite($form3['quantiteNuitee']->getData());
                            $em->persist($ligne);
                        }
                    }
                    foreach($lignesFraisForfait as $ligne) {
                        if ($ligne->getIdFraisForfait()->getId() == 'KM') {
                            $ligne->setQuantite($form3['quantiteKilometres']->getData());
                            $em->persist($ligne);
                        }
                    }
                    foreach($lignesFraisForfait as $ligne) {
                        if ($ligne->getIdFraisForfait()->getId() == 'ETP') {
                            $ligne->setQuantite($form3['quantiteEtape']->getData());
                            $em->persist($ligne);
                        }
                    }
                    $em->flush();
                    return $this->redirectToRoute('renseigner', array('id' => $ficheFrais->getIdVisiteur()->getId()));
                }     
            }
            if ($form2->isSubmitted()) {
                if ($form2->isValid()) {
                    $fiches = $this->getFiches();
                    foreach ($fiches as $fiche) {
                        if ($fiche->getIdVisiteur()->getId() == $session->get('id')) {
                            $ligneFraisHorsForfait->setIdVisiteur($fiche->getIdVisiteur());
                        }
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ligneFraisHorsForfait);
                    $em->flush();
                    return $this->redirectToRoute('renseigner', array('id' => $ligneFraisHorsForfait->getIdvisiteur()));
                }     
            }
        }
        return $this->render('visiteur/renseigner.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView(), 'form3' => $form3->createView(), 'error' => 0));
    }
    
    public function creerLigneFrais(String $idFicheFrais) {
        $ligneFraisRep = new LigneFraisForfait();
        $ligneFraisKm = new LigneFraisForfait();
        $ligneFraisNui = new LigneFraisForfait();
        $ligneFraisEtp = new LigneFraisForfait();
        
        $fiches = $this->getFiches();
        foreach ($fiches as $fiche) {
            if ($fiche->getId() == $idFicheFrais) {
                $ficheFrais = $fiche;
            }
        }
        $ligneFraisRep->setMois($ficheFrais);
        $ligneFraisKm->setMois($ficheFrais);
        $ligneFraisNui->setMois($ficheFrais);
        $ligneFraisEtp->setMois($ficheFrais);
        $ligneFraisRep->setIdVisiteur($ficheFrais->getIdVisiteur());
        $ligneFraisKm->setIdVisiteur($ficheFrais->getIdVisiteur());
        $ligneFraisNui->setIdVisiteur($ficheFrais->getIdVisiteur());
        $ligneFraisEtp->setIdVisiteur($ficheFrais->getIdVisiteur());
        $ligneFraisRep->setQuantite(0);
        $ligneFraisKm->setQuantite(0);
        $ligneFraisNui->setQuantite(0);
        $ligneFraisEtp->setQuantite(0);
        $forfaits = $this->getForfait();
        foreach ($forfaits as $forfait) {
            if ($forfait->getId() == 'REP') {
                $ligneFraisRep->setIdFraisForfait($forfait);
            }
        }
        foreach ($forfaits as $forfait) {
            if ($forfait->getId() == 'KM') {
                $ligneFraisKm->setIdFraisForfait($forfait);
            }
        }
        foreach ($forfaits as $forfait) {
            if ($forfait->getId() == 'NUI') {
                $ligneFraisNui->setIdFraisForfait($forfait);
            }
        }
        foreach ($forfaits as $forfait) {
            if ($forfait->getId() == 'ETP') {
                $ligneFraisEtp->setIdFraisForfait($forfait);
            }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($ligneFraisRep);
        $em->persist($ligneFraisKm);
        $em->persist($ligneFraisNui);
        $em->persist($ligneFraisEtp);
        $em->flush();
    }
    
    public function getFiches() {
        $fiches = $this->getDoctrine()->getRepository(\App\Entity\FicheFrais::class)->findAll();
        return $fiches;
    }
    
    public function getLignesFraisForfait(string $mois, string $idVisiteur) {
        $lignesUtilisateur = array();
        $lignes = $this->getDoctrine()->getRepository(\App\Entity\LigneFraisForfait::class)->findAll();
        foreach ($lignes as $ligne) {
            if ($idVisiteur == $ligne->getIdVisiteur()->getId() && $mois == $ligne->getMois()->getMois()) {
                array_push($lignesUtilisateur, $ligne);
            }
        }
        return $lignesUtilisateur;
    }
    
    public function getForfait() {
        $forfaits = $this->getDoctrine()->getRepository(\App\Entity\FraisForfait::class)->findAll();
        return $forfaits;
    }
    
    public function getEtat() {
        $etats = $this->getDoctrine()->getRepository(\App\Entity\Etat::class)->findAll();
        return $etats;
    }
    
    public function getVisiteurs() {
        $visiteurs = $this->getDoctrine()->getRepository(\App\Entity\Visiteur::class)->findAll();
        return $visiteurs;
    }
}
