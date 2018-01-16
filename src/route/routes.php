<?php
$app->get('/',\lbs\control\Home::class . ':home');
$app->get('/bonjour/{name}',function($req,$rep,$args){
    // le middleware est executer ici

    $rep->getbody()->write('bonjour '.$args['name']);


    // le middleware est executer ici

});
$app->get('/categories[/]', \lbs\control\Categoriescontroller::class . ':getCategories')->setName('categories');
$app->get('/categories/{id}[/]',
    \lbs\control\Categoriescontroller::class . ':getCategorie')
    ->setName('categorie'); // setName fais le lien avec le pathFor du controller, il permet de seulement modifier les routes et de ne pas se préocuper du controller pour le pathfor

// Ajout d'une categorie
$app->post('/categories[/]', \lbs\control\Categoriescontroller::class . ':addCategorie')->setName('categories');
// Modification d'une categorie
$app->put('/categorie[/]', \lbs\control\Categoriescontroller::class . ':changeCategorie')->setName('categorie');

$app->get('/sandwichs[/]',\lbs\control\SandwichController::class . ':getSandwich');

$app->get('/sandwich/{id}',\lbs\control\SandwichController::class . ':getSandwich')->setName('marcel');
