<?php

namespace App\Manager;

use App\Entity\Profile;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TaskManager
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 * @method TaskRepository getRepository()
 * @method TaskManager persistAndFlush(Task $entity)
 * @method TaskManager removeEntity(Task $entity)
 * @method Task newClass()
 */
class TaskManager extends BaseManager
{
    /**
     * TaskManager constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        parent::__construct($manager, Task::class);
    }

    /**
     * @param \DateTime $date
     * @param Profile $profile
     * @return Task
     */
    public function createAndFlushFromDateAndProfile(\DateTime $date, Profile $profile)
    {
        $task = $this->newClass()->setDate($date)->setName('Rendez-vous')->setProfile($profile);
        $this->persistAndFlush($task);

        return $task;
    }

}