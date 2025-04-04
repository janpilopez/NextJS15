<?php

use app\models\SysAdmDepartamentos;
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
             
      $dataFilterIdSysAdmArea =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $datos));  
      $con = 0;
      foreach ($dataFilterIdSysAdmArea as $index => $departamento):  
        $con+=1;
        $area = '';
        $arrayData   = array_filter($datos, array(new FilterData("departamento", $departamento), 'getFilter'));
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
        
        foreach ($arrayData as $index => $row):
                  
          $area = $row['area'];
          $deparamento = $row['departamento'];  
          $h25 += $row['h25'];
          $h50 += $row['h50'];
          $h100 += $row['h100'];
                
        endforeach;

        $totalv25 = 0;
        $totalv50 = 0;
        $totalv100 = 0;
        $value25 = 0;
        $value50 = 0;
        $value100 = 0;

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
          
          $departamento = SysAdmDepartamentos::find()
          ->where(['departamento' => $deparamento])
          ->one();

          if($contratoemp->fecha_salida == null || date('n',strtotime($contratoemp->fecha_salida)) == date('n',strtotime($fechafin)) || $contratoemp->fecha_salida > $fechafin):
        
            if($cargoemp->reg_horas_extras == 'S'):

              $valorhora    =  floatval($sueldoemp/240);

              $val25 = floatval(($valorhora * 0.25) * $data['h25']);

              $newvalor = floatval(($valorhora * 0.50)) + $valorhora;
                      
              $val50  = floatval($newvalor * floatval($data['h50']));

              $val100   = floatval(($valorhora * 2) * $data['h100']);

              if($cargoemp->id_sys_adm_departamento == $departamento->id_sys_adm_departamento):

                $value25  += number_format($val25, 2, '.', '');
                $value50  += number_format($val50, 2, '.', '');
                $value100 += number_format($val100, 2, '.', ''); 

              endif;

              $totalv25  += number_format($val25, 2, '.', '');
              $totalv50  += number_format($val50, 2, '.', '');
              $totalv100 += number_format($val100, 2, '.', ''); 
        
              $totalh25  += $data['h25'];
              $totalh50  += $data['h50'];
              $totalh100 += $data['h100'];

            endif;
          
          endif;

        endforeach;
        
      
    ?> 
             
    <tr>
      <td><?= $con?></td>
      <td><?= $area?></td>
      <td><?= $deparamento?></td>
      <td><?= DecimaltoHoras(number_format($h25, 2, '.', ''))?></td> 
      <td><?= $value25 != 0 ? number_format($value25, 2, '.', '') : '.00' ?></td> 
      <td><?= DecimaltoHoras(number_format($h50, 2, '.', ''))?></td> 
      <td><?= $value50 != 0 ? number_format($value50, 2, '.', '') : '.00' ?></td> 
      <td><?= DecimaltoHoras(number_format($h100, 2, '.', ''))?></td> 
      <td><?= $value100 != 0 ? number_format($value100, 2, '.', '') : '.00' ?></td> 
    </tr>        
    <?php          
      endforeach;
    ?>
    </tbody>
    <tfoot>
     <tr style="background-color: #ccc">
        <th colspan="3" style="text-align: right;">Total General</th>
        <th><?=DecimaltoHoras(number_format($totalh25, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv25, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv50, 2, '.', '')?></th>
        <th><?=DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
        <th><?='$'.number_format($totalv100, 2, '.', '')?></th>
     </tr>
    </tfoot>
</table>   
    