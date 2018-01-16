<?php
use Firebase\JWT\JWT;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

require_once '../src/vendor/autoload.php';

$config = [
       'driver'    => 'mysql',
       'host'      => 'db',
       'database'  => 'lbs',
       'username'  => 'lbs',
       'password'  => 'lbs',
       'charset'   => 'utf8',
       'collation' => 'utf8_unicode_ci',
       'prefix'    => '' ];

$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'production' => true
    ]
]);

$container = $app->getContainer();



//======================================================================
// GESTON DE ERRORES
//======================================================================

$container['notFoundHandler'] = function($c) {
	return function( $req, $resp ) {
		$resp= $resp->withStatus(400)->withHeader('Content-Type', 'application/json');
        $resp->getBody()->write( json_encode(['message'=>'Bad Request']));
        return $resp;
		// Debes hacelo en 2 pasos porque write no crea una requet, solo modifica una existente
	};
};

$container['errorHandler'] = function($c) {
	return function( $req, $resp, $e ) {
		$resp= $resp->withStatus(500)->withHeader('Content-Type', 'application/json');
        $resp->getBody()->write( json_encode([$e])); // 'message'=>'Internal Server Error'
        return $resp;
	};
};

$container['notAllowedHandler'] = function($c) {
	return function( $req, $resp, $methods ) {
		$resp= $resp->withStatus(405)->withHeader('Allow', implode(',', $methods), 'application/json');
        $resp->getBody()->write(json_encode($methods));
        return $resp;
		// Por si no lo notaste, $methods, es un array que guarda todos los metodos (GET, POST...), disponibles para esta URI
	};
};

//======================================================================
// Probando como funciona el contenedor de dependencias
//======================================================================
$app->get('/hello/{name}',
	function(Request $req, Response $resp, $args) {
	$name = $args['name'];
	// Se accede igual que a un array de arrays
	echo $estadoDeProduccion = $this[ 'settings' ]['production'];

});

//======================================================================
// Un Sandwich
//======================================================================

$app->get('/sandwiches/{id}',
	function(Request $req, Response $resp, $args) {
	$id = $args['id'];

      try {
		$mySandwich = lbs\models\Sandwich::findOrFail($id);

	  	$resp = $resp->withHeader('Content-Type', 'application/json');
		$resp->getBody()->write(json_encode($mySandwich));
		return $resp;
      } catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e ) {
			$resp = $resp->withStatus(404)->withHeader('Content-Type', 'application/json');
			$resp->getBody()->write(json_encode('CHALES MAISTRO, NO TENEMOS ESE SANDWICH'));
			return $resp;
      }

});

//======================================================================
// Todos los sandwiches
//======================================================================

$app->get('/sandwichs[/]',\lbs\control\SandwichController::class . ':getSandwich');

//======================================================================
// Una cateogoria
//======================================================================


$app->get('/categories/{id}',
	function(Request $req, Response $resp, $args) {
	$id = $args['id'];

	 try {
		$mycategorie = lbs\models\Categorie::findOrFail($id);

	  	$resp = $resp->withHeader('Content-Type', 'application/json');
		$resp->getBody()->write(json_encode($mycategorie));
		return $resp;
    } catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e ) {
       		$resp = $resp->withStatus(404)->withHeader('Content-Type', 'application/json');
			$resp->getBody()->write(json_encode('CHALES MAISTRO, NO TENEMOS ESA CATEGORIA'));
			return $resp;
  	}

});


//======================================================================
// Todas las categorias
//======================================================================

/*
$app->get('/categories[/]',
	function(Request $req, Response $resp, $args) {

	$categories = lbs\models\Categorie::select();

  	$resp = $resp->withHeader('Content-Type', 'application/json');
	$resp->getBody()->write(json_encode($categories));
	return $resp;

});
*/

//======================================================================
// Registrar un Sanwich
//======================================================================

/*
$app->post('/sandwich[/]',
	function(Request $req, Response $resp, $args) {
		$data = $req->getParsedBody();
		$sandwich = new lbs\models\Sandwich();
		$sandwich->nom = $data['nom'];
		$sandwich->description = $data['description'];
		$sandwich->type_pain = $data['type_pain'];
		$sandwich->img = $data['img'];
});
*/

//======================================================================
// Registrar una categoria
//======================================================================

