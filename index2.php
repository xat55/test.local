<?php
// Подключение Faker (загружен через composer)
require_once "vendor/autoload.php";
# Load Fakers own autoloader
// require_once 'phpQuery/phpQuery/phpQuery.php';
require_once 'phpQuery-onefile.php';

$curl = curl_init();
$url = 'https://vk.com/id55437525';
//Указываем адрес страницы
curl_setopt($curl, CURLOPT_URL, $url);
//Ответ сервера сохранять в переменную, а не на экран	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//Переходить по редиректам
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
//Выполняем запрос:
$result = curl_exec($curl);

//Отлавливаем ошибки подключения
if ($result === false) {
  echo "Ошибка CURL: " . curl_error($curl);
} else {
  echo $result;
}
// $str = '';

$pq = phpQuery::newDocument($result);

$elem = $pq->find('#elem');
$text = $elem->html();
print_r($pq);

// $faker = Faker\Factory::create();
// echo 'id'.$faker->numberBetween($min = 10000000, $max = 99999999);

