<?php
class TethStorage implements Iterator, ArrayAccess, Countable {
  public static $data;
  public $collection;
  public $position = 0;
  public $filters= array();
  public $operators = array(
    "="=>"==",
    "~"=>"regex_filter",
    "%"=>"substring_filter"
  );

  function __construct($collection = null){ $this->collection = $collection; }
  
  //Static methods
  public static function get($model = false){
    $class = get_called_class();
    $obj = new $class;
    if($model instanceof TethModel) $obj->filter($model);
    return $obj;
  }

  public static function save($data){
    $class = get_called_class();
    if($data instanceof TethModel){
      $data->data["teth_class"] = get_class($data);
      $data = $data->data;
    }
    $class::$data[] = $data;
  }
  
  //Data access methods
  public function filter($field, $value=null, $operator="="){
    if($field instanceof TethModel) $this->filters[] = array("field"=>"teth_class", "value"=>get_class($field), "operator"=>"=");
    else $this->filters[] = array("field"=>$field, "value"=>$value, "operator"=>$operator);
    return $this;
  }
  
  public function remove_filter($field, $value=null, $operator=null){
    foreach($this->filters as $key => $filter){
      if($filter['field'] == $field)
        if(
          ($value == null && $operator == null) ||
          ($filter['value'] == $value && $operator == null) ||
          ($filter['value'] == $value && $filter['operator'] == $operator)
        ) unset($this->filters[$key]);
    }
    return $this;
  }

  public function or_operation($filter, $row){
    $max_num = max(count($filter['field']),count($filter['value']),count($filter['operator']));
    foreach(array("field","value","operator") as $j){
      $new_filter[$j] = false;
      if(!is_array($filter[$j])) $filter[$j] = array($filter[$j]);
    }
    for ($i=0; $i < $max_num; $i++) {
      foreach(array("field","value","operator") as $j)
        if($filter[$j][$i]) $new_filter[$j] = $filter[$j][$i];
      if($this->filter_match($new_filter,$row)) return true;
    }
  }

  public function filter_match($filter, $row){
    $left = $row[$filter["field"]];
    $right = $filter["value"];
    $mapped = $this->operators[$filter["operator"]];
    if($mapped && method_exists($this,$mapped)){
      if($this->{$mapped}($left, $right)) return true;
    }else{
      if(!($operator = $mapped)) $operator = $filter["operator"];
      if(eval("return \$left $operator \$right;")) return true;
    }
  }
  
  public function all(){
    $class = get_class($this);
    foreach($class::$data as $row){
      foreach($this->filters as $filter){
        if(is_array($filter["field"]) || is_array($filter["value"]) || is_array($filter["operator"])){
          if(!$this->or_operation($filter, $row)) continue 2;
        }else{
          if(!$this->filter_match($filter, $row)) continue 2;
        }
        
      }
      $ret[] = $row;
    }
    $this->collection = $ret;
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