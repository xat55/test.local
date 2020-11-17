<pre><?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Подключение Faker (загружен через composer)
require "vendor/autoload.php";

// Установление соединения с БД
require "data_base.php";

/**
* class Employee без возможности наследовать от него
*/
final class Employee
{  
  public $param = null;
  
  public function __construct()
  {
    // $this->create(); 
    // $this->fill(); 
  }
  
  // Магический метод, обрабатывающий обращения к несуществующим свойствам объекта класса
  // и вызывающий на исполнение приватные методы класса
  public function __get($property)
  {
    if ($property === 'dataWorkerAndCabinetTables') {
      return $this->getDataWorkerAndCabinetTables();
    }
    
    if ($property === 'workersWithMaxCapacityCabinet') {
      return $this->getWorkersWithMaxCapacityCabinet();
    }
    
    if ($property === 'workersOnFloor') {
      return $this->getWorkersOnFloor($this->param);
    }
    
    if ($property === 'workersMaxSalaryOnFloor') {
      return $this->getWorkersMaxSalaryOnFloor($this->param);
    }
    
    return $this->$property;
  }
  
  private function getDataWorkerAndCabinetTables()
  {
    $query = "SELECT w.id AS id, w.name, w.tel, w.salary, w.address,  c.num, c.floor, c.capacity
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id";

    return $query;
  }
  
  private function getWorkersWithMaxCapacityCabinet()
  {
    $query = "SELECT w.*
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE capacity = (SELECT MIN(capacity) FROM cabinet)";
    
    return $query;
  }
  
  private function getWorkersOnFloor($floor)
  {
    $query = "SELECT w.*
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE floor='$floor'";
    
    return $query;
  }
  
  private function getWorkersMaxSalaryOnFloor($floor)
  {
    $query = "SELECT w.*
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE floor='$floor' AND salary =(SELECT MAX(salary) FROM worker)";
    
    return $query;
  }
  
  private function create()
  {
    $query = "CREATE TABLE worker (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(30),
      tel VARCHAR(30),
      address VARCHAR(50),
      salary INT(6),
      vkId VARCHAR(100),
      photo VARCHAR(30)
    )";
    DataBase::getInstance()->sendingQuery($query);
    
    $query = "CREATE TABLE cabinet (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      num INT(6),
      floor INT(6),
      capacity INT(6)
    )";
    DataBase::getInstance()->sendingQuery($query);

    $query = "CREATE TABLE cabinet_worker (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
      worker_id INT(6) UNSIGNED,
      cabinet_id INT(6) UNSIGNED
    )";
    DataBase::getInstance()->sendingQuery($query);
  }
  
  private function fill($numberOfEmployees = 10, $numCabinets = 7, $totalOffices = 20)
  {
    $db = DataBase::getInstance();
    // Если нужна русская локализация, передать её параметром в метод create 'ru_RU'
    $faker = Faker\Factory::create();     
    
    for ($i = 0; $i < $numberOfEmployees; $i ++) {      
      // $firstName = $faker->firstName;
      $firstName = $faker->userName;
      $tel = $faker->e164PhoneNumber;
      $address = $faker->streetAddress;
      $salary = $faker->numberBetween($min = 100, $max = 1000);
      $vkId = $faker->uuid;
      $tel = $faker->e164PhoneNumber;
      
      $query = "INSERT INTO worker 
      (name, tel, address, salary, vkId, photo) 
      VALUES 
      ('$firstName', '$tel', '$address', '$salary', '$vkId', '')";
      
      $db->sendingQuery($query);
    }
    // ----------------------
    
    $arrCabinets = $this->checkinUniqNumber($numCabinets, $totalOffices);
    
    for ($i = 0; $i < $numCabinets; $i ++) {
      $num = $arrCabinets[$i];
      $floor = mt_rand(1, 10);
      $capacity = mt_rand(4, 7);
    
      $query = "INSERT INTO cabinet 
      (num, floor, capacity) 
      VALUES 
      ('$num', '$floor', '$capacity')";  
    
      $db->sendingQuery($query);
    }
    // -----------------------------------------------
    // Определим максимальную вместимость кабинета
    // $query = "SELECT MIN(capacity) AS minCapacity FROM cabinet";
    // $result = mysqli_query($link, $query) or die(mysqli_error($link));  
    // for ($capacity = []; $row = mysqli_fetch_assoc($result); $capacity[] = $row); 
    // $minCapacity = $capacity[0]['minCapacity'];
    
    $query = "SELECT id, capacity FROM cabinet";
    $data = $db->getData($query);
    
    $cabinetArr = [];
    
    foreach ($data as $value) {
      $value['id'];
      $value['capacity'];
      for ($i=0; $i < $value['capacity'] ; $i++) {
        $cabinetArr[] = $value['id'];
      }
    }
    $query = "SELECT id FROM worker";
    $workers = $db->getData($query);
    
    // Заполняем таблицу 'cabinet_worker'
    for ($i = 0; $i < $numberOfEmployees; $i ++) { 
      $worker_id = $workers[$i]['id'];
      $cabinet_id = $cabinetArr[$i];
    
      $query = "INSERT INTO cabinet_worker (worker_id, cabinet_id) VALUES ('$worker_id', '$cabinet_id')";      
      $db->sendingQuery($query);
    }
  }
  
  private function checkinUniqNumber($numCabinets, $totalOffices)
  {
    /*  Если число запрошенных кабинетов больше общего количества кабинетов  */
    if ($numCabinets > $totalOffices) {
      return false;
    } 
    
    $arrNums = [];
    
    for ($i = 1; $i <= $numCabinets; $i ++) {
      $num = random_int(1, $totalOffices);
      
      if (in_array($num, $arrNums)) {
        $i--;
        continue;
      } else {
        array_push($arrNums, $num);
      }
    }
    
    return $arrNums;
  }
  
  public function makeDirectoriesWorkerTables()
  {
    $db = DataBase::getInstance();
    $query = "SELECT id FROM worker";
    $data = $db->getData($query);
    
    foreach ($data as $key => $value) {
      $worker_id = $value['id'];
      mkdir("docs/worker.$worker_id");
    }
    // foreach ($data[0] as $fieldName => $value) {
    // }
      // mkdir("docs/worker.1");
  }
  
  public function getNames()
  {
    // $db = DataBase::getInstance();
    // $query = "SELECT name FROM worker ORDER BY name";
    // $data = $db->getData($query);
    // print_r($data);
    // 
    // foreach ($data as $key => $value) {
    //   $worker_name = $value['name'].'.txt';
    //   // echo "$worker_name <br>";
    //   file_put_contents("docs/worker.name/$worker_name", '');
    //   // mkdir("docs/worker.name/$fileName");
    // }
    
    $files = array_diff(scandir('docs/worker.name'), ['.', '..']);
    print_r($files);
    // // preg_match('#^[a-zA-z0-9]+/.txt$#', $fileName);
    // 
    foreach ($files as $key => $fileName) {
    
      if (preg_match('#[a-z]+[0-9]+\.txt#', $fileName)) {
        echo "$fileName <br>";
      }
    }
    
    
  }
}

$employee = new Employee;
$employee->getNames();

// $db = DataBase::getInstance();


