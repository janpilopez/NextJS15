<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$con = 0;

 if($datos): ?>  
   <table class="table table-bordered table-condensed" style="background-color: white; font-size: 8px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Nombres y Apellidos</th>
                <th>Cédula</th>
                <th>Área</th>
                <th>Departamento</th>
                <th>Motivo de salida</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Salida</th>
                <th>Género</th>
                <th>Tiempo Laborado</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($datos as $data):
               $con++;
           ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['nombres']?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['area']?></td>
                <td><?= $data['departamento']?></td>
                <td><?= $data['descripcion']?></td>
                <td><?= $data['fecha_ingreso']?></td>
                <td><?= $data['fecha_salida']?></td>
                <td><?= $data['genero']?></td>
                <td><?= tiempoTranscurridoFechas($data['fecha_ingreso'], $data['fecha_salida'])?></td>
             </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>    



<?php 

function tiempoTranscurridoFechas($fechaInicio,$fechaFin)
{
    $fecha1 = new DateTime($fechaInicio);
    $fecha2 = new DateTime($fechaFin);
    $fecha = $fecha1->diff($fecha2);
    $tiempo = "";
    
    //años
    if($fecha->y > 0)
    {
        $tiempo .= $fecha->y;
        
        if($fecha->y == 1)
            $tiempo .= " año, ";
            else
                $tiempo .= " años, ";
    }
    
    //meses
    if($fecha->m > 0)
    {
        $tiempo .= $fecha->m;
        
        if($fecha->m == 1)
            $tiempo .= " mes, ";
            else
                $tiempo .= " meses, ";
    }
    
    //dias
    if($fecha->d > 0)
    {
        $tiempo .= $fecha->d;
        
        if($fecha->d == 1)
            $tiempo .= " día, ";
            else
                $tiempo .= " días, ";
    }
    
    //horas
    if($fecha->h > 0)
    {
        $tiempo .= $fecha->h;
        
        if($fecha->h == 1)
            $tiempo .= " hora, ";
            else
                $tiempo .= " horas, ";
    }
    
        return $tiempo;
}




?> 
