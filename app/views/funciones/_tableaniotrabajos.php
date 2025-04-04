<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
AppAsset::register($this);
$con = 0;
 if($datos): ?>  
    <table id='tableempleados' class='table table-bordered table-condensed' style='background-color: white; font-size: 11px; width: 100%;'>
         <thead>
             <tr>
                <th width = '5%'>No</th>
                <th>Area</th>
                <th>Departamento</th>
                <th>Nombres</th>
                <th>Fecha</th>
                <th>Años Trabajando</th>
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
                <td><?= $data['nombres']?></td>
                <td><?= date('Y').'-'.date('m-d', strtotime($data['fecha_ingreso']))?></td>
                <td><?= $data['anio_trabajos']?></td>
               </tr>
            <?php endforeach;?>
         </tbody>    
    </table>
<?php endif;?>    
