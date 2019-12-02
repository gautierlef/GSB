<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChoisirMoisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder  
            ->add('mois', ChoiceType::class, ['choices' => ['Janvier' => 'Janvier', 'Février' => 'Février','Mars' => 'Mars', 'Avril' => 'Avril','Mai' => 'Mai', 'Juin' => 'Juin', 'Juillet' => 'Juillet', 'Août' => 'Août','Septembre' => 'Septembre', 'Octobre' => 'Octobre','Novembre' => 'Novembre', 'Décembre' => 'Décembre']])
            ->add('valider',SubmitType::class, array('label' => 'Valider'))
            ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FicheFrais::class,
        ]);
    }

}
