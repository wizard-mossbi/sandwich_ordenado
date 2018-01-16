<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 22/11/17
 * Time: 11:51
 */

namespace lbs\control;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Home
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function home($req,$rep){
        $rep->getbody()->write("Bonjour");
    }
}