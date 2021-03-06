<?php
/**
 * BqWechatSdk (https://github.com/elvis-bi/BqWechatSdk)
 *
 * @link https://github.com/elvis-bi/BqWechatSdk for this canonical source repository
 * @copyright elvis bi (elvis@dwenzi.com)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */

namespace BqWechatSdk;

use ReflectionObject;
use Exception;
use BqWechatSdk\Message\MessageInterface;
use BqWechatSdk\Message\OutputMessageInterface;

/**
 * 类Message用于封装微信消息
 *
 * 在实例化一个message实例以后请先使用setType()或setTypePrototype()访求为消息设
 * 置一个消息类型，再进行其它属性的设置
 */
class Message
{
    const TYPE_TEXT              = 'text';
    const TYPE_IMAGE             = 'image';
    const TYPE_LOCATION          = 'location';
    const TYPE_LINK              = 'link';
    const TYPE_EVENT             = 'event';
    const TYPE_MUSIC             = 'music';
    const TYPE_NEWS              = 'news';

    protected $typePrototype;

    public function exchangeXml($xml)
    {
        $this->setType($xml->MsgType);
        $this->typePrototype->exchangeXml($xml);
    }

    public function getAllType()
    {
        return array(
            self::TYPE_TEXT,
            self::TYPE_IMAGE,
            self::TYPE_LOCATION,
            self::TYPE_LINK,
            self::TYPE_EVENT,
            self::TYPE_MUSIC,
            self::TYPE_NEWS
        );
    }

    public function isText()
    {
        return $this->getType() == self::TYPE_TEXT;
    }

    public function isImage()
    {
        return $this->getType() == self::TYPE_IMAGE;
    }

    public function isLocation()
    {
        return $this->getType() == self::TYPE_LOCATION;
    }

    public function isLink()
    {
        return $this->getType() == self::TYPE_LINK;
    }

    public function isEvent()
    {
        return $this->getType() == self::TYPE_EVENT;
    }

    public function setType($type)
    {
        if(in_array($type, $this->getAllType())) {
            $typeClassName = sprintf('BqWechatSdk\Message\%s', ucfirst($type));
            $this->setTypePrototype(new $typeClassName());
            return $this;
        }

        throw new Exception(sprintf('Unknown type %s', $type));
    }

    public function setTypePrototype(MessageInterface $typePrototype)
    {
        $this->typePrototype = $typePrototype;
        return $this;
    }

    public function allowOutput()
    {
        return $this->typePrototype instanceof OutputMessageInterface;
    }

    public function toXml()
    {
        return $this->typePrototype->toXmlString();
    }

    public function __call($method, $argument)
    {
        if($this->typePrototype instanceof MessageInterface) {
            $typeRb = new ReflectionObject($this->typePrototype);
            if($typeRb->hasMethod($method)) {
                $method = $typeRb->getMethod($method);
                return $method->invokeArgs($this->typePrototype, $argument);
            }
        }

        if($this->typePrototype === null) {
            throw new Exception(
                sprintf('Please call %s::setTypePrototype()', __class__));
        }

        $error = sprintf(
            'member function %s() not found in %s', 
            $method, 
            __class__);
        throw new Exception($error);
    }
}
