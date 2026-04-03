<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-db', name: 'test_db')]
    public function test(Connection $connection): Response
    {
        try {
            $connection->connect();
            return new Response("✅ connection etablie yaa rojlaaaaaaaa 😎");
        } catch (\Exception $e) {
            return new Response("❌ Error: " . $e->getMessage());
        }
    }
}