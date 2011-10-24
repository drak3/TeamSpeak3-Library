<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3\Query\Transport\Common;

/**
 * 
 *
 * @author drak3
 */
class CommandTranslator implements \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface
{
    /**
     * Translates a command to its query-representation
     * @param \devmx\Teamspeak3\Query\Command $cmd
     * @return string the query representation 
     */
    public function translate( \devmx\Teamspeak3\Query\Command $cmd )
    {
        if ( !$this->isValid( $cmd ) )
        {
            throw new \InvalidArgumentException("Invalid command ".$cmd->getName());
        }

        $queryRepresentation = $cmd->getName() . " ";

        foreach ( $cmd->getParameters() as $name => $value )
        {
            $name = $this->escape( $name );
            if ( is_array( $value ) )
            {
                foreach ( $value as $param )
                {
                    $queryRepresentation .= $name . "=" . $this->escape( $param ) . "|";
                }
                $queryRepresentation = \rtrim( $queryRepresentation , "|" ) . " ";
            } else
            {
                $queryRepresentation .= $name . "=" . $this->escape( $value ) . " ";
            }
        }

        foreach ( $cmd->getOptions() as $name => $flag )
        {
            $queryRepresentation .= "-" . $this->escape( $name ) . " ";
        }
        $queryRepresentation = \rtrim( $queryRepresentation );
        $queryRepresentation .= "\n";
        return $queryRepresentation;

    }

    /**
     * Checks if the given command is valid
     * @param \devmx\Teamspea3\Query\Command $cmd
     * @return boolean
     */
    public function isValid( \devmx\Teamspeak3\Query\Command $cmd )
    {

        if ( !$this->isValidName( $cmd->getName() ) )
        {
            return FALSE;
        }

        if ( !$this->areValidOptions( $cmd->getOptions() ) )
        {
            return FALSE;
        }

        if ( !$this->areValidParams( $cmd->getParameters() ) )
        {
            return FALSE;
        }

        return TRUE;

    }

    /**
     * Escapes a value so it can be used on the commonQuery
     * @param $string $value
     * @return $string
     */
    protected function escape( $value )
    {
        $to_escape = Array ( "\\" , "/" , "\n" , " " , "|" , "\a" , "\b" , "\f" , "\n" , "\r" , "\t" , "\v" );
        $replace_with = Array ( "\\\\" , "\/" , "\\n" , "\\s" , "\\p" , "\\a" , "\\b" , "\\f" , "\\n" , "\\r" , "\\t" , "\\v" );
        return str_replace( $to_escape , $replace_with , $value );

    }

    /**
     * Checks if the Name is valid to send it to a CommonQuery
     * @param string $name
     * @return boolean
     */
    protected function isValidName( $name )
    {
        if ( !is_string( $name ) )
        {
            return FALSE;
        }
        if ( !preg_match( "/^[a-z_-]*$/iD" , $name ) )
        {
            return FALSE;
        }
        return TRUE;

    }

    /**
     * Returns if the Parameterlist is valid
     * @param array $params
     * @return boolean
     */
    protected function areValidParams( array $params )
    {
        foreach ( $params as $name => $param )
        {
            if ( !is_string( $name ) )
            {
                return FALSE;
            }
            foreach ( $param as $val )
            {
                if ( !is_string( (string) $val ) )
                {
                    return FALSE;
                }
             }
            
        }
        return TRUE;

    }

    /**
     * Returns if the optionslist is valid or not
     * @param array $options
     * @return bool
     */
    protected function areValidOptions( array $options )
    {
        foreach ( $options as $name => $option )
        {
            if ( !is_string( $name ) )
            {
                return FALSE;
            }
            if( !is_bool( $option) )
                return FALSE;
        }
        return TRUE;

    }
}

?>
