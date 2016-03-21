<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/
require_once __DIR__.'/../vendor/autoload.php';

use Illuminate\Http\Request;

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server($loop);
$socket->on('connection', function ($conn) {
    $conn->write("Hello there!\n");
    $conn->write("Welcome to this amazing server!\n");
    $conn->write("Here's a tip: don't say anything.\n");

    $conn->on('data', function ($data) use ($conn) {
    	if(strstr($data,"quit"))
        	$conn->close();
        else
        	runLumen($conn,$data);
    });
});
$socket->listen(1337);

$loop->run();


function runLumen($conn,$data){
	$app = require __DIR__.'/../bootstrap/app.php';
	/*
	|--------------------------------------------------------------------------
	| Run The Application
	|--------------------------------------------------------------------------
	|
	| Once we have the application, we can handle the incoming request
	| through the kernel, and send the associated response back to
	| the client's browser allowing them to enjoy the creative
	| and wonderful application we have prepared for them.
	|
	*/
	$request = Request::capture();
	$request->setMethod("POST");
	$request->request->set("X",$data);
	$request->server->set('REQUEST_URI', '/test/url');
	$response = $app->dispatch($request);
    $conn->write($response->getContent());
	return $app;

}

