<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;

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

    $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

    $objPHPExcel =  new Spreadsheet();
    
    $titulo= "Informe Resumen Asistencia";
    
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
    
    
    $hojita->setCellValue('D2', "Resume Horas laboradas - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:O2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    
    
    $hojita->setCellValue('A4', "Fecha");
    $hojita->getStyle('A4')->getFont()->setSize(12);
    $hojita->getStyle('A4')->getFont()->setBold(true);
    $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('B4', "Area");
    $hojita->getStyle('B4')->getFont()->setSize(12);
    $hojita->getStyle('B4')->getFont()->setBold(true);
    $hojita->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $hojita->setCellValue('C4', "Departamento");
    $hojita->getStyle('C4')->getFont()->setSize(12);
    $hojita->getStyle('C4')->getFont()->setBold(true);
    $hojita->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('D4', "Horas");
    $hojita->getStyle('D4')->getFont()->setSize(12);
    $hojita->getStyle('D4')->getFont()->setBold(true);
    $hojita->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setAutoFilter("A4:D4");
    
    
    $i = 4;
    
    if($datos):
    
            $data_fecha =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
            $indice = 0;
            $horas_resumen = 0;
            $cont_resumen = 0;
            
            foreach ($data_fecha as $index => $fecha):
            
                $array_fecha   = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                
                $data_area =  array_unique(array_map(array(new FilterColumn("id_sys_adm_area"), 'getValues'), $array_fecha));
                
                foreach ($data_area as $indexArea  => $rowarea):
                
                $area   = array_filter($array_fecha, array(new FilterData("id_sys_adm_area", $rowarea), 'getFilter'));
                $data_departamento = array_unique(array_map(array(new FilterColumn("id_sys_adm_departamento"), 'getValues'), $area));
                
                foreach ($data_departamento as $indexDepartamento => $rowdepartamento):
                
                        $indice ++;
                        $total_horas = 0;
                        $cont = 0;
                        $departamento = array_filter($array_fecha, array(new FilterData("id_sys_adm_departamento", $rowdepartamento), 'getFilter'));
                        
                        foreach ($departamento as $index3 => $row):
                        
                            if ($row['entrada'] != null && $row['salida'] != null):
                            
                                $horas = getTotalhoras($row['entrada'], $row['salida']);
                                
                                if ($horas != "00:00:00"):
                                
                                    //Descontar almuerzo
                                    $horas_decimal = 0;
                                    
                                    if ($row['permiso'] == "S/D" && $row['vacaciones'] == 0):
                                    
                                        if ($row['almuerzo'] > 0 || $row['merienda'] > 0):
                                            
                                            $min = (intval($row['almuerzo']) + intval($row['merienda'])) / 60;
                                            $horas_decimal = floatval(number_format(HorasToDecimal($horas), 2, '.', '')) - $min;
                                            
                                        else:
                                        
                                            $horas_decimal = HorasToDecimal($horas);
                                            
                                        endif;
                                        
                                        $cont ++;
                                        $total_horas = $total_horas + $horas_decimal;
                                        
                                    endif;
                                
                                endif;
                            
                            endif;
                            
                        endforeach;
                        
                        
                        
                        if ($total_horas > 0):
                        
                            $horas_resumen = $horas_resumen + ($total_horas > 0 ? $total_horas/$cont : $total_horas);
                            $cont_resumen ++;
                        
                        endif;
                        
                        $i++;
                        $hojita->setCellValue('A'.$i, $departamento[$indexDepartamento]['fecha']);
                        $hojita->setCellValue('B'.$i, $departamento[$indexDepartamento]['area']);
                        $hojita->setCellValue('C'.$i, $departamento[$indexDepartamento]['departamento']);
                        $hojita->setCellValue('D'.$i, DecimaltoHoras(number_format($total_horas > 0 ? $total_horas/$cont : 0, 2, '.', '')));
                        

                   endforeach;  
                                              
                endforeach;  
                                
            endforeach;
            
            foreach(range('A','D') as $columnID) {
                $hojita->getColumnDimension($columnID)->setAutoSize(true);
            }
             
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