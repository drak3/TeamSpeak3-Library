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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Logging\Proxy;

/**
 *
 * @author drak3
 */
class MonologProxy implements \devmx\Teamspeak3\Query\Transport\Decorator\Logging\LoggingInterface
{
    protected $logger;
    
    protected $logLevelMap = array(
        self::LOGGING_LEVEL_INFO => \Monolog\Logger::INFO,
        self::LOGGING_LEVEL_NOTICE => \Monolog\Logger::INFO, //to support 1.0 too
        self::LOGGING_LEVEL_WARNING => \Monolog\Logger::WARNING,
        self::LOGGING_LEVEL_ERROR => \Monolog\Logger::ERROR,
        self::LOGGING_LEVEL_FATAL => \Monolog\Logger::CRITICAL
    );
    
    public function __construct(\Monolog\Logger $l) {
        $this->logger = $l;
    }
    
    public function addLog($message, $logLevel) {
        $level = isset($this->logLevelMap[$logLevel]) ? $this->logLevelMap[$logLevel] : \Monolog\Logger::INFO;
        $this->logger->addRecord($level, $message);
    }
}

?>
