<?php

namespace App\Utils;

use App\Entity\Intent;
use App\Entity\Link;
use App\Entity\Profile;
use App\Entity\ProfileLink;
use App\Entity\Routine;
use App\Entity\Task;
use App\Entity\Type;
use App\Manager\ProfileManager;
use App\Manager\RoutineManager;
use App\Manager\TaskManager;
use Doctrine\Common\Persistence\ObjectManager;
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

    /** @var ProfileManager profileManager */
    private $profileManager;

    /** @var Session session */
    private $session;

    /** @var TaskManager taskManager */
    private $taskManager;

    /** @var RoutineManager routineManager */
    private $routineManager;

    /**
     * IntentHandler constructor.
     * @param ObjectManager $manager
     * @param ProfileManager $profileManager
     * @param TaskManager $taskManager
     * @param RoutineManager $routineManager
     * @param SessionInterface $session
     */
    public function __construct(ObjectManager $manager, ProfileManager $profileManager,
                                TaskManager $taskManager, RoutineManager $routineManager, SessionInterface $session)
    {
        $this->manager = $manager;
        $this->profileManager = $profileManager;
        $this->session = $session;
        $this->taskManager = $taskManager;
        $this->routineManager = $routineManager;
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
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
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
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
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
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
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
            return Helper::formatDate($task->getDate(), 'd F Y \à H\hi');
        }, $tasks))];
    }

    /**
     * @param Intent $intent
     * @return null|string
     */
    protected function removeTask(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
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

        if (null === $task = $this->taskManager->getRepository()->findOneBy(['date' => $date, 'profile' => $profile])) {
            return BotMessage::DATE_NOT_FOUND;
        }

        $this->taskManager->removeEntity($task);

        return BotMessage::TASK_REMOVED;
    }


    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function launchMusic(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $music = $this->manager->getRepository(Link::class)->findOneBy(['name' => $identifier])) {
            if(!empty($this->getProfile())){ 
            $profile = $this->getProfile();
            $newProfileLink = new ProfileLink;
            $newProfileLink->setProfile($profile);
            $newProfileLink->setLink($music);
            $this->manager->persist($newProfileLink);
            $this->manager->flush();
            }

            return [sprintf(BotMessage::LAUNCH_LINK, $identifier), 'Music' => $music->getUrl()];
        }

        return sprintf(BotMessage::MUSIC_NOT_FOUND, $identifier);
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function listLink(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        $identifier = $parameters[$intentParameters[0]];
           
        if(ucfirst($identifier) == 'Série' || ucfirst($identifier) == 'Séries' || ucfirst($identifier) == 'Series'){
            $identifier = 'Serie';
        }
        if(ucfirst($identifier) == 'Musiques'){
            $identifier = 'Musique';
        }
        $type = $this->manager->getRepository(Type::class)->findOneBy(['name' => $identifier]);
        $list = $this->manager->getRepository(Link::class)->findBy(['type' => $type]);
        $listNames = [];
        foreach ($list as $link) {
            $listNames[] = $link->getName();
        }

        if (!empty($list)) {
            $identifier = lcfirst($identifier).'s';
            return [sprintf(BotMessage::LINK_LIST, $identifier), 'List' => implode(', ', $listNames)];
        }
        return sprintf(BotMessage::NO_LINK, $identifier);
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function addLink(Intent $intent)
    {
        if(!empty($this->getProfile())){ 
            $parameters = $this->getParameters();
            $intentParameters = $intent->getParameters();
            if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
                return $message;
            }

            $identifier = $parameters[$intentParameters[0]];
            if (null !== $this->manager->getRepository(Type::class)->findOneBy(['name' => $identifier])) {
                return [sprintf(BotMessage::LINK_ADD, $identifier), 'Add' => $identifier];
            }
            return BotMessage::LINK_UNAVAILABLE;
        }
        return BotMessage::NOT_CONNECTED;
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function preferedLink(Intent $intent)
    {
        if(!empty($this->getProfile())){    
            $parameters = $this->getParameters();
            $intentParameters = $intent->getParameters();
            if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
                return $message;
            }

            $identifier = $parameters[$intentParameters[0]];

            if(ucfirst($identifier) == 'Série'){
                $identifier = 'Serie';
            }

            if (null !== $prefered = $this->manager->getRepository(Type::class)->findOneBy(['name' => $identifier])) {
                    $profile = $this->getProfile();
                    $listLinks = $this->manager->getRepository(ProfileLink::class)->findBy(['profile' => $profile]);
                    $linksArray = [];
                    foreach ($listLinks as $listLink) {
                        $link = $listLink->getLink();
                        if($link->getType()->getName() == ucfirst($identifier)){
                            $linksArray[] = $link->getName();
                        }
                    }
                    
                    if(count($linksArray) >= 1){
                        $linksArrayCount[] = array_count_values($linksArray);
                        $preferedLink = array_search(max($linksArrayCount['0']),$linksArrayCount['0']);
                    }else{
                        $preferedLink = 'Vous n\'avez pas encore lancé une seule '.$identifier.'.';
                    }
                    return [sprintf(BotMessage::PREFERED_SHOW, $identifier), 'List' => $preferedLink];
            }
            return BotMessage::PREFERED_UNAVAILABLE;
        }
        return BotMessage::NOT_CONNECTED;

    }


    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function addRoutine(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        if (null === $profile = $this->getProfile()) {
            return BotMessage::ROUTINE_NOT_LOGGED_IN;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null === $this->routineManager->getRepository()->findOneByProfileAndName($profile, $identifier)) {
            return [sprintf(BotMessage::ROUTING_ADDING, $identifier), 'AddRoutine' => $identifier];
        }

        return BotMessage::ROUTING_ALREADY_EXISTING;
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function launchRoutine(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        if (null === $profile = $this->getProfile()) {
            return BotMessage::ROUTINE_NOT_LOGGED_IN;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $routine = $this->routineManager->getRepository()->findOneByProfileAndName($profile, $identifier)) {
            return [sprintf(BotMessage::ROUTING_LAUNCHING, $identifier), 'LaunchRoutine' => $routine->getTasks()];
        }

        return sprintf(BotMessage::ROUTING_NOT_EXISTING, $identifier);
    }

    /**
     * @param Intent $intent
     * @return array|null|string
     */
    protected function removeRoutine(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        if (null === $profile = $this->getProfile()) {
            return BotMessage::ROUTINE_NOT_LOGGED_IN;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $routine = $this->routineManager->getRepository()->findOneByProfileAndName($profile, $identifier)) {
            $this->routineManager->removeEntity($routine);

            return sprintf(BotMessage::ROUTING_REMOVING, $identifier);
        }

        return sprintf(BotMessage::ROUTING_NOT_EXISTING, $identifier);

    }

    /**
     * @return array|string
     */
    protected function listRoutines()
    {
        if (null === $profile = $this->getProfile()) {
            return BotMessage::ROUTINE_NOT_LOGGED_IN;
        }

        $routines = $this->routineManager->getRepository()->findBy(['profile' => $profile]);
        if (empty($routines)) {
            return BotMessage::NO_ROUTING;
        }

        return [BotMessage::ROUTINGS, 'List' => implode(", ", array_map(function (Routine $routine) {
            return $routine->getName();
        }, $routines))];
    }

    protected function listRoutine(Intent $intent)
    {
        $parameters = $this->getParameters();
        $intentParameters = $intent->getParameters();
        if (null !== $message = $this->verifyParameters($intentParameters, $parameters)) {
            return $message;
        }

        if (null === $profile = $this->getProfile()) {
            return BotMessage::ROUTINE_NOT_LOGGED_IN;
        }

        $identifier = $parameters[$intentParameters[0]];
        if (null !== $routine = $this->routineManager->getRepository()->findOneByProfileAndName($profile, $identifier)) {
            return [sprintf(BotMessage::ROUTING_LAUNCHING, $identifier), 'List' => implode(', ', $routine->getTasks())];
        }

        return sprintf(BotMessage::ROUTING_NOT_EXISTING, $identifier);
    }

    /* Helper methods */

    /**
     * @param array $intentParameters
     * @param array $parameters
     * @return null|string
     */
    private function verifyParameters(array $intentParameters, array $parameters)
    {
        foreach ($intentParameters as $identifier) {
            if (!isset($parameters[$identifier])) {
                return BotMessage::WRONG_FORMED_MESSAGE;
            }
        }

        return null;
    }
}