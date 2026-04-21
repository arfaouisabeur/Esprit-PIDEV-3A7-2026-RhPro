<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Messenger_messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private string $body;

    #[ORM\Column(nullable: false)]
    private string $headers;

    #[ORM\Column(nullable: false)]
    private string $queue_name;

    #[ORM\Column(nullable: false)]
    private string $created_at;

    #[ORM\Column(nullable: false)]
    private string $available_at;

    #[ORM\Column(nullable: true)]
    private ?string $delivered_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function setHeaders(string $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function getQueueName(): ?string
    {
        return $this->queue_name;
    }

    public function setQueueName(string $queue_name): static
    {
        $this->queue_name = $queue_name;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAvailableAt(): ?string
    {
        return $this->available_at;
    }

    public function setAvailableAt(string $available_at): static
    {
        $this->available_at = $available_at;

        return $this;
    }

    public function getDeliveredAt(): ?string
    {
        return $this->delivered_at;
    }

    public function setDeliveredAt(?string $delivered_at): static
    {
        $this->delivered_at = $delivered_at;

        return $this;
    }
}