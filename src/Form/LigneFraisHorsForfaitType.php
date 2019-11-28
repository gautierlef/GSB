<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityRepository;
use App\Entity\LigneFraisHorsForfait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LigneFraisHorsForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $id = $options['id'];
        $builder
            ->add('libelle', TextType::class, array('label' => 'Libellé : '))
            ->add('date', DateType::class, array('label' => 'Date : '))
            ->add('montant', NumberType::class, array('label' => 'Montant : '))
            ->add('mois', EntityType::class, array('class' => FicheFrais::class, 'query_builder' => function(EntityRepository $er) use ($id) { return $er->createQueryBuilder('u')->andWhere("u.idVisiteur = '".$id."'"); }, 'choice_label' => 'mois', 'label' => 'Mois : '))
            ->add('valider', SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success')))
            ->add('annuler', ResetType::class, array('label' => 'Annuler', 'attr' =>array('class' => 'btn btn-danger')))
        ;
    }

//    public function getFicheMois(EntityRepository $er, $id) {
//        return $er->createQueryBuilder('u')->andWhere("u.idVisiteur = '" + $id + "'");
//    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LigneFraisHorsForfait::class,
            'id' => null
        ]);
    }
}
