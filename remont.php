<?php

require './vendor/autoload.php';

// $catList = [
//     ['name' => 'Tom', 'color' => 'red'],
//     ['name' => 'Bars', 'color' => 'white'],
//     ['name' => 'Jane', 'color' => 'Yellow'],
// ];

$data = SimpleXLSX::parse('input_data/remont.xlsx');

echo "<pre>";
// print_r($data->rows());
print_r(array_filter($data->rows(), function ($r) {
    $rrr['Об`єкт'] = $r[0];
    echo "<pre>";
    print_r($r[0]);
    echo "</pre>";
}));
echo "</pre>";

// $document = new \PHPExcel();

// $sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе

// $columnPosition = 0; // Начальная координата x
// $startLine = 2; // Начальная координата y

// // Вставляем заголовок в "A2" 
// $sheet->setCellValueByColumnAndRow($columnPosition, $startLine, 'Our cats');

// // Выравниваем по центру
// $sheet->getStyleByColumnAndRow($columnPosition, $startLine)->getAlignment()->setHorizontal(
//     PHPExcel_Style_Alignment::HORIZONTAL_CENTER
// );

// // Объединяем ячейки "A2:C2"
// $document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, $startLine, $columnPosition + 2, $startLine);

// // Перекидываем указатель на следующую строку
// $startLine++;

// // Массив с названиями столбцов
// $columns = ['№', 'Name', 'Color'];

// // Указатель на первый столбец
// $currentColumn = $columnPosition;

// // Формируем шапку
// foreach ($columns as $column) {
//     // Красим ячейку
//     $sheet->getStyleByColumnAndRow($currentColumn, $startLine)
//         ->getFill()
//         ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
//         ->getStartColor()
//         ->setRGB('4dbf62');

//     $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $column);

//     // Смещаемся вправо
//     $currentColumn++;
// }

// // Формируем список
// foreach ($catList as $key => $catItem) {
//     // Перекидываем указатель на следующую строку
//     $startLine++;
//     // Указатель на первый столбец
//     $currentColumn = $columnPosition;
//     // Вставляем порядковый номер
//     $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $key + 1);

//     // Ставляем информацию об имени и цвете
//     foreach ($catItem as $value) {
//         $currentColumn++;
//         $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $value);
//     }
// }

// $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel5');
// $objWriter->save("output_data/remont.xls");
