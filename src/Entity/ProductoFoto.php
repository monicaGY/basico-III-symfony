<?php

namespace App\Entity;

use App\Repository\ProductoFotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoFotoRepository::class)]
class ProductoFoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100), Assert\File(
        maxSize:"10M",
        mimeTypes: [
            "img/jpeg",
            "img/jpg",
            "img/png",
            "img/gif",
        ],
        mimeTypesMessage: "La foto debe ser PNG | JPG | PNEG | GIF",
        maxSizeMessage: "La foto no puede pesar más de 10 megabytes"
    )]
    private ?string $foto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): static
    {
        $this->producto = $producto;

        return $this;
    }
}
