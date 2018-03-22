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
                'url' => 'https://l.messenger.com/l.php?u=https%3A%2F%2Fwww.netflix.com%2Fwatch%2F70276688%3FtrackId%3D13752289%26tctx%3D0%252C0%252C27f0c60afa4b930bba11ffe52c703c2b30b6d2a5%253A0649a3d1dfad4f79fe46b2ec989baf539269063f%252C%252C&h=ATN3UKpGheBaXF6nnEIn_etcKqsoZZxt0XkWjoqCTJxtLrCUuHgCpTVJdXxYyw3ZdQp7GXM1Kyk0hn-_TQF13Iv4gk98TdXPSplUsJ7F1SB96B6sDE8YjA',
                'type' => 'Série'
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
