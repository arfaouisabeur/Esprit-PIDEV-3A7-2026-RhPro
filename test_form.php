<?php

use App\Kernel;
use App\Entity\User;
use App\Form\ProfileCandidatType;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

$formFactory = $container->get('form.factory');

$user = new User();
dump("User created");

try {
    $form = $formFactory->create(ProfileCandidatType::class, $user);
    
    // Submit empty data which should fail NotBlank
    $form->submit(['prenom' => '', 'nom' => '', 'email' => '']);
    
    dump("Form is valid? " . ($form->isValid() ? 'YES' : 'NO'));
    
    $errors = $form->getErrors(true, true);
    dump("Errors count: " . count($errors));
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            dump($error->getOrigin()->getName() . " -> " . $error->getMessage());
        }
    }
} catch (\Exception $e) {
    dump("EXCEPTION: " . $e->getMessage());
}
