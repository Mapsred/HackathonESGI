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
     * @param array $response
     * @return array
     */
    public function getParameters(array $response)
    {
        return array_combine(array_column($response['entities'], 'type'), array_column($response['entities'], 'entity'));
    }

    /**
     * @param Intent $intent
     * @param array $response
     * @return mixed
     */
    public function handle(Intent $intent, array $response)
    {
        if (method_exists($this, $intent->getName())) {
            return call_user_func_array([$this, $intent->getName()], [$intent, $response]);
        }

        return null;
    }


    protected function createAccount(Intent $intent, array $response)
    {
        $parameters = $this->getParameters($response);
        foreach ($intent->getParameters() as $parameter) {
            if (!isset($parameters[$parameter])) {
                return sprintf('Le paramètre %s n\'existe pas', $parameter);
            }
        }

        //TODO
    }

    protected function useAccount(Intent $intent, array $response)
    {
        //TODO
    }
}