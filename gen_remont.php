<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (empty($_POST)) {
    include('gen_remont.html');
    exit;
}

header('Content-Type: application/xlsx');

setlocale(LC_ALL, 'ru_RU.UTF-8');

require './vendor/autoload.php';

if ($_FILES['resources']['error'] === 0) {
    $resources = SimpleXLSX::parse($_FILES['resources']['tmp_name'])->rows(0);
}

if ($_FILES['estimates']['error'] === 0) {
    $estimates = SimpleXLSX::parse($_FILES['estimates']['tmp_name'])->rows(0);
}

if ($_FILES['grafik_year']['error'] === 0) {
    $grafik_year = SimpleXLSX::parse($_FILES['grafik_year']['tmp_name'])->rows(0);
}

$temp_array[] = [
    'subdivision' => 'Підрозділ',
    'repair_method' => 'Спосіб ремонту',
    'repair_type' => 'Вид ремонту',
    'ps' => 'Підстанція',
    'disp' => 'Диспетчерське найменування',
    'type' => 'Тип',
    'month' => 'Місяць',
    'cipher' => 'Шифр ремонту',
    'resource' => 'Ресурс',
    'quantity' => 'Кількість',
    'number_r3' => 'Номер R3',
    'vid_resource' => 'Вид ресурсу',
    'unit' => 'Одиниця виміру',
    'price' => 'Ціна без НДС, грн',
    'price_full' => 'Сума без НДС, грн',
    'type_estimate' => 'Тип по кошторису',
];

$subdivision = '';
if ($_POST['subdivision'] === 'sp') {
    $subdivision = 'СП';
} else if ($_POST['subdivision'] === 'sl') {
    $subdivision = 'СЛ';
} else if ($_POST['subdivision'] === 'srm') {
    $subdivision = 'СРМ';
} else if ($_POST['subdivision'] === 'srza') {
    $subdivision = 'СРЗА';
}

$repair_method = '';
if ($_POST['repair_method'] === 'repair_method_gs') {
    $repair_method = 'Господарський спосіб';
} else {
    $repair_method = 'Підрядний спосіб';
}

$repair_type = '';
if ($_POST['repair_type'] === 'repair_type_kr') {
    $repair_type = 'Капітальний ремонт';
} else if ($_POST['repair_type'] === 'repair_type_pr') {
    $repair_type = 'Поточний ремонт';
} else {
    $repair_type = 'Технічне обслуговування';
}

// echo "<pre>";
// print_r($grafik_year);
// echo "</pre>";
// exit;

foreach ($estimates as $key => $smeta) {
    foreach ($grafik_year as $key => $grafik) {
        if ($smeta[0] === $grafik[0]) {
            $array['subdivision'] = isset($subdivision) ? $subdivision : null;
            $array['repair_method'] = isset($repair_method) ? $repair_method : null;
            $array['repair_type'] = isset($repair_type) ? $repair_type : null;
            $array['ps'] = isset($grafik[1]) ? $grafik[1] : null;
            $array['disp'] = isset($grafik[2]) ? $grafik[2] : null;
            $array['type'] = isset($grafik[3]) ? $grafik[3] : null;
            $array['month'] = isset($grafik[4]) ? $grafik[4] : null;

            $array['cipher'] =  isset($smeta[0]) ? $smeta[0] : null;
            $array['resource'] =  isset($smeta[1]) ? $smeta[1] : null;
            $array['quantity'] =  isset($smeta[2]) ? $smeta[2] : null;
            foreach ($resources as $key => $resource) {
                if ($resource[0] === $array['resource']) {
                    $array['number_r3'] = isset($resource[1]) ? $resource[1] : null;
                    $array['vid_resource'] = isset($resource[2]) ? $resource[2] : null;
                    $array['unit'] = isset($resource[3]) ? $resource[3] : null;
                    $array['price'] = isset($resource[4]) ? $resource[4] : null;
                    $array['price_full'] = $array['quantity'] * $array['price'];
                    $array['type_estimate'] = isset($smeta[3]) ? $smeta[3] : null;
                    array_push($temp_array, $array);
                }
            }
        }
    }
}

// echo "<pre>";
// print_r($temp_array);
// echo "</pre>";
// exit;

$xlsx = new SimpleXLSXGen();
$xlsx->setDefaultFont('Times New Roman');
$xlsx->setDefaultFontSize(10);
$xlsx->addSheet($temp_array, 'Ремонтна програма ' . $subdivision);
$xlsx->downloadAs('grafik_' . date('Y-m-d_H_i_s') . '.xlsx');
exit();

// $xlsx = SimpleXLSXGen::fromArray( $temp_array )->downloadAs('grafik_'.date('Y-m-d_H_i_s').'.xlsx');
// $xlsx->saveAs('./output_data/grafik_'.date('Y-m-d_H_i_s').'.xlsx');

// header('Location: gen_remont.php');