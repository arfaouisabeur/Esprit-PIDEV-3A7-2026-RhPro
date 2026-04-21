<?php

namespace App\Command;

use App\Entity\User;
use App\Form\ProfileCandidatType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormFactoryInterface;

#[AsCommand(name: 'app:test-form')]
class TestFormCommand extends Command
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        parent::__construct();
        $this->formFactory = $formFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $form = $this->formFactory->create(ProfileCandidatType::class, $user);

        // Submit empty values to trigger NotBlank
        $form->submit(['prenom' => '', 'nom' => '', 'email' => '']);

        $output->writeln("Valid? " . ($form->isValid() ? 'TRUE' : 'FALSE'));
        
        $errors = $form->getErrors(true, true);
        $output->writeln("Errors count: " . count($errors));
        foreach ($errors as $error) {
            $output->writeln($error->getOrigin()->getName() . ": " . $error->getMessage());
        }

        return Command::SUCCESS;
    }
}
