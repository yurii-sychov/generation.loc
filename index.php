<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require_once './vendor/autoload.php';

$data = SimpleXLSX::parse('input_data/data.xlsx');

$objects[0] = '';
$users_position[0] = '';
$users_name[0] = '';
$objects_address[0] = '';

foreach ($data->rows(1) as $key => $item) {
	array_push($objects, $item[1]);
	array_push($users_position, $item[2]);
	array_push($users_name, $item[3]);
	array_push($objects_address, $item[4]);
}

asort($objects);

if (!count($_GET)) {
	include('select.html');
}

if (isset($_GET['object'])) {
	if ($xlsx = SimpleXLSX::parse('input_data/' . $_GET['object'] . '.xlsx')) {
		$xlsx->setDateTimeFormat('d.m.Y');
		$tables = [];
		$sheets = count($xlsx->sheets());

		$expl_ved = [];
		foreach ($xlsx->sheetNames() as $key => $item) {
			if (count($xlsx->sheetNames()) - 1 !== $key) {
				array_push($expl_ved, $item);
			}
		}

		$table_name = [];
		for ($i = 0; $i < $sheets; $i++) {
			if ($xlsx->sheetName($i) === '1Т') {
				array_push($table_name, 'Силовий трансформатор 1Т');
			} else if ($xlsx->sheetName($i) === '2Т') {
				array_push($table_name, 'Силовий трансформатор 2Т');
			} else if ($xlsx->sheetName($i) === '3Т') {
				array_push($table_name, 'Силовий трансформатор 3Т');
			} else if ($xlsx->sheetName($i) === 'ТВП') {
				array_push($table_name, 'Трансформатори власних потреб');
			} else if ($xlsx->sheetName($i) === 'ВД') {
				array_push($table_name, 'Відділювачі');
			} else if ($xlsx->sheetName($i) === 'КЗ') {
				array_push($table_name, 'Короткозамикачі');
			} else if ($xlsx->sheetName($i) === 'Трансформатори струму') {
				array_push($table_name, 'Трансформатори струму');
			} else if ($xlsx->sheetName($i) === 'ТН') {
				array_push($table_name, 'Трансформатори напруги');
			} else if ($xlsx->sheetName($i) === 'ЕВ') {
				array_push($table_name, 'Експлуатаційна відомість');
			} else {
				array_push($table_name, $xlsx->sheetName($i));
			}

			$rows = [];
			foreach ($xlsx->rows($i) as $k => $val) {
				unset($val[0]);
				array_push($rows, $val);
			}
			array_push($tables, $rows);
		}

		$header_tvp = [
			'0' => 'Об`єкт',
			'1' => 'Вид обладнання',
			'2' => 'Приєднання',
			'3' => 'Тип',
			'4' => 'Зав. №',
			'5' => 'Рік випуску',
			'6' => 'Рік вводу',
			'7' => 'Uвн, кВ',
			'8' => 'Uнн, кВ',
			'9' => 'Uкз, %',
			'10' => 'Виробник',
			'11' => 'Приміткаsacasdcdsa',
		];

		$header_vikl = [
			'0' => 'Об`єкт',
			'1' => 'Вид обладнання',
			'2' => '№ ком.',
			'3' => 'Приєднання',
			'4' => 'Тип',
			'5' => 'Зав. №',
			'6' => 'Рік випуску',
			'7' => 'Рік вводу',
			'8' => 'Тип приводу',
			'9' => 'I, A',
			'10' => 'Iкз, кA',
			'11' => 'Примітка',
			'12' => 'Виробник',
		];

		include('passport.html');
	} else {
		echo SimpleXLSX::parseError();
		echo "\n";
	}
}

if (isset($_GET['remont'])) {
	if ($xlsx = SimpleXLSX::parse('input_data/remont.xlsx')) {
		include('remont.html');
	} else {
		echo SimpleXLSX::parseError();
		echo "\n";
	}
}
