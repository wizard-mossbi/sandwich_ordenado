<?php

return [
    'notFoundHandler' => function($c) {
        return function ($request, $response) use ($c) {
            $tab = ['type' => "error" ,"error" => 404, "Message :" => "not found: Ressource non trouvée."];

            $response = $response->withHeader('Content-Type','application/json');
            $response->withStatus(404)->getBody()->write(json_encode($tab));
            return $response;
        };
    },
    'notAllowedHandler' => function($c) {
        return function (  $req,  $resp,$methods) {
            $tab = array('type' => "error" ,"error" => 405, "Message :" => "La Methode n'est pas autorisée" );
            $resp = $resp->withHeader('Content-Type','application/json');
            $resp->withStatus(405)->getBody()->write(json_encode($tab));
            return $resp;
        };
    },
    'badRequestHandler' => function($c) {
        return function (  $req,  $resp) {
            $tab = array('type' => "error" ,"error" => 400, "Message :" => "Bad request: l\'uri indiqué n'est pas connue de l'api");
            $resp = $resp->withHeader('Content-Type','application/json');
            $resp->withStatus(400)->getBody()->write(json_encode($tab));
            return $resp;
        };
    },

    'errorHandler' => function ($c) {
        return function ($request, $response, $exception) use ($c) {
            return $c['response']->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Something went wrong!: Internal Server Error');
        };
    }
];
