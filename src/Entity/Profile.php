<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var ArrayCollection|Task[]
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="profile")
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Link", inversedBy="profiles")
     * @ORM\JoinTable(name="profilelink",
     *   joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="link_id", referencedColumnName="id")}
     * )
     */
    private $links;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Routine", mappedBy="profile")
     */
    private $routines;

    /**
     * Profile constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->routines = new ArrayCollection();
    }

    /**
     * @return int
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
     * @return Profile
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return Profile
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProfile($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return Profile
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProfile() === $this) {
                $task->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Link[]
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): self
    {
        if (!$this->links->contains($link)) {
            $this->links[] = $link;
        }

        return $this;
    }

    public function removeLink(Link $link): self
    {
        if ($this->links->contains($link)) {
            $this->links->removeElement($link);
        }

        return $this;
    }

    /**
     * @return Collection|Routine[]
     */
    public function getRoutines(): Collection
    {
        return $this->routines;
    }

    public function addRoutine(Routine $routine): self
    {
        if (!$this->routines->contains($routine)) {
            $this->routines[] = $routine;
            $routine->setProfile($this);
        }

        return $this;
    }

    public function removeRoutine(Routine $routine): self
    {
        if ($this->routines->contains($routine)) {
            $this->routines->removeElement($routine);
            // set the owning side to null (unless already changed)
            if ($routine->getProfile() === $this) {
                $routine->setProfile(null);
            }
        }

        return $this;
    }
}
