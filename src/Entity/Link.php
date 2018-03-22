<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="link")
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $url
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var Type $type
     * @ORM\ManyToOne(targetEntity="App\Entity\Type")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var ArrayCollection|ProfileLink[]
     * @ORM\OneToMany(targetEntity="App\Entity\ProfileLink", mappedBy="link")
     */
    private $profileLinks;

    /**
     * Link constructor.
     */
    public function __construct()
    {
        $this->profileLinks = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Link
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Link
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Type|null
     */
    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     * @return Link
     */
    public function setType(Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|ProfileLink[]
     */
    public function getProfileLinks(): Collection
    {
        return $this->profileLinks;
    }

    /**
     * @param ProfileLink $profileLink
     * @return Link
     */
    public function addProfileLink(ProfileLink $profileLink): self
    {
        if (!$this->profileLinks->contains($profileLink)) {
            $this->profileLinks[] = $profileLink;
            $profileLink->setLink($this);
        }

        return $this;
    }

    /**
     * @param ProfileLink $profileLink
     * @return Link
     */
    public function removeProfileLink(ProfileLink $profileLink): self
    {
        if ($this->profileLinks->contains($profileLink)) {
            $this->profileLinks->removeElement($profileLink);
            // set the owning side to null (unless already changed)
            if ($profileLink->getLink() === $this) {
                $profileLink->setLink(null);
            }
        }

        return $this;
    }
}
