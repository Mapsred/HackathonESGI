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
     * @var ArrayCollection|Routine[]
     * @ORM\OneToMany(targetEntity="App\Entity\Routine", mappedBy="profile")
     */
    private $routines;

    /**
     * @var ArrayCollection|ProfileLink
     * @ORM\OneToMany(targetEntity="App\Entity\ProfileLink", mappedBy="profile")
     */
    private $profileLinks;

    /**
     * Profile constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->routines = new ArrayCollection();
        $this->profileLinks = new ArrayCollection();
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
     * @return ArrayCollection|Routine[]
     */
    public function getRoutines(): Collection
    {
        return $this->routines;
    }

    /**
     * @param Routine $routine
     * @return Profile
     */
    public function addRoutine(Routine $routine): self
    {
        if (!$this->routines->contains($routine)) {
            $this->routines[] = $routine;
            $routine->setProfile($this);
        }

        return $this;
    }

    /**
     * @param Routine $routine
     * @return Profile
     */
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

    /**
     * @return ArrayCollection|ProfileLink[]
     */
    public function getProfileLinks(): Collection
    {
        return $this->profileLinks;
    }

    /**
     * @param ProfileLink $profileLink
     * @return Profile
     */
    public function addProfileLink(ProfileLink $profileLink): self
    {
        if (!$this->profileLinks->contains($profileLink)) {
            $this->profileLinks[] = $profileLink;
            $profileLink->setProfile($this);
        }

        return $this;
    }

    /**
     * @param ProfileLink $profileLink
     * @return Profile
     */
    public function removeProfileLink(ProfileLink $profileLink): self
    {
        if ($this->profileLinks->contains($profileLink)) {
            $this->profileLinks->removeElement($profileLink);
            // set the owning side to null (unless already changed)
            if ($profileLink->getProfile() === $this) {
                $profileLink->setProfile(null);
            }
        }

        return $this;
    }
}
