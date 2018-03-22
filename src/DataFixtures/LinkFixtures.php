<?php

namespace App\DataFixtures;

use App\Entity\Link;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LinkFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $links = [
            [
                'name' => 'nightcore',
                'url' => 'https://www.youtube.com/watch?v=QI5aXPC_0Fs&list=RDh2XTsWgN0CU&index=4',
                'type' => 'Musique'
            ],
            [
                'name' => 'Amir',
                'url' => 'https://www.youtube.com/watch?v=41Xj6DLYRXk&list=RD41Xj6DLYRXk',
                'type' => 'Musique'
            ],
            [
                'name' => 'Pentatonix',
                'url' => 'https://www.youtube.com/watch?v=41Xj6DLYRXk&list=RD41Xj6DLYRXk',
                'type' => 'Musique'
            ],
            [
                'name' => 'Arrow',
                'url' => 'https://www.netflix.com/watch/70276688?trackId=13752289&tctx=0%2C0%2C27f0c60afa4b930bba11ffe52c703c2b30b6d2a5%3A0649a3d1dfad4f79fe46b2ec989baf539269063f%2C%2C',
                'type' => 'Serie'
            ],
        ];

        foreach ($links as $data) {
            if (null !== $type = $manager->getRepository(Type::class)->findOneBy(['name' => $data['type']])) {
                $entity = new Link();
                $entity->setName($data['name'])->setUrl($data['url'])->setType($type);
                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            TypeFixtures::class
        ];
    }
}
