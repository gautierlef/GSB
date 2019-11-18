<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use App\Form\RenseignerType;
use App\Form\LigneFraisHorsForfaitType;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Service\Test;

class VisiteurController extends AbstractController
{
    /**
     * @Route("/visiteur", name="visiteur")
     */
    public function index()
    {
        return $this->render('visiteur/index.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
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
        $form2 = $this->createForm(LigneFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $form2->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($renseigner);
                    $em->flush();
                    return $this->redirectToRoute('renseigner', array('id' => $renseigner->getIdvisiteur()));
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
        return $this->render('visiteur/renseigner.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView()));
    }
    
    public function getFiches() {
        $fiches = $this->getDoctrine()->getRepository(\App\Entity\FicheFrais::class)->findAll();
        return $fiches;
    }
}
