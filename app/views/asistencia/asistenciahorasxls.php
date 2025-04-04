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
    
    $titulo= "INFORME_HORAS_INDIVIDUAL";
    
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
    $imagenLogo->setCoordinates('A1');
    $imagenLogo->setWidthAndHeight(250,250);
    $imagenLogo->setWorksheet($hojita);

    $hojita->setCellValue('C2', "Resumen Horas - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
    $hojita->getStyle('C2')->getFont()->setSize(15);
    $hojita->getStyle('C2')->getFont()->setBold(true);
    $hojita->mergeCells('C2:F2');
    $hojita->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    $hojita->setCellValue('A5', "Horas Trabajadas");
    $hojita->getStyle('A5')->getFont()->setBold(true);
    $hojita->getStyle('A5')->getFont()->setSize(12);
    
    $hojita->setCellValue('B5', "Horas Extras");
    $hojita->getStyle('B5')->getFont()->setBold(true);
    $hojita->getStyle('B5')->getFont()->setSize(12);
    
    $hojita->setCellValue('C5', "Horas Pagadas");
    $hojita->getStyle('C5')->getFont()->setBold(true);
    $hojita->getStyle('C5')->getFont()->setSize(12);
   
    $hojita->setCellValue('D5', "Horas Compensadas");
    $hojita->getStyle('D5')->getFont()->setBold(true);
    $hojita->getStyle('D5')->getFont()->setSize(12);
    
    $hojita->setCellValue('E5', "Diferencia de Horas");
    $hojita->getStyle('E5')->getFont()->setBold(true);
    $hojita->getStyle('E5')->getFont()->setSize(12);
    
    $i = 5;
    
    
    $total25          = 0;
    $total50          = 0;
    $total100         = 0;
    $totalHoras       = 0;
    $totalHorasExtras = 0;
    $totalHorasCanceladas = 0;
    $cont             = 0;



    $data =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
        //sort($data)
    foreach ($data as $index => $fecha): 
    
        $fechaAsistencia = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                  
        $dataAsistencia =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $fechaAsistencia));
                         
        foreach ($dataAsistencia as $index2 => $id_sys_rrhh_cedula):
                  
            $entrada        = '00:00:00';
            $salida         = '00:00:00';
            $thoras         = '00:00:00';
            $h25            = '00:00:00';
            $h50            = '00:00:00';
            $h100           = '00:00:00';
                          
            $atraso         = '00:00:00';
            $saltemp        = '00:00:00';
            $horaentrada    = gethora_entrada($id_sys_rrhh_cedula);
                          
            $fecha_ent     = '';
            $fecha_sal     = '';
            $observacion   = '';
            $contador = 0;
            $contador2 = 0;
                  
            $marcaciones = array_filter($fechaAsistencia, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
                      
            if($marcaciones[$index2]['fecha_marcacion'] != null):

                foreach ($marcaciones as $marcacion):

                    if($marcacion['tipo'] == 'E'):

                        $contador += 1;
    
                    endif;

                    if($marcacion['tipo'] == 'S'):

                        $contador += 1;
    
                    endif;

                    if($marcacion['tipo'] == 'SD'):

                        $contador2 += 1;
    
                    endif;

                    if($marcacion['tipo'] == 'SA'):

                        $contador2 += 1;
    
                    endif;

                    if($marcacion['tipo'] == 'SM'):

                        $contador2 += 1;
    
                    endif;

                endforeach;
                                        
                foreach ($marcaciones as $marcacion):
                                        
                    if($marcacion['tipo'] == 'E'):
                                            
                        $entrada =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                        $fecha_ent = $marcacion['fecha_marcacion'];
                                                
                        if($marcaciones[$index2]['agendamiento'] != -1):
                            
                            $horaentrada = gethora_agendamiento($marcaciones[$index2]['agendamiento'],$marcaciones[$index2]['fecha'],$id_sys_rrhh_cedula);
                            
                            if($entrada > date('H:i:s', strtotime($horaentrada['hora_inicio']))):
                                                        
                                $fechaUno=new DateTime(date('H:i:s', strtotime($horaentrada['hora_inicio'])));
                                $fechaDos=new DateTime($entrada);

                                $dateInterval = $fechaUno->diff($fechaDos);
                                $atraso = $dateInterval->format('%H:%I:%S');
                            
                            endif;
                        
                        else:
                                                
                            if($entrada > $horaentrada):
                                                        
                                $fechaUno=new DateTime($horaentrada);
                                $fechaDos=new DateTime($entrada);

                                $dateInterval = $fechaUno->diff($fechaDos);
                                $atraso = $dateInterval->format('%H:%I:%S');

                            endif;
                        
                        endif;

                    elseif($marcacion['tipo'] == 'S'):
                                    
                        $salida  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                        $fecha_sal = $marcacion['fecha_marcacion'];
                                                
                    endif;
                                        
                endforeach;
                                        
                if( $contador  == 2):
                                        
                    $thoras = getTotalhoras($fecha_ent, $fecha_sal);
                                              
                    if($thoras != "00:00:00"):
                                                     
                        if($marcaciones[$index2]['pago50'] > 0 ):
                                                         
                            $total50 += $marcaciones[$index2]['h50'];
                                                         
                        endif;

                        if($marcaciones[$index2]['pago100'] > 0 ):
                                                             
                            $total100 += $marcaciones[$index2]['h100'];
                                                     
                        endif;
                                                
                        $h50  = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado']));
                                                        
                        $h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado'],$marcaciones[$index2]['agendamiento']));

                    endif;
                
                endif;
                                    
            endif;
                                    
            if ( $thoras != "00:00:00"):
                
                $cont++;
                $totalHoras = floatval( $totalHoras + round(HorasToDecimal($thoras),2));
                $totalHorasExtras = floatval( $totalHorasExtras + round(HorasToDecimal($h50),2) + round(HorasToDecimal($h100),2));
            
            endif;
               
        endforeach;

    endforeach; 

    $totalHorasCanceladas = floatval($total50)+ floatval($total100);

    $dataPermiso = getDatosPermisos($fechaini,$fechafin,$cedula);

    $sumTotalHoras = 0;

    foreach($dataPermiso as $dpermiso):

        if($dpermiso['tipo'] == 'C'):

            $sumTotalHoras += 8;

        else:

            $date1 = new \DateTime($dpermiso['hora_ini']);
            $date2 = new \DateTime($dpermiso['hora_fin']);
            $diff  = $date1->diff($date2);
                    
            $horas = $diff->format('%H:%I:%S');

            $decimalH = HorasToDecimal($horas);

            $sumTotalHoras += $decimalH;

        endif;
            

    endforeach;

        $diferencia = floatval($totalHorasExtras) - floatval($totalHorasCanceladas) - floatval($sumTotalHoras);

        $i++;
            
        $hojita->setCellValue('A'.$i, DecimaltoHoras(number_format($totalHoras, 2, '.', '')));
        $hojita->setCellValue('B'.$i, DecimaltoHoras(number_format($totalHorasExtras, 2, '.', '')));
        $hojita->setCellValue('C'.$i, DecimaltoHoras(number_format($totalHorasCanceladas, 2, '.', '')));
        $hojita->setCellValue('D'.$i, DecimaltoHoras(number_format($sumTotalHoras, 2, '.', '')));
        $hojita->setCellValue('E'.$i, DecimaltoHoras(number_format($diferencia, 2, '.', '')));
    
    foreach(range('A','E') as $columnID) {
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
