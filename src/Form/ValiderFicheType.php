<?php

namespace App\Form;

use App\Entity\Fichefrais;
use App\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ValiderFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mois', ChoiceType::class, array('choices'  => ['Janvier' => 'Janvier', 'Février' => 'Février', 'Mars' => 'Mars', 'Avril' => 'Avril', 'Mai' => 'Mai', 'Juin' => 'Juin', 'Juillet' => 'Juillet', 'Août' => 'Août', 'Septembre' => 'Septembre', 'Octobre' => 'Octobre', 'Novembre' => 'Novembre', 'Décembre' => 'Décembre'], 'label' => 'Mois : '))
            ->add('idVisiteur', EntityType::class, array('class' => Visiteur::class, 'choice_label' => 'id', 'label' => 'Visiteur : ', 'attr' => array('class' => 'form-control')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fichefrais::class,
        ]);
    }
}
