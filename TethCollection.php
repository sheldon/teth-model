<?php
class TethCollection implements Iterator, ArrayAccess, Countable {
  public $collection;
  public $position = 0;

  //Iterator methods
  public function rewind(){ $this->position = 0; }
  public function current(){ return $this->collection[$this->position]; }
  public function key(){ return $this->position; }
  public function next(){ ++$this->position; }
  public function valid(){ return isset($this->collection[$this->position]); }

  //ArrayAccess methods
  public function offsetSet($offset, $value){ $this->collection[$offset] = $value; }
  public function offsetExists($offset){ return isset($this->collection[$offset]); }
  public function offsetUnset($offset){ unset($this->collection[$offset]); }
  public function offsetGet($offset){ return $this->offsetExists($offset) ? $this->collection[$offset] : null; }

  //Countable method
  public function count(){ return count($collection); }
}?>