<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Evenement;
use App\Entity\EventParticipation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_inscription')
            ->add('statut')
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'id',
            ])
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventParticipation::class,
        ]);
    }
}
