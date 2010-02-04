<?php
class TethModel {
  public $data = array();
  public function __set($name,$value){ return $this->data[$name] = $value; }
  public function __get($name){ return $this->data[$name]; }
}?>