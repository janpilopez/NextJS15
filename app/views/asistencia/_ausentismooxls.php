<?php
/* @var $this yii\web\View */

echo $this->render('funciones');

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        
        return $i[$this->colName] ;
    }
}
class FilterData {
    private $colName;
    private $value;
    
    function __construct($colName, $value) {
        $this->colName = $colName;
        $this->value = $value;
    }
    function getFilter($i) {
        return $i[$this->colName] == $this->value;
    }
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhMarcacionesEmpleados;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


 /*if($datos): */
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Resumen Asistencia Empleados";
        
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
        
        $hojita->setCellValue('B2', "Resumen Asistencia Empleados - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
        $hojita->getStyle('B2')->getFont()->setSize(15);
        $hojita->getStyle('B2')->getFont()->setBold(true);
        $hojita->mergeCells('B2:I2');
        $hojita->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('A5', "Area");
        $hojita->getStyle('A5')->getFont()->setSize(12);
        $hojita->getStyle('A5')->getFont()->setBold(true);
        $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('B5', "Departamento");
        $hojita->getStyle('B5')->getFont()->setSize(12);
        $hojita->getStyle('B5')->getFont()->setBold(true);
        $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('C5', "Identificación");
        $hojita->getStyle('C5')->getFont()->setSize(12);
        $hojita->getStyle('C5')->getFont()->setBold(true);
        $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D5', "Nombres");
        $hojita->getStyle('D5')->getFont()->setSize(12);
        $hojita->getStyle('D5')->getFont()->setBold(true);
        $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('E5', "Género");
        $hojita->getStyle('E5')->getFont()->setSize(12);
        $hojita->getStyle('E5')->getFont()->setBold(true);
        $hojita->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('F5', "Horas Laboradas");
        $hojita->getStyle('F5')->getFont()->setSize(12);
        $hojita->getStyle('F5')->getFont()->setBold(true);
        $hojita->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
       
        $hojita->setAutoFilter("A5:F5");
        
        $i = 5;

        if ($datos):
             
            $dataFilterIdSysRrhhCedula =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));  
           
            $con = 0;
            foreach ($dataFilterIdSysRrhhCedula as $index => $id_sys_rrhh_cedula):  
             $con+=1;
             $area = '';
             $deparamento = '';
             $nombres = '';
             $genero = '';
             $arrayData   = array_filter($datos, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
             $horas = '';
             $horasDecimal  = 0;
             foreach ($arrayData as $index => $row):
               
                 $area = $row['area'];
                 $deparamento = $row['departamento'];
                 $nombres = $row['nombres'];
                 $genero = $row['genero'];
                 
                 if ($row['entrada'] != null && $row['salida'] != null):
                 
                    $horas = getTotalhoras($row['entrada'], $row['salida']);
                 
                    if ($horas != '00:00:00'):
                         $horasDecimal += floatval(number_format(HorasToDecimal($horas),2, '.', ''));
                    endif;
                 
                 endif;
             
             endforeach;

                if ($horasDecimal > 0){
                    $i++;
                    $hojita->setCellValue('A'.$i,  $area);
                    $hojita->setCellValue('B'.$i,  $deparamento);
                    $hojita->setCellValue('C'.$i,  $id_sys_rrhh_cedula);
                    $hojita->setCellValue('D'.$i,  $nombres);
                    $hojita->setCellValue('E'.$i,  $genero == "M" ? "Masculino" : "Femenino");
                    $hojita->setCellValue('F'.$i,  DecimaltoHoras( number_format($horasDecimal, 2, '.', '')));
                }

            endforeach;

        endif;
        
        
        foreach(range('A','F') as $columnID) {
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
    
/* endif;?>*/ 
