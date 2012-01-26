<?php

namespace devmx\Teamspeak3\Query\Transport\Common;
use devmx\Teamspeak3\Query\Command;

 


/**
 * Test class for CommandTranslator.
 * Generated by PHPUnit on 2011-11-07 at 21:47:48.
 */
class CommandTranslatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CommandTranslator
     */
    protected $translator;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->translator = new CommandTranslator;

    }
    
    /**
     * tests if command with simple arguments is translated correctly
     */
    public function testTranslateArguments()
    {
        $cmd = new Command("use", Array("port"=>9987));
        $this->assertEquals("use port=9987\n",$this->translator->translate($cmd));
        
        $cmd = new Command("asdf", Array("foo"=>"bar", "asdf"=>123));
        $this->assertEquals("asdf foo=bar asdf=123\n", $this->translator->translate($cmd));
    }
    
    public function testTranslateMultipleArgumentValues() {
        $cmd = new Command("banclient", array("clid"=>array(1,2,3)));
        $this->assertEquals("banclient clid=1|clid=2|clid=3\n", $this->translator->translate( $cmd ));
    }
    
    public function testTranslateOptions() {
        $cmd = new Command("test", Array(), Array("foo"));
        $this->assertEquals("test -foo\n", $this->translator->translate($cmd));
        $cmd = new Command("test", Array(), Array("foo", "bar"));
        $this->assertEquals("test -foo -bar\n", $this->translator->translate($cmd));
    }
    
    public function testEscaping() {
        $this->assertEquals('test foo=\s\sasdf\p\p\s\s\s'."\n", $this->translator->translate(new Command("test", Array("foo"=>"  asdf||   "))));
    }
    
    public function testTranslateFullCommand() {
        $cmd = new Command("test", Array("foo"=>"bar", "asdf"=>Array("  ", "asdf")), Array("fnord"));
        $this->assertEquals('test foo=bar asdf=\s\s|asdf=asdf -fnord'."\n", $this->translator->translate( $cmd ));
    }
    
    /**
     * @dataProvider invalidCommandProvider
     */
    public function testIsValidInvalidCommands($cmd)
    {
        $this->assertFalse($this->translator->isValid($cmd));
    }
    
    /**
     *@dataProvider invalidCommandProvider
     *@expectedException \InvalidArgumentException 
     */
    public function testExceptionOnInvalidCommand($cmd) {
        $this->translator->translate($cmd);
    }
    
    
    public function invalidCommandProvider() {
        return Array (
            Array(new Command(new \DateTime())),
            Array(new Command("!asddf")),
            Array(new Command("asdf", array("foo" => "bar"), Array(false) )),
            Array(new Command("as df")),
            Array(new Command("foo", array("fo\\bar"=>"bar"))),
            Array(new Command("foo", array(), array("fo o"))),
            Array(new Command('foo', array('foo'=>array(new \DateTime(), 123.3)))),
            Array(new Command('foo', array('foo'=>new \DateTime()))),
        );
    }
    
    public function testBooleanToIntTranslation() {
        $cmd = new Command('foo', array('foo'=>true, 'bar'=>false));
        $this->assertEquals("foo foo=1 bar=0\n", $this->translator->translate($cmd));
    }

}

?>
