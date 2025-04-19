<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository spécifique et des annotations ORM
use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de la classe en tant qu'entité Doctrine avec son repository associé
#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    // Identifiant unique de l'attachement (clé primaire auto-générée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom du fichier enregistré sur le serveur (nom sécurisé/unique)
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    // Nom original du fichier tel qu'il a été uploadé par l'utilisateur
    #[ORM\Column(length: 255)]
    private ?string $originalFilename = null;

    // Type MIME du fichier (ex: image/png, application/pdf)
    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    // Taille du fichier en octets
    #[ORM\Column]
    private ?int $size = null;

    // Association à l'entité Post (chaque attachement appartient à un post)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le nom du fichier
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    // Setter pour le nom du fichier
    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    // Getter pour le nom original du fichier
    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    // Setter pour le nom original du fichier
    public function setOriginalFilename(string $originalFilename): static
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    // Getter pour le type MIME
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    // Setter pour le type MIME
    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    // Getter pour la taille du fichier
    public function getSize(): ?int
    {
        return $this->size;
    }

    // Setter pour la taille du fichier
    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    // Getter pour l'objet Post associé
    public function getPost(): ?Post
    {
        return $this->post;
    }

    // Setter pour l'objet Post associé
    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}
