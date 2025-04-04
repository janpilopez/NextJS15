<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use yii\data\Sort;
if($datos):
?>  
            <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="11%">Identificación</th>
                    <th>Nombres</th>
                    <th>Area</th>
                    <th>Departamento</th>
                  </tr>
                </thead>
                <tbody>
                   <?php foreach ($datos as $index => $data):?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['id_sys_rrhh_cedula']?></td>
                        <td><?= $data['nombres']?></td>
                        <td><?= $data['area']?></td>
                        <td><?= $data['departamento']?></td>
                     </tr>
                   <?php endforeach;?>
                </tbody> 
             </table>        
<?php endif;?>    

 