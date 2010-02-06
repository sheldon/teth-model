<?php
class TethCollection implements Iterator, ArrayAccess, Countable {
  public $model;
  public $collection;
  public $position = 0;

  function __construct($collection = null, $model = "TethModel"){
    $this->model = $model;
    $this->collection = $collection;
  }

  //Iterator methods
  public function rewind(){ $this->position = 0; }
  public function current(){ return new $this->model($this->collection[$this->position]); }
  public function key(){ return $this->position; }
  public function next(){ ++$this->position; }
  public function valid(){ return isset($this->collection[$this->position]); }

  //ArrayAccess methods
  public function offsetSet($offset, $value){ $this->collection[$offset] = $value; }
  public function offsetExists($offset){ return isset($this->collection[$offset]); }
  public function offsetUnset($offset){ unset($this->collection[$offset]); }
  public function offsetGet($offset){ return $this->offsetExists($offset) ? new $this->model($this->collection[$offset]) : null; }

  //Countable method
  public function count(){ return count($this->collection); }
}?>