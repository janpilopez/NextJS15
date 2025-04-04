<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
use yii\bootstrap\Html;
AppAsset::register($this);
$con = 0;
$dias = 0;
$uti_empleados = 0;
$total_uti_empleados = 0;
$uti_cargas = 0;
$total_uti_cargas = 0;
$tribunal = 0;
$total_tribunal = 0;
$utilidad= 0;
$total_utilidad = 0;

?>
 <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 11px; width: 100%;">
         <thead>
             <tr>
                <th width = "5%">No</th>
                <th>Empresa</th>
                <th>Ruc</th>
                <th>Nombres</th>
                <th>Cédula</th>
                <th>Genero</th>
                <th>Estado</th>
                <th>Dias</th>
                <th>Valor Empleado</th>
                <th># Carga</th>
                <th>Valor Carga</th>
                <th>Tribunal</th>
                <th>Total</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($modeldet as $data):
              
               $con++;
           
               $total_uti_empleados += $data['uti_empleados'];
               
               $total_uti_cargas += $data['uti_cargas'];
               
               $total_tribunal+= $data['tribunal'];
               
               $utilidad = ($data['total_uti']) - ($data['tribunal']);
               
               $total_utilidad+= $utilidad               
               ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['razon_social']?></td>
                <td><?= $data['ruc']?></td>
                <td><?= $data['nombres']?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['genero']?></td>
                <td><?= $data['estado'] == 'A' ? 'Activo': 'Inactivo'?></td>
                <td><?= $data['dias']?></td>
                <td class="text-right"><?=  number_format($data['uti_empleados'], 2 , '.', '');?></td>
                <td class="text-right"><?=  number_format($data['cargas_familiares'],2, '.', '');?></td>
                <td class="text-right"><?=  number_format($data['uti_cargas'],2, '.', '')?></td>
                <td class="text-right"><?=  number_format($data['tribunal'],2, '.', '');?></td>
                <td class="text-right"><?=  number_format(($data['total_uti'] - $data['tribunal']), 2 , '.', '');?></td>
             </tr>
            <?php endforeach;?>
         </tbody> 
         <tfoot>
           <tr>
              <th colspan="7" class="text-right">Total</th>
              <th class="text-right"><?= number_format( array_sum(array_column($modeldet, 'dias')), 2, '.', ',')?></th>
              <th class="text-right"><?= number_format($total_uti_empleados, 2, '.', ',')?></th>
              <th class="text-right"><?= number_format( array_sum(array_column($modeldet, 'cargas_familiares')), 2, '.', ',')?></th>
              <th class="text-right"><?= number_format($total_uti_cargas, 2, '.', ',')?></th>
              <th class="text-right"><?= number_format($total_tribunal, 2, '.', ',')?></th>
              <th class="text-right"><?= number_format($total_utilidad, 2, '.', ',')?></th>
           </tr>
         </tfoot>
</table>
  
