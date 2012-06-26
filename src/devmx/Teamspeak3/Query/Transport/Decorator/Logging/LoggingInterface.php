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
namespace devmx\Teamspeak3\Query\Transport\Decorator\Logging;

/**
 * Provides a simple logging interface
 * @author Maximilian Narr 
 */
interface LoggingInterface
{
    /**
     * Info loglevel, should be used for additional data which might be nice to have for debugging 
     */
    const LOGGING_LEVEL_INFO = 0;
    
    /**
     * Notice loglevel, should be used for data which might be helpful for debugging 
     */
    const LOGGING_LEVEL_NOTICE = 1;
    
    /**
     * Warning loglevel, should be used to protocolate events which might have an impact on the apps stability 
     */
    const LOGGING_LEVEL_WARNING = 2;
    
    /**
     * Error loglevel, should be used to protocolate errors which are that critical that the the user have to take care, but don't leave the application in an undefined state   
     */
    const LOGGING_LEVEL_ERROR = 3;
    
    /**
     * Fatal loglevel, should be used to protocolate errors which leave the application in an undefined state 
     */
    const LOGGING_LEVEL_FATAL = 4;

    /**
     * Adds a log message
     * @param string $message Log message
     * @param int $logLevel Log level
     * @author Maximilian Narr 
     */
    public function addLog($message, $logLevel);
}

?>
