<?php

namespace App\Form;

use App\Entity\CongeTt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeTtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeConge', ChoiceType::class, [
                'label'       => false,
                'placeholder' => 'Choisir un type...',
                'choices'     => [
                    'Congé annuel'       => 'Congé annuel',
                    'Congé maladie'      => 'Congé maladie',
                    'Congé maternité'    => 'Congé maternité',
                    'Congé paternité'    => 'Congé paternité',
                    'Congé professionnel'=> 'Congé professionnel',
                    'Congé exceptionnel' => 'Congé exceptionnel',
                    'Congé sans solde'   => 'Congé sans solde',
                    'RTT'                => 'RTT',
                ],
                'attr' => ['class' => 'cg-select'],
            ])
            ->add('dateDebut', DateType::class, [
                'label'  => false,
                'widget' => 'single_text',
                'html5'  => true,
                'attr'   => ['class' => 'cg-date'],
            ])
            ->add('dateFin', DateType::class, [
                'label'  => false,
                'widget' => 'single_text',
                'html5'  => true,
                'attr'   => ['class' => 'cg-date'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'class'       => 'cg-textarea',
                    'placeholder' => 'Décrivez votre demande, ou laissez l\'IA générer...',
                    'rows'        => 4,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CongeTt::class]);
    }
}