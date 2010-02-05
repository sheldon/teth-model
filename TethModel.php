<?php
class TethModel {
  public $data;
  function __construct($data = array()){ $this->data = $data; }
  public function __set($name,$value){ return $this->data[$name] = $value; }
  public function __get($name){ return $this->data[$name]; }
}?>