<?php

namespace App\Form;

use App\Entity\FicheFrais;
use App\Entity\Etat;
use App\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class RenseignerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mois', ChoiceType::class, array('choices'  => ['Janvier' => 'Janvier', 'Février' => 'Février', 'Mars' => 'Mars', 'Avril' => 'Avril', 'Mai' => 'Mai', 'Juin' => 'Juin', 'Juillet' => 'Juillet', 'Août' => 'Août', 'Septembre' => 'Septembre', 'Octobre' => 'Octobre', 'Novembre' => 'Novembre', 'Décembre' => 'Décembre'], 'label' => 'Mois : '))
            ->add('nbjustificatifs', NumberType::class, array('label' => 'Nombre de justificatifs : '))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success')))
            ->add('annuler', ResetType::class, array('label' => 'Quitter', 'attr' =>array('class' => 'btn btn-warning')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FicheFrais::class,
        ]);
    }
}
