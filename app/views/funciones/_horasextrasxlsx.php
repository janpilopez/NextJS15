<?php
/* @var $this yii\web\View */

use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosNovedades;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysAdmCcostos;
class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
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

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];


$tipo = '';

if($periodo == 1 ):

$tipo = 'Quincenal';

elseif($periodo == 2 ) :

$tipo = 'Mensual';

elseif($periodo ==  90):

$tipo = 'Beneficios';

elseif($periodo == 71):

$tipo = 'Dec. Tercero';

elseif($periodo == 72):

$tipo = 'Dec. Cuarto';

endif;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();



$dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));

$dia = $dia > 30 ? '30': $dia;


 if($datos):
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Horas Extras";
        
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
        $imagenLogo->setCoordinates('E1');
        $imagenLogo->setWidthAndHeight(250,250);
        $imagenLogo->setWorksheet($hojita);
        
        $hojita->setCellValue('A4', "Año ".$anio);
        $hojita->getStyle('A4')->getFont()->setSize(12);
        $hojita->getStyle('A4')->getFont()->setBold(true);
        $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
  
        $hojita->setCellValue('A5', "Mes ".$meses[$mes]);
        $hojita->getStyle('A5')->getFont()->setSize(12);
        $hojita->getStyle('A5')->getFont()->setBold(true);
        $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D4', "Tipo Rol ".$tipo);
        $hojita->getStyle('D4')->getFont()->setSize(12);
        $hojita->getStyle('D4')->getFont()->setBold(true);
        $hojita->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
       
        $hojita->setCellValue('D5', "Fecha Inicio:  01/".$mes.'/'.$anio);
        $hojita->getStyle('D5')->getFont()->setSize(12);
        $hojita->getStyle('D5')->getFont()->setBold(true);
        $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        
        
        $hojita->setCellValue('I4', "Fecha fin:  ".$dia."/".$mes."/".$anio);
        $hojita->getStyle('I4')->getFont()->setSize(12);
        $hojita->getStyle('I4')->getFont()->setBold(true);
        $hojita->getStyle('I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('A6', "Area");
        $hojita->getStyle('A6')->getFont()->setSize(12);
        
        $hojita->setCellValue('B6', "Departamento");
        $hojita->getStyle('B6')->getFont()->setSize(12);
        
        $hojita->setCellValue('C6', "Cédula");
        $hojita->getStyle('C6')->getFont()->setSize(12);
        
        $hojita->setCellValue('D6', "Nombres");
        $hojita->getStyle('D6')->getFont()->setSize(12);
        
        $hojita->setCellValue('E6', "Dias");
        $hojita->getStyle('E6')->getFont()->setSize(12);
        
        $hojita->setCellValue('F6', "H25");
        $hojita->getStyle('F6')->getFont()->setSize(12);
        
        $hojita->setCellValue('G6', "H50");
        $hojita->getStyle('G6')->getFont()->setSize(12);
        
        $hojita->setCellValue('H6', "H100");
        $hojita->getStyle('H6')->getFont()->setSize(12);
        
        $hojita->getStyle('A6:H6')->getFont()->setBold(true);
        $hojita->getStyle('A6:H6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setAutoFilter("A6:H6");
        
        $i = 6;
        
        
        foreach ($datos  as $data)
        {
            $i++;
            $hojita->setCellValue('A'.$i, $data['area']);
            $hojita->setCellValue('B'.$i, $data['departamento']);
            $hojita->setCellValue('C'.$i, $data['id_sys_rrhh_cedula']);
            $hojita->setCellValue('D'.$i, utf8_encode($data['nombres']));
            $hojita->setCellValue('E'.$i, $data['cantidad']);
            $hojita->setCellValue('F'.$i, $data['h25']);
            $hojita->setCellValue('G'.$i, $data['h50']);
            $hojita->setCellValue('H'.$i, $data['h100']);
       
            
        }
        
        $hojita->getStyle('F6:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('F6:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        $hojita->getStyle('G6:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('G6:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        $hojita->getStyle('H6:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('H6:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        
        
        foreach(range('A','H') as $columnID) {
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
        
        
 endif;?>     

<?php 
function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
    $position = array();
    $newRow = array();
    foreach ($toOrderArray as $key => $row) {
        $position[$key]  = $row[$field];
        $newRow[$key] = $row;
    }
    //$position = array_unique($position);
    if ($inverse) {
        arsort($position);
    }
    else {
        asort($position);
    }
    $returnArray = array();
    foreach ($position as $key => $pos) {
        $returnArray[] = $newRow[$key];
    }
    
    return $returnArray;
}
?>
