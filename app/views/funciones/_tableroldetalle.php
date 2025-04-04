<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosRolLiq;
use yii\data\Sort;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysRrhhEmpleadosRolCab;
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

$contrato =  null;
$clasecolor = "";
$rol = getRol($anio, $mes, $periodo);
 if($datos):
            
            $haberes    = ObtenerHaberes($anio, $mes, $periodo, $area, $departamento) + 1;   
            $descuentos = ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento) + 1;  
         
            $totoal     = $haberes + $descuentos;
            
            $listhaberes      =  ListHaberes($anio, $mes, $periodo, $area,  $departamento); 
            $listdescuentos   =  ListDescuentos($anio, $mes, $periodo, $area, $departamento);   
            
            ?>  
             <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
                <thead>
                  <tr>
                    <th rowspan = "2">No</th>
                    <th rowspan = "2" style="width:200px;">Nombres</th>
                    <th colspan = "<?= $haberes + 3?>" style="text-align: center;"><?= $periodo <>  '90' ? 'Haberes': 'Provisiones';?></th>
                    <?php if(count($listdescuentos) > 0):?>
                     <th colspan = "<?= $descuentos?>" style="text-align: center;"><?= $periodo <>  '90'? 'Descuentos': 'Aportaciones';?></th>
                     <th style="text-align: center;" rowspan = "2"><?= $periodo <>  '90' ? 'Neto Recibir': 'Total Beneficios';?></th>
                      <?php else:?>
                     <th rowspan = "2">Neto Recibir</th>
                     <?php endif;?>
                  </tr>
                  <tr>
                     <th></th>
                     <th>Dias</th>
                     <th>Faltas</th>
                     <?php  foreach ($listhaberes as $haberes):?>      
                     <th><?= ucfirst(strtolower(str_replace("_", " ", $haberes['id_sys_rrhh_concepto'] )))?></th>
                     <?php endforeach; ?>
                     <th><?= $periodo <>  '90' ? 'Total Hab': 'Total Pro';?></th>
                     <?php if(count($listdescuentos) > 0):?>
                        <?php foreach ($listdescuentos as $descuentos):?>      
                         <th><?=  ucfirst(strtolower(str_replace("_", " ", $descuentos['id_sys_rrhh_concepto'] ))) ?></th>
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
                
                       
                   
                       foreach ($codigodeparta as $index => $idepartamento):
               
                       
                       ?>
                       
                             <tr>

                                 <th colspan= "<?= $totoal + 2?>"><?php echo $Id.'-'.$idepartamento?></th>
                             </tr>
                           
                           
                             <?php 
                               $empleados = array_filter($datos, array(new FilterData("departamento", $idepartamento), 'getFilter'));
                               $empleados = array_values($empleados);
                               $empleados =  orderMultiDimensionalArray ($empleados, "nombres");
                               $totalEmpleado = 0;
                              foreach ($empleados as $index => $emp):
                              
                               $totalEmpleado ++;
                               $cont++;
                               $faltas = Faltas($anio, $mes, $emp['id_sys_rrhh_cedula']);
                               $dias   = Dias($anio, $mes, $emp['id_sys_rrhh_cedula'], $periodo);
                               
                              //Valida contrato
                               
                               $contrato = getContrato($emp['id_sys_rrhh_cedula']);
                               
                               $clasecolor = "";
                               
                               if($contrato):

                                  if($contrato->fecha_salida != null):
                                  
                                      $clasecolor = "#FFD7CF";
                               
                                  elseif($contrato->fecha_ingreso >= $rol->fecha_ini && $contrato->fecha_ingreso <= $rol->fecha_fin ):
                                      
                                      $clasecolor = "#FFE4B5";
                                      
                                  endif;
                  
                               endif;
                               
                         
                             ?>
                               <tr bgcolor = "<?= $clasecolor?>" >
                                 <td><?= $cont?></td>
                                 <td><?= $emp['nombres']?></td>
                                 <td width="30px"><?= $emp['forma_pago']?></td>
                                 <td width="15px"  class="text-right"><?= $dias ?></td>
                                 <td width="15px"  class="text-right"><?= $faltas ?></td>
                                <?php 
                                
                                  $totalhab  = 0;
                                  $totaldesc = 0;
                                  
                                   foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totalhab = $totalhab + $valor;
                                           ?>      
                                      	  <td class="text-right"><?= number_format($valor, 2, '.', ',')  ?></td>
                                  <?php endforeach; ?>
                                          <td class="text-right"><?= number_format($totalhab, 2, '.', ',') ?></td>
                                  
                                  <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =  ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $emp['id_sys_rrhh_cedula']);
                                           $totaldesc = $totaldesc + $valor;
                                           ?>      
                                      	  <td class="text-right"><?=  number_format($valor, 2, '.', ',') ?></td>
                                      <?php endforeach; ?>
                                      <td class="text-right"><?=  number_format( $totaldesc, 2, '.', ',')  ?></td>
                                      <?php if($periodo <> '90'):?>
                                         <td class="text-right"><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                       <?php else:?>
                                          <td class="text-right"><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                       <?php endif?>
                                      
                                   <?php else:?>
                                      <?php if($periodo <> '90'):?>
                                        <td class="text-right"><?= number_format($totalhab - $totaldesc, 2, '.', ',')?> </td>
                                      <?php else:?>
                                         <td class="text-right"><?= number_format($totalhab + $totaldesc, 2, '.', ',')?> </td>
                                      <?php endif;?>
                                   <?php endif;?>
                                
                              </tr>
                            <?php 
                                endforeach; //empleados 
                         
                            ?>
                             <tr>
                                <td colspan="4"><b>NO EMPLEADOS  : </b><?= $totalEmpleado?></td><td style= "text-align: right" ><b>TOTAL:</b></td>
                                <?php
                                $totalhabdep  = 0;
                                $totaldecdep = 0;
                                
                                foreach ($listhaberes as $haberes):
                                    $valor =  TotalDepartamento($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                    $totalhabdep = $totalhabdep + $valor;
                                ?>
                                   <td class="text-right"><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                <?php endforeach; ?>
                                  
                                  <td class="text-right"><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                            $valor =  TotalDepartamento($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id, $idepartamento);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td class="text-right"><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td class="text-right"><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                  
                                  <?php if($periodo <> '90'): ?>
                                      <td class="text-right"><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                   <?php else:?>
                                      <td class="text-right"><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php endif?>
                           </tr>   
                       <?php   endforeach; //departamentos ?>
                     <?php       
                     endforeach;  //area ?>   
                         <tr>
                             <td colspan= "5" style= "text-align: right" ><b>TOTAL GENERAL:</b></td>
                                   <?php
                                       $totalhabdep  = 0;
                                       $totaldecdep = 0;
                                           
                                           foreach ($listhaberes as $haberes):
                                           $valor =  ObtenerValorConcepto($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'] , 'I', $area);
                                           $totalhabdep = $totalhabdep + $valor;
                                           ?>
                                   <td class="text-right"><b><?=  number_format($valor, 2, '.', ',')  ?></b></td>
                                  <?php endforeach; ?>
                                  
                                  <td class="text-right"><b><?=  number_format($totalhabdep, 2, '.', ',') ?></b></td>
                                    <?php 
                                   if(count($listdescuentos) > 0) :?>
                                      <?php  
                                      foreach ($listdescuentos as $descuento):
                                           $valor =  ObtenerValorConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'] , 'E', $area);
                                           $totaldecdep = $totaldecdep + $valor;
                                           ?>      
                                      	  <td class="text-right"><b><?=  number_format($valor, 2, '.', ',') ?></b></td>
                                      <?php endforeach; ?>
                                      <td class="text-right"><b><?=  number_format( $totaldecdep, 2, '.', ',')  ?></b></td>
                                   <?php endif;?>
                                 <?php if($periodo <> '90'):?>  
                                   <td class="text-right"><b><?= number_format($totalhabdep - $totaldecdep, 2, '.', ',')?></b></td>
                                  <?php else: ?>
                                    <td class="text-right"><b><?= number_format($totalhabdep + $totaldecdep, 2, '.', ',')?></b></td>
                                 <?php endif;?>
                                
                           </tr> 
                </tbody>
          </table>      
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
    

    if($area == '' && $departamento == ''){
    
        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
            ->andwhere("conceptos.tipo = 'I'")
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andwhere("rol_mov.valor > 0")
            ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
            ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
            ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }elseif($area != '' && $departamento != ''){
        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
            ->andwhere("conceptos.tipo = 'I'")
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andwhere("rol_mov.valor > 0")
            ->andWhere("departamento.id_sys_adm_departamento = $departamento")
            ->andWhere("area.id_sys_adm_area = $area")
            ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }elseif($area != ''){
        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
            ->andwhere("conceptos.tipo = 'I'")
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andwhere("rol_mov.valor > 0")
            ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
            ->andWhere("area.id_sys_adm_area = $area")
            ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }
    
}


function ObtenerValorConcepto($anio, $mes, $periodo, $id_sys_rrhh_concepto, $tipo, $area){

    if($area != ''){
    
        return   (new \yii\db\Query())->select('sum(rol_mov.valor)')
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.tipo = '{$tipo}'")
        ->andwhere("rol_mov.valor > 0")
        ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$id_sys_rrhh_concepto}'")
        ->andWhere("area.id_sys_adm_area = $area")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }else{
        return   (new \yii\db\Query())->select('sum(rol_mov.valor)')
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
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
    
}

function ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento){

    if($area == '' && $departamento == ''){
    
        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
        ->andwhere("conceptos.tipo = 'E'")
        ->andwhere("rol_mov.valor > 0")
        ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
        ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }elseif($area != '' && $departamento != ''){

        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
        ->andwhere("conceptos.tipo = 'E'")
        ->andwhere("rol_mov.valor > 0")
        ->andWhere("departamento.id_sys_adm_departamento = $departamento")
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());

    }elseif($area != ''){

        return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
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
        ->andwhere("conceptos.tipo = 'E'")
        ->andwhere("rol_mov.valor > 0")
        ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }
}


