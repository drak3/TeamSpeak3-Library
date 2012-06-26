<?php

namespace devmx\Teamspeak3\Query\Exception;

require_once dirname( __FILE__ ) . '/../../../../../../src/devmx/Teamspeak3/Query/Exception/InvalidServerException.php';

/**
 * Test class for InvalidServerException.
 * Generated by PHPUnit on 2012-06-24 at 16:53:03.
 */
class InvalidServerExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation() {
        $e = new InvalidServerException('asdf');
        $this->assertInstanceOf('devmx\Teamspeak3\Query\Exception\ExceptionInterface', $e);
    }
}

?>
