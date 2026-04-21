<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Salaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois')
            ->add('annee')
            ->add('montant')
            ->add('date_paiement')
            ->add('statut')
            ->add('contract', EntityType::class, [
                'class' => Contract::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salaire::class,
        ]);
    }
}
