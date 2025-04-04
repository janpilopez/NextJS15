<?php
/* @var $this yii\web\View */

use app\models\SysEmpresa;
use app\models\SysRrhhEventos;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
$nombreEvento = SysRrhhEventos::find()->where(['idEvento'=>$evento])->one();

if($datos):
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= $nombreEvento->nombreEvento;
        
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
      //  $imagenLogo->setPath('logo/1391744064001/logo_reporte.jpg');
        $imagenLogo->setPath('logo/'.$empresa->ruc.'/'.$empresa->logo);
        $imagenLogo->setCoordinates('B2');
        $imagenLogo->setWidthAndHeight(260,200);
        $imagenLogo->setWorksheet($hojita);

        $hojita->setCellValue('C3', "LISTADO ".$nombreEvento->nombreEvento);
        $hojita->getStyle('C3')->getFont()->setSize(15);
        $hojita->getStyle('C3')->getFont()->setBold(true);
        $hojita->mergeCells('C3:G3');
        $hojita->getStyle('C3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $hojita->setCellValue('B7', "Área");
        $hojita->getStyle('B7')->getFont()->setSize(12);
        $hojita->getStyle('B7')->getFont()->setBold(true);
        $hojita->getStyle('B7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('C7', "Departamento");
        $hojita->getStyle('C7')->getFont()->setSize(12);
        $hojita->getStyle('C7')->getFont()->setBold(true);
        $hojita->getStyle('C7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('D7', "Cédula");
        $hojita->getStyle('D7')->getFont()->setSize(12);
        $hojita->getStyle('D7')->getFont()->setBold(true);
        $hojita->getStyle('D7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('E7', "Nombres y Apellidos");
        $hojita->getStyle('E7')->getFont()->setSize(12);
        $hojita->getStyle('E7')->getFont()->setBold(true);
        $hojita->getStyle('E7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $hojita->setCellValue('F7', "Cargo");
        $hojita->getStyle('F7')->getFont()->setSize(12);
        $hojita->getStyle('F7')->getFont()->setBold(true);
        $hojita->getStyle('F7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
    
        
        $hojita->setAutoFilter("B7:F7");

        $i = 7;

        if ($datos):

            foreach($datos as $dat): 

                $i++;

                $hojita->setCellValue('B'.$i,  $dat['area']);
                $hojita->setCellValue('C'.$i,  $dat['departamento']);
                $hojita->setCellValue('D'.$i,  $dat['id_sys_rrhh_cedula']);
                $hojita->setCellValue('E'.$i,  $dat['nombres']);
                $hojita->setCellValue('F'.$i,  $dat['cargo']);

            endforeach;

        endif;
        
        
        foreach(range('B','F') as $columnID) {
            $hojita->getColumnDimension($columnID)->setAutoSize(true);
        }

        $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
       

        $nombreArchivo=$titulo.'.xlsx';
        
        $writer = new Xlsx($objPHPExcel);
        $writer->save($nombreArchivo);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
        exit;
        
        
 endif;
 ?>