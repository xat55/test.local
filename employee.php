<pre><?php

// Подключение Faker (загружен через composer)
require_once "vendor/autoload.php";

// Подключение файла для однократного установление соединения с БД
require "data_base.php";

/**
* class Employee без возможности наследовать от него
*/
final class Employee
{  
  public $param = null;
  private $db;
  
  public function __construct()
  {
    // $this->create(); 
    // $this->fill();
    $this->db = DataBase::getInstance();
  }
  
  /**
   * Магический метод, обрабатывающий обращения к несуществующим свойствам объекта класса
   * и вызывающий на исполнение приватные методы класса
   */
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
    
    if ($property === 'namesFromWorkerDir') {
      return $this->getNamesFromWorkerDir($this->param);
    }
    
    if ($property === 'directoriesWorkerTable') {
      return $this->makeDirectoriesWorkerTable();
    }
    
    if ($property === 'andInsertFotoInDir') {
      return $this->getAndInsertFotoInDir();
    }
    
    return $this->$property;
  }
  
  /**
   * Метод реализует создание таблиц worker, cabinet, cabinet_worker
   */
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
    $this->db->sendingQuery($query);
    
    $query = "CREATE TABLE cabinet (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      num INT(6),
      floor INT(6),
      capacity INT(6)
    )";
    $this->db->sendingQuery($query);
    
    $query = "CREATE TABLE cabinet_worker (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
      worker_id INT(6) UNSIGNED,
      cabinet_id INT(6) UNSIGNED
    )";
    $this->db->sendingQuery($query);
  }
  
  /**
   * Метод заполняет таблицы 'worker', 'cabinet', 'cabinet_worker' данными
   */
  private function fill($numberOfEmployees = 10, $numCabinets = 7, $totalOffices = 20)
  {
    $db = $this->db;
    
    // Если нужна русская локализация, передать её параметром в метод create 'ru_RU'
    $faker = Faker\Factory::create();     
    
    // Заполняем таблицу 'worker'
    for ($i = 0; $i < $numberOfEmployees; $i ++) {    
      $firstName = $faker->userName;
      $tel = $faker->e164PhoneNumber;
      $address = $faker->streetAddress;
      $salary = $faker->numberBetween($min = 100, $max = 1000);
      $vkId = 'id'.$faker->numberBetween($min = 10000000, $max = 99999999);
      $tel = $faker->e164PhoneNumber;
      
      $query = "INSERT INTO worker 
      (name, tel, address, salary, vkId, photo) 
      VALUES 
      ('$firstName', '$tel', '$address', '$salary', '$vkId', '')";
      
      $db->sendingQuery($query);
    }
    
    // Заполняем таблицу 'cabinet'
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
  
  /**
   * Метод получает все данные из таблиц 'worker', 'cabinet'
   */
  private function getDataWorkerAndCabinetTables()
  {
    $query = "SELECT w.id AS id, w.name, w.tel, w.salary, w.address,  c.num, c.floor, c.capacity
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id";
    
    return $this->db->getData($query);
  }
  
  /**
   * Метод получает всех работников из кабинета с максимальной вместительностью
   */
  private function getWorkersWithMaxCapacityCabinet()
  {
    $query = "SELECT w.*
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE capacity = (SELECT MIN(capacity) FROM cabinet)";
    
    return $this->db->getData($query);
  }
  
  /**
   * Метод получает всех работников на заданном этаже
   */
  private function getWorkersOnFloor($floor)
  {
    $query = "SELECT w.*
    FROM worker AS w 
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE floor = '$floor'";
    
    return $this->db->getData($query);
  }
  
 /**
  * Метод получает работника на этаже с максимальной зарплатой
  */
  private function getWorkersMaxSalaryOnFloor($floor)
  {  
    // Запрос в разработке!!!
    $query = "SELECT w.*
    FROM worker AS w  
    LEFT JOIN cabinet_worker AS a 
    ON a.worker_id = w.id 
    LEFT JOIN cabinet AS c
    ON c.id = a.cabinet_id 
    WHERE salary = (SELECT MAX(salary) FROM worker) IN floor='$floor'";
    
    return $this->db->getData($query);
  }
  
  /**
   * Метод создает массив неповторяющихся чисел
   */
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
  
  /**
   * Метод создает на сервере папки вида 'worker_id'
   */
  private function makeDirectoriesWorkerTable()
  {
    $query = "SELECT id FROM worker";
    $data = $this->db->getData($query);
    
    foreach ($data as $key => $value) {
      $worker_id = $value['id'];
      mkdir("docs/worker.$worker_id");
    }
  }
  
  /**
   * Метод выбирает на сервере в папке 'worker.id' файлы и выводит их на экран
   * по условию наличия хотя бы одной латинской буквы и одной цифры
   */
  private function getNamesFromWorkerDir($id)
  {
    $path = "docs/worker.$id";
    $files = array_diff(scandir($path), ['.', '..']);
    
    foreach ($files as $key => $fileName) {
      
      if (preg_match('#[a-z][0-9]\.txt#', $fileName)) {
        echo "$fileName <br>";
      }
    }  
  }
  
  /**
   * Метод формирует массив ссылок пользователей Вконтакте, состоящих из 
   * значений vkId, извлеченных из таблицы 'worker'
   */
  private function getVkPaths()
  {
    $query = "SELECT vkId FROM worker";
    $data = $this->db->getData($query);
    $workerVkIdArr = [];
    
    foreach ($data as $value) {
      $workerVkIdArr[] = "https://vk.com/{$value['vkId']}";
    }
    
    return $workerVkIdArr;
  }
  
  /**
   * Метод получает массив HTML страниц, полученных с помощью библиотеки CURL.
   * Выбирает из каждого элемента массива ссылку на фото в профиле пользователя Вконтакте
   * и отправляет ее в базу данных в поле 'photo'
   */
  private function getAndInsertFotoInDir()
  {
    require_once 'phpQuery/phpQuery/phpQuery.php';
    
    $dataCurlArr = $this->getCurlData();
    $linksArr = [];
    
    foreach ($dataCurlArr as $dataCurl) {
      $pq = phpQuery::newDocument($dataCurl);
      $elem = $pq->find('img.page_avatar_img');
      $linksArr[] = $elem->attr('src');
    }    
      
    /**
     * Раскомментируйте код на стр. 324-331, если хотите получить массив ссылок с помощью регулярки.
     * И закоментируйте код на стр. 271, 236-280.
     */
    // foreach ($dataCurlArr as $dataCurl) {
    //   $reg = '#<img\s+class="page_avatar_img"\s+src="(.+?)"#su';
    //   preg_match_all($reg, $dataCurl, $matches);
    //   $linksArr[] = $matches[1][0];
    // }  
    $db = $this->db;
    
    foreach ($linksArr as $key => $link) {  
      $id = ++$key;
      $query = "UPDATE worker SET photo='$link' WHERE id='$id'";
      $db->sendingQuery($query);
    }
  }
  
  /**
   * Метод возвращает массив HTML страниц, полученных с помощью библиотеки CURL
   */
  private function getCurlData()
  {
    $workerVkIdArr = $this->getVkPaths();
    $dataCurlArr = [];
    $curl = curl_init(); // Инициализируем сеанс
    
    foreach ($workerVkIdArr as $workerVkId) { 
      curl_setopt($curl, CURLOPT_URL, $workerVkId); // Указываем адрес страницы
      curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // Автоматом идём по редиректам
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Не проверять SSL сертификат
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Не проверять Host SSL сертификата
      curl_setopt($curl, CURLOPT_URL, $workerVkId); // Куда отправляем
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Возвращаем, но не выводим на экран результат
      $result = curl_exec($curl);
      $dataCurlArr[] = iconv('windows-1251', 'utf-8', $result);
    }

    return $dataCurlArr;
  }  
}

$employee = new Employee;
$employee->param = 3;
print_r($employee->workersMaxSalaryOnFloor);