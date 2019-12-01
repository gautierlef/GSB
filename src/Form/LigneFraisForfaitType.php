<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LigneFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('quantiteRepas', NumberType::class, array('label' => "Quantité repas : ", "empty_data" => 0))
            ->add('quantiteNuitee', NumberType::class, array('label' => "Quantité nuitée : ", "empty_data" => 0))
            ->add('quantiteKilometres', NumberType::class, array('label' => "Quantité kilomètres : ", "empty_data" => 0))
            ->add('quantiteEtape', NumberType::class, array('label' => "Quantité étape : ", "empty_data" => 0))
            ->add('mois', EntityType::class, array('class' => FicheFrais::class, 'query_builder' => function(EntityRepository $er) use ($id) { return $er->createQueryBuilder('u')->andWhere("u.idVisiteur = '".$id."'"); }, 'choice_label' => 'mois', 'label' => 'Mois : '))
            ->add('valider',SubmitType::class, array('label' => 'Valider', 'attr' =>array('class' => 'btn btn-success')))
            ->add('annuler', ResetType::class, array('label' => 'Annuler', 'attr' =>array('class' => 'btn btn-danger')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'id' => null
        ]);
    }
}
