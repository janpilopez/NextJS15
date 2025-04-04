<?php
/* @var $this yii\web\View */

use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosRolLiq;
use yii\data\Sort;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysRrhhEmpleadosRolCab;
use app\models\SysRrhhEmpleadosRolMov;
use app\models\User;

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

$usuario = '';
$clave = '';
$referencia = '';

$datosaEnviar=[];
$datosaEnviarfinal = [];

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Sep', 10 => 'Octubre', 11 => 'Nov', 12 => 'Dic' ];

//$rol = getRol($anio, $mes, $periodo);
 if($datos):
            
            if($datos[0]['area'] == 'UNIDAD DE CALIDAD HIGIENE Y PRODUCTO FINAL'){
                $datos[0]['area'] = 'UNI. CALIDAD';
            }
            //$haberes    = ObtenerHaberes($anio, $mes, $periodo, $area, $departamento) + 1;   
            //$descuentos = ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento) + 1;  
         
            ///$totoal     = $haberes + $descuentos;
            
            //$listhaberes      =  ListHaberes($anio, $mes, $periodo, $area);
            if($datos[0]['area'] == 'MATERIA PRIMA'){
                $referencia = "MP";
            }else{
                $referencia = substr($datos[0]['area'],0,1);
            }
            
              
            
            ?>  
             <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
                <thead>
                  <tr>
                    <th>Cuenta de Mayor/Codigo</th>
                    <th>Cuenta Mayor Nombre</th>
                    <th>Debito</th>
                    <th>Credito</th>
                    <th>Comentarios</th>
                    <th>Centro Costo</th>
                    <th>Area</th>
                  </tr>
                </thead>
                <tbody>
                    <?php

                    if($periodo == 2):

                        $totaldebito = 0;

                        foreach($datos as $dat): 

                            $totalSobreTiempo = 0;
                            $cuentaMayor = '';
                            $cuentaMayorNombre = '';
                            
                            $listhaberes      =  ListHaberes($anio, $mes, $periodo, $dat['id_sys_adm_ccosto']); 

                            foreach($listhaberes as $haberes):

                                $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'],$area,$dat['id_sys_adm_ccosto']);

                                if($dataConcepto !== false):

                                    if($dataConcepto['cta_mayor_nombre'] == 'Sobretiempo'):

                                        $totalSobreTiempo += $dataConcepto['valor'];
                                        $cuentaMayor = $dataConcepto['cta_debito'];
                                        $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];

                                    else:

                                        $totaldebito += $dataConcepto['valor'];

                                        if($haberes['id_sys_rrhh_concepto'] != 'FOND- RESERV ANT'):

                                            array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_debito'],"Debe"=>floatval($dataConcepto['valor']),"Haber"=>0,"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>substr($dat['id_sys_adm_ccosto'],0,-2),"CCostos2"=>$dat['id_sys_adm_ccosto']]); 
                                    ?>
                                            <tr>
                                                <td><?= $dataConcepto['cta_debito']?></td>
                                                <td><?= $dataConcepto['cta_mayor_nombre']?></td>
                                                <td><?= $dataConcepto['valor']?></td>
                                                <td></td>
                                                <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                                <td><?= substr($dat['id_sys_adm_ccosto'],0,-2)?></td>
                                                <td><?= $dat['id_sys_adm_ccosto']?></td>
                                            </tr>
                                    <?php
                                        else:

                                            array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_debito'],"Debe"=>floatval($dataConcepto['valor']),"Haber"=>0,"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>"","CCostos2"=>""]);
                                            ?>
                                            <tr>
                                                <td><?= $dataConcepto['cta_debito']?></td>
                                                <td><?= $dataConcepto['cta_mayor_nombre']?></td>
                                                <td><?= $dataConcepto['valor']?></td>
                                                <td></td>
                                                <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                    <?php

                                        endif;

                                    endif;

                                endif;

                            endforeach;

                            if($totalSobreTiempo != 0):
                                array_push($datosaEnviar,["CtaContable"=>$cuentaMayor,"Debe"=>floatval($totalSobreTiempo),"Haber"=>0,"Proyecto"=>$cuentaMayorNombre,"CCostos1"=>substr($dat['id_sys_adm_ccosto'],0,-2),"CCostos2"=>$dat['id_sys_adm_ccosto']]);
                            ?>

                            <tr>
                                <td><?= $cuentaMayor?></td>
                                <td><?= $cuentaMayorNombre?></td>
                                <td><?= $totalSobreTiempo?></td>
                                <td></td>
                                <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                <td><?= substr($dat['id_sys_adm_ccosto'],0,-2)?></td>
                                <td><?= $dat['id_sys_adm_ccosto'] ?></td>
                            </tr>

                            <?php

                            endif;

                            $totaldebito = $totaldebito + $totalSobreTiempo;

                       endforeach;

                    ?>

                    <?php

                    $totalcredito = 0;
                    $centroCostos = [];

                    foreach($datos as $dat): 

                        array_push($centroCostos,$dat['id_sys_adm_ccosto']);

                    endforeach;



                    $listdescuentos      =  ListDescuentos($anio, $mes, $periodo, $area); 

                    foreach($listdescuentos as $descuento):

                        if($descuento['id_sys_rrhh_concepto'] == 'DES_HORAS_NL' || $descuento['id_sys_rrhh_concepto'] == 'DESC_DIAS_NL'):

                            foreach($datos as $dat):
                                
                                $dataConcepto = ObtenerConceptoCredito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$dat['id_sys_adm_ccosto']);
                                
                                if($dataConcepto !== false):

                                    $totalcredito += $dataConcepto['valor'];

                                    if($dataConcepto['valor'] != 0):

                                        array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_credito'],"Debe"=>0,"Haber"=>floatval($dataConcepto['valor']),"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>substr($dat['id_sys_adm_ccosto'],0,-2),"CCostos2"=>$dat['id_sys_adm_ccosto']]);
                                    ?>
                                        <tr>
                                            <td><?= $dataConcepto['cta_credito']?></td>
                                            <td><?= $dataConcepto['cta_mayor_nombre']?></td>
                                            <td></td>
                                            <td><?= $dataConcepto['valor']?></td>
                                            <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                            <td><?= substr($dat['id_sys_adm_ccosto'],0,-2)?></td>
                                            <td><?= $dat['id_sys_adm_ccosto']?></td>
                                        </tr>
                                    <?php
                                    endif;

                                endif;

                            endforeach;

                        else:

                            $dataConcepto = ObtenerConceptoCredito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$centroCostos);

                            $totalcredito += $dataConcepto['valor'];

                            if($dataConcepto['valor'] != 0):

                                array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_credito'],"Debe"=>0,"Haber"=>floatval($dataConcepto['valor']),"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>"","CCostos2"=>""]);
                    ?>
                        <tr>
                            <td><?= $dataConcepto['cta_credito']?></td>
                            <td><?= $dataConcepto['cta_mayor_nombre']?></td>
                            <td></td>
                            <td><?= $dataConcepto['valor']?></td>
                            <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php
                            endif;

                        endif;

                    endforeach;

                    $totalsalario = $totaldebito - $totalcredito;

                    array_push($datosaEnviar,["CtaContable"=>'2105-001-00',"Debe"=>0,"Haber"=>floatval(round($totalsalario,2)),"Proyecto"=>'Salarios',"CCostos1"=>"","CCostos2"=>""]);
                    ?>
                    <tr>
                        <td>2105-001-00</td>
                        <td>Salarios</td>
                        <td></td>
                        <td><?= $totalsalario ?></td>
                        <td>Reg. Contab Roles <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php

                    $totalcredito = $totalcredito + $totalsalario;

                    ?>
                    <tr>
                        <td colspan="2"></td>
                        <td><?= $totaldebito ?></td>
                        <td><?= $totalcredito ?></td>
                    </tr>

                    <?php 

                    array_push($datosaEnviarfinal,['Movimientos'=>$datosaEnviar]);
                    array_push($datosaEnviarfinal,["Fecha"=>obtenerUltimoDiaDelMes($mes,$anio)]);
                    array_push($datosaEnviarfinal,["Referencia1"=> 'Reg. Contab Roles '.ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio]);
                    array_push($datosaEnviarfinal,["Referencia2"=>$anio.$mes.'R'.$referencia]);

                    else:  

                        $totaldebito = 0;

                        foreach($datos as $dat): 
                            
                            $listhaberes      =  ListProvisiones($anio, $mes, $periodo, $dat['id_sys_adm_ccosto']); 
                            $totalIess = 0;
                            $cuentaMayor = '';
                            $cuentaMayorNombre = '';

                            foreach($listhaberes as $haberes):

                                $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $area,$dat['id_sys_adm_ccosto']);

                                if($dataConcepto !== false):

                                    if($dataConcepto['cta_mayor_nombre'] == 'Aporte al Iess'):

                                        $totalIess += $dataConcepto['valor'];
                                        $cuentaMayor = $dataConcepto['cta_debito'];
                                        $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];
                                        
                                    else:

                                        $totaldebito += $dataConcepto['valor'];

                                        if($dataConcepto['cta_mayor_nombre'] == '14to. Sueldo/Decimo Cuarto Sueldo' || $dataConcepto['cta_mayor_nombre'] == '13er. Sueldo/Decimo Tercer Sueldo'):

                                            $concepto = explode("/", $dataConcepto['cta_mayor_nombre']);

                                            $dataConcepto['cta_mayor_nombre'] = $concepto[0];

                                        endif;

                                        array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_debito'],"Debe"=>floatval($dataConcepto['valor']),"Haber"=>0,"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>substr($dat['id_sys_adm_ccosto'],0,-2),"CCostos2"=>$dat['id_sys_adm_ccosto']]); 

                                    ?>
                                        <tr>
                                            <td><?= $dataConcepto['cta_debito']?></td>
                                            <td><?= $dataConcepto['cta_mayor_nombre']?></td>
                                            <td><?= $dataConcepto['valor']?></td>
                                            <td></td>
                                            <td>Reg. Contab Beneficios <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                            <td><?= substr($dat['id_sys_adm_ccosto'],0,-2)?></td>
                                            <td><?= $dat['id_sys_adm_ccosto']?></td>
                                        </tr>
                                    <?php

                                    endif;

                                endif;

                            endforeach;

                            array_push($datosaEnviar,["CtaContable"=>$cuentaMayor,"Debe"=>floatval(round($totalIess,2)),"Haber"=>0,"Proyecto"=>$cuentaMayorNombre,"CCostos1"=>substr($dat['id_sys_adm_ccosto'],0,-2),"CCostos2"=>$dat['id_sys_adm_ccosto']]); 

                            ?>
                                <tr>
                                    <td><?= $cuentaMayor?></td>
                                    <td><?= $cuentaMayorNombre?></td>
                                    <td><?= $totalIess?></td>
                                    <td></td>
                                    <td>Reg. Contab Beneficios <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                    <td><?= substr($dat['id_sys_adm_ccosto'],0,-2)?></td>
                                    <td><?= $dat['id_sys_adm_ccosto']?></td>
                                </tr>
                            <?php

                            $totaldebito = $totaldebito + $totalIess;

                       endforeach;

                    ?>

                    <?php

                    $centroCostos = [];
                    $totalcredito = 0;

                    foreach($datos as $dat): 

                        array_push($centroCostos,$dat['id_sys_adm_ccosto']);

                    endforeach;

                    $listdescuentos      =  ListProvisionesXArea($anio, $mes, $periodo, $area); 
                    $totalIessCredito = 0;
                    $cuentaMayor = "";
                    $cuentaMayorNombre = "";

                    foreach($listdescuentos as $descuento):

                        $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$centroCostos);
                    
                        if($dataConcepto['cta_mayor_nombre'] == 'Aporte al Iess'):

                            $totalIessCredito += $dataConcepto['valor'];
                            $cuentaMayor = $dataConcepto['cta_credito'];
                            $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];
                            
                        else:

                            $totalcredito += $dataConcepto['valor'];

                            if($dataConcepto['cta_mayor_nombre'] == '14to. Sueldo/Decimo Cuarto Sueldo' || $dataConcepto['cta_mayor_nombre'] == '13er. Sueldo/Decimo Tercer Sueldo'):

                                $concepto = explode("/", $dataConcepto['cta_mayor_nombre']);

                                $dataConcepto['cta_mayor_nombre'] = $concepto[1];

                            endif;

                            array_push($datosaEnviar,["CtaContable"=>$dataConcepto['cta_credito'],"Debe"=>0,"Haber"=>floatval($dataConcepto['valor']),"Proyecto"=>$dataConcepto['cta_mayor_nombre'],"CCostos1"=>"","CCostos2"=>""]);

                        ?>
                            <tr>
                                <td><?= $dataConcepto['cta_credito'] ?></td>
                                <td><?= $dataConcepto['cta_mayor_nombre'] ?></td>
                                <td></td>
                                <td><?= $dataConcepto['valor']?></td>
                                <td>Reg. Contab Beneficios <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php

                        endif;

                    endforeach;

                    array_push($datosaEnviar,["CtaContable"=>$cuentaMayor,"Debe"=>0,"Haber"=>floatval(round($totalIessCredito,2)),"Proyecto"=>$cuentaMayorNombre,"CCostos1"=>"","CCostos2"=>""]);

                    ?>
                        <tr>
                            <td><?= $cuentaMayor?></td>
                            <td><?= $cuentaMayorNombre?></td>
                            <td></td>
                            <td><?= $totalIessCredito ?></td>
                            <td>Reg. Contab Beneficios <?= ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php

                    $totalcredito = $totalcredito + $totalIessCredito;

                    ?>
                    <tr>
                        <td colspan="2"></td>
                        <td><?= $totaldebito ?></td>
                        <td><?= $totalcredito ?></td>
                    </tr>

                    <?php 

                    array_push($datosaEnviarfinal,['Movimientos'=>$datosaEnviar]);
                    array_push($datosaEnviarfinal,["Fecha"=>obtenerUltimoDiaDelMes($mes,$anio)]);
                    array_push($datosaEnviarfinal,["Referencia1"=> 'Reg. Contab Beneficios '.ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio]);
                    array_push($datosaEnviarfinal,["Referencia2"=>$anio.$mes.'B'.$referencia]);
                    endif;
                    ?>
                </tbody>
          </table>
          <?php
          if(User::hasRole('JEFECONTABLE')):?>
          <div class="container my-5">
            <form id="formulario">
                <script>
                    var data = <?php echo json_encode($datosaEnviarfinal);?>;
                </script>
                <div class="form-group mx-sm-3 mb-2">
                    <input type="text" name="usuario" placeholder="Ingrese su usurio" class="form-control my-3">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <input type="password" name="pass" placeholder="Ingrese contraseña" class="form-control my-3">
                </div>
                <div class="text-center">
                    <button class="btn btn-primary" type="submit">Generar Asiento</button>
                </div>
                
            </form>   
          </div>
          <div id="loading"></div>
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

