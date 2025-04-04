<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosRolCab;
use app\models\SysRrhhEmpleadosRolLiq;
use app\models\SysRrhhEmpleadosSueldos;
use yii\data\Sort;
use app\models\SysRrhhEmpleadosRolMov;

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

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];


 
 if($datos):
            
            $haberes    = ObtenerHaberes($anio, $mes, $periodo, $area, $departamento) + 1;   
            $descuentos = ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento) + 1;  
         
            $totoal     = $haberes + $descuentos;
            
            $listhaberes      =  ListHaberes($anio, $mes, $periodo, $area,  $departamento); 
            $listdescuentos   =  ListDescuentos($anio, $mes, $periodo, $area, $departamento);  
            
            if($periodo != 70 && $periodo != 71):
            
            ?>  
             <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
                <thead>
                  <tr>
                        <th rowspan = "2">No</th>
                        <th rowspan = "2" style="width:200px;">Nombres</th>
                        <th rowspan = "2">Cedula</th>
                        <th rowspan = "2">Cargo</th>
                        <th rowspan = "2">Fecha Ingreso</th>
                        <th rowspan = "2">Fecha Salida</th>
                        <th rowspan = "2">C.Costos</th>
                        <th colspan = "<?= $haberes + 6?>" style="text-align: center;"><?= $periodo <>  '90' ? 'Haberes': 'Provisiones';?></th>
                        <?php if(count($listdescuentos) > 0):?>
                         <th colspan = "<?= $descuentos?>" style="text-align: center;"><?= $periodo <>  '90'? 'Descuentos': 'Aportaciones';?></th>
                         <th style="text-align: center;"><?= $periodo <>  '90' ? 'Neto Recibir': 'Total Beneficios';?></th>
                          <?php else:?>
                         <th>Neto Recibir</th>
                         <?php endif;?>
                  </tr>
                  <tr>
                         <th></th>
                         <th>Dias</th>
                         <th>Faltas</th>
                         <th>Sueldo Nominal</th>
                         <th>Subsidio</th>
                         <th>Días NL</th>
                         <?php  foreach ($listhaberes as $haberes):?>      
                         <th><?= strtolower(str_replace("_", " ", $haberes['id_sys_rrhh_concepto'] ))?></th>
                         <?php endforeach; ?>
                         <th><?= $periodo <>  '90' ? 'Total Hab': 'Total Pro';?></th>
                         <?php if(count($listdescuentos) > 0):?>
                            <?php foreach ($listdescuentos as $descuentos):?>      
                             <th><?=  strtolower(str_replace("_", " ", $descuentos['id_sys_rrhh_concepto'] )) ?></th>
                             <?php endforeach; ?>
                              <th><?= $periodo <>  '90' ? 'Total Desc': 'Total Apor';?></th>
                             <th></th>
                          <?php else:?>
                           <th></th>
                         <?php endif;?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $data =  array_unique(array_map(array(new FilterColumn("area"), 'getValues'), $datos));
                  sort($data);
                  $cont = 0;
                  $totalGeneralgeneral = 0;
                  $totalGeneralsubsidio = 0;
                  $totalGeneralDesDias = 0;
                  
                  foreach ($data as $index => $Id):
                
                   $departamentos = array_filter($datos, array(new FilterData("area", $Id), 'getFilter'));
                   $departamentos = array_values($departamentos);
                   $departamentos = orderMultiDimensionalArray ($departamentos, "departamento");
                   
                   $codigodeparta =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $departamentos));
                   sort($codigodeparta);
                
                       foreach ($codigodeparta as $index => $idepartamento):?>
                       
                             <tr>

                                 <th style= "text-align: left"  colspan= "<?= $totoal + 5?>"><?php echo $Id.'-'.$idepartamento?></th>
                             </tr>
                           
                             <?php 
                               $empleados = array_filter($datos, array(new FilterData("departamento", $idepartamento), 'getFilter'));
                               $empleados = array_values($empleados);
                               $empleados =  orderMultiDimensionalArray ($empleados, "nombres");

                                $sueldoTotal = 0;
                                $subsidioTotal = 0;
                                $diasNLTotal = 0;

                              foreach ($empleados as $index => $emp):
                               $cont++;
                               $faltas = Faltas($anio, $mes, $emp['id_sys_rrhh_cedula']);
                               $dias   = Dias($anio, $mes, $emp['id_sys_rrhh_cedula'], $periodo);
                               $sueldo = Sueldo($emp['id_sys_rrhh_cedula']);
                               $subsidio = Subsidio($anio, $mes, $emp['id_sys_rrhh_cedula'],$sueldo);
                               $descDias = $sueldo - ((floatval($sueldo)/30) * $dias);
                               $sueldoTotal += $sueldo;
                               $subsidioTotal += $subsidio;
                               $diasNLTotal += $descDias;
                             ?>
                               <tr>
                                 <td><?= $cont?></td>
                                 <td><?= utf8_decode($emp['nombres'])?></td>
                                 <td><?= $emp['id_sys_rrhh_cedula']?></td>
                                 <td><?= $emp['cargo']?></td>
                                 <td><?= date("Y-m-d", strtotime($emp['fecha_ingreso']))?></td>
                                 <td><?= $emp['fecha_salida'] != null ? date("Y-m-d", strtotime($emp['fecha_salida'])) : '' ?></td>
                                 <td><?= $emp['id_sys_adm_ccosto']?></td>
                                 <td><?= $emp['forma_pago']?></td>
                                 <td><?= $dias?></td>
                                 <td><?= $faltas?></td>
                                 <td><?= number_format($sueldo, 2, '.', ',')?></td>
                                 <td><?= number_format($subsidio, 2, '.', ',') ?></td>
                                 <td><?= number_format($descDias, 2, '.', ',') ?></td>
                                <?php 
                                
                                  $totalhab  = 0;
                                  $totaldesc = 0;
                                  
                                   foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totalhab = $totalhab + $valor;
                                           ?>      
                                      	  <td><?= number_format($valor, 2, '.', ',')  ?></td>
                                  <?php endforeach; ?>
                                          <td><?= number_format($totalhab, 2, '.', ',') ?></td>
                                  
                                  <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totaldesc = $totaldesc + $valor;
                                           ?>      
                                      	  <td><?=  number_format($valor, 2, '.', ',') ?></td>
                                      <?php endforeach; ?>
                                      <td><?=  number_format( $totaldesc, 2, '.', ',')  ?></td>
                                      <?php if($periodo <> '90'):?>
                                         <td><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                       <?php else:?>
                                          <td><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                       <?php endif?>
                                      
                                   <?php else:?>
                                      <?php if($periodo <> '90'):?>
                                        <td><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                      <?php else:?>
                                         <td><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                      <?php endif;?>
                                   <?php endif;?>
                                
                              </tr>
                            <?php 
                                endforeach; //empleados 
                       
                            ?>
                             <tr>
                                <td colspan= "10" style= "text-align: left" ><b>TOTAL:</b></td>
                                <?php
                                $totalhabdep  = 0;
                                $totaldecdep = 0;
                                $totalGeneralgeneral += $sueldoTotal;
                                $totalGeneralsubsidio += $subsidioTotal;
                                $totalGeneralDesDias += $diasNLTotal;
                                ?>

                                <td><b><?= number_format($sueldoTotal, 2, '.', ',') ?></b></td>
                                <td><b><?= number_format($subsidioTotal, 2,'.',',') ?></b></td>
                                <td><b><?= number_format($diasNLTotal, 2,'.',',') ?></b></td>

                                <?php
                                foreach ($listhaberes as $haberes):
                                    $valor =  TotalDepartamento($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                    $totalhabdep = $totalhabdep + $valor;
                                ?>
                                   <td><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                <?php endforeach; ?>
                                  
                                  <td><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                            $valor =  TotalDepartamento($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                  
                                  <?php if($periodo <> '90'): ?>
                                      <td><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                   <?php else:?>
                                      <td><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php endif?>
                           </tr>   
                       <?php   endforeach; //departamentos ?>
                           
                             
                           
                     <?php endforeach; //areas?>
                           <tr>
                                   <td colspan= "9" style= "text-align: left" ><b>TOTAL GENERAL:</b></td>
                                   <td><b><?= number_format($totalGeneralgeneral, 2, '.', ',') ?></b></td>
                                   <td><b><?= number_format($totalGeneralsubsidio, 2, '.', ',') ?></b></td>
                                   <td><b><?= number_format($totalGeneralDesDias, 2, '.', ',') ?></b></td>
                                   <?php
                                       $totalhabdep  = 0;
                                       $totaldecdep = 0;
                                           
                                           foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerValorConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'] , 'I', $area);
                                           $totalhabdep = $totalhabdep + $valor;
                                           ?>
                                   <td><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                  <?php endforeach; ?>
                                  
                                  <td><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =   ObtenerValorConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'] , 'E', $area);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                 <?php if($periodo <> '90'):?>  
                                   <td><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php else: ?>
                                    <td><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                 <?php endif;?>
                                
                           </tr> 
                </tbody>
             </table>
             <?php 
            else:?>
            <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
                <thead>
                  <tr>
                        <th rowspan = "2">No</th>
                        <th rowspan = "2" style="width:200px;">Nombres</th>
                        <th rowspan = "2">Cedula</th>
                        <th rowspan = "2">Cargo</th>
                        <th rowspan = "2">C.Costos</th>
                        <th colspan = "<?= $haberes + 3?>" style="text-align: center;"><?= $periodo <>  '90' ? 'Haberes': 'Provisiones';?></th>
                        <?php if(count($listdescuentos) > 0):?>
                         <th colspan = "<?= $descuentos?>" style="text-align: center;"><?= $periodo <>  '90'? 'Descuentos': 'Aportaciones';?></th>
                         <th style="text-align: center;"><?= $periodo <>  '90' ? 'Neto Recibir': 'Total Beneficios';?></th>
                          <?php else:?>
                         <th>Neto Recibir</th>
                         <?php endif;?>
                  </tr>
                  <tr>
                         <th></th>
                         <th>Dias</th>
                         <th>Faltas</th>
                         <?php  foreach ($listhaberes as $haberes):?>      
                         <th><?= strtolower(str_replace("_", " ", $haberes['id_sys_rrhh_concepto'] ))?></th>
                         <?php endforeach; ?>
                         <th><?= $periodo <>  '90' ? 'Total Hab': 'Total Pro';?></th>
                         <?php if(count($listdescuentos) > 0):?>
                            <?php foreach ($listdescuentos as $descuentos):?>      
                             <th><?=  strtolower(str_replace("_", " ", $descuentos['id_sys_rrhh_concepto'] )) ?></th>
                             <?php endforeach; ?>
                              <th><?= $periodo <>  '90' ? 'Total Desc': 'Total Apor';?></th>
                             <th></th>
                          <?php else:?>
                           <th></th>
                         <?php endif;?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $data =  array_unique(array_map(array(new FilterColumn("area"), 'getValues'), $datos));
                  sort($data);
                  $cont = 0;
                  
                  foreach ($data as $index => $Id):
                
                   $departamentos = array_filter($datos, array(new FilterData("area", $Id), 'getFilter'));
                   $departamentos = array_values($departamentos);
                   $departamentos = orderMultiDimensionalArray ($departamentos, "departamento");
                   
                   $codigodeparta =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $departamentos));
                   sort($codigodeparta);
                
                       foreach ($codigodeparta as $index => $idepartamento):?>
                       
                             <tr>

                                 <th style= "text-align: left"  colspan= "<?= $totoal + 5?>"><?php echo $Id.'-'.$idepartamento?></th>
                             </tr>
                           
                             <?php 
                               $empleados = array_filter($datos, array(new FilterData("departamento", $idepartamento), 'getFilter'));
                               $empleados = array_values($empleados);
                               $empleados =  orderMultiDimensionalArray ($empleados, "nombres");
     
                              foreach ($empleados as $index => $emp):
                               $cont++;
                               $faltas = Faltas($anio, $mes, $emp['id_sys_rrhh_cedula']);
                               $dias   = Dias($anio, $mes, $emp['id_sys_rrhh_cedula'], $periodo);
                             ?>
                               <tr>
                                 <td><?= $cont?></td>
                                 <td><?= utf8_decode($emp['nombres'])?></td>
                                 <td><?= $emp['id_sys_rrhh_cedula']?></td>
                                 <td><?= $emp['cargo']?></td>
                                 <td><?= $emp['id_sys_adm_ccosto']?></td>
                                 <td><?= $emp['forma_pago']?></td>
                                 <td><?= $dias?></td>
                                 <td><?= $faltas?></td>
                                <?php 
                                
                                  $totalhab  = 0;
                                  $totaldesc = 0;
                                  
                                   foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totalhab = $totalhab + $valor;
                                           ?>      
                                      	  <td><?= number_format($valor, 2, '.', ',')  ?></td>
                                  <?php endforeach; ?>
                                          <td><?= number_format($totalhab, 2, '.', ',') ?></td>
                                  
                                  <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totaldesc = $totaldesc + $valor;
                                           ?>      
                                      	  <td><?=  number_format($valor, 2, '.', ',') ?></td>
                                      <?php endforeach; ?>
                                      <td><?=  number_format( $totaldesc, 2, '.', ',')  ?></td>
                                      <?php if($periodo <> '90'):?>
                                         <td><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                       <?php else:?>
                                          <td><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                       <?php endif?>
                                      
                                   <?php else:?>
                                      <?php if($periodo <> '90'):?>
                                        <td><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                      <?php else:?>
                                         <td><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                      <?php endif;?>
                                   <?php endif;?>
                                
                              </tr>
                            <?php 
                                endforeach; //empleados 
                       
                            ?>
                             <tr>
                                <td colspan= "8" style= "text-align: left" ><b>TOTAL:</b></td>
                                <?php
                                $totalhabdep  = 0;
                                $totaldecdep = 0;
                                
                                foreach ($listhaberes as $haberes):
                                    $valor =  TotalDepartamento($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                    $totalhabdep = $totalhabdep + $valor;
                                ?>
                                   <td><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                <?php endforeach; ?>
                                  
                                  <td><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                            $valor =  TotalDepartamento($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                  
                                  <?php if($periodo <> '90'): ?>
                                      <td><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                   <?php else:?>
                                      <td><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php endif?>
                           </tr>   
                       <?php   endforeach; //departamentos ?>
                           
                             
                           
                     <?php endforeach; //areas?>
                           <tr>
                                   <td colspan= "8" style= "text-align: left" ><b>TOTAL GENERAL:</b></td>
                                   <?php
                                       $totalhabdep  = 0;
                                       $totaldecdep = 0;
                                           
                                           foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerValorConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'] , 'I', $area);
                                           $totalhabdep = $totalhabdep + $valor;
                                           ?>
                                   <td><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                  <?php endforeach; ?>
                                  
                                  <td><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =   ObtenerValorConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'] , 'E', $area);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                 <?php if($periodo <> '90'):?>  
                                   <td><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php else: ?>
                                    <td><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                 <?php endif;?>
                                
                           </tr> 
                </tbody>
             </table>
            
            <?php endif; ?>
             
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

function ObtenerHaberes($anio, $mes, $periodo, $area, $departamento){
    
    
   return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
      //  ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
     //   ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.tipo = 'I'")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andwhere("rol_mov.valor > 0")
        ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
        ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    
}

function ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento){
    
    return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
  //  ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
  //  ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'E'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
}


function ListHaberes($anio, $mes, $periodo, $area, $departamento){
    
    $datos = [];
    
    $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    //->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'I'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());

    return $datos;
}

function ListDescuentos($anio, $mes, $periodo, $area, $departamento){
    
    $datos = [];
    
    $datos =  (new\yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
   // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
   // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'E'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());
    
    
    return $datos;
    
}
function ObtenerConcepto($anio, $mes, $periodo, $concepto, $cedula){
    
    return   (new \yii\db\Query())->select(['rol_mov.valor'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
    ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$cedula}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
   
}
function TotalDepartamento($anio, $mes, $periodo, $concepto, $area, $departamento){
    
    return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
   // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
   // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
    ->andWhere("departamento.departamento like '%{$departamento}%'")
    ->andWhere("area.area like '%{$area}%'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
}

function Faltas($anio, $mes, $cedula){
    
    return SysRrhhEmpleadosRolLiq::find()->select('faltas')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->scalar();
    
    
}

function Subsidio($anio, $mes, $cedula,$sueldo){

    $rol = SysRrhhEmpleadosRolCab::find()->where(['anio'=>$anio])->andWhere(['mes'=>$mes])->andWhere(['periodo'=>2])->one();

    $fecha_ini = date("Y-m-d",strtotime($rol['fecha_ini_liq']));
    $fecha_fin = date("Y-m-d",strtotime($rol['fecha_fin_liq']));

    $valordia = (floatval($sueldo)/30);

    $subcidio = 0;
    $cont = 0;

    while($fecha_ini <= $fecha_fin){

        $cont++;
    
        $permiso = getPermiso($fecha_ini,$cedula,'001');

        if($permiso):
                                                                                                            
            if($permiso['estado_permiso'] == 'A'):

                if($permiso['descuento'] != 'S'):
                                
                    if($permiso['subcidio'] > 0):

                        if($cont < 31){
                        
                            $porcentaje = $permiso['subcidio']/100;
                            $subcidio = $subcidio + (floatval($valordia * $porcentaje));
                            
                        }
                                
                    endif;
                        
                endif;

            endif;

        endif;

        $fecha_ini = date("Y-m-d", strtotime($fecha_ini . " + 1 day"));
    }

    return $subcidio;
    
}

function Sueldo($cedula){
    
    $db =  $_SESSION['db'];
    return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSueldoActualEmpleado] @id_sys_rrhh_cedula= '$cedula'")->queryScalar();
    
}

function getPermiso($fecha, $id_sys_rrhh_cedula, $id_sys_empresa){
         
    return  (new \yii\db\Query())
    ->select(["sys_rrhh_empleados_permisos.id_sys_rrhh_permiso", "tipo", "hora_ini", "hora_fin", "fecha_ini", "fecha_fin", "estado_permiso", "subcidio", "descuento"])
    ->from("sys_rrhh_empleados_permisos")
    ->Join('join','sys_rrhh_permisos','sys_rrhh_empleados_permisos.id_sys_rrhh_permiso = sys_rrhh_permisos.id_sys_rrhh_permiso')
    ->where("fecha_ini <= '{$fecha}' and fecha_fin >= '{$fecha}'")
    ->andwhere("sys_rrhh_empleados_permisos.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
    ->andwhere("estado_permiso <> 'N'")
    ->orderby("nivel")
    ->one(SysRrhhEmpleados::getDb());
    
}

function Dias($anio, $mes, $cedula, $periodo){
    
    
    
    if($periodo == '70' || $periodo == '71'):
    
        return SysRrhhEmpleadosRolMov::find()->select('cantidad')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['periodo'=> $periodo])->orderBy(['cantidad' => SORT_DESC])->scalar();
    
    else:
        
        return SysRrhhEmpleadosRolLiq::find()->select('dias')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->scalar();
    
   endif;
    
  
    
}



function TotalArea($anio, $mes, $periodo, $concepto, $area){
    
    return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
 //   ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
 //   ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
    ->andWhere("area.area like '%{$area}%'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();
    
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }
        
        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }
        
        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    
    return $new_array;
}
function ObtenerValorConcepto($anio, $mes, $periodo, $id_sys_rrhh_concepto, $tipo, $area){
    
    return   (new \yii\db\Query())->select('sum(rol_mov.valor)')
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
   // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
  //  ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = '{$tipo}'")
    ->andwhere("rol_mov.valor > 0")
    ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$id_sys_rrhh_concepto}'")
    ->andWhere("area.id_sys_adm_area like '%{$area}%'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
    
}
?>
