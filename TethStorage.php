<?php
class TethStorage{
  public static $data;
  public static function get(){
    $class = get_called_class();
    $obj = new $class;
    if(func_num_args() > 0) $obj->collection = func_get_arg(0);
    return $obj;
  }
  public static function save($data){
    if($data instanceof TethModel) $data = $data->data;
    $this->data[] = $data;
  }
}?>