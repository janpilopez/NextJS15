<?php 
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhEmpleados;

//listado de funciones de calculos
echo $this->render('funciones');

?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Fecha</th>
        <th>Area</th>
        <th>Departamento</th>
        <th>Identificación</th>
        <th>Nombres</th>
        <th>(H)25</th> 
        <th>($)25</th> 
        <th>(H)50</th>
        <th>($)50</th>
        <th>(H)100</th>
        <th>($)100</th> 
      </tr>
    </thead>
    <tbody>
    <?php 
    
      $totalh25 = 0;
      $totalv25 = 0;
      $totalh50 = 0;
      $totalv50 = 0;
      $totalh100 = 0;
      $totalv100 = 0;
   
    foreach ($datos as $index  => $data):

      $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
      ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
      ->andWhere(['estado'=> 'A'])
      ->scalar();

      $contratoemp  =  SysRrhhEmpleadosContratos::find()
      ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
      ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
      ->one();

      $empleado     = SysRrhhEmpleados::find()
      ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
      ->one();

      $cargoemp     =  SysAdmCargos::find()
      ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
      ->one();
    
      if($contratoemp->fecha_salida == null || date('n',strtotime($contratoemp->fecha_salida)) == date('n',strtotime($fechafin)) || $contratoemp->fecha_salida > $fechafin):
        
        if($cargoemp->reg_horas_extras == 'S'):
          

          $valorhora    =  floatval($sueldoemp/240);

          $val25 = floatval(($valorhora * 0.25) * $data['h25']);

          $newvalor = floatval(($valorhora * 0.50)) + $valorhora;
                    
          $val50  = floatval($newvalor * floatval($data['h50']));

          $val100   = floatval(($valorhora * 2) * $data['h100']);


          $totalv25  += number_format($val25, 2, '.', '');
          $totalv50  += number_format($val50, 2, '.', '');
          $totalv100 += number_format($val100, 2, '.', '');

          $totalh25  += $data['h25'];
          $totalh50  += $data['h50'];
          $totalh100 += $data['h100'];
      
    ?>
     <tr>
       <td><?=$index +1?></td>
       <th><?=$data['fecha']?></th>
       <td><?=$data['area']?></td>
       <td><?=$data['departamento']?></td>
       <td><?=$data['id_sys_rrhh_cedula']?></td>
       <td><?=$data['nombres']?></td>
       <td><?= DecimaltoHoras(number_format($data['h25'], 2, '.', ''))?></td> 
       <td><?= $val25 != 0 ? number_format($val25, 2, '.', '') : '.00' ?></td> 
       <td><?= DecimaltoHoras(number_format($data['h50'], 2, '.', ''))?></td> 
       <td><?= $val50 != 0 ? number_format($val50, 2, '.', '') : '.00' ?></td> 
       <td><?= DecimaltoHoras(number_format($data['h100'], 2, '.', ''))?></td> 
       <td><?= $val100 != 0 ? number_format($val100, 2, '.', '') : '.00' ?></td>
     </tr>
    <?php endif;
      endif;
    endforeach;?>
    </tbody>
    <tfoot>
     <tr style="background-color: #ccc">
       <th colspan="6" style="text-align: right;">Total General</th>
       <th><?=DecimaltoHoras(number_format($totalh25, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv25, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv50, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv100, 2, '.', '')?></th>
     </tr>
     </tr>
    </tfoot>
  </table>
