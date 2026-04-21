<?php

namespace App\Form;

use App\Entity\DemandeService;
use App\Entity\Employe;
use App\Entity\TypeService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_demande')
            ->add('statut')
            ->add('etape_workflow')
            ->add('date_derniere_etape')
            ->add('priorite')
            ->add('deadline_reponse')
            ->add('sla_depasse')
            ->add('pdf_path')
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
            ])
            ->add('type', EntityType::class, [
                'class' => TypeService::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeService::class,
        ]);
    }
}
