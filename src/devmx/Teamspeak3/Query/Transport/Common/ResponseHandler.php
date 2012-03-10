<?php
/*
  This file is part of TeamSpeak3 Library.

  TeamSpeak3 Library is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  TeamSpeak3 Library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with TeamSpeak3 Library. If not, see <http://www.gnu.org/licenses/>.
 */
namespace devmx\Teamspeak3\Query\Transport\Common;
use devmx\Teamspeak3\Query\Exception;
use devmx\Teamspeak3\Query\Command;

/**
 * The Responsehandler handles all output given by the Query.
 * It checks if the server is valid, checks commands and events for completeness and parses them
 * @author drak3
 */
class ResponseHandler implements \devmx\Teamspeak3\Query\Transport\ResponseHandlerInterface
{
    /**
     * The Length of the message sent by a common query on connect
     */

    const WELCOME_LENGTH = 150;

    /**
     * A string included in the welcomemessage
     */
    const WELCOME_IDENTIFY = "TS3";

    /**
     * The error id returned on success 
     */
    const ID_OK = 0;

    /**
     * The errormessage returned on success
     */
    const MSG_OK = "ok";

    /**
     * The string between two responses/events
     */
    const SEPERATOR_RESPONSE = "\n";

    /**
     * The string between two items (List of data)
     */
    const SEPERATOR_ITEM = "|";

    /**
     * The string between two data packages (e.g. key/value pair)
     */
    const SEPERAOR_DATA = " ";

    /**
     * The string between a key/value pair
     */
    const SEPERATOR_KEY_VAL = "=";
    
    /**
     * When you got banned but reconnect to a server, the server throws an error with this error-id 
     */
    const BAN_ERROR_ID = 3329;
    
    /**
     * When you got banned in action, the server throws an error with this error-id
     */
    const FLOOD_BAN_ERROR_ID = 3331;
    
    /**
     * The event prefix 
     */
    const EVENT_PREFIX = 'notify';

    /**
     * The chars masked by the query and their replacements
     * @var Array 
     */
    protected $unEscapeMap = Array(
        "\\\\" => "\\",
        "\\/" => "/",
        "\\n" => "\n",
        "\\s" => " ",
        "\\p" => "|",
        "\\a" => "\a",
        "\\b" => "\b",
        "\\f" => "\f",
        "\\r" => "\r",
        "\\t" => "\t",
        "\\v" => "\v",
    );

    /**
     * The regular expression to describe the error block of a response
     * @var string
     */
    protected $errorRegex = "/error id=[0-9]* msg=[a-zA-Z\\\\]*/";
    
    /**
     * The regular expression to describe the extra_message of a (flood)ban response
     * @var string
     */
    protected $floodBanRegex = "/you may retry in (\d*) seconds/i";
    
    /**
     * Replaces all masked characters with their regular replacements (e.g. \\ with \)
     * uses $unEscapeMap
     * @param string $string
     * @return string the unmasked string 
     */
    public function unescape($string)
    {
        $string = strtr($string, $this->unEscapeMap);
        return $string;
    }

    /**
     * Parses a response coming from the query for a given command
     * Event notifications occured before sending the command are parsed too
     * @param Command $cmd the command which caused this response
     * @param string $raw the raw query response
     * @return \devmx\Teamspeak3\Query\Response[] in form Array('response' => $responseObject, 'events' => Array($eventobject1,$eventobject2));  
     */
    public function getResponseInstance(Command $cmd, $raw)
    {
        $response = Array('response' => NULL, 'events' => Array());
        $parsed = Array();

        $raw = \trim($raw, "\r\n");
        $parsed = \explode(static::SEPERATOR_RESPONSE, $raw);

        //find error message
        foreach($parsed as $key=>$value) {
            if($this->match($this->errorRegex, $value)) {
                $error = $value;
                unset($parsed[$key]);
                break;
            }
        }
        $data = '';
        foreach($parsed as $part) {
            if(substr($part, 0, strlen(static::EVENT_PREFIX)) === static::EVENT_PREFIX) {
                $response['events'][] = $this->parseEvent($part);
            }
            else {
                $data = $part;
            }
        }
        
        $response['response'] = $this->parseResponse($cmd, $error, $data);
        return $response;
    }
    
    /**
     * Parses Events coming from the query
     * @param string $raw the raw response
     * @return \devmx\Teamspeak3\Query\Event[] all events found in the raw string
     */
    public function getEventInstances($raw)
    {
        $ret = array();
        $events = \explode(static::SEPERATOR_RESPONSE, rtrim($raw));
        foreach ($events as $rawevent)
        {
            $ret[] = $this->parseEvent($rawevent);
        }
        return $ret;
    }
    
