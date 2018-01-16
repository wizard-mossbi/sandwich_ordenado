<?php

namespace lbs\control;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\model\Sandwich;

class SandwichController
{
    // Récupération du conteneur de dépendance
    private $container;
    public function __construct(\Slim\Container $container){
      $this->container = $container;
    }

    public function getSandwich(Request $req, Response $resp,$args){

              // Verification des parametre
              $paramT = $req->getQueryParam('t',null);
              $img = (is_null($req->getQueryParam('img',null))) ? 0 : 1;
              $page = $req->getQueryParam('page',1);// si il est absent alors 1

              if($page<0){
                  $page=1;
              }


              $size = $req->getQueryParam('size',10);// si il est absent alors 20


              try{

                  // MES COLLONES
                  $query = Sandwich::select('id','nom','type_pain');


                  if(!is_null($paramT)){

                      $query = $query->where('type_pain','like','%'.$paramT.'%');
                  }

                  if($img === 1){
                      $query = $query->whereNotNull('img');
                  }

                  // NB D'ELEMENT TOTAL RÉPONDANT A LA REQ INDÉPENDANT DE LA PAGINATION
                  $total = $query->count();

                  $nbpageMax = round(($total/$size)+1);

                  if($page > $nbpageMax){

                      $page = $nbpageMax;
                  }

                  // ELEMENT DE PAGINATION
                  $query = $query->skip(($page - 1) * $size)->take($size)->get();
                  // DATE ET COUNT DE LA PAGE COURANTE
                  $date = date("d-m-y");
                  $countSize = $query->count();
                  $tab = $query;

                  // TABLEAU DE RECEPTION
                  $tableau = array();

                  foreach ($tab as $sandwich){

                      $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('marcel', ['id'=>$sandwich->id])]]);
                      array_push($link,$sandwich);

                      array_push($tableau,$link);
                  }


                  $head = ["type" => "collection","meta" => ["count" => $total,"size" => $countSize,"date" => $date]];

                  array_push($head,$tableau);

                  $resp = $resp->withHeader('Content-Type','application/json')->withStatus(200);
                  $resp->getBody()->write(json_encode($head));
                  return $resp;


              } catch (ModelNotFoundException $exception){


                  echo('La ressource n\'existe pas');

              }
    }
}
