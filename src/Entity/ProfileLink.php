<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileLinkRepository")
 */
class ProfileLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Profile", inversedBy="profileLink")
    * @ORM\JoinColumn(name="id_profile", referencedColumnName="id", nullable=false) 
    */
    private $profile;

    /**
    * @ORM\ManyToOne(targetEntity="Link", inversedBy="profileLink")
    * @ORM\JoinColumn(name="id_link", referencedColumnName="id", nullable=false) 
    */
    private $link;

    public function getId()
    {
        return $this->id;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile($profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link): self
    {
        $this->link = $link;

        return $this;
    }
}
