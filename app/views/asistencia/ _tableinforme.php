<?php 

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
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Departamento</th>
        <th>Nombres</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>T.Horas</th>
        <th>Atraso</th>
        <th>Sal.Temp</th>
        <th>H25</th>
        <th>H50</th>
        <th>H100</th>
        <th>Observación</th>  
      </tr>
    </thead>
    <tbody>
     <?php 
             $data =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
             $cont = 0;
             $vacaciones = 0;
             $permisos = 0;
             $faltas = 0;
             $totalHoras = 0;
             //sort($data)
             foreach ($data as $index => $fecha):   
      ?>
              <tr  style="background-color: #ccc">
                  <td colspan="12"><strong>  <?= $dias[date('N',strtotime($fecha))]." ".date('d',strtotime($fecha))." de ".$meses[date('n',strtotime($fecha))] ." del ".date('Y', strtotime($fecha)) ?> </strong></td>
               </tr>  
               
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
                          $errorMarcacion  = false; 
                          
                          $atraso         = '00:00:00';
                          $saltemp        = '00:00:00';
                          
                          $fecha_ent     = '';
                          $fecha_sal     = '';
                          $observacion   = '';
                  
                  
                  
                                    $marcaciones = array_filter($fechaAsistencia, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
                      
                                    if($marcaciones[$index2]['fecha_marcacion'] != null):
                                    
                                        if(count($marcaciones) == 1 or count($marcaciones) > 2):
                                        
                                            if($marcaciones[$index2]['permiso'] != null):
                                            
                                                 $observacion = $marcaciones[$index2]['permiso'];
                                            
                                            else:
                                                 $errorMarcacion = true;
                                                 $observacion = 'Error Marcación. El usuario tiene una o mas marcaciones';
                                            
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
                                        
                                        if( count($marcaciones)  == 2):
                                        
                                       
                                                    $thoras = getTotalhoras($fecha_ent, $fecha_sal);
                                                
                                                    if($thoras != "00:00:00"):
                                                    
                                                            if($marcaciones[$index2]['permiso'] != null):
                                                            
                                                                 $observacion = $marcaciones[$index2]['permiso'];
                                                            
                                                            endif;
                                                        
                                                            //Calcular horas extras 
                                                            
                                                            //Horas extras 25 
                                                            if($marcaciones[$index2]['h25'] > 0):
                                                            
                                                                 $h25 = DecimaltoHoras($marcaciones[$index2]['h25']);
                                                            else:
                                                                
                                                                 $h25  = getRendonminutos(gethoras25 ($fecha_ent, $fecha_sal,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha']));
                                                            
                                                            endif;
                                                           
                                                            //Horas extras 50
                                                            
                                                            if($marcaciones[$index2]['h50'] > 0):
                                                            
                                                                 $h50  = DecimaltoHoras($marcaciones[$index2]['h50']);
                                                            else:
                                                                 $h50  = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha']));
                                                            endif;
                                                            
                                                            //Horas extras 100
                                                            if ($marcaciones[$index2]['h100'] > 0):
                                                            
                                                                 $h100  = DecimaltoHoras($marcaciones[$index2]['h100']);
                                                            else:
                                                            
                                                                $h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$marcaciones[$index2]['id_sys_rrhh_cedula'], $marcaciones[$index2]['fecha'], $marcaciones[$index2]['feriado'],$marcaciones[$index2]['agendamiento']));
                                                                
                                                            endif;
                                                            
                                                    else:
                                                        
                                                        $errorMarcacion = true;
                                                        $observacion = 'Error Marcación';
                                                    
                                                    endif;
                                              
                                        endif;
                                        
                                    else:
                                    
                                            if($marcaciones[$index2]['agendamiento'] == 0):
                                            
                                                $observacion = 'DIA LIBRE';
                                                              
                                            elseif($marcaciones[$index2]['permiso'] != null):
                                            
                                                $observacion = $marcaciones[$index2]['permiso'];
                                            
                                            elseif($marcaciones[$index2]['vacaciones'] == 1):
                                            
                                                $observacion = 'GOZO DE VACACIONES';
                                            
                                            elseif ($marcaciones[$index2]['feriado'] != null):
                                            
                                            
                                                $observacion = $marcaciones[$index2]['feriado'];
                                            
                                            else :
                                            
                                            
                                            
                                                if($marcaciones[$index2]['agendamiento'] > 0 ):
                                                
                                                    $observacion = 'FALTA';
                                                
                                                else:
                                                
                                                    $dia =  date("N", strtotime($marcaciones[$index2]['fecha']));
                                                    
                                                    if($dia >= 1 && $dia <= 5):
                                                    
                                                        $observacion = 'FALTA';
                                                    
                                                    else:
                                                    
                                                        $observacion = 'DIA DE DESCANZO';
                                                    
                                                    endif;
                                                
                                                
                                                endif;
                                            
                                            
                                            endif;
                                    
                                    endif;
                                    
               ?>
      
      
              <?php if($tipo == 'T'):?>
                   <?php  $totalHoras += HorasToDecimal($thoras)?>
                   <?php  $cont += 1; ?>
                   <tr>
                      <td><?= $cont?></td>
                      <td><?= $marcaciones[$index2]['departamento'] ?></td>
                      <td><?= $marcaciones[$index2]['nombres'] ?></td>
                      <td><?= $entrada ?></td>
                      <td><?= $salida ?></td>
                      <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                      <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                      <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                      <td><?= $h25?></td>
                      <td><?= $h50?></td>
                      <td><?= $h100?></td>
                      <td><?= $observacion?></td>
                   </tr>
               <?php elseif($tipo == 'F' && $observacion == 'FALTA'):?>
                   <?php  $totalHoras += HorasToDecimal($thoras)?>
                   <?php  $cont += 1; ?>
                       <tr>
                        <td><?= $cont?></td>  
                          <td><?= $marcaciones[$index2]['departamento'] ?></td>
                          <td><?= $marcaciones[$index2]['nombres'] ?></td>
                          <td><?= $entrada ?></td>
                          <td><?= $salida ?></td>
                          <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                          <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                          <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                          <td><?= $h25?></td>
                          <td><?= $h50?></td>
                          <td><?= $h100?></td>
                          <td><?= $observacion?></td>
                       </tr>
                 <?php elseif($tipo == 'P' &&  $marcaciones[$index2]['permiso'] != null):?>
                  <?php  $totalHoras += HorasToDecimal($thoras)?>
                  <?php  $cont += 1; ?>
                   <tr>
                      <td><?= $cont?></td>
                      <td><?= $marcaciones[$index2]['departamento'] ?></td>
                      <td><?= $marcaciones[$index2]['nombres'] ?></td>
                      <td><?= $entrada ?></td>
                      <td><?= $salida ?></td>
                      <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                      <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                      <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                      <td><?= $h25?></td>
                      <td><?= $h50?></td>
                      <td><?= $h100?></td>
                      <td><?= $observacion?></td>
                   </tr>
                <?php elseif($tipo == 'V' &&  $marcaciones[$index2]['vacaciones'] > 0):?>
                 <?php  $totalHoras += HorasToDecimal($thoras)?>
                 <?php  $cont += 1; ?>
                   <tr>
                      <td><?= $cont?></td>
                      <td><?= $marcaciones[$index2]['departamento'] ?></td>
                      <td><?= $marcaciones[$index2]['nombres'] ?></td>
                      <td><?= $entrada ?></td>
                      <td><?= $salida ?></td>
                      <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                      <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                      <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                      <td><?= $h25?></td>
                      <td><?= $h50?></td>
                      <td><?= $h100?></td>
                      <td><?=$observacion?></td>
                   </tr>
                 <?php elseif($tipo == 'L' &&  $marcaciones[$index2]['agendamiento'] == 0):?>
                 <?php  $totalHoras += HorasToDecimal($thoras)?>
                  <?php  $cont += 1; ?>
                   <tr>
                     <td><?= $cont?></td>
                      <td><?= $marcaciones[$index2]['departamento'] ?></td>
                      <td><?= $marcaciones[$index2]['nombres'] ?></td>
                      <td><?= $entrada ?></td>
                      <td><?= $salida ?></td>
                      <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                      <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                      <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                      <td><?= $h25?></td>
                      <td><?= $h50?></td>
                      <td><?= $h100?></td>
                      <td><?= $observacion?></td>
                   </tr>
                <?php elseif($tipo == 'E' && $errorMarcacion == true):?>
                 <?php  $totalHoras += HorasToDecimal($thoras)?>
                  <?php  $cont += 1; ?>
                   <tr>
                     <td><?= $cont?></td>
                      <td><?= $marcaciones[$index2]['departamento'] ?></td>
                      <td><?= $marcaciones[$index2]['nombres'] ?></td>
                      <td><?= $entrada ?></td>
                      <td><?= $salida ?></td>
                      <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                      <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                      <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                      <td><?= $h25?></td>
                      <td><?= $h50?></td>
                      <td><?= $h100?></td>
                      <td><?= $observacion?></td>
                   </tr>
               <?php endif;?>
               
      <?php     endforeach;?>
     <?php  endforeach; ?>
            <tr>
               <th colspan="5" class="text-right">Total Horas: </th>
               <th><?= DecimaltoHoras( number_format($totalHoras, 2, '.', '')) ?></th>
               <th colspan="6"></th>
            </tr>
    </tbody>
  </table>
