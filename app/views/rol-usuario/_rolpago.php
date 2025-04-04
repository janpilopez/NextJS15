<?php
use yii\helpers\Html;
use app\assets\RolAsset;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosRolCab;
RolAsset::register($this);
$totalhaberes    = 0;
$totaldescuentos = 0;
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 =>'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];


//obetener Area

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

$area =(new \yii\db\Query())->select(
    [
        "area",
    ])
    ->from("sys_adm_cargos cargo")
    ->innerJoin("sys_adm_departamentos departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("cargo.id_sys_empresa = '{$datos['id_sys_empresa']}'")
    ->andwhere("cargo.id_sys_adm_cargo = '{$datos['id_sys_adm_cargo']}'")
    ->Scalar(SysRrhhEmpleados::getDb());
    
 $rol = SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->one();
    
?>
<!-- Datos de Factura -->
<div class="row without-margin">
	<div class="col-xs-12 text-center">
		 <img src="<?=Yii::getAlias('@web')."/logo/".$empresa->ruc."/".$empresa->logo?>" height="39" width="180">
	</div>
	<div class="col-xs-12 text-center">
		<h2>Rol de Pago</h2>
		<h3>Mes de <?= $meses[$mes]?> del <?= $anio?></h3>
	</div>
</div>

<div class="row without-margin">
      <table style="font-size: 11px; width:100%">
         <tr>
            <td width="12%"><b>Nombres:</b></td>
            <td width="38%"><?= $datos["nombres"]?></td>
            <td width="14%"><b>Forma Pago:</b></td>
            <td width="36%"><?= $datos["forma_pago"]?></td>
         </tr>
           <tr>
            <td><b>Área:</b></td>
            <td><?= $area?></td>
            <td><b>Fecha Ingreso:</b></td>
            <td><?= $datos["fecha_ingreso"]?></td>
         </tr>
           <tr>
            <td><b>Cargo:</b></td>
            <td><?= $datos["cargo"]?></td>
            <td><b>Dias Lab:</b></td>
            <td><?= intval($datos["cantidad"])?></td>
         </tr>
         <tr>
            <td><b>Novedades:</b></td>
            <td colspan="3" style="text-align: left;" ><?= " DEL ".date('d', strtotime($rol->fecha_ini_liq))." DE ". strtoupper($meses[date('n', strtotime($rol->fecha_ini_liq))])." AL ".date('d', strtotime($rol->fecha_fin_liq))." DE ".strtoupper($meses[date('n', strtotime($rol->fecha_fin_liq))])?></td>
         </tr>
      </table>
</div>

<div class="row without-margin">
     <div class = "col-xs-6">
           <div class = "detalle-rol">
                    <table style="font-size: 10px; width:100%;">
                     <tr>
                       <th colspan="2" class="text-center">Haberes</th>
                      </tr>
                      <?php 
                      $haberes = (new \yii\db\Query())->select(
                                  [
                                      "concepto",
                                      "unidad",
                                      "cantidad",
                                      "rol_mov.valor",
                                      "imprime"
                                  ])
                                  ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                                  ->join("INNER JOIN","sys_rrhh_conceptos as conceptos","conceptos.id_sys_rrhh_concepto=rol_mov.id_sys_rrhh_concepto")
                                  ->Where("rol_mov.anio = '{$anio}'")
                                  ->andwhere("rol_mov.mes= '{$mes}'")
                                  ->andwhere("rol_mov.periodo = '{$periodo}'")
                                  ->andwhere("rol_mov.id_sys_empresa= '001'")
                                  ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$datos['id_sys_rrhh_cedula']}'")
                                  ->andwhere("conceptos.tipo= 'I'")
                                  ->orderby("orden")
                                  ->all(SysRrhhEmpleados::getDb());
                                
                       if(count($haberes)):?>
                          <?php foreach ($haberes as $haber):?>
                          
                              <?php if($haber['imprime'] == 'S'):?>
                              
                                   <tr>
                                     <td width ="45%"><?= $haber['concepto']?></td>
                                     <td width ="5%"  style="text-align: right"><?= number_format($haber['valor'], 2, ',', '.');?></td>
                                   </tr>
                               
                               <?php endif;?>
                           
                          <?php endforeach;?>
                          <?php $totalhaberes = array_sum(array_column($haberes, 'valor'))?>
                      <?php endif;?>
                    </table>
           </div>
           <p style ="text-align: right; font-size: 11px" ><b>Total Haberes: </b><?= number_format ($totalhaberes, 2, ',', '.') ?></p>
           <p style ="text-align: left; margin: 0px; font-size: 11px" ><b>Banco: </b> <?= $datos['banco']?></p>
           <p style ="text-align: left; margin: 0px; font-size: 11px" ><b>Cuenta: </b><?= $datos['cta_banco']?></p>
     </div>
     <div class = "col-xs-6">
       <div class = "detalle-rol">
         <table style="font-size: 10px; width:100%;">
           <tr>
            <th colspan="2" class="text-center">Descuentos</th>
          </tr>
          <?php 
             $descuentos = (new \yii\db\Query())->select(
                   [
                      "concepto",
                      "unidad",
                      "cantidad",
                      "rol_mov.valor",
                      "imprime"
                  ])
                  ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                  ->join("INNER JOIN","sys_rrhh_conceptos as conceptos","conceptos.id_sys_rrhh_concepto=rol_mov.id_sys_rrhh_concepto")
                  ->Where("rol_mov.anio = '{$anio}'")
                  ->andwhere("rol_mov.mes= '{$mes}'")
                  ->andwhere("rol_mov.periodo = '{$periodo}'")
                  ->andwhere("rol_mov.id_sys_empresa= '001'")
                  ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$datos['id_sys_rrhh_cedula']}'")
                  ->andwhere("conceptos.tipo= 'E'")
                  ->orderby("orden")
                  ->all(SysRrhhEmpleados::getDb());
          
              if(count($descuentos)):?>
              <?php foreach ($descuentos as $descuento):?>
              
                 <?php if($descuento['imprime'] == 'S'):?>
              
                   <tr>
                     <td width ="45%"><?= $descuento['concepto']?></td>
                     <td width ="5%" style="text-align: right"><?= number_format($descuento['valor'], 2, ',', '.');?></td>
                   </tr>
                   
                 <?php endif;?>
                   
                <?php endforeach;?>
               <?php $totaldescuentos = array_sum(array_column($descuentos,'valor'));?>
             <?php endif;?>
          
        </table>
       </div>
       <p style ="text-align: right; margin: 0px; font-size: 11px"><b>Total Descuentos: </b><?= number_format ($totaldescuentos, 2, ',', '.')?></p>
       <br>
       <p style ="text-align: right; margin: 0px; font-size: 11px"><b>NETO A RECIBIR: </b><?= number_format (($totalhaberes - $totaldescuentos),2, ',', '.')?></p>
     </div>
</div>
<br>
<br>
<div class="row without-margin">
    <table style="font-size: 10px; width:100%;">
      <tr>
        <td style ="text-align: right;"><b>Firmar del Beneficiario: ..................................</b></td>
      </tr>
      <tr>
        <td style ="text-align: right;"><b>C.c:</b> <?= $datos["id_sys_rrhh_cedula"] ?></td>
      </tr>
       <tr>
        <td style ="text-align: right;"><b>Email: </b> <?= $datos["email"] ?></td>
      </tr>
    </table>
</div>



