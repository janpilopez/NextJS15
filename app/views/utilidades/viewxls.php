<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

if($model):

    $objPHPExcel =  new Spreadsheet();
    
    $titulo= "Utilidades ".$model->anio;
    $objPHPExcel->getProperties()
    ->setCreator("Gestion")
    ->setLastModifiedBy("Gestion")
    ->setTitle($titulo)
    ->setSubject($titulo);
   
    $hojita = $objPHPExcel->getActiveSheet();
    $hojita->setTitle($titulo);
    $hojita->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    $hojita->getPageMargins()->setRight(0.30);
    $hojita->getPageMargins()->setLeft(0.30);
   
    $imagenLogo = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $imagenLogo->setName('Logo');
    $imagenLogo->setDescription('pespesca');
    $imagenLogo->setPath('logo/'.$empresa->ruc.'/'.$empresa->logo);
    $imagenLogo->setCoordinates('A1');
    $imagenLogo->setWidthAndHeight(250,250);
    $imagenLogo->setWorksheet($hojita);
      
    $hojita->setCellValue('A7', "Empresa");
    $hojita->getStyle('A7')->getFont()->setSize(12);
    
    $hojita->setCellValue('B7', "Ruc");
    $hojita->getStyle('B7')->getFont()->setSize(12);
    
    $hojita->setCellValue('C7', "Identificación");
    $hojita->getStyle('C7')->getFont()->setSize(12);
    
    $hojita->setCellValue('D7', "Nombres");
    $hojita->getStyle('D7')->getFont()->setSize(12);
    
    $hojita->setCellValue('E7', "Genero");
    $hojita->getStyle('E7')->getFont()->setSize(12);
    
    $hojita->setCellValue('F7', "Estado");
    $hojita->getStyle('F7')->getFont()->setSize(12);
    
    $hojita->setCellValue('G7', "Días Laborados");
    $hojita->getStyle('G7')->getFont()->setSize(12);
    
    $hojita->setCellValue('H7', "Valor Empleado");
    $hojita->getStyle('H7')->getFont()->setSize(12);
    
    $hojita->setCellValue('I7', "No Cargas");
    $hojita->getStyle('I7')->getFont()->setSize(12);
    
    $hojita->setCellValue('J7', "Valor Carga");
    $hojita->getStyle('J7')->getFont()->setSize(12);
    
    $hojita->setCellValue('K7', "Tribunal");
    $hojita->getStyle('K7')->getFont()->setSize(12);
    
    $hojita->setCellValue('L7', "Total a Recibir");
    $hojita->getStyle('L7')->getFont()->setSize(12);
    
    $hojita->getStyle('A7:L7')->getFont()->setBold(true);
    $hojita->getStyle('A7:L7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setAutoFilter("A7:L7");
    
    $i = 7;
    
    foreach ($modeldet  as $data)
    {
        $i++;
        $hojita->setCellValue('A'.$i,  $data['razon_social']);
        $hojita->setCellValue('B'.$i,  $data['ruc']);
        $hojita->setCellValue('C'.$i,  $data['id_sys_rrhh_cedula']);
        $hojita->setCellValue('D'.$i,  $data['nombres']);
        $hojita->setCellValue('E'.$i,  $data['genero']);
        $hojita->setCellValue('F'.$i,  $data['estado'] == 'A' ? 'Activo': 'Inactivo');
        $hojita->setCellValue('G'.$i,  number_format($data['dias'], 2, '.', ','));
        $hojita->setCellValue('H'.$i,  number_format($data['uti_empleados'], 2, '.', ','));
        $hojita->setCellValue('I'.$i,  number_format($data['cargas_familiares']), 2, '.', ',');
        $hojita->setCellValue('J'.$i,  number_format($data['uti_cargas']), 2 , '.', ',');
        $hojita->setCellValue('K'.$i,  number_format($data['tribunal'], 2, '.', ','));
        $hojita->setCellValue('L'.$i,  number_format(($data['total_uti'] - $data['tribunal']), 4, '.', ','));  
    }
    
    $hojita->getStyle('G8:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('H8:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('I8:I'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('J8:J'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('K8:K'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('K8:L'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    
    foreach(range('A','L') as $columnID) {
        $hojita->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
    $nombreArchivo="Utilidades_".$model->anio.".xlsx";
    
    $writer = new Xlsx($objPHPExcel);
    $writer->save($nombreArchivo);
    $objPHPExcel->disconnectWorksheets();
    unset($objPHPExcel);
    header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
    exit;

endif;
?>
