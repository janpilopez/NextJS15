<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysEmpresa;
use app\models\User;
use app\models\SysRrhhComedor;

$lunchh    =  SysRrhhComedor::find()->all();
$lunch     =  [];

foreach ($lunchh as $data){
    $lunch += [intval($data['id_sys_rrhh_comedor']) => $data['alimento']];
}

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
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

$c = 0;

if($datos):
 $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
?>
<div class="site-index">
 <div class="row">
        <div class="col-xs-12 text-center">
           <img src="<?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>"  height="50" width="140">
         </div>
		<div class="col-xs-12">
			<h3 class="subtitle text-center"><b>Listado de  <?= $lunch[$tipo]?> </b></h3>
			<h6 class="subtitle text-center"><b><?php echo "Desde ".$fechaini. " Hasta ".$fechafin?></b></h6>
		</div>
 </div>
  <div class= "row">
       <div class ="col-xs-12">
               <table style="background-color: white; font-size: 11px; width: 100%;">
               <thead>
                  <tr style="background-color: #20B2AA !important;">
                     <th>Cédula</th>
                     <th>Nombres</th>
                     <th>Fecha</th>
                     <th>Hora</th> 
                     <th>Valor</th>
                     <th>Total</th>    
                  </tr>
               </thead>
               <tbody>
                <?php 
                
                if($tipoinfo == 1):
                
                    $total      = array_sum(array_column($datos, 'valor'));
                    $datalunch =  array_unique(array_map(array(new FilterColumn("fecha"), 'getValues'), $datos));
                    sort($datalunch);
                    
                    foreach ($datalunch as $fecha):
                      
                        //Contar al lunch por fecha
                        $lunchfecha = array_filter($datos, array(new FilterData("fecha", $fecha), 'getFilter'));
                        //filtrar por area
                        $dataarea =  array_unique(array_map(array(new FilterColumn("area"), 'getValues'), $lunchfecha));
                        sort($dataarea); 
                        
                        $indexArea = 0;
                        
                        foreach ($dataarea as $area):
                            
                            $indexArea++;
                            $indexDtpo = 0;
                            $departamentos = array_filter($lunchfecha, array(new FilterData("area", $area), 'getFilter'));
                            $totalArea = array_sum(array_column($departamentos, 'valor'));
                            $departamentos = array_values($departamentos);
                            $departamentos = orderMultiDimensionalArray ($departamentos, "departamento");
                            $codigodeparta =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $departamentos));
                            sort($codigodeparta);
                                        
                        ?>
                        <tr style="background-color: #20B2AA !important;">
                            <td colspan="4"><b><?= $indexArea." - ".$area?></b></td>
                            <td><b><?= User::hasRole('JEFECOSTOS') ? number_format( $totalArea, 2, '.',',') : ""?></b></td>
                            <td><b><?= count($departamentos)?></b></td>
                        </tr>
                         <?php 
                              foreach ($codigodeparta as $idepartamento):
                                              
                                $indexDtpo++;
                                $empleados = array_filter($lunchfecha, array(new FilterData("departamento", $idepartamento), 'getFilter'));
                                $totalDpto = array_sum(array_column($empleados, 'valor'));
                                $empleados = array_values($empleados);
                                $empleados = orderMultiDimensionalArray ($empleados, "nombres");
                                $c = $c + count($empleados);
                          ?>
                               <tr style="background-color: #87CEFA !important;">
                                   <td colspan = "4"><b><?= $indexArea.".".$indexDtpo." - ".$idepartamento?></b></td>
                                   <td><b><?= User::hasRole('JEFECOSTOS') ? number_format( $totalDpto, 2, '.', ',') : ""?></b></td>
                                   <td><b><?=count($empleados)?></b></td>
                               </tr>            
                                <?php foreach ($empleados as $emp):?>            
                                      <tr>
                                         <td><?= $emp['id_sys_rrhh_cedula']?></td>
                                         <td><?= $emp['nombres']?></td>
                                         <td><?= $emp['fecha']?></td>
                                         <td><?= date('H:i:s', strtotime($emp['hora']))?></td>
                                         <td><?= User::hasRole('JEFECOSTOS') ? $emp['valor'] : ""?></td>
                                         <td></td>
                                      </tr>
                                <?php endforeach; 
                                
                              endforeach;
                              
                     endforeach;
                     ?>
                      <tr>
                  		<td colspan="4" style="text-align: right"><b>TOTAL</b></td>
                        <td><b><?= User::hasRole('JEFECOSTOS') ? number_format($total, 2, '.', ',') : ""?></b></td>
                        <td><b> <?= $c?></b></td> 
                      </tr>
                 <?php endforeach;?>   
                 
                 <?php 
                 
                 else:
                         $total      = array_sum(array_column($datos, 'valor'));
                         $datalunch  =  array_unique(array_map(array(new FilterColumn("area"), 'getValues'), $datos));
                         sort($datalunch);
                        
                         foreach ($datalunch as $area):
                         
                                 $luncharea = array_filter($datos, array(new FilterData("area", $area), 'getFilter'));
                                 $totalArea = array_sum(array_column($luncharea, 'valor'));
                                 $dataarea =  array_unique(array_map(array(new FilterColumn("area"), 'getValues'), $luncharea));
                                 sort($dataarea);
                                 
                                 foreach ($dataarea as $area):
                                 
                                         $departamentos = array_filter($luncharea, array(new FilterData("area", $area), 'getFilter'));
                                         $departamentos = array_values($departamentos);
                                         $departamentos = orderMultiDimensionalArray ($departamentos, "departamento");
                                         $codigodeparta =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $departamentos));
                                         sort($codigodeparta);
                                         
                                         ?>
                                         <tr style="background-color: #20B2AA !important;">
                                            <td colspan="4"><b><?= $area?></b></td>
                                            <td><b><?= User::hasRole('JEFECOSTOS') ? number_format( $totalArea, '2', '.', ',') : ""?></b></td>
                                            <td><b><?= count($departamentos)?></b></td>
                                          </tr>
                                          <?php 
                                          foreach ($codigodeparta as $idepartamento):
                                              
                                             $empleados = array_filter($luncharea, array(new FilterData("departamento", $idepartamento), 'getFilter'));
                                             $totalDpto = array_sum(array_column($empleados, 'valor'));     
                                             $c= $c + count($empleados);                            
                                           ?>
                                           <tr style="background-color: #87CEFA !important;">
                                              <td colspan = "4"><b><?= $idepartamento?></b></td>
                                              <td><?= User::hasRole('JEFECOSTOS') ? number_format($totalDpto, 2, '.', ',') : ""?></td>
                                              <td><b><?=count($empleados)?></b></td>
                                            </tr>                  
                                           <?php          
                                         endforeach;
                                         
                                 endforeach;
                                 
                            endforeach;
                         ?>
                          <tr>
                              <td colspan = "4"><b>TOTAL </b></td>
                              <td><?= User::hasRole('JEFECOSTOS') ? number_format( $total, 2, '.', ',') : ""?></td>
                              <td><b><?=$c?></b></td>
                         </tr>
                 <?php endif; ?>    
               </tbody>
            </table> 
       </div>
  </div>
</div>
<?php endif;?> 

<?php 
function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
    $position = array();
    $newRow = array();
    foreach ($toOrderArray as $key => $row) {
        $position[$key]  = $row[$field];
        $newRow[$key] = $row;
    }
    //$position = array_unique($position);
    if ($inverse) {
        arsort($position);
    }
    else {
        asort($position);
    }
    $returnArray = array();
    foreach ($position as $key => $pos) {
        $returnArray[] = $newRow[$key];
    }
    
    return $returnArray;
}
?>




