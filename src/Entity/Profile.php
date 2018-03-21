<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineCommonCollectionsArrayCollection;

/**
 * @ORM\Table(name="profile")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
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
     * @var ArrayCollection Link $links
     * Owning Side
     *
     * @ORM\ManyToMany(targetEntity="Link", inversedBy="profiles", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="ProfileLink",
     *   joinColumns={@ORM\JoinColumn(name="Profile_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="Link_id", referencedColumnName="id")}
     * )
     */
    private $links;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
