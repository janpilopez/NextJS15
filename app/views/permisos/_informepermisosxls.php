<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhPermisos;

echo $this->render('funciones');

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
    
    $titulo= "Informe Permisos Empleados";
    
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
    
    $hojita->setCellValue('C2', "Informe de Permisos desde ".$fechaini." hasta ".$fechafin);
    $hojita->getStyle('C2')->getFont()->setSize(15);
    $hojita->getStyle('C2')->getFont()->setBold(true);
    $hojita->mergeCells('C2:G2');
    $hojita->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A4', "Fecha Inicio/Fecha Fin");
    $hojita->getStyle('A4')->getFont()->setSize(12);
    $hojita->getStyle('A4')->getFont()->setBold(true);
    $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('B4', "Nombres");
    $hojita->getStyle('B4')->getFont()->setSize(12);
    $hojita->getStyle('B4')->getFont()->setBold(true);
    $hojita->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('C4', "Area");
    $hojita->getStyle('C4')->getFont()->setSize(12);
    $hojita->getStyle('C4')->getFont()->setBold(true);
    $hojita->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('D4', "Departamento");
    $hojita->getStyle('D4')->getFont()->setSize(12);
    $hojita->getStyle('D4')->getFont()->setBold(true);
    $hojita->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('E4', "Cargo");
    $hojita->getStyle('E4')->getFont()->setSize(12);
    $hojita->getStyle('E4')->getFont()->setBold(true);
    $hojita->getStyle('E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('F4', "Permiso");
    $hojita->getStyle('F4')->getFont()->setSize(12);
    $hojita->getStyle('F4')->getFont()->setBold(true);
    $hojita->getStyle('F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('G4', "Entidad Emisora");
    $hojita->getStyle('G4')->getFont()->setSize(12);
    $hojita->getStyle('G4')->getFont()->setBold(true);
    $hojita->getStyle('G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setAutoFilter("A4:G4");
    
    
    $i = 4;
    
    if($datos):
    
        $data =  array_unique(array_map(array(new FilterColumn("nombres"), 'getValues'), $datos));
        
        foreach ($data as $index => $nombres):
            
            $fechaPermiso = array_filter($datos, array(new FilterData("nombres", $nombres), 'getFilter'));
                  
            $dataPermiso =  array_unique(array_map(array(new FilterColumn("codigo"), 'getValues'), $fechaPermiso));
                
            $tipo_permiso = "";

            foreach ($dataPermiso as $index2 => $codigo):
        
                $permisos = array_filter($fechaPermiso, array(new FilterData("codigo", $codigo), 'getFilter'));
                
                $tipo_permiso = obtenerPermiso($permisos[$index2]['id_sys_rrhh_permiso']);

                $entidad = "";

                foreach ($datos_medicos as $index3 => $dataMed):
                    
                    if(date('Y-m-d', strtotime($dataMed['inicio'])) == $permisos[$index2]['inicio']){
                        if ($dataMed['identificacion'] == $permisos[$index2]['identificacion']) {
                            $entidad = $dataMed['entidad'];
                        }
                    }
                    
                endforeach;

            $i++;
            $hojita->setCellValue('A'.$i, date('Y-m-d', strtotime($permisos[$index2]['inicio'])). " / " .date('Y-m-d', strtotime($permisos[$index2]['fin'])));
            $hojita->setCellValue('B'.$i, $nombres);
            $hojita->setCellValue('C'.$i, $permisos[$index2]['area']);
            $hojita->setCellValue('D'.$i, $permisos[$index2]['departamento']);
            $hojita->setCellValue('E'.$i, $permisos[$index2]['cargo']);
            $hojita->setCellValue('F'.$i, $tipo_permiso);            
            $hojita->setCellValue('G'.$i, $entidad  != "" ? $entidad : "NO APLICA");
                
            endforeach;

            foreach(range('A','G') as $columnID) {
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

function obtenerPermiso($id){

    $permisos = SysRrhhPermisos::find()->where(['id_sys_rrhh_permiso'=> $id])->one();
    
    return $permisos['permiso'];

}

?>