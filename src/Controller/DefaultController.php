<?php


namespace App\Controller;

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

    /**
     * @Route("/add", name="add", options={"expose"=true})
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {

        $name = $request->get('name');
        $url = $request->get('url');

        $typeMusic = $this->getDoctrine()->getManager()->getRepository(Type::class)->findOneBy(['name' => 'Music']);

        $result = 'je n\'arrive pas à l\'ajouter...';

        if (!empty($name) && !empty($url))
        {
            $newLink = new Link;
            $newLink->setName($name);
            $newLink->setUrl($url);
            $newLink->setType($typeMusic);
            $this->getDoctrine()->getManager()->persist($newLink);
            $this->getDoctrine()->getManager()->flush();

            $result = 'Voilà, c\'est fait !';
        }

        return new JsonResponse([
            'message' => $result,
            'name' => $this->get(IntentHandler::class)->getSessionIdentifier() ?: "Invité"
        ]);
    }
}