<?php
class TethCollection implements Iterator, ArrayAccess, Countable {
  public $collection;
  public $position = 0;

  function __construct($collection = null){ $this->collection = $collection; }

  public static function get(){
    $class = get_called_class();
    $obj = new $class;
    if(func_num_args() > 0) $obj->collection = func_get_arg(0);
    return $obj;
  }

  //Iterator methods
  public function rewind(){ $this->position = 0; }
  public function current(){ $this->offsetGet($this->position); }
  public function key(){ return $this->position; }
  public function next(){ ++$this->position; }
  public function valid(){ return isset($this->collection[$this->position]); }

  //ArrayAccess methods
  public function offsetSet($offset, $value){ $this->collection[$offset] = $value; }
  public function offsetExists($offset){ return isset($this->collection[$offset]); }
  public function offsetUnset($offset){ unset($this->collection[$offset]); }
  public function offsetGet($offset){
    if(!$this->offsetExists($offset)) return null;
    if(is_array($this->collection[$offset]) && ($class = $this->collection[$offset]["teth_class"]))
      unset($this->collection[$offset]["teth_class"]);
    else
      $class = Config::$settings['classes']['default_model']['class'];
    return new $class($this->collection[$offset]);
  }

  //Countable method
  public function count(){ return count($this->collection); }
}?>