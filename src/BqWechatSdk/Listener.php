<?php
namespace BqWechatSdk;

use Zend\EventManager\EventManager;
use BqWechatSdk\Event\Result as EventResult;
use BqWechatSdk\Event\Push as PushEvent;

class Listener
{
    protected $eventManager;

    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        return $this;
    }

    public function run()
    {
        if(isset($_GET['echostr'])) {
            $results = $this->triggerValidEvent();
        } else {
            $results = $this->triggerPushEvent();
        }

        $result = $results->last();
        if($result instanceof EventResult) {
            echo $result->output();
        }
    }

    protected function triggerValidEvent()
    {
        $event = new PushEvent();
        $event
            ->setName(PushEvent::EVENT_VALID)
            ->setParam('echostr', $_GET['echostr'])
            ->setParam('timestamp', $_GET['timestamp'])
            ->setParam('nonce', $_GET['nonce'])
            ->setParam('signature', $_GET['signature']);
        return $this->eventManager->trigger($event);
    }

    protected function triggerPushEvent()
    {
        $event = new PushEvent();
        $event->setName(PushEvent::EVENT_PUSH);

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string(
                $postStr, 
                'SimpleXMLElement', 
                LIBXML_NOCDATA);
            $message = new Message();
            $message->exchangeXml($postObj);
            $event->setMessage($message);
        }

        return $this->eventManager->trigger($event);
    }
}
