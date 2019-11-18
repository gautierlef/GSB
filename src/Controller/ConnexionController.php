<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ConnexionType;
use App\Entity\Visiteur;
use App\Controller\ComptableController;
use Symfony\Component\HttpFoundation\Session\Session;

class ConnexionController extends AbstractController
{
    /**
     * @Route("/connexion", name="connexion")
     */
    public function index(Request $query)
    {
        $session = new Session();
        $visiteur = new Visiteur();
        $form = $this->createForm(ConnexionType::class, $visiteur);
        $form->handleRequest($query);
        if ($query->isMethod('POST')) {
            if ($form->isValid()) {
                $visiteurs = $this->getVisiteurs();
                foreach ($visiteurs as $visiteur) {
                    if ($visiteur->getLogin() == $form['login']->getData() && $visiteur->getMdp() == $form['mdp']->getData()) {
                        if ($visiteur->isComptable()) {
                            $this->addFlash('notice','Bienvenue!');
                            $session->set('nom', $visiteur->getNom());
                            $session->set('prenom', $visiteur->getPrenom());
                            $session->set('id', $visiteur->getId());
                            return $this->redirectToRoute('comptable');
                        }
                        $this->addFlash('notice','Bienvenue!');
                        $session->set('nom', $visiteur->getNom());
                        $session->set('prenom', $visiteur->getPrenom());
                        $session->set('id', $visiteur->getId());
                        return $this->redirectToRoute('visiteur');
                    }
                }
                return $this->render('connexion/index.html.twig', array('form' => $form->createView()));
            }
        }
        return $this->render('connexion/index.html.twig', array('form' => $form->createView()));
    }
    
    public function getVisiteurs() {
        $visiteurs = $this->getDoctrine()->getRepository(\App\Entity\Visiteur::class)->findAll();
        return $visiteurs;
    }
    
    /**
     * @Route("/connexion/validation", name="validation")
     */
    public function validation()
    {
        return $this->render('connexion/index.html.twig', [
            'controller_name' => 'ConnexionControllerValider', 'visiteurs' => $this->getVisiteurs()
        ]);
    }
}
