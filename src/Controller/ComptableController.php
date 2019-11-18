<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ValiderFicheType;
use App\Form\ModifierQuantiteRepasType;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;

class ComptableController extends AbstractController
{
    /**
     * @Route("/comptable", name="comptable")
     */
    public function index(Request $query)
    {
        return $this->render('comptable/index.html.twig');
    }
    
    /**
     * @Route("/comptable_suivi", name="suivi")
     */
    public function suivi(Request $query)
    {
        return $this->render('comptable/suivi.html.twig');
    }
    
    /**
     * @Route("/comptable_valider", name="valider")
     */
    public function valider(Request $query, SessionInterface $session)
    {
        $ficheAValider = new FicheFrais();
        $form = $this->createForm(ValiderFicheType::class, $ficheAValider);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $fiches = $this->getFiches();
                foreach ($fiches as $fiche) {
                    if ($fiche->getMois() == $form['mois']->getData() && $fiche->getIdvisiteur() == $form['idVisiteur']->getData()) {
                        $session->set('ficheMois', $fiche->getMois());
                        $session->set('ficheIdVisiteur', $fiche->getIdVisiteur()->getId());
                        $session->set('ficheId', $fiche->getId());
                        return $this->redirectToRoute('comptable_validation');
                    }
                }
                return $this->render('comptable/valider.html.twig', array('form' => $form->createView(), 'error' => 1));
            }
        }
        return $this->render('comptable/valider.html.twig', array('form' => $form->createView(), 'error' => 0));
    }
    
    /**
     * @Route("/comptable_validation", name="comptable_validation")
     */
    public function validation(Request $query, SessionInterface $session)
    {
        $ficheId = $session->get('ficheId');
        $ficheMois = $session->get('ficheMois');
        $ficheIdVisiteur = $session->get('ficheIdVisiteur');
        $fiches = $this->getFiches();
        foreach ($fiches as $fiche) {
            if ($fiche->getId() == $ficheId) {
                $lignesFraisForfait = $this->getLignesFraisForfait($ficheMois, $ficheIdVisiteur);
                $lignesFraisHorsForfait = $this->getLignesFraisHorsForfait($ficheMois, $ficheIdVisiteur);
                $modifierLigneRepas = $this->getForfaitRepas($lignesFraisForfait);
                $form1 = $this->createForm(ModifierQuantiteRepasType::class, $modifierLigneRepas);
                $form1->handleRequest($query);
                $modifierLigneNuitee = $this->getForfaitNuitee($lignesFraisForfait);
                $form2 = $this->createForm(ModifierQuantiteRepasType::class, $modifierLigneNuitee);
#                $form2->handleRequest($query);
                $modifierLigneKilometres = $this->getForfaitKilometres($lignesFraisForfait);
                $form3 = $this->createForm(ModifierQuantiteRepasType::class, $modifierLigneKilometres);
#                $form3->handleRequest($query);
                $modifierLigneEtape = $this->getForfaitEtape($lignesFraisForfait);
                $form4 = $this->createForm(ModifierQuantiteRepasType::class, $modifierLigneEtape);
#                $form4->handleRequest($query);
                $lignesFraisForfait = array($modifierLigneRepas, $modifierLigneNuitee, $modifierLigneKilometres, $modifierLigneEtape);
                if ($query->isMethod('POST')) {
                    $em = $this->getDoctrine()->getManager();
                    if ($form1->isSubmitted() && $form1->isValid() && $form1->getData()) {
                        $em->persist($modifierLigneRepas);
                    }
                    if ($form2->isSubmitted() && $form2->isValid()) {
                        $em->persist($modifierLigneNuitee);
                    }
                    if ($form3->isSubmitted() && $form3->isValid()) {
                        $em->persist($modifierLigneKilometres);
                    }
                    if ($form4->isSubmitted() && $form4->isValid()) {
                        $em->persist($modifierLigneEtape);
                    }
                    return $this->render('comptable/validation.html.twig', array('fiche' => $fiche, 'lignesFraisHorsForfait' => $lignesFraisHorsForfait, 'lignesFraisForfait' => $lignesFraisForfait, 'form1' => $form1->createView(), 'form2' => $form2->createView(), 'form3' => $form3->createView(), 'form4' => $form4->createView()));
                }
                return $this->render('comptable/validation.html.twig', array('fiche' => $fiche, 'lignesFraisHorsForfait' => $lignesFraisHorsForfait, 'lignesFraisForfait' => $lignesFraisForfait, 'form1' => $form1->createView(), 'form2' => $form2->createView(), 'form3' => $form3->createView(), 'form4' => $form4->createView()));
            }
        }
    }
    
    /**
     * @Route("/supprimerHorsForfait/{id}", name="supprimerHorsForfait")
     */
    public function supprimerHorsForfait(Request $query, SessionInterface $session, $id) {
        $ficheMois = $session->get('ficheMois');
        $ficheIdVisiteur = $session->get('ficheIdVisiteur');
        $entityManager = $this->getDoctrine()->getManager();
        $lignesFraisHorsForfait = $this->getLignesFraisHorsForfait($ficheMois, $ficheIdVisiteur);
        foreach ($lignesFraisHorsForfait as $ligneFraisHorsForfait) {
            if ($ligneFraisHorsForfait->getId() == $id) {
                $entityManager->remove($ligneFraisHorsForfait);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('comptable_validation');
    }
    
    public function getFiches() {
        $fiches = $this->getDoctrine()->getRepository(\App\Entity\FicheFrais::class)->findAll();
        return $fiches;
    }
    
    public function getLignesFraisHorsForfait(string $mois, string $idVisiteur) {
        $lignesUtilisateur = array();
        $lignes = $this->getDoctrine()->getRepository(\App\Entity\LigneFraisHorsForfait::class)->findAll();
        foreach ($lignes as $ligne) {
            if ($idVisiteur == $ligne->getIdvisiteur()->getId() && $mois == $ligne->getMois()->getMois()) {
                array_push($lignesUtilisateur, $ligne);
            }
        }
        return $lignesUtilisateur;
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
    
    public function getForfaitRepas(array $lignes) {
        foreach($lignes as $ligne) {
            if ($ligne->getIdFraisForfait()->getId() == 'REP') {
                return $ligne;
            }
        }
    }
    
    public function getForfaitNuitee(array $lignes) {
        foreach($lignes as $ligne) {
            if ($ligne->getIdFraisForfait()->getId() == 'NUI') {
                return $ligne;
            }
        }
    }
    
    public function getForfaitEtape(array $lignes) {
        foreach($lignes as $ligne) {
            if ($ligne->getIdFraisForfait()->getId() == 'ETP') {
                return $ligne;
            }
        }
    }
    
    public function getForfaitKilometres(array $lignes) {
        foreach($lignes as $ligne) {
            if ($ligne->getIdFraisForfait()->getId() == 'KM') {
                return $ligne;
            }
        }
    }
}

