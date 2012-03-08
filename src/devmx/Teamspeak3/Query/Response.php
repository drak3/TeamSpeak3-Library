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
namespace devmx\Teamspeak3\Query;

/**
 * The base class for all responses sent by the Query
 * A response acts like an array, so you can acces all items directly via the response object
 * @todo remove the raw response
 * @author drak3
 */
class Response implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * The returned items
     * @var string[]
     */
    protected $items;
    
    /**
     * The raw response
     * @var string 
     */
    protected $raw;

    /**
     * Returns all holded items
     * @return mixed[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Sets the items
     * @param array $items 
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
    
    /**
     * Checks if the item with the given index is present
     * @param int $index 
     */
    public function hasItem($index) {
        return isset($this->items[$index]);
    }

    /**
     * Returns a item of the response
     * @param string $index
     * @param mixed $else
     * @return mixed[]|mixed array of (String => misc) if set else $else
     */
    public function getItem($index, $else = array())
    {
        if (isset($this->items[$index]))  {
            return $this->items[$index];
        }
        else {
            return $else;
        }
    }
    
    /**
     * Tests if a value is present
     * @param string $name the name of the value
     * @param int $itemIndex the index of the item to get the value from, defaults to 0
     * @return boolean 
     */
    public function hasValue($name, $itemIndex=0) {
        return isset($this->items[$itemIndex][$name]);
    }

    /**
     * Returns the value of an item
     * @param string $name the name of the value
     * @param int $itemIndex indicates of which item the value should be taken, defaults to 0
     * @param misc $else the value returned when there is no such value
     * @return misc the value
     */
    public function getValue($name, $itemIndex=0, $else = NULL)
    {
        if (isset($this->items[$itemIndex][$name])) return $this->items[$itemIndex][$name];
        else return $else;
    }

    /**
     * Converts the items array to an associative array with $key as key
     * @param string $key
     * @return array[] the assoc array in form string=>array
     */
    public function toAssoc($key)
    {
        $assoc = Array();
        foreach ($this->items as $val)
        {
            if (isset($val[$key]))
            {
                $assoc[$val[$key]] = $val;
            }
        }
        return $assoc;
    }
    
    /**
     * Sets the raw response
     * @deprecated
     * @param string $raw 
     */
    public function setRawResponse($raw) {
        $this->raw = $raw;
    }
    
    /**
     * Gets the raw response
     * @deprecated
     * @return string
     */
    public function getRawResponse() {
        return $this->raw;
    }

    //implementing \ArrayAccess, \Iterator and \Countable
    
    /**
     * Implementation of \ArrayAccess::offsetSet
     * single items couldn't be set
     * @param mixed $offset
     * @param mixed $value 
     */
    public function offsetSet($offset, $value)
    {
        
    }
    
    /**
     * Implementation of \ArrayAccess::offsetExists
     * You can test existance of items by supplying a int to offsetExists
     * When you give a string as $offset it checks the availability of a value with this name in the first item 
     * @param int|string $offset
     * @return boolean 
     */
    public function offsetExists($offset)
    {
        if(is_string($offset) && isset($this->items[0][$offset])) {
            return true;
        }
        return isset($this->items[$offset]);
    }
    
    /**
     * Implementation of \ArrayAccess::offsetUnset
     * offsets are readonly
     * @param mixed $offset 
     */
    public function offsetUnset($offset)
    {
        
    }
    
    /**
     * Implementation of \ArrayAccess::offsetExists
     * You get items by supplying a var which isn't a string (most likely ints) to offsetGet
     * When you give a string as $offset it returns the value with the given name of the first item
     * @param int|string|mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if(is_string($offset) && isset($this->items[0][$offset])) {
            return $this->items[0][$offset];
        }
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
    
    /**
     * Implementation of \Iterator 
     */
    public function rewind()
    {
        reset($this->items);
    }
    
    /**
     * Implementation of \Iterator
     * @return array 
     */
    public function current()
    {
        return current($this->items);
    }
    
    /**
     * Implementation of \Iterator
     * @return int
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Implementation of \Iterator
     * @return array
     */
    public function next()
    {
        return next($this->items);
    }
    
    /**
     * Implementation of \Iterator
     * @return boolean
     */
    public function valid()
    {
        return $this->current() !== false;
    }
    
    /**
     * Implementation of \Countable
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

}

?>
