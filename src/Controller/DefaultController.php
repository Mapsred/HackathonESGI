<?php


namespace App\Controller;

use App\Utils\LuisSDK;
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
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/query/{value}", name="query")
     * @param Request $request
     * @return Response
     */
    public function queryAction(Request $request, $value)
    {
        $response = $this->get(LuisSDK::class)->query($request->query->get('q'));

        var_dump($response);

        return new Response();
    }
}