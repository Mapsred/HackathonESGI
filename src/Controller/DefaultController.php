<?php


namespace App\Controller;

use App\Entity\Type;
use App\Manager\RoutineManager;
use App\Utils\BotMessage;
use App\Utils\IntentHandler;
use App\Utils\LuisSDK;
use App\Entity\Link;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('Default/homepage.html.twig');
    }

    /**
     * @Route("/bot", name="bot")
     */
    public function bot()
    {
        return $this->render('Default/bot.html.twig');
    }

    /**
     * @Route("/routine", name="routine")
     */
    public function routine()
    {
        return $this->render('Default/routine.html.twig');
    }

    /**
     * @Route("/routine_create", name="routine_create")
     */
    public function routine_create()
    {
        return $this->render('Default/routine_create.html.twig');
    }

    /**
     * @Route("/planning", name="planning")
     */
    public function planning()
    {
        return $this->render('Default/planning.html.twig');
    }

    /**
     * @Route("/construction", name="construction")
     */
    public function construction()
    {
        return $this->render('Default/construction.html.twig');
    }

    /**
     * @Route("/query", name="query", options={"expose"=true})
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function query(Request $request)
    {
        $response = $this->get(LuisSDK::class)->query($request->request->get('q'));

        $intent = $response['topScoringIntent']['intent'];

        $result = 'Rien trouvé';
        if (null !== $intent = $this->get(IntentHandler::class)->getIntent($intent)) {
            $result = $this->get(IntentHandler::class)->handle($intent, $response);
        }

        return new JsonResponse([
            'message' => $result,
            'response' => $response,
            'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
        ]);
    }

    /**
     * @Route("/add", name="add", options={"expose"=true})
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $name = $request->get('name');
        $url = $request->get('url');
        $type = $request->get('type');
        $findType = $this->getDoctrine()->getManager()->getRepository(Type::class)->findOneBy(['name' => $type]);

        $result = 'je n\'arrive pas à l\'ajouter...';

        if (!empty($name) && !empty($url)) {
            $newLink = new Link;
            $newLink->setName($name);
            $newLink->setUrl($url);
            $newLink->setType($findType);
            $this->getDoctrine()->getManager()->persist($newLink);
            $this->getDoctrine()->getManager()->flush();

            $result = 'Voilà, c\'est fait !';
        }

        return new JsonResponse([
            'message' => $result,
            'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
        ]);
    }

    /**
     * @Route("/routing_add", name="routine_add", options={"expose"=true})
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function routingAdd(Request $request)
    {
        if (!$request->request->has('name') || !$request->request->has('content')) {
            return new JsonResponse([
                'message' => 'Je n\'ai pas réussi à ajouter votre routine, une erreur s\'est produite',
                'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
            ]);
        }

        if (null === $profile = $this->get(IntentHandler::class)->getProfile()) {
            return new JsonResponse([
                'message' => BotMessage::ROUTINE_NOT_LOGGED_IN,
                'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
            ]);
        }

        $this->get(RoutineManager::class)->createAndFlushFromProfileAndContent(
            $profile,
            $request->request->get('content'),
            $request->request->get('name')
        );

        return new JsonResponse([
            'message' => 'Votre routine a bien été ajoutée',
            'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
        ]);
    }
}