function obtenerUltimoDiaDelMes($mes, $anio) {
    // Crear una fecha con el primer día del mes
    $fecha = DateTime::createFromFormat('Y-m-d', "$anio-$mes-01");
    
    // Cambiar la fecha al último día del mes
    $fecha->modify('last day of this month');
    
    // Retornar el último día en formato "Y-m-d"
    return $fecha->format('Y-m-d');
}

function ObtenerHaberes($anio, $mes, $periodo, $area, $departamento){
    
    
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
    
}


function ObtenerValorConcepto($anio, $mes, $periodo, $id_sys_rrhh_concepto, $tipo, $area){
    
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

function ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento){
    
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
}


function ListHaberes($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'I'")
    ->andwhere("rol_mov.valor > 0")
    ->andWhere("co.id_sys_adm_ccosto = '{$area}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());

    return $datos;
}

function ListProvisiones($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere(["conceptos.tipo"=>['I','E']])
    ->andwhere("rol_mov.valor > 0")
    ->andWhere("co.id_sys_adm_ccosto = '{$area}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());

    return $datos;
}

function ListProvisionesXArea($anio, $mes, $periodo, $area){
    $datos = [];

    if($area == 1):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 4):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
         $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere(["conceptos.tipo"=>['I','E']])
            ->andwhere("rol_mov.valor > 0")
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[3]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere(["conceptos.tipo"=>['I','E']])
            ->andwhere("rol_mov.valor > 0")
            ->andWhere(["area.id_sys_adm_area"=>[2]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andWhere(["area.id_sys_adm_area"=>[5]])
        ->andwhere("rol_mov.valor > 0")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    endif;

    return $datos;
}

function ListDescuentos($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    if($area == 1):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 4):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
                ->andWhere(["area.id_sys_adm_area"=>[2]])
                ->andwhere("conceptos.id_sys_empresa  = '001'")
                ->distinct()
                ->orderby("conceptos.orden")
                ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[3]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    endif;
    
    
    return $datos;
    
}
function ObtenerConceptoDebito($anio, $mes, $periodo, $concepto, $area,$ccosto){
    
    if($area == 1):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[1,8]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[3]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_credito","conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area ==4):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[4]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[2]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());    
    elseif($area == 6):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());   
    elseif($area == 5):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    endif;
    
   
}

function ObtenerConceptoCredito($anio, $mes, $periodo, $concepto, $area,$ccosto){
    

    if($area == 1):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[3]])
            ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area ==4):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[6]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());  
    elseif($area == 2):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[2]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    endif;
}
function TotalDepartamento($anio, $mes, $periodo, $concepto, $area, $departamento){
    
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
  
    return 0;
}
function TotalArea($anio, $mes, $periodo, $concepto, $area){
    
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

function Faltas($anio, $mes, $cedula){
    
    return SysRrhhEmpleadosRolLiq::find()->select('faltas')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->scalar();

    
}

function Dias($anio, $mes, $cedula, $periodo){
    
    
    
    if($periodo == '70' || $periodo == '71'):
    
    return SysRrhhEmpleadosRolMov::find()->select('cantidad')->where(['anio'=> intval($anio)])->andWhere(['mes'=> intval($mes)])->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['periodo'=> $periodo])->scalar();
    
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
