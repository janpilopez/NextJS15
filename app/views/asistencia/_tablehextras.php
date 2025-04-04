<?php

use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Debug\Tests\Fixtures\ToStringThrower;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhEmpleados;

$holgura =  15;
//listado de funciones de calculos
echo $this->render('funciones');
$meses =  Yii::$app->params['meses'];
$dias =   Yii::$app->params['dias'];

class FilterColumn {
  private $colName;
  
  function __construct($colName) {
      $this->colName = $colName;
  }
  
  function getValues($i) {
      
      return $i[$this->colName] ;
  }
}
class FilterData {
  private $colName;
  private $value;
  
  function __construct($colName, $value) {
      $this->colName = $colName;
      $this->value = $value;
  }
  function getFilter($i) {
      return $i[$this->colName] == $this->value;
  }
}

?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
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
             
      $dataFilterIdSysRrhhCedula =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));  
      $totalv25 = 0;
      $totalv50 = 0;
      $totalv100 = 0; 
      $con = 0;
      foreach ($dataFilterIdSysRrhhCedula as $index => $id_sys_rrhh_cedula):  
        $con+=1;
        $area = '';
        $deparamento = '';
        $nombres = '';
        $arrayData   = array_filter($datos, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
        $horas = '';
        $horasDecimal  = 0;
        $h25 = 0;
        $h50 = 0;
        $h100 = 0;
        $tv25 = 0;
        $tv50 = 0;
        $tv100 = 0;
        $totalh25 = 0;
        $totalh50 = 0;
        $totalh100 = 0;
        $valorhora = 0;

        $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
        ->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->andWhere(['estado'=> 'A'])
        ->scalar();

        $contratoemp  =  SysRrhhEmpleadosContratos::find()
        ->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
        ->one();

        $empleado     = SysRrhhEmpleados::find()
        ->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->one();

        $cargoemp     =  SysAdmCargos::find()
        ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
        ->one();

        if($contratoemp->fecha_salida == null || date('n',strtotime($contratoemp->fecha_salida)) == date('n',strtotime($fechafin)) || $contratoemp->fecha_salida > $fechafin):
        
          if($cargoemp->reg_horas_extras == 'S'):

            foreach ($arrayData as $index => $row):
                      
              $area = $row['area'];
              $deparamento = $row['departamento'];
              $nombres = $row['nombres'];  
              $h25 += $row['h25'];
              $h50 += $row['h50'];
              $h100 += $row['h100'];
                    
            endforeach;
          
            $valorhora    =  floatval($sueldoemp/240);

            $val25 = floatval(($valorhora * 0.25) * $h25);

            $newvalor = floatval(($valorhora * 0.50)) + $valorhora;
                    
            $val50  = floatval($newvalor * floatval($h50));

            $val100   = floatval(($valorhora * 2) * $h100);


            $totalv25  += number_format($val25, 2, '.', '');
            $totalv50  += number_format($val50, 2, '.', '');
            $totalv100 += number_format($val100, 2, '.', '');

            foreach ($datos as $index  => $data):

              $totalh25  += $data['h25'];
              $totalh50  += $data['h50'];
              $totalh100 += $data['h100'];

            endforeach; 
        
      
    ?> 
             
    <tr>
      <td><?= $con?></td>
      <td><?= $area?></td>
      <td><?= $deparamento?></td>
      <td><?= $id_sys_rrhh_cedula?></td>
      <td><?= $nombres ?></td>
      <td><?= DecimaltoHoras(number_format($h25, 2, '.', ''))?></td> 
      <td><?= $val25 != 0 ? number_format($val25, 2, '.', '') : '.00' ?></td> 
      <td><?= DecimaltoHoras(number_format($h50, 2, '.', ''))?></td> 
      <td><?= $val50 != 0 ? number_format($val50, 2, '.', '') : '.00' ?></td> 
      <td><?= DecimaltoHoras(number_format($h100, 2, '.', ''))?></td> 
      <td><?= $val100 != 0 ? number_format($val100, 2, '.', '') : '.00' ?></td> 
    </tr>        
    <?php
          endif;  
        endif;        
      endforeach;
    ?>
    </tbody>
    <tfoot>
     <tr style="background-color: #ccc">
        <th colspan="5" style="text-align: right;">Total General</th>
        <th><?=DecimaltoHoras(number_format($totalh25, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv25, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv50, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv100, 2, '.', '')?></th>
     </tr>
    </tfoot>
</table>   
    