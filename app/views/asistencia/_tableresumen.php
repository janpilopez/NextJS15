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
?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Fecha</th>
        <th>Area</th>
        <th>Departamento</th>
        <th>Horas Laboradas</th>
   </thead>
   <body>
   <?php if ($datos):
   
             $data_fecha =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
             $indice = 0;
           
             $cont = 0;
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
                                                            $total_horas += $horas_decimal;  
                                                     
                                                    endif;
                                                    
                                                endif;

                                            endif;
                                 
                                      endforeach;

                                      if ($total_horas > 0):
                                      
                                          $horas_resumen += ($total_horas/$cont); 
                                          $cont_resumen ++;
                                        
                                      endif;
                                      ?>
                                      <tr>
                                         <td><?= $indice?></td>
                                         <td><?= $departamento[$indexDepartamento]['fecha']?></td>
                                         <td><?= $departamento[$indexDepartamento]['area']?></td>
                                         <td><?= $departamento[$indexDepartamento]['departamento']?></td>
                                         <td><?= DecimaltoHoras(number_format($total_horas > 0 ? $total_horas/$cont : 0, 2, '.', ''));?></td>
                                    </tr>
                                  <?php 
                                  endforeach;  
                                  
                        endforeach;  
                        
             endforeach;
             
       endif;?>
   </body>
   <tfoot>
     <tr>
     	<th colspan="4" class="text-right">TOTAL HORAS</th>
     	<th><?=DecimaltoHoras(number_format($horas_resumen, 2, '.', ''));?></th>
     </tr>
     <tr>
     	<th colspan="4" class="text-right">PROMEDIO HORAS</th>
     	<th> <?= $cont_resumen > 0 ?  DecimaltoHoras(number_format($horas_resumen/$cont_resumen, 2, '.', '')) : '00:00:00';?></th>
     </tr>
   </tfoot>
</table>

