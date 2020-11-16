<pre><?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
// $numCabinets =7;
$iter = 0;

function checkinUniqNumber($numCabinets)
{
  $arrNums = [];
  
  for ($i = 1; $i <= $numCabinets; $i ++) {
    $num = random_int(1, $numCabinets);
    
    if (in_array($num, $arrNums)) {
      $i--;
      continue;
    } else {
      array_push($arrNums, $num);
    }
  }
  
  return $arrNums;
}

print_r(checkinUniqNumber(7));
 echo "Число итераций: $iter";

// $faker = Faker\Factory::create('ru_RU'); // Если нужен русская локализация, передать её параметром в метод create
// echo $faker->firstName;
// echo '<br>';
// echo $faker->streetAddress;
// echo $faker->uuid;
// echo '<br>';
// echo $faker->text;
