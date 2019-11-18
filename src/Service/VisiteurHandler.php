<?php

namespace App\Service; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VisiteurHandler
 *
 * @author etudiant
 */
class VisiteurHandler extends AbstractController {
    //put your code here
    public function getFiches() {
        $fiches = $this->getDoctrine()->getRepository(\App\Entity\FicheFrais::class)->findAll();
        return $fiches;
    }
}

