<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\AdvertRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'advert:item']),
        new Post(denormalizationContext: ['groups' => 'advert:post']),
        new GetCollection(normalizationContext: ['groups' => 'advert:list'])
    ],
    order: ['id' => 'ASC'],
    paginationEnabled: true,
)]
#[ApiFilter(OrderFilter::class, properties: ['publishedAt', 'price'])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(SearchFilter::class, properties: ['category'])]
class Advert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['advert:item', 'advert:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, length: 1200)]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?string $author = null;

    #[ORM\Column(length: 255)]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?Category $category = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 1000000,
    )]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['advert:item', 'advert:list'])]
    private ?string $state = "draft";

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['advert:item', 'advert:list'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['advert:item', 'advert:list'])]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Picture::class,cascade: ['persist'])]
    #[Groups(['advert:item', 'advert:list', 'advert:post'])]
    private Collection $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->createdAt = (new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt = null): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setAdvert($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            if ($picture->getAdvert() === $this) {
                $picture->setAdvert(null);
            }
        }

        return $this;
    }
}
