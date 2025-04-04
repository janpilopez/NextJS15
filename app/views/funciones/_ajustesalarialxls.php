<?php
/* @var $this yii\web\View */

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
 
        $titulo= "Informe Ajuste Salarial Año".$anio."";
        
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
        
        $hojita->setCellValue('C2', "Informe Ajuste Salarial Año ".$anio."");
        $hojita->getStyle('C2')->getFont()->setSize(15);
        $hojita->getStyle('C2')->getFont()->setBold(true);
        $hojita->mergeCells('C2:H2');
        $hojita->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('A5', "Cédula");
        $hojita->getStyle('A5')->getFont()->setSize(12);
        $hojita->getStyle('A5')->getFont()->setBold(true);
        $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('B5', "Apellidos y Nombres");
        $hojita->getStyle('B5')->getFont()->setSize(12);
        $hojita->getStyle('B5')->getFont()->setBold(true);
        $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('C5', "Área");
        $hojita->getStyle('C5')->getFont()->setSize(12);
        $hojita->getStyle('C5')->getFont()->setBold(true);
        $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D5', "Departamento");
        $hojita->getStyle('D5')->getFont()->setSize(12);
        $hojita->getStyle('D5')->getFont()->setBold(true);
        $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('E5', "Cargo");
        $hojita->getStyle('E5')->getFont()->setSize(12);
        $hojita->getStyle('E5')->getFont()->setBold(true);
        $hojita->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $hojita->setCellValue('F5', "Fecha Ingreso");
        $hojita->getStyle('F5')->getFont()->setSize(12);
        $hojita->getStyle('F5')->getFont()->setBold(true);
        $hojita->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('G5', "Fecha Sueldo Anterior");
        $hojita->getStyle('G5')->getFont()->setSize(12);
        $hojita->getStyle('G5')->getFont()->setBold(true);
        $hojita->getStyle('G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('H5', "Sueldo Anterior");
        $hojita->getStyle('H5')->getFont()->setSize(12);
        $hojita->getStyle('H5')->getFont()->setBold(true);
        $hojita->getStyle('H5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $hojita->setCellValue('I5', "Fecha Sueldo");
        $hojita->getStyle('I5')->getFont()->setSize(12);
        $hojita->getStyle('I5')->getFont()->setBold(true);
        $hojita->getStyle('I5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('J5', "Sueldo");
        $hojita->getStyle('J5')->getFont()->setSize(12);
        $hojita->getStyle('J5')->getFont()->setBold(true);
        $hojita->getStyle('J5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
       
        $hojita->setAutoFilter("A5:J5");
        
        $i = 5;

        if ($datos):

            foreach ($datos as $row){
                $sueldoAnt = getObtenerSueldoAnterior($row['id_sys_rrhh_cedula'],$row['fecha']);
                $i++;
                $hojita->setCellValue('A'.$i,  $row['id_sys_rrhh_cedula']);
                $hojita->setCellValue('B'.$i,  $row['nombres']);
                $hojita->setCellValue('C'.$i,  $row['area']);
                $hojita->setCellValue('D'.$i,  $row['departamento']);
                $hojita->setCellValue('E'.$i,  $row['cargo']);
                $hojita->setCellValue('F'.$i,  $row['fecha_ingreso']);
                $hojita->setCellValue('G'.$i,  $sueldoAnt['fecha']);
                $hojita->setCellValue('H'.$i,  number_format($sueldoAnt['sueldo'],2, ',', ''));
                $hojita->setCellValue('I'.$i,  $row['fecha']);
                $hojita->setCellValue('J'.$i,  number_format($row['sueldo'],2, ',', ''));
            }

        endif;
        
        
        foreach(range('A','J') as $columnID) {
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
    
        function getObtenerSueldoAnterior($cedula, $fecha){

            $db    = $_SESSION['db'];
                
            return   Yii::$app->$db->createCommand("[dbo].[ObtenerSuldoAnterior] @id_sys_rrhh_cedula = '{$cedula}', @fecha = '{$fecha}'")->queryOne();
        
        }
/* endif;?>*/ 
