<?php

namespace App\Form;

use App\Entity\Candidat;
use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = $options['is_admin'];

        if ($isAdmin) {
            $builder
                ->add('dateCandidature', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Date de candidature',
                    'required' => true,
                ])
                ->add('statut', ChoiceType::class, [
                    'label' => 'Statut',
                    'choices' => [
                        'En attente' => 'en_attente',
                        'Entretien' => 'entretien',
                        'Acceptée' => 'acceptee',
                        'Refusée' => 'refusee',
                    ],
                    'placeholder' => 'Choisir un statut',
                    'required' => true,
                ])
                ->add('candidat', EntityType::class, [
                    'class' => Candidat::class,
                    'label' => 'Candidat',
                    'choice_label' => function (Candidat $candidat) {
                        return $candidat->getUser()
                            ? $candidat->getUser()->getNom() . ' ' . $candidat->getUser()->getPrenom()
                            : 'Candidat #' . $candidat->getUserId();
                    },
                    'choice_value' => function (?Candidat $candidat) {
                        return $candidat?->getUserId();
                    },
                    'placeholder' => 'Choisir un candidat',
                    'required' => true,
                ])
                ->add('offreEmploi', EntityType::class, [
                    'class' => OffreEmploi::class,
                    'choice_label' => 'titre',
                    'label' => 'Offre d’emploi',
                    'placeholder' => 'Choisir une offre',
                    'required' => true,
                ]);
        }

        $constraints = [
            new File(
                maxSize: '5M',
                mimeTypes: [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ],
                mimeTypesMessage: 'Veuillez télécharger un fichier valide (PDF, DOC, DOCX)',
            ),
        ];

        if (!$isAdmin) {
            array_unshift(
                $constraints,
                new NotNull(
                    message: 'Veuillez ajouter votre CV.',
                )
            );
        }

        $builder->add('cvFile', FileType::class, [
            'label' => 'CV (PDF)',
            'mapped' => false,
            'required' => !$isAdmin,
            'constraints' => $constraints,
            'attr' => [
                'accept' => '.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
            'is_admin' => false,
        ]);

        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}
