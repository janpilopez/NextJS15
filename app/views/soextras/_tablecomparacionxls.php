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

echo $this->render('funciones');
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
    
    $titulo= "Resumen HGene VS HSoli";
    
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

    $hojita->setCellValue('E2', "Horas Generadas VS Horas Solicitadas - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
    $hojita->getStyle('E2')->getFont()->setSize(15);
    $hojita->getStyle('E2')->getFont()->setBold(true);
    $hojita->mergeCells('E2:H2');
    $hojita->getStyle('E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A5', "Área");
    $hojita->getStyle('A5')->getFont()->setBold(true);
    $hojita->getStyle('A5')->getFont()->setSize(12);
    
    $hojita->setCellValue('B5', "Departamento");
    $hojita->getStyle('B5')->getFont()->setBold(true);
    $hojita->getStyle('B5')->getFont()->setSize(12);
    
    $hojita->setCellValue('D5', "Horas 50 Generadas");
    $hojita->getStyle('D5')->getFont()->setBold(true);
    $hojita->getStyle('D5')->getFont()->setSize(12);
    
    $hojita->setCellValue('E5', "Horas 50 Solicitadas");
    $hojita->getStyle('E5')->getFont()->setBold(true);
    $hojita->getStyle('E5')->getFont()->setSize(12);
    
    $hojita->setCellValue('F5', "Horas 50 Aprobadas");
    $hojita->getStyle('F5')->getFont()->setBold(true);
    $hojita->getStyle('F5')->getFont()->setSize(12);
    
    $hojita->setCellValue('G5', "Horas 100 Generadas");
    $hojita->getStyle('G5')->getFont()->setBold(true);
    $hojita->getStyle('G5')->getFont()->setSize(12);
    
    $hojita->setCellValue('H5', "Horas 100 Solicitadas");
    $hojita->getStyle('H5')->getFont()->setBold(true);
    $hojita->getStyle('H5')->getFont()->setSize(12);
    
    $hojita->setCellValue('I5', "Horas 100 Aprobadas");
    $hojita->getStyle('I5')->getFont()->setBold(true);
    $hojita->getStyle('I5')->getFont()->setSize(12);
    
    $i = 5;
    
    $dataFilterIdSysAdmArea =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $datos));  
    $con = 0;
    

    foreach ($dataFilterIdSysAdmArea as $index => $departamento):
        $area = "";
        $con++;
        $totalh50 = 0;
        $totalH50 = 0;
        $totalNh50 = 0;
        $totalh100 = 0;
        $totalH100 = 0;
        $totalNh100 = 0;
        $totalA50 = 0;
        $totalA100 = 0;

        $arrayData   = array_filter($datos, array(new FilterData("departamento", $departamento), 'getFilter'));

        foreach ($arrayData as $index => $row):
      
            //if($row['solh50'] != null){
            
            $area = $row['area'];
            $departamento = $row['departamento']; 
            $h50  = getRendonminutos(gethoras50(date("Y-m-d H:i:s",strtotime($row['entrada'])),  date("Y-m-d H:i:s",strtotime($row['salida'])),$row['id_sys_rrhh_cedula'], $row['fecha'], $row['feriado']));
            $h100  = getRendonminutos(gethoras100(date("Y-m-d H:i:s",strtotime($row['entrada'])),  date("Y-m-d H:i:s",strtotime($row['salida'])),$row['id_sys_rrhh_cedula'], $row['fecha'], $row['feriado'],$row['agendamiento']));
            
            $Dh50 = HorasToDecimal($h50);
            $Dh100 = HorasToDecimal($h100);

            $totalH50 += $Dh50;
            $totalH100 += $Dh100;
            $totalh50 += $row['solh50'];
            $totalh100 += $row['solh100'];
            $totalA50 += $row['solA50'];
            $totalA100 += $row['solA100'];
        
            //}

        endforeach;


        if($area != ""):
              
                $i++;
                
                $hojita->setCellValue('A'.$i, $area);
                $hojita->setCellValue('B'.$i, $departamento);
                $hojita->setCellValue('D'.$i, DecimaltoHoras(number_format($totalH50, 2, '.', '')));
                $hojita->setCellValue('E'.$i, DecimaltoHoras(number_format($totalh50, 2, '.', '')));
                $hojita->setCellValue('F'.$i, DecimaltoHoras(number_format($totalA50, 2, '.', '')));
                $hojita->setCellValue('G'.$i, DecimaltoHoras(number_format($totalH100, 2, '.', '')));
                $hojita->setCellValue('H'.$i, DecimaltoHoras(number_format($totalh100, 2, '.', '')));
                $hojita->setCellValue('I'.$i, DecimaltoHoras(number_format($totalA100, 2, '.', '')));
                
        endif;


    endforeach;

    
    foreach(range('A','I') as $columnID) {
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
