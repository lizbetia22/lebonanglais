<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'picture:item']),
        new Post(denormalizationContext: ['groups' => 'picture:post']),
        new GetCollection(normalizationContext: ['groups' => 'picture:list'])
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: true,
)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['picture:item', 'picture:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['picture:item', 'picture:list'])]
    private ?string $path = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['picture:item', 'picture:list'])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    private ?Advert $advert = null;

    #[Vich\UploadableField(mapping: 'picture', fileNameProperty: 'path')]
    #[Groups(['picture:item', 'picture:list', 'picture:post'])]
    private ?File $imageFile = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): static
    {
        $this->advert = $advert;

        return $this;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
