<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhPrestamosDet;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$con = 0;
 if($datos): ?>  
   <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Área</th>
                <th>Departamento</th>
                <th>Cédula</th>
                <th>Nombres y Apellidos</th>
                <th>Cargo</th>
                <th>Fecha Prestamo</th>
                <th>Mes Inicio Pago</th>
                <th>Mes Final Pago</th>
                <th>Monto Total</th>
                <th>Cuotas</th>
                <th>Valor Cuotas</th>
             </tr>
         </thead>
         <tbody>
         <?php foreach ($datos as $data):
               
                $mesfinal = $data['mes_ini']+$data['coutas']-1;
                $mesfinalCond = $data['mes_ini']+$data['coutas']-1;

                $anioactual = date('Y',strtotime($data['fecha']));

                if($mesfinal > 12):

                    $mesfinal = $mesfinal - 12;
                    $aniosiguiente = date('Y',strtotime($data['fecha']."+1 years"));

                else:
                
                    $aniosiguiente = date('Y',strtotime($data['fecha']));

                endif;

                $valores = [];
                $valoresT = "";

                $cuotas = obtenerCoutasPrestamo($data['id_sys_rrhh_prestamos_cab']);

                foreach($cuotas as $couta){
                    array_push($valores, $couta['valor']);
                }

                $valoresNo = array_unique($valores);
                foreach($valoresNo as $index){
                    $valoresT .= $index." ";
                }
                if($mesfinalCond >= date('n') && $aniosiguiente >= date('Y')):
                    $con++;
           ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['area']?></td>
                <td><?= $data['departamento']?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['nombres']?></td> 
                <td><?= $data['cargo']?></td>
                <td><?= $data['fecha']?></td>
                <td><?= $meses[$data['mes_ini']]?> / <?= $anioactual?></td>
                <td><?= $meses[$mesfinal] ?> / <?= $aniosiguiente?></td>
                <td><?= $data['valor']?></td>
                <td><?= $data['coutas']?></td>
                <td><?= $valoresT ?></td>
             </tr>
            <?php endif;
            endforeach;?>
        </tbody>
    </table>
<?php endif;?>    

<?php

function obtenerCoutasPrestamo($id){
    
    return SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_cab' => $id])->all(); 
}

?>

 
