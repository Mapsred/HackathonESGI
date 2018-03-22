<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $types = [
            ['name' => 'Musique'],
            ['name' => 'Série'],
        ];

        foreach ($types as $data) {
            $type = new Type();
            $type->setName($data['name']);
            $manager->persist($type);
            $manager->flush();

        }
        $manager->flush();
    }
}
