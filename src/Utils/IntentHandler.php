<?php


namespace App\Utils;


use App\Entity\Intent;
use Doctrine\Common\Persistence\ObjectManager;

class IntentHandler
{
    /** @var ObjectManager manager */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $name
     * @return Intent|bool
     */
    public function getIntent($name)
    {
        return $this->manager->getRepository(Intent::class)->findOneBy(['name' => $name]);
    }

    /**
     * @param Intent $intent
     * @return mixed
     */
    public function handle(Intent $intent)
    {
        if (method_exists($this, $intent->getName())) {
            return call_user_func_array([$this, $intent->getName()], [$intent]);
        }

        return null;
    }


    protected function createAccount(Intent $intent)
    {
        //TODO
    }

    protected function useAccount(Intent $intent)
    {
        //TODO
    }
}