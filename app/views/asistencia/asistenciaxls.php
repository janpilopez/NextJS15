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
$modelArea  = SysAdmAreas::find()->where(['id_sys_adm_area' => $area])->one();
$modelDpto  = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento' => $departamento])->one();

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
    
    $titulo= "INFORME_HORAS_EXTRAS_".$fechaini;
    
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
    $imagenLogo->setCoordinates('E1');
    $imagenLogo->setWidthAndHeight(250,250);
    $imagenLogo->setWorksheet($hojita);
    
    
    $hojita->setCellValue('A5', "Fecha");
    $hojita->getStyle('A5')->getFont()->setSize(12);
    $hojita->getStyle('A5')->getFont()->setBold(true);
    $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('B5', $fechaini);
    $hojita->getStyle('B5')->getFont()->setSize(12);
    $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A6', "Area ");
    $hojita->getStyle('A6')->getFont()->setSize(12);
    $hojita->getStyle('A6')->getFont()->setBold(true);
    $hojita->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('B6', $modelArea != null ? $modelArea->area : "Todos");
    $hojita->getStyle('B6')->getFont()->setSize(12);
    $hojita->getStyle('B6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A7', "Departamento");
    $hojita->getStyle('A7')->getFont()->setSize(12);
    $hojita->getStyle('A7')->getFont()->setBold(true);
    $hojita->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    
    $hojita->setCellValue('B7', $modelDpto != null ? $modelArea->departamento : "Todos");
    $hojita->getStyle('B7')->getFont()->setSize(12);
    $hojita->getStyle('B7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    
    
    $hojita->setCellValue('A8', "Area");
    $hojita->getStyle('A8')->getFont()->setBold(true);
    $hojita->getStyle('A8')->getFont()->setSize(12);
    
    $hojita->setCellValue('B8', "Departamento");
    $hojita->getStyle('B8')->getFont()->setBold(true);
    $hojita->getStyle('B8')->getFont()->setSize(12);
    
    $hojita->setCellValue('C8', "Nombres");
    $hojita->getStyle('C8')->getFont()->setBold(true);
    $hojita->getStyle('C8')->getFont()->setSize(12);
   
    $hojita->setCellValue('D8', "Identificación");
    $hojita->getStyle('D8')->getFont()->setBold(true);
    $hojita->getStyle('D8')->getFont()->setSize(12);
    
    $hojita->setCellValue('E8', "Entrada");
    $hojita->getStyle('E8')->getFont()->setBold(true);
    $hojita->getStyle('E8')->getFont()->setSize(12);
    
    $hojita->setCellValue('F8', "Salida");
    $hojita->getStyle('F8')->getFont()->setBold(true);
    $hojita->getStyle('F8')->getFont()->setSize(12);
    
    $hojita->setCellValue('G8', "T.Horas");
    $hojita->getStyle('G8')->getFont()->setBold(true);
    $hojita->getStyle('G8')->getFont()->setSize(12);
    
    $hojita->setCellValue('H8', "H25");
    $hojita->getStyle('H8')->getFont()->setBold(true);
    $hojita->getStyle('H8')->getFont()->setSize(12);
    
    $hojita->setCellValue('I8', "Valor");
    $hojita->getStyle('I8')->getFont()->setBold(true);
    $hojita->getStyle('I8')->getFont()->setSize(12);
    
    $hojita->setCellValue('J8', "H50");
    $hojita->getStyle('J8')->getFont()->setBold(true);
    $hojita->getStyle('J8')->getFont()->setSize(12);
    
    $hojita->setCellValue('K8', "Valor");
    $hojita->getStyle('K8')->getFont()->setBold(true);
    $hojita->getStyle('K8')->getFont()->setSize(12);
    
    $hojita->setCellValue('L8', "H100");
    $hojita->getStyle('L8')->getFont()->setBold(true);
    $hojita->getStyle('L8')->getFont()->setSize(12);
    
    $hojita->setCellValue('M8', "Valor");
    $hojita->getStyle('M8')->getFont()->setBold(true);
    $hojita->getStyle('M8')->getFont()->setSize(12);
    
    $i = 8;
    
    
         $data =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));
        //sort($data)
        
        foreach ($data as $index => $Id):
            
            $entrada        = '00:00:00';
            $salida         = '00:00:00';
            $thoras         = '00:00:00';
            $h25            = '00:00:00';
            $h50            = '00:00:00';
            $h100           = '00:00:00';
            $atraso         = '00:00:00';
            $saltemp        = '00:00:00';
            
            //$observacion   = BuscaPermiso($fechaini, $data['id_sys_rrhh_cedula']);
            $fecha_ent     = '';
            $fecha_sal     = '';
            $observacion   = '';
            
        
        $marcaciones = array_filter($datos, array(new FilterData("id_sys_rrhh_cedula", $Id), 'getFilter'));
            
        if($marcaciones[$index]['fecha_marcacion'] != null):
        
            if(count($marcaciones) == 1 or count($marcaciones) > 2):
                
                if($marcaciones[$index]['permiso'] != null):
                
                     $observacion = $marcaciones[$index]['permiso'];
                
                else:
                
                      $observacion = 'Error Marcación. El usuario tiene una o más marcaciones';
                
                endif;
                
            endif;
            
            foreach ($marcaciones as $marcacion):
            
                if($marcacion['tipo'] == 'E'):
                    
                    $entrada =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                    $fecha_ent = $marcacion['fecha_marcacion'];
                
                else:
                    
                    $salida  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                    $fecha_sal = $marcacion['fecha_marcacion'];
                
                endif;
            
            endforeach;
        
            if(count($marcaciones)  == 2):
                
                $thoras = getTotalhoras($fecha_ent, $fecha_sal);
                
                if($thoras != "00:00:00"):
                
                        if($marcaciones[$index]['permiso'] != null):
                        
                        $observacion = $marcaciones[$index]['permiso'];
                        
                        endif;
                        
                       //Calcular horas extras
                        
                        //Horas extras 25
                        if($marcaciones[$index]['h25'] > 0):
                        
                            $h25 = DecimaltoHoras($marcaciones[$index]['h25']);
                        
                        else:
                        
                            $h25 = getRendonminutos(gethoras25 ($fecha_ent, $fecha_sal,$marcaciones[$index]['id_sys_rrhh_cedula'], $marcaciones[$index]['fecha']));
                        
                        endif;
                        
                        ///Horas extras 50
                        
                        if($marcaciones[$index]['h50'] > 0):
                        
                            $h50 = DecimaltoHoras($marcaciones[$index]['h50']);
                        
                        else:
                        
                            $h50 = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$marcaciones[$index]['id_sys_rrhh_cedula'], $marcaciones[$index]['fecha']));
                        
                        endif;
                        
                        ///Horas extras 100
                        if ($marcaciones[$index]['h100'] > 0):
                        
                            $h100  = DecimaltoHoras($marcaciones[$index]['h100']);      
                        else:
                        
                            $h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$marcaciones[$index]['id_sys_rrhh_cedula'], $marcaciones[$index]['fecha'], $marcaciones[$index]['feriado'],$marcaciones[$index]['agendamiento']));
                        
                        endif;
                    
                else:
                
                  $observacion = 'Error Marcación. El usuario tiene una o más marcaciones';
                
                endif;
            
        endif;
        
        else:
        
            if($marcaciones[$index]['agendamiento'] == 0):
            
                
                $observacion = 'DIA LIBRE';
            
                
            elseif($marcaciones[$index]['permiso'] != null):
            
            
                 $observacion = $marcaciones[$index]['permiso'];
            
            
            elseif($marcaciones[$index]['vacaciones'] == 1):
            
                 $observacion = 'GOZO DE VACACIONES';
            
            
            elseif ($marcaciones[$index]['feriado'] != null):
            
            
                 $observacion = $marcaciones[$index]['feriado'];
            
            
            else :
                
                if($marcaciones[$index]['agendamiento'] > 0 ):
                
                    $observacion = 'FALTA';
                
                else:
                
                    $dia =  date("N", strtotime($marcaciones[$index]['fecha']));
                    
                    if($dia >= 1 && $dia <= 5):
                    
                        $observacion = 'FALTA';
                    
                    else:
                    
                        $observacion = 'DIA DE DESCANZO';
                    
                    endif;
                
                
                endif;
            
            endif;
            
       endif;
    
       $i++;
        
       $hojita->setCellValue('A'.$i, $marcaciones[$index]['area']);
       $hojita->setCellValue('B'.$i, $marcaciones[$index]['departamento']);
       $hojita->setCellValue('C'.$i, $marcaciones[$index]['nombres']);
       $hojita->setCellValue('D'.$i, $marcaciones[$index]['id_sys_rrhh_cedula']);
       $hojita->getStyle('D'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
       $hojita->setCellValue('E'.$i, $entrada);
       $hojita->setCellValue('F'.$i, $salida);
       $hojita->setCellValue('G'.$i, $thoras  != "00:00:00" ? $thoras : $thoras );
       $hojita->setCellValue('H'.$i, $h25 );
       $hojita->setCellValue('I'.$i, $marcaciones[$index]['pago25'] == 1 ? number_format((($marcaciones[$index]['valor_hora'] * 0.25) *(HorasToDecimal($h25))), 2,'.', ',' ) : 0,00);
       $hojita->setCellValue('J'.$i, $h50 );
       $hojita->setCellValue('K'.$i, $marcaciones[$index]['pago50'] == 1 ? number_format(((($marcaciones[$index]['valor_hora'] * 0.50)+ $marcaciones[$index]['valor_hora']) *(HorasToDecimal($h50))), 2,'.', ',' ) : 0,00);
       $hojita->setCellValue('L'.$i, $h100 );
       $hojita->setCellValue('M'.$i, $marcaciones[$index]['pago100'] == 1 ? number_format((($marcaciones[$index]['valor_hora'] * 2) *(HorasToDecimal($h100))), 2,'.', ',' ): 0,00);
       
       
       
    endforeach;
    
    
    
    $hojita->getStyle('I9:I'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);    
    $hojita->getStyle('K9:K'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $hojita->getStyle('M9:M'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   
    
    foreach(range('A','M') as $columnID) {
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