function ListHaberes($anio, $mes, $periodo, $area, $departamento){
    
    $datos = [];

    if($area == '' && $departamento == ''){
    
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
    }elseif($area != '' && $departamento != ''){

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
        ->andWhere("departamento.id_sys_adm_departamento = $departamento")
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    }elseif($area != ''){

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
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    }

    return $datos;
}

function ListDescuentos($anio, $mes, $periodo, $area, $departamento){
    
    $datos = [];

    if($area == '' && $departamento == ''){
    
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

    }elseif($area != '' && $departamento != ''){

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
        ->andWhere("departamento.id_sys_adm_departamento = $departamento")
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());

    }elseif($area != ''){

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
        ->andWhere("area.id_sys_adm_area = $area")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());

    }
    
    
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

    if($area == '' && $departamento == ''){
    
        return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
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
    }elseif($area != '' && $departamento != ''){
        return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
        ->andWhere("departamento.departamento = '{$departamento}'")
        ->andWhere("area.area = '{$area}'")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }elseif($area != ''){
        return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
        ->andWhere("departamento.departamento like '%{$departamento}%'")
        ->andWhere("area.area = '{$area}'")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }
  
    return 0;
}
function TotalArea($anio, $mes, $periodo, $concepto, $area){
    
    if($area != ''){
        return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    //  ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    //  ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
        ->andWhere("area.area = {$area}")
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    }else{
        return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
      //  ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
      //  ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
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
}

function Faltas($anio, $mes, $cedula){
    
    return SysRrhhEmpleadosRolLiq::find()->select('faltas')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->scalar();

    
}

function Dias($anio, $mes, $cedula, $periodo){
    
    
    
    if($periodo == '70' || $periodo == '71'):
    
    return SysRrhhEmpleadosRolMov::find()->select('cantidad')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['periodo'=> $periodo])->orderBy(['cantidad' => SORT_DESC])->scalar();
    
    else:
    
    return SysRrhhEmpleadosRolLiq::find()->select('dias')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->scalar();
    
    endif;
    
    
    
}

function getContrato ($cedula){
    
    return SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=> $cedula])->orderBy(['fecha_ingreso'=> SORT_DESC])->one();
}

function getRol($anio, $mes, $periodo){
    
    return SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->one();
    
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
?>
