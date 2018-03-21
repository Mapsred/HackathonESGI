<?php


namespace App\Utils;


use App\Entity\Intent;
use App\Entity\Profile;
use App\Manager\ProfileManager;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Class IntentHandler
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 */
class IntentHandler
{
    /** @var ObjectManager manager */
    private $manager;

    /** @var array $parameters */
    private $parameters;

    /** @var LoggerInterface logger */
    private $logger;

    /** @var ProfileManager profileManager */
    private $profileManager;

    /**
     * IntentHandler constructor.
     * @param ObjectManager $manager
     * @param LoggerInterface $logger
     * @param ProfileManager $profileManager
     */
    public function __construct(ObjectManager $manager, LoggerInterface $logger, ProfileManager $profileManager)
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->profileManager = $profileManager;
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
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $entities
     * @return IntentHandler
     */
    public function setParameters(array $entities)
    {
        $this->parameters = array_combine(array_column($entities, 'type'), array_column($entities, 'entity'));

        return $this;
    }

    /**
     * @param Intent $intent
     * @param array $response
     * @return mixed
     */
    public function handle(Intent $intent, array $response)
    {
        if (method_exists($this, $intent->getName())) {
            $this->setParameters($response['entities']);

            return call_user_func_array([$this, $intent->getName()], [$intent, $response]);
        }

        return null;
    }

    /* Magic called methods (from IntentHandler::handle() */

    /**
     * @param Intent $intent
     * @return null|string
     */
    protected function createAccount(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters, $intent->getName())) {
            return $message;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $this->manager->getRepository(Profile::class)->findOneBy(['name' => $identifier])) {
            $message = sprintf('Désolé, un profil avec l\'identifiant <b>%s</b> existe dèjà', $identifier);
        }else {
            $message = sprintf('Bien reçu, je vous crée un profil avec l\'identifiant <b>%s</b>', $identifier);
            $this->profileManager->createAndFlushFromIdentifier($identifier);
        }
        
        return $message;
    }

    protected function useAccount(Intent $intent, array $response)
    {
        //TODO
    }

    /**
     * @param array $intentParameters
     * @param array $parameters
     * @param string $intentName
     * @return null|string
     */
    private function verifyParameters(array $intentParameters, array $parameters, string $intentName)
    {
        foreach ($intentParameters as $identifier) {
            if (!isset($parameters[$identifier])) {
                $this->log(sprintf('Le paramètre %s n\'a pas été trouvé pour l\'intent %s', $identifier, $intentName));

                return BotMessage::WRONG_FORMED_MESSAGE;
            }
        }

        return null;
    }

    /**
     * @param $message
     * @param string $type
     */
    private function log($message, $type = 'info')
    {
        call_user_func_array([$this->logger, $type], [$message]);
    }
}