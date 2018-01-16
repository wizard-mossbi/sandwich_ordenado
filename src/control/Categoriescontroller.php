<?php

namespace lbs\control;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Sandwich;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\model\Categorie;
use Slim\Exception\ContainerException;
use Slim\Exception\NotFoundException;
use Slim\Handlers\NotFound;


class Categoriescontroller {

    // Récupération du conteneur de dépendance

    private $container;

    public function __construct(\Slim\Container $container){

        $this->container = $container;

    }

    public function getCategories(Request $req, Response $resp,$args){
        //requête sql
        $tabAll = Categorie::all();
        // si je veux tous les parametre var_dump(getparams());

        $resp = $resp->withHeader('Content-Type','application/json');
        $resp->getBody()->write(json_encode($tabAll));

        return $resp;
    }

    public function getCategorie(Request $req, Response $resp,$args){

        try{

            if($tab = Categorie::where('id',"=",$args['id'])->firstOrFail())
            {
                $resp = $resp->withHeader('Content-Type','application/json');

                $resp->getBody()->write(json_encode($tab));
                return $resp;

            } else {

                throw new ModelNotFoundException($req, $resp);


            }

        } catch (ModelNotFoundException $exception){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);

        }


    }

    // création de ressource
    public function addCategorie(Request $req, Response $resp,$args){

        $tab = $req->getParsedBody();


        $c = new Categorie();
        $c->nom = filter_var($tab['nom'],FILTER_SANITIZE_SPECIAL_CHARS);
        $c->description = filter_var($tab['description'],FILTER_SANITIZE_SPECIAL_CHARS);

        try{

            $c->save();
        }catch (\Exception $e){
            // revoyer erreur format json
            $resp = $resp->withHeader('Content-Type','application/json');
            $resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));

        }

        $resp = $resp->withHeader('location',$this->container['router']->pathFor('categorie',['id' => $c->id]));
        $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
        $resp->getBody()->write(json_encode($c->toArray()));
        return $resp;
    }

    public function changeCategorie(Request $req, Response $resp,$args){

        // vérifier si la categorie existe

        // Récuperation de données envoyé
        $tab = $req->getParsedBody();
        // Néttoyage de la donné recu
        $id = filter_var($tab['id'],FILTER_SANITIZE_STRING);
        $nom = filter_var($tab['nom'],FILTER_SANITIZE_STRING);
        $description = filter_var($tab['description'],FILTER_SANITIZE_STRING);
        // Récuperation de l'id dans la base
        $categ = Categorie::select('id')->where('id','=',$id)->first();


        // si categ existe alors on la modifie
        if(isset($categ)){
            $categ->nom = $nom;
            $categ->description = $description;

            try{

                $categ->save();

            } catch(\Exception $e) {

                $resp = $resp->withHeader('Content-Type','text/html')->withStatus(404);
                $resp->getBody()->write('nononono '.$e->getMessage());
            }

            $resp = $resp->withHeader('location',$this->container['router']->pathFor('categorie',['id' => $categ->id]));
            $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
            $resp->getBody()->write(json_encode($categ->toArray()));
            return $resp;

        }
    }
}
