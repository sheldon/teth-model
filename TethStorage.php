<?php
class TethStorage{
  public static $data;
  public static function save($data){
    $class = get_called_class();
    if($data instanceof TethModel) $data = $data->data;
    $class::$data[] = $data;
  }
}?>