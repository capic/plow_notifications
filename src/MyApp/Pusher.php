<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {
    protected $subscribedClients = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        echo "onSubscribe";
        $this->subscribedClients[$topic->getId()] = $topic;
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
        echo "onUnSubscribe";
    }
    public function onOpen(ConnectionInterface $conn) {
        echo "onOpen";
    }
    public function onClose(ConnectionInterface $conn) {
        echo "onClose";
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        echo "onCall";
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        echo "onPublish";
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "onError";
    }

    public function onLinkAddedToDownloadsList($entry) {
        //$entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
//        if (!array_key_exists($entryData['category'], $this->subscribedClients)) {
//            return;
//        }
        echo $this->subscribedClients;
        $topic = $this->subscribedClients[0];

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast("Link added to downloads list");
    }
}