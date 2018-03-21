<?php


namespace App\Controller;

use App\Utils\IntentHandler;
use App\Utils\LuisSDK;
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
        return $this->render('Default/bot.html.twig');
    }

    /**
     * @Route("/query", name="query", options={"expose"=true})
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function queryAction(Request $request)
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
}