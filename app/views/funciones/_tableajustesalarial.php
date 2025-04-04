<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;

$con = 0;

 if($datos): ?>  
   <table class="table table-bordered table-condensed" style="background-color: white; font-size: 8px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Cédula</th>
                <th>Nombres y Apellidos</th>
                <th>Área</th>
                <th>Departamento</th>
                <th>Cargo</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Sueldo Anterior</th>
                <th>Sueldo Anterior</th>
                <th>Fecha Sueldo</th>
                <th>Sueldo</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($datos as $data):
               $con++;

               $sueldoAnt = getObtenerSueldoAnterior($data['id_sys_rrhh_cedula'],$data['fecha']);
           ?>
             <tr>
                <td><?= $con?></td> 
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['nombres']?></td>
                <td><?= $data['area']?></td>
                <td><?= $data['departamento']?></td>
                <td><?= $data['cargo']?></td>
                <td><?= $data['fecha_ingreso']?></td>
                <td><?= $sueldoAnt['fecha']?></td>
                <td><?= $sueldoAnt['sueldo']?></td>
                <td><?= $data['fecha']?></td>
                <td><?= $data['sueldo']?></td>
             </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>    

<?php 

function getObtenerSueldoAnterior($cedula, $fecha){

    $db    = $_SESSION['db'];
        
    return   Yii::$app->$db->createCommand("[dbo].[ObtenerSuldoAnterior] @id_sys_rrhh_cedula = '{$cedula}', @fecha = '{$fecha}'")->queryOne();

}


?>