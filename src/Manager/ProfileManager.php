<?php


namespace App\Manager;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ProfileManager
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 * @method ProfileRepository getRepository()
 * @method ProfileManager persistAndFlush(Profile $entity)
 * @method ProfileManager removeEntity(Profile $entity)
 * @method Profile newClass()
 */
class ProfileManager extends BaseManager
{
    /**
     * ProfileManager constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        parent::__construct($manager, Profile::class);
    }

    /**
     * @param string $identifier
     * @return Profile
     */
    public function createAndFlushFromIdentifier(string $identifier)
    {
        $profile = $this->newClass()->setName($identifier);
        $this->persistAndFlush($profile);

        return $profile;
    }
}