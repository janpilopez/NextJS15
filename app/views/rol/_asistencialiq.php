<?php 
use app\models\SysRrhhEmpleados;
use yii\bootstrap\Html;
           
 if(count($datos)> 0):  
 
 ?>
	 <table  class="table table-bordered" style="background-color: white; font-size: 11px; width: 100%;">
          <thead>
            <tr class = "info">
                  <td colspan= "6"><b><?=$empleado->nombres?> </b></td>
            </tr>
            <tr>
               <th>Fecha</th>
               <th>Día</th>
               <th>Valor</th>
               <th>Falta</th>
               <th>Permiso</th>
               <th>Hoas Permiso</th>
            </tr>
          <thead>
          <tbody>
           <?php foreach ($datos as $data):
            $class = '';
           
            
            if($data['faltas']== '1'):
                
                $class = 'danger';
                
            elseif($data['permisos']== '1'):
             
                $class = 'warning';
          
           endif;
          
           ?>
             <tr class = "<?= $class ?>">
               <td><?= $data['fecha']?></td>
               <td><?= $data['dias']?></td>
               <td><?= number_format($data['sueldoliq'], 2, '.', ',')?></td>
               <td><?= $data['faltas']?></td>
               <td><?= $data['permisos']?></td>
               <td><?= number_format($data['horaspermiso'], 2, '.', ',')  ?></td>
             </tr>
            <?php endforeach;?>
          </tbody> 
    </table>
 <?php endif;?>