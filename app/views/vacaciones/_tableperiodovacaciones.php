<?php
/* @var $this yii\web\View */

$con = 0;

 if($datos): ?>  
   <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Area</th>
                <th>Departamento</th>
                <th>Cedula</th>
                <th>Nombres y Apellidos</th>
                <th>Fecha Ing.</th>
                <th>Periodo</th>
                <th>D.Disp</th>
                <th>D.Otor</th>
                <th>D.Pen</th>
                <th>Estado</th>
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
                <td><?= $data['fechaing']?></td>
                <td><?= $data['periodo']?></td> 
                <td><?= $data['dias_disponibles']?></td> 
                <td><?= $data['dias_otorgados']?></td> 
                <td><?= $data['dias_disponibles'] - $data['dias_otorgados']?></td> 
                <td class="<?= $data['estado']== 'T'? 'success': 'warning'?>"><?= $data['estado']== 'T'? 'Gozados': 'Pendientes'?></td>
             </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>    


 
