<?php 

use app\models\SysRrhhComedor;
use yii\helpers\Html;
use app\models\SysRrhhEmpleadosLunch;

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
        <th rowspan="2">No</th>
        <th rowspan="2">Departamento</th>
        <th rowspan="2">Nombres</th>
        <th style="text-align: center" colspan="3">Desayuno</th>
        <th style="text-align: center" colspan="3">Almuerzo</th>
        <th style="text-align: center" colspan="3">Merienda</th>
      </tr>
      <tr  style="background-color: #ccc">   
            <td style="text-align: center"><strong>Entrada</strong></td>
            <td style="text-align: center"><strong>Salida</strong></td>
            <td style="text-align: center"><strong>Total</strong></td>
            <td style="text-align: center"><strong>Entrada</strong></td>
            <td style="text-align: center"><strong>Salida</strong></td>
            <td style="text-align: center"><strong>Total</strong></td>
            <td style="text-align: center"><strong>Entrada</strong></td>
            <td style="text-align: center"><strong>Salida</strong></td>
            <td style="text-align: center"><strong>Total</strong></td>
        </tr> 
    </thead>
    <tbody>
     <?php 
             $data =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
             $cont = 0;
             $totalHorasDesayuno = 0;
             $totalHorasAlmuerzo = 0;
             $totalHorasMerienda = 0;
             //$totalHorasComedor = 0;
             //sort($data)
             foreach ($data as $index => $fecha):   
      ?>
              <tr  style="background-color: #ccc">
                  <td colspan="15"><strong>  <?= $dias[date('N',strtotime($fecha))]." ".date('d',strtotime($fecha))." de ".$meses[date('n',strtotime($fecha))] ." del ".date('Y', strtotime($fecha)) ?> </strong></td>
               </tr>  
               
       <?php 
       
                  $fechaAsistencia = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                  
                  $dataAsistencia =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $fechaAsistencia));
                  
                  
                  foreach ($dataAsistencia as $index2 => $id_sys_rrhh_cedula):
                  
                    $entradadesayuno = '00:00:00';
                    $salidadesayuno  = '00:00:00';
                    $thorasdesayuno  = '00:00:00';
                    $entradaalmuerzo = '00:00:00';
                    $salidaalmuerzo  = '00:00:00';
                    $thorasalmuerzo  = '00:00:00';
                    $entradamerienda = '00:00:00';
                    $salidamerienda  = '00:00:00';
                    $thorasmerienda  = '00:00:00';
                

                    $salida          = '00:00:00';
                          
                    $fecha_sal       = '';
                          
                    $observacion     = '';

                    $tiempo_desayuno = SysRrhhComedor::find()->where(['id_sys_rrhh_comedor' => 1])->one();
                    $tiempo_almuerzo = SysRrhhComedor::find()->where(['id_sys_rrhh_comedor' => 2])->one();
                    $tiempo_merienda = SysRrhhComedor::find()->where(['id_sys_rrhh_comedor' => 3])->one();

                    $td = $tiempo_desayuno->tiempo_descuento;
                    $ta = $tiempo_almuerzo->tiempo_descuento;
                    $tm = $tiempo_merienda->tiempo_descuento;

                    $activodesayuno = 0;
                    $activoalmuerzo = 0;
                    $activomerienda = 0;
                  
                    $marcaciones = array_filter($fechaAsistencia, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
                      
                        foreach ($marcaciones as $marcacion):
                                    
                            if($marcacion['fecha_marcacion'] != NULL):
                                
                                $comidas = SysRrhhEmpleadosLunch::find()->where(['id_sys_rrhh_cedula'=>$id_sys_rrhh_cedula])->andWhere(['fecha'=>$marcacion['fecha']])->all();
                            
                                if($comidas):

                                    foreach($comidas as $index => $item):
    
                                        if($item->id_sys_rrhh_comedor == 1):
    
                                            $entradadesayuno = date('H:i:s', strtotime($item->hora));
                                                        
                                        elseif($item->id_sys_rrhh_comedor == 2):
    
                                            $entradaalmuerzo = date('H:i:s', strtotime($item->hora));
                                                        
                                        else:
                                                            
                                            $entradamerienda = date('H:i:s', strtotime($item->hora));
    
                                        endif;
                                                    
                                    endforeach;
    
                                endif;

                                if($marcacion['tipo'] == 'SD'):
                                                
                                    $salidadesayuno  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                elseif($marcacion['tipo'] == 'SA'):
    
                                    $salidaalmuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                elseif($marcacion['tipo'] == 'SM'):
    
                                    $salidamerienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                endif;
    
                            elseif($marcacion['agendamiento'] == -1):
                                
                                $comidas = SysRrhhEmpleadosLunch::find()->where(['id_sys_rrhh_cedula'=>$id_sys_rrhh_cedula])->andWhere(['fecha'=>$marcacion['fecha']])->all();

                                if($comidas):

                                    foreach($comidas as $index => $item):
    
                                        if($item->id_sys_rrhh_comedor == 1):
    
                                            $entradadesayuno = date('H:i:s', strtotime($item->hora));
                                                        
                                        elseif($item->id_sys_rrhh_comedor == 2):
    
                                            $entradaalmuerzo = date('H:i:s', strtotime($item->hora));
                                                        
                                        else:
                                                            
                                            $entradamerienda = date('H:i:s', strtotime($item->hora));
    
                                        endif;
                                                    
                                    endforeach;
    
                                endif;

                                if($marcacion['tipo'] == 'SD'):
                                                
                                    $salidadesayuno  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                elseif($marcacion['tipo'] == 'SA'):
    
                                    $salidaalmuerzo  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                elseif($marcacion['tipo'] == 'SM'):
    
                                    $salidamerienda  =  date('H:i:s', strtotime($marcacion['fecha_marcacion']));

                                endif;

                            endif;

                        endforeach;
                                        
                    $thorasdesayuno = getTotalhorascomedor($entradadesayuno, $salidadesayuno);
                    $thorasalmuerzo = getTotalhorascomedor($entradaalmuerzo, $salidaalmuerzo);
                    $thorasmerienda = getTotalhorascomedor($entradamerienda, $salidamerienda);
                                    
               ?>
      
            <?php
            
            $totalHorasDesayuno += HorasToDecimalComedor($thorasdesayuno);
            $totalHorasAlmuerzo += HorasToDecimalComedor($thorasalmuerzo);
            $totalHorasMerienda += HorasToDecimalComedor($thorasmerienda);

            //$thorasdesayuno = HorasToDecimalComedor($thorasdesayuno);
            
            ?>
            <?php  $cont += 1; ?>
                <tr>
                    <td><?= $cont?></td>
                    <td><?= $marcaciones[$index2]['departamento'] ?></td>
                    <td><?= $marcaciones[$index2]['nombres'] ?></td>
                    <td><?= $entradadesayuno != '00:00:00' ? $entradadesayuno : '' ?></td>
                    <td><?= $salidadesayuno != '00:00:00' ? $salidadesayuno : '' ?></td>
                    <td bgcolor= "<?= $thorasdesayuno  > $td ? '#FF8E8E': ''?>"><?= $thorasdesayuno  != "00:00:00" ? '<b>'.$thorasdesayuno.'</b>' : '' ?></td>
                    <td><?= $entradaalmuerzo != '00:00:00' ? $entradaalmuerzo : '' ?></td>
                    <td><?= $salidaalmuerzo != '00:00:00' ? $salidaalmuerzo : '' ?></td>
                    <td bgcolor= "<?= $thorasalmuerzo  > $ta ? '#FF8E8E': ''?>"><?= $thorasalmuerzo  != "00:00:00" ? '<b>'.$thorasalmuerzo.'</b>' : '' ?></td>
                    <td><?= $entradamerienda != '00:00:00' ? $entradamerienda : '' ?></td>
                    <td><?= $salidamerienda != '00:00:00' ? $salidamerienda : '' ?></td>
                    <td bgcolor= "<?= $thorasmerienda  > $tm ? '#FF8E8E': ''?>"><?= $thorasmerienda  != "00:00:00" ? '<b>'.$thorasmerienda.'</b>' : '' ?></td>
                </tr>
      <?php endforeach;?>
     <?php endforeach; ?>
            <tr>
               <th colspan="5" class="text-right">Total Horas: </th>
               <th><?= DecimaltoHorasComedor($totalHorasDesayuno) ?></th>
               <th colspan="2"></th>
               <th><?= DecimaltoHorasComedor($totalHorasAlmuerzo) ?></th>
               <th colspan="2"></th>
               <th><?= DecimaltoHorasComedor($totalHorasMerienda) ?></th>
            </tr>
    </tbody>
  </table>
