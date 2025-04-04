<?php
use app\models\SysRrhhPrestamosDet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 =>'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

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

    $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

    $objPHPExcel =  new Spreadsheet();
    
    $titulo= "Préstamos Por Años";
    
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
    $imagenLogo->setCoordinates('B1');
    $imagenLogo->setWidthAndHeight(250,250);
    $imagenLogo->setWorksheet($hojita);
    
    $hojita->setCellValue('D2', "Informe de Préstamos Desde ".$fechaini." Hasta ".$fechafin );
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:G2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A4', "Area");
    $hojita->getStyle('A4')->getFont()->setSize(12);
    $hojita->getStyle('A4')->getFont()->setBold(true);
    $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('B4', "Departamento");
    $hojita->getStyle('B4')->getFont()->setSize(12);
    $hojita->getStyle('B4')->getFont()->setBold(true);
    $hojita->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('C4', "Cédula");
    $hojita->getStyle('C4')->getFont()->setSize(12);
    $hojita->getStyle('C4')->getFont()->setBold(true);
    $hojita->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('D4', "Nombres");
    $hojita->getStyle('D4')->getFont()->setSize(12);
    $hojita->getStyle('D4')->getFont()->setBold(true);
    $hojita->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('E4', "Cargo");
    $hojita->getStyle('E4')->getFont()->setSize(12);
    $hojita->getStyle('E4')->getFont()->setBold(true);
    $hojita->getStyle('E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('F4', "Fecha Préstamo");
    $hojita->getStyle('F4')->getFont()->setSize(12);
    $hojita->getStyle('F4')->getFont()->setBold(true);
    $hojita->getStyle('F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('G4', "Mes Inicio Pago");
    $hojita->getStyle('G4')->getFont()->setSize(12);
    $hojita->getStyle('G4')->getFont()->setBold(true);
    $hojita->getStyle('G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('H4', "Mes Final Pago");
    $hojita->getStyle('H4')->getFont()->setSize(12);
    $hojita->getStyle('H4')->getFont()->setBold(true);
    $hojita->getStyle('H4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('I4', "Monto Total");
    $hojita->getStyle('I4')->getFont()->setSize(12);
    $hojita->getStyle('I4')->getFont()->setBold(true);
    $hojita->getStyle('I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('J4', "Cuotas");
    $hojita->getStyle('J4')->getFont()->setSize(12);
    $hojita->getStyle('J4')->getFont()->setBold(true);
    $hojita->getStyle('J4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('K4', "Valor Cuotas");
    $hojita->getStyle('K4')->getFont()->setSize(12);
    $hojita->getStyle('K4')->getFont()->setBold(true);
    $hojita->getStyle('K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setAutoFilter("A4:K4");
    
    $i = 4;
    
    if($datos):

        foreach ($datos as $data):
    
            $mesfinal = $data['mes_ini']+$data['coutas']-1;
            $mesfinalCond = $data['mes_ini']+$data['coutas']-1;
            $anioactual = date('Y',strtotime($data['fecha']));

            if($mesfinal > 12):

                $mesfinal = $mesfinal - 12;
                $aniosiguiente = date('Y',strtotime($data['fecha']."+1 years"));

            else:
                
                $aniosiguiente = date('Y',strtotime($data['fecha']));

            endif;

            $valores = [];
            $valoresT = "";

            $cuotas = obtenerCoutasPrestamo($data['id_sys_rrhh_prestamos_cab']);

            foreach($cuotas as $couta){
                array_push($valores, $couta['valor']);
            }

            $valoresNo = array_unique($valores);
            foreach($valoresNo as $index){
                $valoresT .= $index." ";
            }
                
                $i++;
                $hojita->setCellValue('A'.$i, $data['area']);
                $hojita->setCellValue('B'.$i, $data['departamento']);
                $hojita->setCellValue('C'.$i, $data['id_sys_rrhh_cedula']);
                $hojita->setCellValue('D'.$i, $data['nombres']);
                $hojita->setCellValue('E'.$i, $data['cargo']);
                $hojita->setCellValue('F'.$i, $data['fecha']);            
                $hojita->setCellValue('G'.$i, $meses[$data['mes_ini']].'/'.$anioactual);
                $hojita->setCellValue('H'.$i, $meses[$mesfinal].'/'.$aniosiguiente);
                $hojita->setCellValue('I'.$i, $data['valor']);
                $hojita->setCellValue('J'.$i, $data['coutas']);
                $hojita->setCellValue('K'.$i, $valoresT);

            foreach(range('A','J') as $columnID) {
                $hojita->getColumnDimension($columnID)->setAutoSize(true);
            }
        
        endforeach;
             
    endif;
    
    $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
    $nombreArchivo=$titulo.'.xlsx';
    $writer = new Xlsx($objPHPExcel);
    $writer->save($nombreArchivo);
    $objPHPExcel->disconnectWorksheets();
    unset($objPHPExcel);
    header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
    exit;

?>

<?php

function obtenerCoutasPrestamo($id){
    
    return SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_cab' => $id])->all(); 
}

?>