    /**
     * Checks if the given string is a complete event.
     * currently this just checks if the string is non empty, and if it ends with the response seperator (usually "\n")
     * @param string $raw
     * @return boolean
     */
    public function isCompleteEvent($raw)
    {
        $this->checkForBan($raw);
        if ($raw !== '' && $raw[strlen($raw)-1] === static::SEPERATOR_RESPONSE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Checks if the given string is a complete response
     * The function is doing that by checking for a error section
     * @param string $raw
     * @return boolean 
     */
    public function isCompleteResponse($raw)
    {
        $this->checkForBan($raw);
        if ($this->match($this->errorRegex, $raw) && $raw[strlen($raw)-1] == "\n")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Returns the length of a normal welcomemessage
     * @return int
     */
    public function getWelcomeMessageLength()
    {
        return static::WELCOME_LENGTH;
    }
    
    /**
     * Checks if the provided string is the identifyer of a valid Teamspeak3-Server
     * @param string $ident
     * @return boolean
     */
    public function isValidQueryIdentifyer($ident)
    {
        return \rtrim($ident) == static::WELCOME_IDENTIFY;
    }

    /**
     * Parses a response (no events in it) for a given command
     * Builds up a response object
     * @param Command $cmd the command which caused this response
     * @param string $error the error message
     * @param string $data the response data
     * @return \devmx\Teamspeak3\Query\Response 
     */
    protected function parseResponse(Command $cmd, $error, $data='')
    {
        $parsedError = $this->parseData($error);
        $errorID = $parsedError[0]['id'];
        $errorMessage = $parsedError[0]['msg'];

        if ($data !== '')
        {
            $items = $this->parseData($data);
        }
        else
        {
            $items = Array();
        }

        return new \devmx\Teamspeak3\Query\CommandResponse($cmd, $items, $errorID, $errorMessage, $parsedError[0]);
    }

    /**
     * Parses a single event
     * @param string $event
     * @return \devmx\Teamspeak3\Query\Event 
     */
    protected function parseEvent($event)
    {
        $reason = '';
        $event = explode(static::SEPERAOR_DATA, $event, 2);
        $reason = $this->parseValue($event[0]); //the eventtype or eventreason is a single word at the beginnning of the event
        $event = $event[1];
        $data = $this->parseData($event); //the rest is a single block of data

        return new \devmx\Teamspeak3\Query\Event($reason, $data);
    }

    /**
     * parses the data of a event or response.
     * First splits up in blocks (seperated by '|')
     * then in data packages (or key value pairs) (sperated by ' ')
     * if the datapackage is a key value pair it split this at '='
     * @param string $data
     * @return array in form Array(0=>Array('key0'=>'val0','key1'=>'val1'), 1=>Array('key0'=>'val2','key1','val3'));
     */
    protected function parseData($data)
    {
        $parsed = Array();
        $items = \explode(static::SEPERATOR_ITEM, $data); //split up into single lists or blocks
        foreach ($items as $itemkey => $item)
        {
            $keyvals = explode(static::SEPERAOR_DATA, $item); //split up into data items or keyvalue pairs
            foreach ($keyvals as $keyval)
            {
                $keyval = explode(static::SEPERATOR_KEY_VAL, $keyval, 2); //parses key value pairs
                if (\trim($keyval[0]) === '')
                {
                    continue;
                }
                $keyval[1] = isset($keyval[1]) ? $keyval[1] : null;
                $parsed[$itemkey][$keyval[0]] = $this->parseValue($keyval[1]);
            }
        }
        return $parsed;
    }

    /**
     * Parses a value from the query
     * detects the following types:
     * int,boolean,null and string, where strings get unescaped
     * @param string $val
     * @return string|int|boolean|null
     */
    protected function parseValue($val)
    {
        $val = \trim($val);
        if (ctype_digit($val))
        {
            return (int) $val;
        }
        if ($this->match("/^true$/Di", $val))
        {
            return true;
        }
        if ($this->match("/^false$/Di", $val))
        {
            return false;
        }
        if ($val === '' || $val === null)
        {
            return '';
        }

        return $this->unescape($val);
    }
    
    /**
     * Wrapper for preg_match to detect reliable
     * @param string $regex the regular expression
     * @param string $raw the string to match on
     * @return boolean|array if the match failed, false is returned, else the return of preg_match is returned
     * @throws Exception\RuntimeException 
     */
    private function match($regex, $raw) {
        $parsed = array();
        $matched = preg_match($regex, $raw, $parsed);
        if(  preg_last_error() !== PREG_NO_ERROR) {
            throw new Exception\RuntimeException('Error while using preg_match try to increase your pcre.backtrack_limit '. "\n". $raw, preg_last_error());
        }
        if($matched === 0) {
            return false;
        }
        return $parsed;
    }
    
    /**
     * Checks if the raw response contains a Ban message and throws Exception
     * @param string $raw
     * @throws \RuntimeException 
     */
    private function checkForBan($raw) {
        $parsed = $this->match($this->errorRegex, $raw);
        if($parsed) {
            $parsed = $this->parseData($raw);
            if(isset($parsed[0]['id']) && ($parsed[0]['id'] == static::BAN_ERROR_ID || $parsed[0]['id'] == static::FLOOD_BAN_ERROR_ID)) {
                throw new Exception\BannedException($this->getBanTime($parsed));
            }
        }
    }
    
    /**
     * Extracts the ban time out of the parsed error message
     * @param array $error the parsed error section
     * @return int|string the time to wait
     */
    private function getBanTime($error) {
        if(isset($error[0]['extra_message'])) {
            $time = $this->match($this->floodBanRegex, $error[0]['extra_message']);
            if($time !== false) {
                return (int) $time[1];
            }
        }
        return "<could'nt extract ban time>";
    }

}

?>
