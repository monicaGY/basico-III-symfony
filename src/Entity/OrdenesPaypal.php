<?php

namespace App\Entity;

use App\Repository\OrdenesPaypalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdenesPaypalRepository::class)]
class OrdenesPaypal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $token = null;

    #[ORM\Column(length: 1000)]
    private ?string $orden = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $monto = null;

    #[ORM\Column(nullable: true)]
    private ?int $country_code = null;

    #[ORM\Column]
    private ?int $paypal_request = null;

    #[ORM\Column]
    private ?int $estado = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $idCaptura = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getOrden(): ?string
    {
        return $this->orden;
    }

    public function setOrden(string $orden): static
    {
        $this->orden = $orden;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getMonto(): ?int
    {
        return $this->monto;
    }

    public function setMonto(int $monto): static
    {
        $this->monto = $monto;

        return $this;
    }

    public function getCountryCode(): ?int
    {
        return $this->country_code;
    }

    public function setCountryCode(?int $country_code): static
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function getPaypalRequest(): ?int
    {
        return $this->paypal_request;
    }

    public function setPaypalRequest(int $paypal_request): static
    {
        $this->paypal_request = $paypal_request;

        return $this;
    }

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getIdCaptura(): ?string
    {
        return $this->idCaptura;
    }

    public function setIdCaptura(?string $idCaptura): static
    {
        $this->idCaptura = $idCaptura;

        return $this;
    }
}
