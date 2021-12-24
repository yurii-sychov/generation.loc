<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

setlocale(LC_ALL, 'ru_RU.UTF-8');

require './vendor/autoload.php';

$ps = SimpleXLSX::parse('input_data/grafik.xlsx')->rows(1);

$stantions = [];

foreach ($ps as $key => $value) {
    if ($key > 0) {
        array_push($stantions, $value[0]);
    }
}

if (count($stantions) > 0 && empty($_GET['object'])) {
    include('grafik.html');
    exit;
}

$data = SimpleXLSX::parse('input_data/grafik.xlsx')->rows(0);

// echo "<pre>";
// print_r($data);
// echo "</pre>";

// $stantions = array_unique($stantions);

if (isset($_GET['object'])) {
    $data = array_filter($data, function ($v, $k) {
        return $v[0] == $_GET['object'];
    }, ARRAY_FILTER_USE_BOTH);
}



// Сортируем массив за видом оборудования
$vid  = array_column($data, 1);

array_multisort($vid, SORT_ASC, SORT_LOCALE_STRING, $data);

$grafik = [];
foreach ($data as $key => $value) {

    $array['ps'] = $value[0]; // Підстанція
    $array['vid'] = $value[1]; // Найменування обладнання
    $array['disp'] = $value[2]; // Диспетчерське найменування
    $array['place'] = $value[3]; // Місце встановлення
    $array['type'] = $value[4]; // Тип обладнання
    $array['class_napr'] = $value[5]; // Клас напруги
    $array['type_izol'] = $value[6]; // Тип ізоляції
    $array['year_made'] = $value[7]; // Рік випуску
    $array['year_expl'] = $value[8]; // Рік вводу
    $array['period_kr'] = (int) $value[9]; // Періодичність капітпальних ремонтів
    $array['period_pr'] = (int) $value[10]; // Періодичність повторних ремонтів
    $array['period_to'] = (int) $value[11]; // Періодичність технічного обслуговування
    $array['year_kr'] = (int) $value[12]; // Рік останього капітального ремонту
    $array['year_pr'] = (int) $value[13]; // Рік останього поточного ремонту
    $array['year_to'] = (int) $value[14]; // Рік останього технічного обслуговування
    $array['affiliation'] = $value[15]; // Приналежність до секції або комірки
    $array['connect'] = $value[16]; // Приєднання
    $array['cipher_kr'] = $value[17]; // Шифр КР
    $array['cipher_pr'] = $value[18]; // Шифр ПР
    $array['cipher_to'] = $value[19]; // Шифр ТО
    $array['note'] = $value[20]; // Примітка
    $year_kr = date('Y') + 1;
    $year_pr = date('Y') + 1;
    $year_to = date('Y') + 1;

    for ($i = 1; $i < 6; $i++) {
        if ($i == 1) {
            if (($array['period_kr'] + $array['year_kr']) == $year_kr || (($array['period_kr'] + $array['year_kr']) < $year_kr && ($array['period_kr'] + $array['year_kr']) > 0)) {
                $array['kr_' . $i] = 'КР';
                $year_pred_kr_1 = $year_kr;
            } else {
                $array['kr_' . $i] = '';
                $year_pred_kr_1 = 0;
            }
            if (($array['period_pr'] + $array['year_pr']) == $year_pr || (($array['period_pr'] + $array['year_pr']) < $year_kr && ($array['period_pr'] + $array['year_pr']) > 0)) {
                $array['pr_' . $i] = 'ПР';
                $year_pred_pr_1 = $year_pr;
            } else {
                $array['pr_' . $i] = '';
                $year_pred_pr_1 = 0;
            }
            if (($array['period_to'] + $array['year_to']) == $year_to || (($array['period_to'] + $array['year_to']) < $year_kr && ($array['period_to'] + $array['year_to']) > 0)) {
                $array['to_' . $i] = 'ТО';
                $year_pred_to_1 = $year_to;
            } else {
                $array['to_' . $i] = '';
                $year_pred_to_1 = 0;
            }
        }

        if ($i == 2) {
            if (($array['period_kr'] + $array['year_kr']) == $year_kr || ($array['period_kr'] + $year_pred_kr_1) == $year_kr) {
                $array['kr_' . $i] = 'КР';
                $year_pred_kr_2 = $year_kr;
            } else {
                $array['kr_' . $i] = '';
                $year_pred_kr_2 = 0;
            }
            if (($array['period_pr'] + $array['year_pr']) == $year_pr || ($array['period_pr'] + $year_pred_pr_1) == $year_pr) {
                $array['pr_' . $i] = 'ПР';
                $year_pred_pr_2 = $year_pr;
            } else {
                $array['pr_' . $i] = '';
                $year_pred_pr_2 = 0;
            }
            if (($array['period_to'] + $array['year_to']) == $year_to || ($array['period_to'] + $year_pred_to_1) == $year_to) {
                $array['to_' . $i] = 'ТО';
                $year_pred_to_2 = $year_to;
            } else {
                $array['to_' . $i] = '';
                $year_pred_to_2 = 0;
            }
        }

        if ($i == 3) {
            if (($array['period_kr'] + $array['year_kr']) == $year_kr || ($array['period_kr'] + $year_pred_kr_1) == $year_kr || ($array['period_kr'] + $year_pred_kr_2) == $year_kr) {
                $array['kr_' . $i] = 'КР';
                $year_pred_kr_3 = $year_kr;
            } else {
                $array['kr_' . $i] = '';
                $year_pred_kr_3 = 0;
            }
            if (($array['period_pr'] + $array['year_pr']) == $year_pr || ($array['period_pr'] + $year_pred_pr_1) == $year_pr || ($array['period_pr'] + $year_pred_pr_2) == $year_pr) {
                $array['pr_' . $i] = 'ПР';
                $year_pred_pr_3 = $year_pr;
            } else {
                $array['pr_' . $i] = '';
                $year_pred_pr_3 = 0;
            }
            if (($array['period_to'] + $array['year_to']) == $year_to || ($array['period_to'] + $year_pred_to_1) == $year_to || ($array['period_to'] + $year_pred_to_2) == $year_to) {
                $array['to_' . $i] = 'ТО';
                $year_pred_to_3 = $year_to;
            } else {
                $array['to_' . $i] = '';
                $year_pred_to_3 = 0;
            }
        }

        if ($i == 4) {
            if (($array['period_kr'] + $array['year_kr']) == $year_kr || ($array['period_kr'] + $year_pred_kr_1) == $year_kr || ($array['period_kr'] + $year_pred_kr_2) == $year_kr || ($array['period_kr'] + $year_pred_kr_3) == $year_kr) {
                $array['kr_' . $i] = 'КР';
                $year_pred_kr_4 = $year_kr;
            } else {
                $array['kr_' . $i] = '';
                $year_pred_kr_4 = 0;
            }
            if (($array['period_pr'] + $array['year_pr']) == $year_pr || ($array['period_pr'] + $year_pred_pr_1) == $year_pr || ($array['period_pr'] + $year_pred_pr_2) == $year_pr || ($array['period_pr'] + $year_pred_pr_3) == $year_pr) {
                $array['pr_' . $i] = 'ПР';
                $year_pred_pr_4 = $year_pr;
            } else {
                $array['pr_' . $i] = '';
                $year_pred_pr_4 = 0;
            }
            if (($array['period_to'] + $array['year_to']) == $year_to || ($array['period_to'] + $year_pred_to_1) == $year_to || ($array['period_to'] + $year_pred_to_2) == $year_to || ($array['period_to'] + $year_pred_to_3) == $year_to) {
                $array['to_' . $i] = 'ТО';
                $year_pred_to_4 = $year_to;
            } else {
                $array['to_' . $i] = '';
                $year_pred_to_4 = 0;
            }
        }

        if ($i == 5) {
            if (($array['period_kr'] + $array['year_kr']) == $year_kr || ($array['period_kr'] + $year_pred_kr_1) == $year_kr || ($array['period_kr'] + $year_pred_kr_2) == $year_kr || ($array['period_kr'] + $year_pred_kr_3) == $year_kr || ($array['period_kr'] + $year_pred_kr_4) == $year_kr) {
                $array['kr_' . $i] = 'КР';
                $year_pred_kr = $year_kr;
                $year_pred_kr_5 = $year_kr;
            } else {
                $array['kr_' . $i] = '';
                $year_pred_kr_5 = 0;
            }
            if (($array['period_pr'] + $array['year_pr']) == $year_pr || ($array['period_pr'] + $year_pred_pr_1) == $year_pr || ($array['period_pr'] + $year_pred_pr_2) == $year_pr || ($array['period_pr'] + $year_pred_pr_3) == $year_pr || ($array['period_pr'] + $year_pred_pr_4) == $year_pr) {
                $array['pr_' . $i] = 'ПР';
                $year_pred_pr = $year_pr;
                $year_pred_pr_5 = $year_pr;
            } else {
                $array['pr_' . $i] = '';
                $year_pred_pr_5 = 0;
            }
            if (($array['period_to'] + $array['year_to']) == $year_to || ($array['period_to'] + $year_pred_to_1) == $year_to || ($array['period_to'] + $year_pred_to_2) == $year_to || ($array['period_to'] + $year_pred_to_3) == $year_to || ($array['period_to'] + $year_pred_to_4) == $year_to) {
                $array['to_' . $i] = 'ТО';
                $year_pred_to = $year_to;
                $year_pred_to_5 = $year_to;
            } else {
                $array['to_' . $i] = '';
                $year_pred_to_5 = 0;
            }
        }

        $year_kr++;
        $year_pr++;
        $year_to++;
    }

    array_push($grafik, $array);
}

