<?php

namespace App\Form;

use App\Entity\CongeTt;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeTtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_conge')
            ->add('date_debut')
            ->add('date_fin')
            ->add('statut')
            ->add('description')
            ->add('document_path')
            ->add('ocr_verified')
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CongeTt::class,
        ]);
    }
}
