<?php 
use yii\bootstrap\Html;
use app\models\SysRrhhEmpleadosLunch;
$holgura =  15;
//listado de funciones de calculos
echo $this->render('funciones');

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
<table id ="tablemodal" class="table table-bordered table-condensed" style="background-color: white; font-size: 11px; width: 100%">
    <thead>
     <tr>
      	<th colspan="14"></th>
      	<th colspan="6" class="text-center"><?= Html::input('submit',null, 'Guardar Horas Extras', ['class'=>"btn btn-xs btn-info", 'style'=> 'margin:1px;', "id"=>"btnGuardarHorasExtras"]);?></th>
      </tr>
     <tr>
        <th colspan= "14" class="text-right">
          Marcar / Desmarcar
        </th>
         <th colspan= "2">
         	 <?= Html::checkbox('chkSelect25', false, ['onclick'=> "select25(this)"]); ?>
         </th>
         <th colspan= "2">
          	 <?= Html::checkbox('chkSelect50', false, ['onclick'=> "select50(this)"]); ?>
         </th>
         <th colspan= "2">
         	 <?= Html::checkbox('chkSelect50', false, ['onclick'=> "select100(this)"]); ?>
         </th>
      </tr>
      <tr>
        <th>Area</th>
        <th>Departamento</th>
        <th>Nombres</th>
        <th>Cedula</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>T.Horas</th>
        <th>Atraso</th>
        <th>Sal.Temp</th>
        <th>h25</th>
        <th>h50</th>
        <th>h100</th>
        <th>Observación</th>
        <th>Acción</th>
        <th>25</th>
        <th>$(25)</th>
        <th>50</th>
        <th>$(50)</th>
        <th>100</th>
        <th>$(100)</th>
      </tr>
    </thead>
    <tbody>
         <?php 
         //verificamos si tiene marcacion
 
          $data =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));
          //sort($data)
         
          foreach ($data as $index => $Id):
    
                  $entrada        = '00:00:00';
                  $entrada_desayuno  = '00:00:00';
                  $salidadesayuno   = '00:00:00';
                  $entrada_almuerzo  = '00:00:00';
                  $salidaalmuerzo   = '00:00:00';
                  $entrada_merienda  = '00:00:00';
                  $salidamerienda   = '00:00:00';
                  $salida         = '00:00:00';
                  $thoras         = '00:00:00';
                  $h25            = '00:00:00';
                  $h50            = '00:00:00';
                  $h100           = '00:00:00';
                  $atraso         = '00:00:00';
                  $saltemp        = '00:00:00';
                  $horaentrada    = gethora_entrada($Id);
                  
                  //$observacion   = BuscaPermiso($fechaini, $data['id_sys_rrhh_cedula']);
                  $fecha_ent     = '';
                  $fecha_sal     = '';
                  $observacion   = '';
                  $contador = 0;
                  $contador2 = 0;
          
        
                   $marcaciones = array_filter($datos, array(new FilterData("id_sys_rrhh_cedula", $Id), 'getFilter'));
                   
                   if($marcaciones[$index]['fecha_marcacion'] != null):
                   
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
                   
                            if($contador == 1 or $contador > 2):
                            
                                if($marcaciones[$index]['permiso'] != null):
                                
                                     $observacion = $marcaciones[$index]['permiso'];
                   
                                else:
                                    
                                    $observacion = 'Error Marcación. El usuario tiene una o más marcaciones';
                                
                                endif;
                               
                            endif;

                            if($contador2 >= 1):

                                if($contador == 0):

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

                            endif;
                            
                            foreach ($marcaciones as $marcacion):

                                $comidas = SysRrhhEmpleadosLunch::find()->where(['id_sys_rrhh_cedula'=>$Id])->andWhere(['fecha'=>$marcacion['fecha_marcacion']])->all();
                                            
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
                                    
                                         if($marcaciones[$index]['agendamiento'] != -1):
                                            $horaentrada = gethora_agendamiento($marcaciones[$index]['agendamiento'],$marcaciones[$index]['fecha'],$Id);
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
        
                                    elseif($marcacion['tipo'] == 'SA'):
            
                                        $salidaalmuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
        
                                    elseif($marcacion['tipo'] == 'SM'):
            
                                        $salidamerienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
        
                                    
                                    /*elseif($marcacion['tipo'] == 'SD'):
                                                
                                        $salida_desayuno  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                    elseif($marcacion['tipo'] == 'SA'):

                                        $salida_almuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                    elseif($marcacion['tipo'] == 'SM'):

                                        $salida_merienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));
                                    */    
                                    endif;
                            
                            endforeach;
                            
                            if( $contador == 2):
                            
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
                                         
                                         //Horas extras 50                   
                                         if($marcaciones[$index]['h50'] > 0):
                                         
                                              $h50 = DecimaltoHoras($marcaciones[$index]['h50']);
                                            
                                         else:
                                         
                                              $h50 = getRendonminutos(gethoras50($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index]['id_sys_rrhh_cedula'], $marcaciones[$index]['fecha'], $marcaciones[$index]['feriado']));
                                         
                                         endif;
                                         
                                         //Horas extras 100
                                         if ($marcaciones[$index]['h100'] > 0):
                                         
                                                $h100  = DecimaltoHoras($marcaciones[$index]['h100']);
                                         else:
                                         
                                                $h100  = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$salidadesayuno,$salidaalmuerzo,$salidamerienda,$marcaciones[$index]['id_sys_rrhh_cedula'], $marcaciones[$index]['fecha'], $marcaciones[$index]['feriado'],$marcaciones[$index]['agendamiento']));
                                         
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
                                     
                                        $observacion = 'DIA DE DESCANSO';
                                     
                                     endif;
                                 
                                 
                                 endif;
                         
                         endif;
                    
                    endif;

                ?>
                
                 <tr>
                  <td><?= $marcaciones[$index]['area'] ?></td>
                  <td><?= $marcaciones[$index]['departamento'] ?></td>
                  <td><?= $marcaciones[$index]['nombres'] ?></td>
                  <td><?= $marcaciones[$index]['id_sys_rrhh_cedula'] ?></td>
                  <td><?= $entrada ?></td>
                  <td><?= $salida ?></td>
                  <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                  <td bgcolor= "<?= $atraso  != "00:00:00" ? '#ffeeba': ''?>" ><?= $atraso?></td>
                  <td bgcolor= "<?= $saltemp != "00:00:00" ? '#ffeeba': ''?>" ><?= $saltemp?></td>
                  <td id ="<?= "td-".$marcaciones[$index]['id_sys_rrhh_cedula']."-25"?>" bgcolor= "<?= $marcaciones[$index]['h25'] > 0 ? '#85f387': ''?>"><?= $h25 ?></td>
                  <td id ="<?= "td-".$marcaciones[$index]['id_sys_rrhh_cedula']."-50"?>" bgcolor= "<?= $marcaciones[$index]['h50'] > 0 ? '#85f387': ''?>"><?= $h50 ?></td>
                  <td id ="<?= "td-".$marcaciones[$index]['id_sys_rrhh_cedula']."-100"?>" bgcolor= "<?= $marcaciones[$index]['h100'] > 0 ? '#85f387': ''?>"><?= $h100 ?></td>
                  <td><?= $observacion?></td>
                  <td style= "text-align: center">
                     <?= Html::input('submit',null, 'Editar', ['class'=>"btn btn-xs btn-primary", 'style'=> 'margin:1px;','onclick'=> "EditarMarcacion('{$marcaciones[$index]['id_sys_rrhh_cedula']}','{$marcaciones[$index]['fecha']}');"]);?>
                  </td>
                  <td>
                      <?php
                        if ($h25 != '00:00:00') :
                            echo  Html::checkbox('pago25', $marcaciones[$index]['pago25'] == 1 ?  true : false, ['id'=> "{$marcaciones[$index]['id_sys_rrhh_cedula']}-25", 'onclick'=> "Add25(this);", "data-field" => "chkSelect25"]);
                        endif;
                       ?>
                  </td>
                  <td id ="<?= "td2-".$marcaciones[$index]['id_sys_rrhh_cedula']."-25"?>"  bgcolor= "<?= $marcaciones[$index]['pago25'] == 1 ? '#85f387': ''?>">
                      <?= Html::hiddenInput('h25', number_format(HorasToDecimal($h25), 2, ',', '.'), ['id'=> "".$marcaciones[$index]['id_sys_rrhh_cedula']."" ]) ?>
                      <?=number_format((($marcaciones[$index]['valor_hora'] * 0.25) *(HorasToDecimal($h25))), 2,',', '.' )?>
                  </td>
                  <td>
                   	  <?php 
                        if($h50 != '00:00:00'):
                            echo  Html::checkbox('pago50',$marcaciones[$index]['pago50'] == 1 ?  true : false, ['id'=> "{$marcaciones[$index]['id_sys_rrhh_cedula']}-50", 'onclick'=> "Add50(this);", "data-field" => "chkSelect50"]);
                        endif;
                      ?>
                  </td>
                  <td id ="<?= "td2-".$marcaciones[$index]['id_sys_rrhh_cedula']."-50"?>" bgcolor= "<?= $marcaciones[$index]['pago50'] == 1 ? '#85f387': ''?>">
                  	  <?= Html::hiddenInput('h50', number_format(HorasToDecimal($h50),2, ',', '.'), ['id'=> "".$marcaciones[$index]['id_sys_rrhh_cedula']."" ]) ?>
                 	  <?=number_format(((($marcaciones[$index]['valor_hora'] * 0.50) + $marcaciones[$index]['valor_hora']) *(HorasToDecimal($h50))), 2,',', '.' )?>
                  </td>
                  <td>
                      <?php
                        if($h100 != '00:00:00'): 
                            echo Html::checkbox('pago100', $marcaciones[$index]['pago100'] == 1 ?  true : false , ['id'=> "{$marcaciones[$index]['id_sys_rrhh_cedula']}-100", 'onclick'=> "Add100(this);", "data-field" => "chkSelect100"]);
                        endif;
                       ?>
                  </td>
                  <td id ="<?= "td2-".$marcaciones[$index]['id_sys_rrhh_cedula']."-100"?>" bgcolor= "<?= $marcaciones[$index]['pago100'] == 1 ? '#85f387': ''?>">
                      <?= Html::hiddenInput('h100', number_format( HorasToDecimal($h100),2,',', '.'), ['id'=> "".$marcaciones[$index]['id_sys_rrhh_cedula']."" ])?>
                      <?=number_format((($marcaciones[$index]['valor_hora'] * 2) *(HorasToDecimal($h100))), 2,',', '.' )?>
                  </td>
               </tr>
               
         <?php  endforeach; ?>
    </tbody>
  </table>