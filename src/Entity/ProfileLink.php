<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="profile_link")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileLinkRepository")
 */
class ProfileLink
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
     * @var Link $link
     * @ORM\ManyToOne(targetEntity="App\Entity\Link", inversedBy="profileLinks")
     * @ORM\JoinColumn(name="id_link", referencedColumnName="id", nullable=false) 
     */
    private $link;

    /**
     * @var Profile $profile
     * @ORM\ManyToOne(targetEntity="App\Entity\Profile", inversedBy="profileLinks")
     * @ORM\JoinColumn(name="id_profile", referencedColumnName="id", nullable=false) 
     */
    private $profile;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return Link|null
     */
    public function getLink(): ?Link
    {
        return $this->link;
    }

    /**
     * @param Link|null $link
     * @return ProfileLink
     */
    public function setLink(?Link $link): self
    {
        $this->link = $link;

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
     * @return ProfileLink
     */
    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }
}
