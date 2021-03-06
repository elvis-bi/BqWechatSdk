<?php
/**
 * BqWechatSdk (https://github.com/elvis-bi/BqWechatSdk)
 *
 * @link https://github.com/elvis-bi/BqWechatSdk for this canonical source repository
 * @copyright elvis bi (elvis@dwenzi.com)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */

namespace BqWechatSdk\Event;

use Exception;
use BqWechatSdk\Message;

class Result
{
    protected $output;
    protected $message;

    public function __construct($output = null)
    {
        $this->output = $output;
    }

    public function setMessage(Message $message)
    {
        if(!$message->allowOutput()) {
            $error = sprintf('must not return %s message ', $message->getType());
            throw new Exception($error);
        }

        $this->message = $message;
        return $this;
    }

    public function output()
    {
        if($this->message !== null) {
            return $this->message->toXml();
        } else {
            return $this->output;
        }
    }
}
