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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var Type $type
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var ArrayCollection|Profile[] $profiles
     * @ORM\ManyToMany(targetEntity="App\Entity\Profile", mappedBy="links")
     */
    private $profiles;

    /**
     * Link constructor.
     */
    public function __construct()
    {
        $this->profiles = new ArrayCollection();
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
     * @return ArrayCollection|Profile[]
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    /**
     * @param Profile $profile
     * @return Link
     */
    public function addProfile(Profile $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
            $profile->addLink($this);
        }

        return $this;
    }

    /**
     * @param Profile $profile
     * @return Link
     */
    public function removeProfile(Profile $profile): self
    {
        if ($this->profiles->contains($profile)) {
            $this->profiles->removeElement($profile);
            $profile->removeLink($this);
        }

        return $this;
    }
}
