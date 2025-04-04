  
<?php  use app\models\SysRrhhEmpleadosLunch;
       use yii\helpers\Html;

$holgura =  15;
//listado de funciones de calculos
echo $this->render('funciones');


$meses =  Yii::$app->params['meses'];
$dias =   Yii::$app->params['dias'];

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
?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr>
        <th>Horas Trabajadas</th>
        <th>Horas Extras</th>
        <th>Horas Pagadas</th>
        <th>Horas Compensadas</th>
        <th>Diferencia De Horas</th>
      </tr>
    </thead>
    <tbody>
     <?php 
     
       
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
           
      ?>
               
               
       <?php 
       
                  $fechaAsistencia = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                  
                  $dataAsistencia =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $fechaAsistencia));
                  
                  
                  foreach ($dataAsistencia as $index2 => $id_sys_rrhh_cedula):
                  
                          $entrada        = '00:00:00';
                          $salida         = '00:00:00';
                          $thoras         = '00:00:00';
                          $h25            = '00:00:00';
                          $h50            = '00:00:00';
                          $h100           = '00:00:00';

                          $entrada_desayuno  = '00:00:00';
                          $salidadesayuno   = '00:00:00';
                          $entrada_almuerzo  = '00:00:00';
                          $salidaalmuerzo   = '00:00:00';
                          $entrada_merienda  = '00:00:00';
                          $salidamerienda   = '00:00:00';
                          $descuentocomida  = 0;
                          
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

                                            $comidas = SysRrhhEmpleadosLunch::find()->where(['id_sys_rrhh_cedula'=>$id_sys_rrhh_cedula])->andWhere(['fecha'=>$marcacion['fecha_marcacion']])->all();
                                            
                                            if($comidas):

                                                foreach($comidas as $index3 => $item):

                                                    if($item->id_sys_rrhh_comedor == 1):

                                                        $entrada_desayuno = date('H:i:s', strtotime($item->hora));
                                                                
                                                    elseif($item->id_sys_rrhh_comedor == 2):

                                                        $entrada_almuerzo = date('H:i:s', strtotime($item->hora));
                                                                
                                                    else:
                                                                    
                                                        $entrada_merienda = date('H:i:s', strtotime($item->hora));

                                                    endif;
                                                            
                                                endforeach;

                                            endif;
                                        
                                            if($marcacion['tipo'] == 'E'):
                                            
                                                $entrada =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                                                $fecha_ent = $marcacion['fecha_marcacion'];
                                                
                                                if($marcaciones[$index2]['agendamiento'] != -1):
                                                    $horaentrada = gethora_agendamiento($marcaciones[$index2]['agendamiento'],$marcaciones[$index2]['fecha'],$id_sys_rrhh_cedula);
                                                    if($horaentrada):
                                                        if($entrada > date('H:i:s', strtotime($horaentrada['hora_inicio']))):
                                                            $fechaUno=new DateTime(date('H:i:s', strtotime($horaentrada['hora_inicio'])));
                                                            $fechaDos=new DateTime($entrada);

                                                            $dateInterval = $fechaUno->diff($fechaDos);
                                                            $atraso = $dateInterval->format('%H:%I:%S');
                                                        endif;
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
                                                
                                            elseif($marcacion['tipo'] == 'SD'):
                                                
                                                $salidadesayuno  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                                                $descuentocomida += HorasToDecimal('00:15:00');
                    
                                            elseif($marcacion['tipo'] == 'SA'):
                        
                                                $salidaalmuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                                                $descuentocomida += HorasToDecimal('00:30:00');
                    
                                            elseif($marcacion['tipo'] == 'SM'):
                        
                                                $salidamerienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                                                $descuentocomida += HorasToDecimal('00:30:00');
                    
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
                                                
                                                    $h50  = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado']));
                                                        
                                                    $h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado'],$marcaciones[$index2]['agendamiento']));

                                                endif;
                
                                        endif;
                                    
                                    endif;
                                    
                                    if ( $thoras != "00:00:00"):
                                        $cont++;
                                        $totalHoras = floatval( $totalHoras + round(HorasToDecimal($thoras),2) - round($descuentocomida,2));
                                        $totalHorasExtras = floatval( $totalHorasExtras + round(HorasToDecimal($h50),2) + round(HorasToDecimal($h100),2));
                                    endif;
               ?>
      <?php     endforeach;?>
     <?php  endforeach; 

        $totalHorasCanceladas = floatval($total50)+ floatval($total100);

        $dataPermiso = getDatosPermisos($fechaini,$fechafin,$empleado['id_sys_rrhh_cedula']);

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

         ?>
      <tr>
          <td><?= DecimaltoHoras(number_format($totalHoras, 2, '.', ''))?></td>
          <td><?= DecimaltoHoras(number_format($totalHorasExtras, 2, '.', ''))?></td>
          <td><?= DecimaltoHoras(number_format($totalHorasCanceladas, 2, '.', ''))?></td>
          <td><?= DecimaltoHoras(number_format($sumTotalHoras, 2, '.', ''))?></td>
          <td><?= DecimaltoHoras(number_format($diferencia, 2, '.', ''))?></td>
      </tr>
    </tbody>
  </table>
