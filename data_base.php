<?php

/**
 * Класс DataBase (тип шаблона Singleton), который устанавливает соединение с базой данных
 * и осуществляет реализацию на единственность создания его экземпляра.
 */
class DataBase {
  
  private $db;
  static private $_ins = NULL;
  
  static public function getInstance() {
    
    if(self::$_ins instanceof self) {
      return self::$_ins;
    }
    
    return self::$_ins = new self;
  }
  
  private function __construct() {
    echo "<h4>Соединение с базой данных</h4>";
    $this->db = new mysqli('localhost','root','12345','test');
    
    if($this->db->connect_error) {
      throw new DbException("Ошибка соединения : ");
    }
    $this->db->query("SET NAMES 'UTF8'");
  }
  
  private function __clone() {
    
  }
  
  public function sendingQuery($query) {
    return $this->db->query($query);
  }
  
  public function getData($query) {
    $result = $this->db->query($query);
    
    for($i = 0; $i < $result->num_rows; $i++) {
      $row[] = $result->fetch_assoc();
    }
    // print_r($row);
    
    return $row;
  }
}
