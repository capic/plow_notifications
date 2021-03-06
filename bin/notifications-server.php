<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$loop   = React\EventLoop\Factory::create();
$notificationsApp = new MyApp\Notifications();

// Listen for the web server to make a ZeroMQ push after an ajax request
$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:6666'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($notificationsApp, 'onLinkAddedToDownloadsList'));

// Set up our WebSocket server for clients wanting real-time updates
$webSock = new React\Socket\Server($loop);
$webSock->listen(7070, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect

$server = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            $notificationsApp
        )
    ),
    $webSock
);

$loop->run();