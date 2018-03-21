<?php

namespace App\Utils;

use App\Entity\Intent;
use App\Entity\Link;
use App\Entity\Profile;
use App\Entity\Task;
use App\Entity\Type;
use App\Manager\ProfileManager;
use App\Manager\TaskManager;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

    /** @var Session session */
    private $session;

    /** @var TaskManager taskManager */
    private $taskManager;

    /** @var DateTimeHelper dateTimeHelper */
    private $dateTimeHelper;

    /**
     * IntentHandler constructor.
     * @param ObjectManager $manager
     * @param LoggerInterface $logger
     * @param ProfileManager $profileManager
     * @param TaskManager $taskManager
     * @param SessionInterface $session
     * @param DateTimeHelper $dateTimeHelper
     */
    public function __construct(ObjectManager $manager, LoggerInterface $logger, ProfileManager $profileManager,
                                TaskManager $taskManager, SessionInterface $session, DateTimeHelper $dateTimeHelper)
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->profileManager = $profileManager;
        $this->session = $session;
        $this->taskManager = $taskManager;
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /* Getter/Setter methods */

    /**
     * @param string $name
     * @return Intent|bool
     */
    public function getIntent($name)
    {
        return $this->manager->getRepository(Intent::class)->findOneBy(['name' => $name]);
    }

    /**
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
     * @return string
     */
    public function getSessionIdentifier(): ?string
    {
        return $this->session->get('current_identifier');
    }

    /**
     * @param string $identifier
     * @return IntentHandler
     */
    public function setSessionIdentifier(string $identifier)
    {
        if (null !== $identifier) {
            $this->session->set('current_identifier', $identifier);
        }

        return $this;
    }

    /**
     * @return Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profileManager->getRepository()->findOneBy(['name' => $this->getSessionIdentifier()]);
    }

    /* Handler (called from the Controller) */

    /**
     * @param Intent $intent
     * @param array $response
     * @return mixed
     */
    public function handle(Intent $intent, array $response)
    {
        if (method_exists($this, $intent->getName())) {
            $this->setParameters($response['entities']);

            return call_user_func_array([$this, $intent->getName()], [$intent]);
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
            $message = sprintf(BotMessage::EXISTING_PROFILE, $identifier);
        } else {
            $message = sprintf(BotMessage::CREATING_PROFILE, $identifier);
            $profile = $this->profileManager->createAndFlushFromIdentifier($identifier);
            $this->setSessionIdentifier($profile->getName());
        }

        return $message;
    }

    /**
     * @param Intent $intent
     * @return null|string
     */
    protected function useAccount(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters, $intent->getName())) {
            return $message;
        }
        $identifier = $parameters[$intentParameters[0]];
        if (null !== $profile = $this->profileManager->getRepository()->findOneBy(['name' => $identifier])) {
            $message = sprintf(BotMessage::USING_PROFILE, $identifier);
            $this->setSessionIdentifier($profile->getName());
        } else {
            $message = sprintf(BotMessage::NON_EXISTING_PROFILE, $identifier);
        }

        return $message;
    }

    /**
     * @param Intent $intent
     * @return null|string
     */
    protected function addTask(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters, $intent->getName())) {
            return $message;
        }

        $baseTime = $parameters[$intentParameters[0]];
        $time = Helper::translateDate($baseTime);
        if (null === $date = DateHelper::getDate($time)) {
            return BotMessage::DATE_NOT_UNDERSTANDED;
        }

        if (null === $profile = $this->getProfile()) {
            return BotMessage::TASK_NOT_LOGGED_IN;
        }

        if (null !== $this->taskManager->getRepository()->findOneBy(['date' => $date, 'profile' => $profile])) {
            return BotMessage::DATE_ALREADY_USED;
        }

        $this->taskManager->createAndFlushFromDateAndProfile($date, $profile);

        return sprintf(BotMessage::DATE_SUCCESS, $baseTime);
    }

    /**
     * @return array|string
     */
    protected function listTasks()
    {
        if (null === $profile = $this->getProfile()) {
            return BotMessage::TASKS_NOT_LOGGED_IN;
        }

        $tasks = $this->taskManager->getRepository()->findBy(['profile' => $profile]);
        if (empty($tasks)) {
            return BotMessage::NO_TASKS;
        }

        return [BotMessage::TASKS, 'List' => implode(', ', array_map(function (Task $task) {
            return Helper::formatDate($this->dateTimeHelper, $task->getDate(), "d MMMM Y à H:m");
        }, $tasks))];
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function launchMusic(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters, $intent->getName())) {
            return $message;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $music = $this->manager->getRepository(Link::class)->findOneBy(['name' => $identifier])) {
            return [sprintf(BotMessage::LAUNCH_MUSIC, $identifier), 'Music' => $music->getUrl()];
        }

        return sprintf(BotMessage::MUSIC_NOT_FOUND, $identifier);
    }

    /**
     * @return array|null|string
     */
    protected function listMusic()
    {
        $typeMusic = $this->manager->getRepository(Type::class)->findOneBy(['name' => 'Music']);
        $listMusic = $this->manager->getRepository(Link::class)->findBy(['type' => $typeMusic]);
        $listNameMusic = [];
        foreach ($listMusic as $music) {
            $listNameMusic[] = $music->getName();
        }

        if (!empty($listMusic)) {
            return [BotMessage::MUSIC_LIST, 'List' => implode(', ', $listNameMusic)];
        }

        return BotMessage::NO_MUSIC;
    }

    /**
     * @param Intent $intent
     * @return null|string
     */
    protected function addLink(Intent $intent)
    {

        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters, $intent->getName())) {
            return $message;
        }

        $identifier = $parameters[$intentParameters[0]];

        $type = $this->manager->getRepository(Type::class)->findByName(['name' => $identifier]);

        if (null !== $type) {
            $message = sprintf('Tu souhaites ajouter un(e) '.$identifier.' ? Quel son nom ?', $identifier);
            $action['type'] = 'Add';
            $action['info'] = $identifier;

        }else {
            $message = sprintf('Impossible d\'ajouter ce genre de chose, être vous sûr du nom ?', $identifier);
        }
        
        return array($message, $action);
    }

    /* Helper methods */

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