$group_vid = [];
$group_affiliation = [];
$group_connect = [];

if (isset($_GET['group']) && $_GET['group'] === 'vid') {
    foreach ($grafik as $item) {
        $item['group'] = $item['vid'];
        if (!array_key_exists($item['vid'], $group_vid)) {
            $group_vid[$item['vid']] = [];
        }
        $group_vid[$item['vid']][] = $item;
    }
    $group = $group_vid;
    ksort($group, SORT_LOCALE_STRING);
}

if (isset($_GET['group']) && $_GET['group'] === 'affiliation') {
    foreach ($grafik as $item) {
        $item['group'] = $item['affiliation'];
        if (!array_key_exists($item['affiliation'], $group_affiliation)) {
            $group_affiliation[$item['affiliation']] = [];
        }
        $group_affiliation[$item['affiliation']][] = $item;
    }
    $group = $group_affiliation;
    ksort($group, SORT_LOCALE_STRING);
}

if (isset($_GET['group']) && $_GET['group'] === 'connect') {
    foreach ($grafik as $item) {
        $item['group'] = $item['connect'];
        if (!array_key_exists($item['connect'], $group_connect)) {
            $group_connect[$item['connect']] = [];
        }
        $group_connect[$item['connect']][] = $item;
    }
    $group = $group_connect;
    ksort($group, SORT_LOCALE_STRING);
}

include('grafik.html');
