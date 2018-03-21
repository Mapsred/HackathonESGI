<?php


namespace App\Manager;

use App\Entity\Profile;
use App\Entity\Routine;
use App\Repository\RoutineRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RoutineManager
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 * @method RoutineRepository getRepository()
 * @method RoutineManager persistAndFlush(Routine $entity)
 * @method RoutineManager removeEntity(Routine $entity)
 * @method Routine newClass()
 */
class RoutineManager extends BaseManager
{
    /**
     * RoutineManager constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        parent::__construct($manager, Routine::class);
    }

    /**
     * @param Profile $profile
     * @param array $content
     * @param string $name
     * @return Routine
     */
    public function createAndFlushFromProfileAndContent(Profile $profile, $content, $name)
    {
        $Routine = $this->newClass()->setName($name)->setProfile($profile)->setTasks($content);
        $this->persistAndFlush($Routine);

        return $Routine;
    }
}