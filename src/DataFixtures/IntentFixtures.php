<?php

namespace App\DataFixtures;

use App\Entity\Intent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class IntentFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $intents = [
            ['name' => 'createAccount', 'parameters' => ['Identifier']],
            ['name' => 'useAccount', 'parameters' => ['Identifier']],
            ['name' => 'addTask', 'parameters' => ['TaskDate']],

            ['name' => 'launchMusic', 'parameters' => ['MusicIdentifier']],
            ['name' => 'listMusic', 'parameters' => []],

            ['name' => 'addLink', 'parameters' => ['LinkIdentifier']],
            ['name' => 'preferedLink', 'parameters' => ['LinkIdentifier']],

            ['name' => 'listTasks', 'parameters' => []],
            ['name' => 'removeTask', 'parameters' => ['TaskDate']],

            ['name' => 'addRoutine', 'parameters' => ['RoutineIdentifier']],
            ['name' => 'launchRoutine', 'parameters' => ['RoutineIdentifier']],
            ['name' => 'removeRoutine', 'parameters' => ['RoutineIdentifier']],
            ['name' => 'listRoutines', 'parameters' => []],
            ['name' => 'listRoutine', 'parameters' => ['RoutineIdentifier']],
        ];

        foreach ($intents as $data) {
            $entity = new Intent();
            $entity->setName($data['name'])->setParameters($data['parameters']);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
