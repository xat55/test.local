<pre><?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Подключение Faker (загружен через composer)
require "vendor/autoload.php";
// Подключение Faker
// require __DIR__ . '/vendor/autoload.php';
/**
* 
*/
class Employee
{
  public function __construct()
  {
    // $this->create(); 
    // $this->fill(); 
  }
  
  public function __get($property)
  {
    // return $this->$property;
  }
  
  public function getWorkersOnFloor($floor)
  {
    require "elems/init.php";
    
    $query = "SELECT w.*
                FROM worker AS w 
              LEFT JOIN cabinet_worker AS a 
                ON a.worker_id = w.id 
              LEFT JOIN cabinet AS c
                ON c.id = a.cabinet_id 
              WHERE floor='$floor'";
    
    $result = mysqli_query($link, $query) or die(mysqli_error($link));  
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    print_r($data);
  }
  
  private function create()
  {
    require "elems/init.php";
    
    $query = "CREATE TABLE worker (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(30),
      tel VARCHAR(30),
      address VARCHAR(50),
      salary INT(6),
      vkId VARCHAR(100),
      photo VARCHAR(30)
    )";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    var_dump($result);
    
    $query = "CREATE TABLE cabinet (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      num INT(6),
      floor INT(6),
      capacity INT(6)
    )";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    var_dump($result);
    
    $query = "CREATE TABLE cabinet_worker (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      
      worker_id INT(6) UNSIGNED,
      
      
      cabinet_id INT(6) UNSIGNED
      
    )";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    var_dump($result);
    // FOREIGN KEY (worker_id) REFERENCES cabinet(id),
    // FOREIGN KEY (cabinet_id) REFERENCES worker(id)
  }
  
  private function fill($numberOfEmployees = 10, $numCabinets = 7, $totalOffices = 20)
  {
    require "elems/init.php";
    
    // Если нужна русская локализация, передать её параметром в метод create
    $faker = Faker\Factory::create('ru_RU');     
    
    for ($i = 0; $i < $numberOfEmployees; $i ++) {      
      $firstName = $faker->firstName;
      $tel = $faker->e164PhoneNumber;
      $address = $faker->streetAddress;
      $salary = $faker->numberBetween($min = 100, $max = 1000);
      $vkId = $faker->uuid;
      $tel = $faker->e164PhoneNumber;
      
      $query = "INSERT INTO worker 
      (name, tel, address, salary, vkId, photo) 
      VALUES 
      ('$firstName', '$tel', '$address', '$salary', '$vkId', '')";
      
      $result = mysqli_query($link, $query) or die(mysqli_error($link));
      var_dump($result);
    }
    // ----------------------
    
    $arrCabinets = $this->checkinUniqNumber($numCabinets, $totalOffices);
    // print_r($arrCabinets);
    
    for ($i = 0; $i < $numCabinets; $i ++) {
      $num = $arrCabinets[$i];
      $floor = mt_rand(1, 10);
      $capacity = mt_rand(4, 7);
      
      $query = "INSERT INTO cabinet 
      (num, floor, capacity) 
      VALUES 
      ('$num', '$floor', '$capacity')";  
      
      $result = mysqli_query($link, $query) or die(mysqli_error($link));
      var_dump($result);
    }
    
    // -----------------------------------------------
    // Определим максимальную вместимость кабинета
    // $query = "SELECT MIN(capacity) AS minCapacity FROM cabinet";
    // $result = mysqli_query($link, $query) or die(mysqli_error($link));  
    // for ($capacity = []; $row = mysqli_fetch_assoc($result); $capacity[] = $row); 
    // $minCapacity = $capacity[0]['minCapacity'];
    
    $query = "SELECT id, capacity FROM cabinet";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));  
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    
    $cabinetArr = [];
    foreach ($data as $value) {
      $value['id'];
      $value['capacity'];
      for ($i=0; $i < $value['capacity'] ; $i++) {
        $cabinetArr[] = $value['id'];
      }
    }
    
    $query = "SELECT id FROM worker";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($workers = []; $row = mysqli_fetch_assoc($result); $workers[] = $row);
    
    // Заполняем таблицу 'cabinet_worker'
    for ($i = 0; $i < $numberOfEmployees; $i ++) { 
      $worker_id = $workers[$i]['id'];
      $cabinet_id = $cabinetArr[$i];
      
      $query = "INSERT INTO cabinet_worker (worker_id, cabinet_id) VALUES ('$worker_id', '$cabinet_id')";      
      $result = mysqli_query($link, $query) or die(mysqli_error($link));
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
}
$createTable = new Employee;
// $createTable->create();
// $createTable->fill();
$createTable->getWorkersOnFloor(1);



// $faker = Faker\Factory::create('ru_RU'); // Если нужен русская локализация, передать её параметром в метод create
// echo $faker->firstName;
// echo '<br>';
// echo $faker->streetAddress;
// echo $faker->uuid;
// echo '<br>';
// echo $faker->text;
