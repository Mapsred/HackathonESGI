<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoutineRepository")
 */
class Routine
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
     * @var Profile $profile
     * @ORM\ManyToOne(targetEntity="App\Entity\Profile", inversedBy="routines")
     */
    private $profile;

    /**
     * @var array $tasks
     * @ORM\Column(name="tasks", type="array", nullable=true)
     */
    private $tasks;

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
     * @return Routine
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile|null $profile
     * @return Routine
     */
    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTasks(): ?array
    {
        return $this->tasks;
    }

    /**
     * @param array|null $tasks
     * @return Routine
     */
    public function setTasks(?array $tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }
}
