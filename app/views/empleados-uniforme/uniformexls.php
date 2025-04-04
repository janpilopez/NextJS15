<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysAdmAreas;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosNovedades;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysAdmDepartamentos;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

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

if($datos):


    $objPHPExcel =  new Spreadsheet();
    
    $titulo= "UNIFORMES";
    
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
    $imagenLogo->setCoordinates('B1');
    $imagenLogo->setWidthAndHeight(250,250);
    $imagenLogo->setWorksheet($hojita);

    $hojita->setCellValue('D2', "Informe de Entrega Uniformes");
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:G2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A5', "Fecha de Entrega");
    $hojita->getStyle('A5')->getFont()->setSize(12);
    $hojita->getStyle('A5')->getFont()->setBold(true);
    $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('B5', "Area ");
    $hojita->getStyle('B5')->getFont()->setSize(12);
    $hojita->getStyle('B5')->getFont()->setBold(true);
    $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('C5', "Departamento");
    $hojita->getStyle('C5')->getFont()->setSize(12);
    $hojita->getStyle('C5')->getFont()->setBold(true);
    $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('D5', "Cédula");
    $hojita->getStyle('D5')->getFont()->setSize(12);
    $hojita->getStyle('D5')->getFont()->setBold(true);
    $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('E5', "Nombres");
    $hojita->getStyle('E5')->getFont()->setBold(true);
    $hojita->getStyle('E5')->getFont()->setSize(12);
    $hojita->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('F5', "N° de Uniforme");
    $hojita->getStyle('F5')->getFont()->setBold(true);
    $hojita->getStyle('F5')->getFont()->setSize(12);
    $hojita->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
   
    $hojita->setCellValue('G5', "Estado");
    $hojita->getStyle('G5')->getFont()->setBold(true);
    $hojita->getStyle('G5')->getFont()->setSize(12);
    $hojita->getStyle('G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setAutoFilter("A5:G5");

    $i = 5;
          
    foreach ($datos as $index => $data):
            
        $i++;
                
        $hojita->setCellValue('A'.$i, $data['fecha_entrega']);
        $hojita->setCellValue('B'.$i, $data['area']);
        $hojita->setCellValue('C'.$i, $data['departamento']);
        $hojita->setCellValue('D'.$i, $data['id_sys_rrhh_cedula']);
        $hojita->setCellValue('E'.$i, $data['nombres']);
        $hojita->setCellValue('F'.$i, $data['numero_uniforme']);
        $hojita->setCellValue('G'.$i, $data['estado'] == 1 ? 'En uso':'Retirado');
          
    endforeach;
    
    
    foreach(range('A','G') as $columnID) {
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
