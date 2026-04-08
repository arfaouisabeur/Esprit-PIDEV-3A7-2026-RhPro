<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Rh;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('statut')
            ->add('description')
            ->add('date_debut')
            ->add('date_fin')
            ->add('rh', EntityType::class, [
                'class' => Rh::class,
                'choice_label' => 'id',
            ])
            ->add('responsable_employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
