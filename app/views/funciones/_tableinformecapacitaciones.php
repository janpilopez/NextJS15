<?php
/* @var $this yii\web\View */

use yii\bootstrap\Html;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$con = 0;

 if($datos): ?>  
   <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Área</th>
                <th>Departamento</th>
                <th>Cédula</th>
                <th>Nombres y Apellidos</th>
                <th>Cargo</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($datos as $data):
               $con++;
           ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['area']?></td>
                <td><?= $data['departamento']?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['nombres']?></td> 
                <td><?= $data['cargo']?></td>
             </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>    


 
