<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$con = 0;
$total_recibir = 0;
$total_pago = 0;
 if($datos): ?>  
   <table class="table table-bordered table-condensed" style="<?= $estilo?>">
         <thead>
             <tr>
                <th width = "5%" >No</th>
                <th width = "12%">Cédula</th>
                <th width = "35%">Nombres</th>
                <th width = "25%">Departamento</th>
                <th width = "10%">Neto Recibir</th>
                <th width = "13%">Firma</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($datos as $data):
             $con++;
             $total_recibir = $data['Total'];
             $total_pago +=  $total_recibir;
           ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['nombres']?></td>
                <td><?= $data['departamento']?></td>
                <td class="text-right"><?= "$".number_format($total_recibir, 2, '.', ',');?></td>
                <td></td>
             </tr>
            <?php endforeach;?>
            <tr>
               <td colspan="4" style="text-align: right"><b>Total a pagar</b></td>
               <td colspan="2" style="text-align: left;"><b> <?="$".number_format($total_pago, 2,'.', ',') ?></b></td>
            </tr>
         </tbody>    
    </table>
<?php endif;?>    
