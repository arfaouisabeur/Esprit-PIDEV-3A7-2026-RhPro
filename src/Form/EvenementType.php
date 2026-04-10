<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label'       => "Titre de l'événement",
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                    new Length(
                        min: 3, max: 150,
                        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le titre ne peut dépasser {{ limit }} caractères.'
                    ),
                ],
                'attr' => ['placeholder' => 'Ex : Team Building 2025'],
            ])
            ->add('date_debut', TextType::class, [
                'label'      => 'Date de début',
                'required'   => false,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'La date de début est obligatoire.'),
                    new Regex(
                        pattern: '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/',
                        message: 'Format invalide : utilisez AAAA-MM-JJ (ex: 2025-06-15).'
                    ),
                ],
                'attr' => ['placeholder' => 'AAAA-MM-JJ', 'autocomplete' => 'off'],
            ])
            ->add('date_fin', TextType::class, [
                'label'      => 'Date de fin',
                'required'   => false,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'La date de fin est obligatoire.'),
                    new Regex(
                        pattern: '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/',
                        message: 'Format invalide : utilisez AAAA-MM-JJ (ex: 2025-06-20).'
                    ),
                ],
                'attr' => ['placeholder' => 'AAAA-MM-JJ', 'autocomplete' => 'off'],
            ])
            ->add('lieu', TextType::class, [
                'label'       => 'Lieu',
                'constraints' => [
                    new NotBlank(message: 'Le lieu est obligatoire.'),
                    new Length(
                        min: 2, max: 200,
                        minMessage: 'Le lieu doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le lieu ne peut dépasser {{ limit }} caractères.'
                    ),
                ],
                'attr' => ['placeholder' => 'Ex : Salle de conférence A'],
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'Description',
                'required'    => false,
                'constraints' => [
                    new Length(
                        max: 1000,
                        maxMessage: 'La description ne peut dépasser {{ limit }} caractères.'
                    ),
                ],
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez cet événement...'],
            ])
            ->add('imageUrl', TextType::class, [
                'label'       => "URL de l'image (optionnel)",
                'required'    => false,
                'constraints' => [
                    new Url(
                        message: "L'URL de l'image n'est pas valide (doit commencer par http:// ou https://).",
                        requireTld: true,
                    ),
                ],
                'attr' => ['placeholder' => 'https://exemple.com/image.jpg'],
            ])
        ;

        // ─── Validation inter-champs: date_fin >= date_debut ───────────────
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            $debut = $form->get('date_debut')->getData();
            $fin   = $form->get('date_fin')->getData();

            if ($debut && $fin
                && preg_match('/^\d{4}-\d{2}-\d{2}$/', $debut)
                && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)
                && $fin < $debut
            ) {
                $form->get('date_fin')->addError(
                    new FormError('La date de fin doit être égale ou postérieure à la date de début.')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Evenement::class]);
    }
}