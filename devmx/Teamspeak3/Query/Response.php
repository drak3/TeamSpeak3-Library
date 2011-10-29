<?php
declare(encoding="UTF-8");
namespace devmx\Teamspeak3\Query;

/**
 * The base class for all responses sent by the Query
 * A response acts like an array, so you can acces all items directly via the response object
 * @author drak3
 */
class Response implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * 
     * @var array of array of (String=>String) 
     */
    protected $items;
    /**
     * Returns all holded items
     * @return array of array of (String=>misc) 
     */
    public function getItems() { return $this->items;}
    
    public function setItems(array $items) {
        $this->items = $items;
    }
    
    /**
     * Returns a item of the response
     * @param string $index
     * @param misc $else
     * @return array | misc array of (String => misc) if set else $else
     */
    public function getItem($index,$else=Array()) {
        if(isset($this->items[$index])) 
            return $this->items[$index];
        else
            return $else;
    }
    
    /**
     * Returns the value of an item
     * @param int $index
     * @param string $name
     * @param misc $else
     * @return misc 
     */
    public function getValue($index, $name, $else=NULL) {
        if(isset($this->items[$index][$name]))
                return $this->items[$index][$name];
        else
            return $else;
    }
    
    /**
     * Converts the items array to an associative array with $key as key
     * @param string $key
     * @return array 
     */
    public function toAssoc( $key )
    {
        $assoc = Array();
        foreach($this->items as $val) {
            if(isset($val[$key])) {
                $assoc[$val[$key]] = $val;
            }
        }
        return $assoc;
    }
    
    //implementing \ArrayAccess, \Iterator and \Countable
    public function offsetSet($offset,$value) {
        
    }

    public function offsetExists($offset) {
     return isset($this->items[$offset]);
    }

    public function offsetUnset($offset) {

    }

    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    public function rewind() {
        reset($this->items);
    }

    public function current() {
        return current($this->items);
    }

    public function key() {
        return key($this->items);
    }

    public function next() {
        return next($this->items);
    }

    public function valid() {
        return $this->current() !== false;
    }   

    public function count() {
     return count($this->items);
    }
    
}

?>
