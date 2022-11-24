<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//? https://symfony.com/doc/current/validation.html
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 * 
 * 
 * @ORM\HasLifecycleCallbacks()
 * 
 */
class Movie
{
     /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"movie_browse"})
     * @Groups({"movie_read"})
     * @Groups({"genre_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\Length(min = 5)
     * @Assert\NotBlank
     * 
     * @Groups({"movie_browse", "another_group"})
     * @Groups({"movie_read"})
     * @Groups({"genre_read"})
     * @Groups({"movie_error_add"})
     */
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * 
     * @Groups({"movie_read"})
     * @Groups({"movie_error_add"})
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poster;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\NotBlank
     * 
     * @Groups({"movie_error_add"})
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="movie", orphanRemoval=true)
     */
    private $seasons;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="movies")
     * 
     * @Groups({"movie_browse"})
     * @Groups({"movie_read"})
     * @Groups({"movie_error_add"})
     */
    private $genres;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * 
     * @Groups({"movie_read"})
     * @Groups({"movie_error_add"})
     */
    private $releaseDate;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie", orphanRemoval=true)
     * @ORM\OrderBy({"creditOrder"="ASC"})
     * 
     * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/reference/annotations-reference.html#orderby
     */
    private $castings;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $synopsis;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Pre : avant
     * Persist : création
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->castings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setMovie($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getMovie() === $this) {
                $season->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, Casting>
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * function spéciale pour l'affichage dans un formulaire
     *
     * @return string au format : title (année release)
     */
    public function titleForm(): string
    {
        return $this->title . ' (' . $this->releaseDate->format('Y') . ')';
    }

    /**
     * @Groups({"movie_browse"})
     */
    public function getTitleForm(): string
    {
        return $this->title . ' (' . $this->releaseDate->format('Y') . ')';
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
