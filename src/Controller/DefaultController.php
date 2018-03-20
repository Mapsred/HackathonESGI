<?php


namespace App\Controller;

use App\Utils\LuisSDK;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        print_r($response);

        return new Response();
    }
}