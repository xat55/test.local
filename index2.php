<pre><?php

require_once 'phpQuery/phpQuery/phpQuery.php';

$url = 'https://vk.com/id55437525';
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);

curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");

curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // Автоматом идём по редиректам
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Не проверять SSL сертификат
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Не проверять Host SSL сертификата
curl_setopt($curl, CURLOPT_URL, $url); // Куда отправляем
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Возвращаем, но не выводим на экран результат


$result = curl_exec($curl);
$result = iconv('windows-1251', 'utf-8', $result);
var_dump($result);
// if ($result === false) {
// echo "Ошибка CURL: " . curl_error($curl);
// } else {
// echo $result;
// }
// <img class="page_avatar_img"
$reg = '#<img\s+class="page_avatar_img"\s+src="(.+?)"#su';

if ($res = preg_match_all($reg, $result, $matches)) {
  echo "нашел ";
  // print_r($res);
  var_dump($matches);
} else {
  echo "нет";
}



// foreach ($dataCurlArr as $dataCurl) {
//   $reg = '#<img\s+class="page_avatar_img"\s+src="(.+?)"#su';
//   preg_match_all($reg, $dataCurl, $matches);
//   $linksArr[] = $matches;
// }    
// var_dump($linksArr);




// $res = 
// $url = 'https://vk.com/id55437525';
// $curl = curl_init();
// // Указываем адрес страницы
// curl_setopt($curl, CURLOPT_URL, $url);
// // Ответ сервера сохранять в переменную, а не на экран	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// // Переходить по редиректам
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// // Выполняем запрос:
// $result = curl_exec($curl);
// 
// // Отлавливаем ошибки подключения
// if ($result === false) {
//   echo "Ошибка CURL: " . curl_error($curl);
// } else {
//   echo $result;
// }
// // $str = '';
// $result = file_get_contents($url);
// // print_r($result);
// // <img class="page_avatar_img"
// if (preg_match('#<img\s+c#su', $result)) {
//   echo "нашел";
// } else {
//   echo "нет";
// }
// preg_match('#img class="page_avatar_img"#', $result);
// $pq = phpQuery::newDocument($result);
// // 
// $elem = $pq->find('.page_avatar_img');
// $attr = $elem->attr('src');
// // $text = $elem->html();
// 
// // print_r($elem);
// var_dump($attr);

// $elem1 = $elem->attr('.page_avatar_img');
// $attr = $elem1->attr('src');

// $faker = Faker\Factory::create();
// echo 'id'.$faker->numberBetween($min = 10000000, $max = 99999999);