$app->post('/categorie[/]',
	function (Request $request, Response $response, $args){

      $data = $request->getParsedBody();
      //var_dump($data);


      if (!isset($data['nom'])) return $response->withStatus(400);
      if (!isset($data['description'])) return $response->withStatus(400);

      $myCategorie = new lbs\models\Categorie();
      $myCategorie->nom = filter_var($data['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
      $myCategorie->description = filter_var($data['description'], FILTER_SANITIZE_SPECIAL_CHARS);
      $myCategorie->save();

      return $response->withStatus(201);

    });


    //======================================================================
    // Registrar un commande
    //======================================================================
    $app->post('/commande[/]',
    	function (Request $request, Response $response, $args){
        $data = $request->getParsedBody();

        if (!isset($data['nom_client'])) return $response->withStatus(400);
        if (!isset($data['mail_client'])) return $response->withStatus(400);
        if (!isset($data['date'])) return $response->withStatus(400);
        if (!isset($data['heure'])) return $response->withStatus(400);

        $myCommande = new lbs\models\Commande();
        $myCommande->id = Uuid::uuid4();
        $myCommande->nom_client = filter_var($data['nom_client'], FILTER_SANITIZE_SPECIAL_CHARS);
        $myCommande->mail_client = filter_var($data['mail_client'], FILTER_SANITIZE_SPECIAL_CHARS);
        $myCommande->date = filter_var($data['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $myCommande->heure = filter_var($data['heure'], FILTER_SANITIZE_SPECIAL_CHARS);
        $myCommande->token = bin2hex(openssl_random_pseudo_bytes(32));
        $myCommande->save();

        $savedCommande = [
          "commande" => [
            "nom_client" => $myCommande->nom_client,
            "mail_client" => $myCommande->mail_client,
            "livraison" => [
              "date" => $myCommande->date,
              "heure" => $myCommande->heure,
            ],
          ],
          "id" => $myCommande->id,
          "token" => $myCommande->token,
        ];

        $response = $response->withStatus(201)->withHeader('Content-Type', 'application/json');
  			$response->getBody()->write(json_encode($savedCommande));
  			return $response;
      });

      //======================================================================
      // Recuperar une commande
      //======================================================================

      $app->get('/commandes/{id}',
      	function(Request $req, Response $resp, $args) {
      	$id = $args['id'];

          try {
        		$myCommande = lbs\models\Commande::findOrFail($id);
            $foundCommande = [
              "commande" => [
                "nom_client" => $myCommande->nom_client,
                "mail_client" => $myCommande->mail_client,
                "livraison" => [
                  "date" => $myCommande->date,
                  "heure" => $myCommande->heure,
                ],
              ],
              "id" => $myCommande->id,
              "token" => $myCommande->token,
            ];
      	  	$resp = $resp->withHeader('Content-Type', 'application/json');
        		$resp->getBody()->write(json_encode($foundCommande));
        		return $resp;
          } catch ( Illuminate\Database\Eloquent\ModelNotFoundException $e ) {
      			$resp = $resp->withStatus(404)->withHeader('Content-Type', 'application/json');
      			$resp->getBody()->write(json_encode('CHALE, NO TENEMOS ESA COMANDA'));
      			return $resp;
          }
      })->add(function(Request $req, Response $resp, callable $next) {

          //Récupérer l'identifiant de cmmde dans la route et le token
          $id = $req->getAttribute('route')->getArgument('id');
          $token = $req->getQueryParam('token', null);

          // vérifier que le token correspond à la command
          try{
            lbs\models\Commande::where('id', '=', $id)
            ->where('token', '=',$token)
            ->firstOrFail();
          }
          catch(ModelNotFoundException $e){
            // générer une erreur
            return $resp;
          };
          return $next ($req, $resp);
        });


//======================================================================
// Modificar una cateogorîa
//======================================================================

 $app->put('/categorie[/]', \lbs\control\Categoriescontroller::class . ':changeCategorie')->setName('categorie');

//======================================================================
//PROBANDO EL FIREBASE
//======================================================================

$app->get('/fire[/]',
	function(Request $req, Response $resp, $args) {
    $token = JWT::encode(['iss' => 'http://auth.myapp.net',
                          'aud' => 'http://api.myapp.net',
                          'iat' => time(), 'exp' => time() + 3600,
                          'uid' => 'usuario_anonimo',
                          'lvl' => 'nivel_dios' ],
                          $secretKey, 'HS512');

                          $resp = $resp->withHeader('Content-Type', 'application/json');
                          $resp->getBody()->write(json_encode($token));
                          return $resp;

});

$app->run();
