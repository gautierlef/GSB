<?php

namespace App\Form;

use App\Entity\Fichefrais;
use App\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ValiderFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mois', TextType::class, array('label' => 'Mois : ', 'attr' => array('class' => 'form-control')))
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
