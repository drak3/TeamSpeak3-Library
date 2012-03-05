<?php

namespace devmx\Teamspeak3\Query;


/**
 * Test class for CommandResponse.
 * Generated by PHPUnit on 2011-11-07 at 21:19:55.
 */
class CommandResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CommandResponse
     */
    protected $response;
    
    /**
     * @var Command
     */
    protected $command;
    
    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->command = new Command("asdf", Array("foo"=>"bar"), Array("fnord"));
        $this->response = new CommandResponse($this->command, Array(Array("foo"=>1, "bar"=>"asdf"), Array("foo"=>2, "bar"=>"fnord")),
                                            12, "error!!", array('extra_message'=>"you're dumb"));
    }


    
    public function testExtraErrorItems() {
        $cmd = new Command('foo');
        $response = new CommandResponse($cmd, array(array('foo'=>'bar')), 0, 'ok', array('extra_message'=>'nothing happened', 'failed_permid'=>123));
        $this->assertEquals('nothing happened', $response->getErrorValue('extra_message'));
        $this->assertEquals(123, $response->getErrorValue('failed_permid'));
        $this->assertEquals('sthelse', $response->getErrorValue('sdfsdfsdf', 'sthelse'));
    }
    
        
    public function testGetCommand()
    {
       $this->assertEquals($this->command, $this->response->getCommand());
    }

   public function testGetErrorID()
    {
        $this->assertEquals(12, $this->response->getErrorID());
    }

    
    public function testGetErrorMessage()
    {
       $this->assertEquals("error!!", $this->response->getErrorMessage());
    }

    
    public function testGetExtraMessage()
    {
        $this->assertEquals("you're dumb", $this->response->getExtraMessage());
    }

    
    public function testErrorOccured()
    {
        $this->assertTrue($this->response->errorOccured());
        $resp = new CommandResponse($this->command, Array(), 0, "ok");
        $this->assertFalse($resp->errorOccured());
    }

    /**
     * @expectedException \devmx\Teamspeak3\Query\Exception\CommandFailedException
     * @expectedExceptionMessage Command "asdf" caused error with message "error!!" and id 12. (Extra message: "you're dumb")
     */
    public function testToException()
    {
        $this->response->toException();
    }
    
    public function testHasErrorValue() {
        $this->assertTrue($this->response->hasErrorValue('extra_message'));
        $this->assertFalse($this->response->hasErrorValue('foobar'));
    }

}

?>
