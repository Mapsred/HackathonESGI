<?php


namespace App\Controller;

use App\Utils\LuisSDK;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Intent;

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
        $em = $this->getDoctrine()->getManager();

        $response = $this->get(LuisSDK::class)->query($request->request->get('q'));

        $intent = $response['topScoringIntent']['intent'];

        $parameters = array_combine(array_column($response['entities'], 'type'), array_column($response['entities'], 'entity')); 

        $getIntent = $em->getRepository(Intent::class)->findOneByName($intent);

        /*if(!empty($getIntent))
        {
            $result = 'Bienvenue '.$parameters['Identifier'].' !';
        }
        else
        {
            $result = 'rien trouvé ! >.<';
        }*/

        return new JsonResponse(array('message' => $result));
    }
}