<?php
class TethStorage implements Iterator, ArrayAccess, Countable {
  public static $data;
  public $collection;
  public $position = 0;
  public $filters= array();
  
  function __construct($collection = null){ $this->collection = $collection; }
  
  //Static methods
  public static function save($data){
    $class = get_called_class();
    if($data instanceof TethModel) $data = $data->data;
    $class::$data[] = $data;
  }
  
  public static function get(){
    $class = get_called_class();
    $obj = new $class;
    if(func_num_args() > 0) $obj->collection = func_get_arg(0);
    return $obj;
  }

  //Data access methods
  public function filter($field, $value, $operator="="){
    $this->filters[] = array($field, $value, $operator);
    return $this;
  }

  //Iterator methods
  public function rewind(){ $this->position = 0; }
  public function current(){ return $this->offsetGet($this->position); }
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