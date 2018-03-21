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

    /**
     * @var ArrayCollection|Task[]
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="profile")
     */
    private $tasks;

    /**
     * Profile constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
}
