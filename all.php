<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

setlocale(LC_ALL, 'ru_RU.UTF-8');

require './vendor/autoload.php';

// Подключаемся к MySQL
$mysqli = new mysqli("s1.thehost.com.ua", "repairppua", "Yuras0910", "repairppua");
$mysqli->set_charset('utf8mb4');

// printf("Успешно... %s\n", $mysqli->host_info);

$stantions = $mysqli->query("SELECT * FROM complete_renovation_objects")->fetch_all(MYSQLI_ASSOC);

$equipments = $mysqli->query("SELECT * FROM equipments")->fetch_all(MYSQLI_ASSOC);

$places = $mysqli->query("SELECT * FROM places")->fetch_all(MYSQLI_ASSOC);

$voltage_class = $mysqli->query("SELECT * FROM voltage_class")->fetch_all(MYSQLI_ASSOC);

$insulation_type = $mysqli->query("SELECT * FROM insulation_type")->fetch_all(MYSQLI_ASSOC);


$data = SimpleXLSX::parse('input_data/all/all.xlsx')->rows(0);

// echo "<pre>";
// print_r($data);
// echo "</pre>";

// $vid  = array_column($data, 1);

// $disp  = array_column($data, 2);

// array_multisort($vid, SORT_ASC, SORT_LOCALE_STRING, $disp, SORT_ASC, SORT_LOCALE_STRING, $data);

$new_array = [];

foreach ($data as $key => $value) {
    $messages = [];
    if ($key > 0) {
        $specific_renovation_objects['subdivision_id'] = 1;
        $passports['subdivision_id'] = 1;

        $specific_renovation_objects['complete_renovation_object_id'] = '';
        foreach ($stantions as $k => $v) {
            if ($v['name'] === $value[0]) {
                $specific_renovation_objects['complete_renovation_object_id'] = $v['id'];
                $passports['complete_renovation_object_id'] = $v['id'];
            }
        }

        $specific_renovation_objects['name'] = $value[2];

        $specific_renovation_objects['equipment_id'] = '';
        foreach ($equipments as $k => $v) {
            if ($v['name'] === $value[1]) {
                $specific_renovation_objects['equipment_id'] = $v['id'];
            }
        }

        $specific_renovation_objects['voltage_class_id'] = '';
        foreach ($voltage_class as $k => $v) {
            if ($v['voltage'] === $value[8] || $v['voltage'] == ($value[8] * 1000)) {
                $specific_renovation_objects['voltage_class_id'] = $v['id'];
            }
        }

        $specific_renovation_objects['year_commissioning'] = NULL;
        $specific_renovation_objects['created_by'] = 1;
        $specific_renovation_objects['updated_by'] = 1;
        $specific_renovation_objects['created_at'] = date('Y-m-d H:i:s');
        $specific_renovation_objects['updated_at'] = date('Y-m-d H:i:s');

        // Проверяем есть ли данные в таблице specific_renovation_objects
        $sql = "SELECT * FROM specific_renovation_objects WHERE equipment_id={$specific_renovation_objects['equipment_id']} AND name='{$specific_renovation_objects['name']}' AND complete_renovation_object_id='{$passports['complete_renovation_object_id']}'";
        $row = $mysqli->query($sql)->num_rows;

        if (!$row) {
            // Вставляем данные в таблицу specific_renovation_objects
            $sql = "INSERT INTO `specific_renovation_objects` (`id`, `subdivision_id`, `complete_renovation_object_id`, `name`, `year_commissioning`, `equipment_id`, `voltage_class_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, '{$specific_renovation_objects['subdivision_id']}', '{$specific_renovation_objects['complete_renovation_object_id']}', '{$specific_renovation_objects['name']}', NULL, {$specific_renovation_objects['equipment_id']}, '{$specific_renovation_objects['voltage_class_id']}', '{$specific_renovation_objects['created_by']}', '{$specific_renovation_objects['updated_by']}', '{$specific_renovation_objects['created_at']}', '{$specific_renovation_objects['updated_at']}')";
            $mysqli->query($sql);
            $specific_renovation_object_id = $mysqli->insert_id;
            $messages['specific_renovation_objects'] = 'Доданий запис з id=' . $specific_renovation_object_id . ' в таблицю specific_renovation_objects!';

            for ($i = 1; $i <= 3; $i++) {
                $query = "INSERT INTO `schedules` (`id`, `specific_renovation_object_id`, `type_service_id`, `periodicity`, `year_last_service`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, '$specific_renovation_object_id', '$i', NULL, NULL, 0, '{$specific_renovation_objects['created_by']}', '{$specific_renovation_objects['updated_by']}', '{$specific_renovation_objects['created_at']}', '{$specific_renovation_objects['updated_at']}')";
                $mysqli->query($query);
            }
            $messages['schedules'] = 'Додані 3 записи в таблицю schedules!';
        }

        if (isset($specific_renovation_object_id)) {
            // Проверяем снова есть ли данные в таблице specific_renovation_objects
            $sql = "SELECT * FROM specific_renovation_objects WHERE equipment_id={$specific_renovation_objects['equipment_id']} AND name='{$specific_renovation_objects['name']}' AND complete_renovation_object_id='{$passports['complete_renovation_object_id']}' AND id={$specific_renovation_object_id}";
            $row_new = $mysqli->query($sql)->num_rows;
        }

        if (isset($specific_renovation_object_id) && $row_new) {
            // После вставки данных в таблицу specific_renovation_objects получаем id и фрпмируем массив для вставки в таблицу passports
            // $passports['specific_renovation_object_id'] = $mysqli->insert_id;
            $passports['place_id'] = '';
            foreach ($places as $k => $v) {
                if ($v['name'] === $value[3]) {
                    $passports['place_id'] = $v['id'];
                }
            }

            $passports['insulation_type_id'] = '';
            foreach ($insulation_type as $k => $v) {
                if ($v['insulation_type'] === $value[9]) {
                    $passports['insulation_type_id'] = $v['id'];
                }
            }

            $passports['type'] = $value[4];
            $passports['number'] = $value[5];
            $passports['production_date'] = $value[6] ? $value[6] . '-12-30' : 'NULL';
            $passports['created_by'] = 1;
            $passports['updated_by'] = 1;
            $passports['created_at'] = date('Y-m-d H:i:s');
            $passports['updated_at'] = date('Y-m-d H:i:s');

            $sql = "INSERT INTO `passports` (`id`, `subdivision_id`, `complete_renovation_object_id`, `specific_renovation_object_id`, `place_id`, `insulation_type_id`, `type`, `production_date`, `number`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, '{$passports['subdivision_id']}', '{$passports['complete_renovation_object_id']}', '{$specific_renovation_object_id}', '{$passports['place_id']}', '{$passports['insulation_type_id']}', '{$passports['type']}', '{$passports['production_date']}', '{$passports['number']}', '{$passports['created_by']}', '{$passports['updated_by']}', '{$passports['created_at']}', '{$passports['updated_at']}')";
            $mysqli->query($sql);
            $messages['passport'] = 'Доданий запис в таблицю passport!';
        }

        array_push($new_array, ['specific_renovation_objects' => $specific_renovation_objects, 'passpports' => $passports, 'messages' => $messages]);
    }
}

// echo "<pre>";
// print_r($new_array);
// echo "</pre>";
header('Content-Type: application/json');
echo json_encode($new_array, JSON_UNESCAPED_UNICODE);
