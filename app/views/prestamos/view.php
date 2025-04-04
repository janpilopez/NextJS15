<?php

use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhPrestamosCab;
use app\models\SysRrhhPrestamosDet;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPrestamosCab */

$this->title = 'Datos Préstamo';
$this->params['breadcrumbs'][] = ['label' => 'Préstamos Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$meses     = Yii::$app->params['meses'];
$detalle = SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_cab'=> $model->id_sys_rrhh_prestamos_cab])->all();

$total = 0;
$saldo = 0;

?>
<div class="sys-rrhh-prestamos-cab-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
             [
               'label'=> '#Préstamo',
               'attribute'=>'id_sys_rrhh_prestamos_cab',
               'value'=> function($model){   
                 return str_pad($model->id_sys_rrhh_prestamos_cab, 5, "0", STR_PAD_LEFT);
               }
             ],
             [
                 'label'=> 'Identificación',
                 'attribute'=>'id_sys_rrhh_cedula',
                 'value'=> function($model){
                     return $model->id_sys_rrhh_cedula;
                 }
            ],
            [
                
                'attribute'=>'Nombres',
                'value'=> function($model){
                
                    $empleados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                     return $empleados->nombres;
                }
            ],  
            
            'valor',
            [
                'label'=> '#Cuotas',
                'attribute'=> 'cuotas'
            ],
            [
                
                'attribute'=>'Valor Cuota',
                'value'=> function($model){
                
                $detalle = SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_cab'=> $model->id_sys_rrhh_prestamos_cab])->one();
                     
                     if($detalle):
                            return $detalle->valor;
                     endif;
                }
            ], 
            
            
            'comentario'
        ],
    ]) ?>
  <?php if($detalle):?>	
  <div class= "row">
      <div class="col-md-4">
         	<table class= "table">
                <caption>Historial de Ingresos</caption>
                <thead>
                      <tr>
               			<th>Año</th>
               			<th>Mes</th>
               			<th>Ing. Quicenal</th>
               			<th>Ing. Mensual</th>
               		  </tr>
                </thead>
      			<?php 
                  $roles = getHistoricoRol($model->id_sys_rrhh_cedula);
                  foreach ($roles as $rol):
                 ?>
                <tbody>
                     <tr>
                       <td><?= $rol['anio']?></td>
                       <td><?= $meses[$rol['mes']]?></td>
                       <td><?= getHaberes($rol['anio'], $rol['mes'], '1', $rol['id_sys_rrhh_cedula']) - getDescuentos($rol['anio'], $rol['mes'], '1', $rol['id_sys_rrhh_cedula'])?></td>
                       <td><?= getHaberes($rol['anio'], $rol['mes'], '2', $rol['id_sys_rrhh_cedula']) - getDescuentos($rol['anio'], $rol['mes'], '2', $rol['id_sys_rrhh_cedula'])?></td>
                     </tr>
                </tbody>
              <?php endforeach;?>
            </table>
      </div>
      <div class="col-md-4">
          <table class= "table">
              <caption>Historial de Préstamos Empresa</caption>
              <thead>
                <tr>
               	   <th>Fecha</th>
               	   <th>Cuotas</th>
               	   <th>Valor</th>
                </tr>
             </thead>
             <tbody>
                <?php
                $prestamos  = getHistorialPrestamos($model->id_sys_rrhh_cedula);
                
                foreach ($prestamos as $prestamo):
                ?>
                <tbody>
                   <tr>
                   	  <td><?= $prestamo['fecha']?></td>
                   	  <td><?= $prestamo['cuotas']?></td>
                   	  <td><?= $prestamo['valor']?></td>
                   </tr>
                </tbody>
                <?php endforeach;?>
             </tbody>
          </table>
      </div>
      <div class="col-md-4">
          <table class= "table">
              <caption>Total de Provisiones</caption>
              <thead>
                <tr>
               	   <th>Provisión</th>
               	   <th>Valor</th>
                </tr>
             </thead>
             <tbody>
                <?php
                $desahucio  = getTotalProvisionesDesahucio($model->id_sys_rrhh_cedula,$model->fecha);
                $decimoCuarto  = getTotalProvisionesDecimoCuarto($model->id_sys_rrhh_cedula);
                $decimoTercero = getTotalProvisionesDecimoTercero($model->id_sys_rrhh_cedula);
                $vacaciones = getTotalProvisionesVacaciones($model->id_sys_rrhh_cedula);
            
                //foreach ($prestamos as $prestamo):
                ?>
                <tbody>
                    <tr>
                   	  <td>Desahucio</td>
                   	  <td><?= number_format($desahucio, 2, '.', '') ?></td>
                   </tr>
                   <tr>
                   	  <td>Décimo Cuarto</td>
                   	  <td><?= number_format($decimoCuarto, 2, '.', '') ?></td>
                   </tr>
                   <tr>
                   	  <td>Décimo Tercero</td>
                   	  <td><?= number_format($decimoTercero, 2, '.', '') ?></td>
                   </tr>
                   <tr>
                   	  <td>Vacaciones</td>
                   	  <td><?= number_format($vacaciones, 2, '.', '') ?></td>
                   </tr>
                </tbody>
                <?php //endforeach;?>
             </tbody>
          </table>
      </div>
      <div class="col-md-4">
          <table class= "table">
              <caption>Historial de Préstamos IESS</caption>
              <thead>
                <tr>
               	   <th>Año</th>
               	   <th>Mes</th>
               	   <th>Préstamos Quirografarios</th>
                   <th>Préstamos Hipotecarios</th>
                </tr>
             </thead>
             <tbody>
                <?php
                $prestamos  = getHistoricoRol($model->id_sys_rrhh_cedula);
                
                foreach ($prestamos as $prestamo):

                  $descPresQuiro = getDescuentoPresQuiro($prestamo['anio'], $prestamo['mes'], '2', $rol['id_sys_rrhh_cedula']);
                  $descPresHipo = getDescuentoPresHipo($prestamo['anio'], $prestamo['mes'], '2', $rol['id_sys_rrhh_cedula']);
                ?>
                <tbody>
                   <tr>
                   	  <td><?= $prestamo['anio']?></td>
                   	  <td><?= $meses[$prestamo['mes']]?></td>
                   	  <td><?= $descPresQuiro ?></td>
                      <td><?= $descPresHipo ?></td>
                   </tr>
                </tbody>
                <?php endforeach;?>
             </tbody>
          </table>
      </div>
  </div> 
  <?php endif;?>
  <?php if($model->autorizacion == 'P'): ?>
  <br>
      <?= Html::a('Aprobar Préstamo', ['aprobarprestamos',  'id'=>$model->id_sys_rrhh_prestamos_cab], ['class' => 'btn btn-success']) ?>
      <?= Html::a('No Aplica Préstamo', ['noaprobarprestamos',  'id'=>$model->id_sys_rrhh_prestamos_cab], ['class' => 'btn btn-danger']) ?>
  <?php 
   	    endif;
   	 ?>
</div>
<?php 
function getTipoUsuario(){
        
  $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> trim(Yii::$app->user->identity->id)])->one();
        
   if($usertipo):
        
     return $usertipo->usuario_tipo;
        
   endif;
   
return 'N';

}

function getHistoricoRol($id_sys_rrhh_cedula){
    
  return   (new \yii\db\Query())->select("top (3) * ")
        ->from("sys_rrhh_empleados_rol_mov")
        ->where("estado = 'P'")
        ->andWhere("id_sys_rrhh_concepto = 'SUELDO'")
        ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->orderBy("anio desc, mes desc")
        ->all(SysRrhhEmpleados::getDb());
     
}


function getHaberes($anio, $mes, $periodo, $id_sys_rrhh_cedula){
    
   return  (new \yii\db\Query())->select(
        [
             "sum(rol_mov.valor)"
        ])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join("INNER JOIN","sys_rrhh_conceptos as conceptos","conceptos.id_sys_rrhh_concepto=rol_mov.id_sys_rrhh_concepto")
        ->Where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes= '{$mes}'")
        ->andwhere("rol_mov.periodo = '{$periodo}'")
        ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->andwhere("conceptos.tipo= 'I'")
        ->scalar(SysRrhhEmpleados::getDb());
    
    
}

function getDescuentoPresQuiro($anio, $mes, $periodo, $id_sys_rrhh_cedula){
    
  return  (new \yii\db\Query())->select(
       [
            "rol_mov.valor"
       ])
       ->from("sys_rrhh_empleados_rol_mov as rol_mov")
       ->Where("rol_mov.anio = '{$anio}'")
       ->andwhere("rol_mov.mes= '{$mes}'")
       ->andwhere("rol_mov.periodo = '{$periodo}'")
       ->andWhere("rol_mov.id_sys_rrhh_concepto = 'DESC_PRES_QUIRO'")
       ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
       ->scalar(SysRrhhEmpleados::getDb());
   
   
}

function getDescuentoPresHipo($anio, $mes, $periodo, $id_sys_rrhh_cedula){
    
  return  (new \yii\db\Query())->select(
       [
            "rol_mov.valor"
       ])
       ->from("sys_rrhh_empleados_rol_mov as rol_mov")
       ->Where("rol_mov.anio = '{$anio}'")
       ->andwhere("rol_mov.mes= '{$mes}'")
       ->andwhere("rol_mov.periodo = '{$periodo}'")
       ->andWhere("rol_mov.id_sys_rrhh_concepto = 'DESC_PRES_HIPO'")
       ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
       ->scalar(SysRrhhEmpleados::getDb());
   
   
}

function getDescuentos($anio, $mes, $periodo, $id_sys_rrhh_cedula){
    
    return  (new \yii\db\Query())->select(
        [
            "sum(rol_mov.valor)"
        ])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join("INNER JOIN","sys_rrhh_conceptos as conceptos","conceptos.id_sys_rrhh_concepto=rol_mov.id_sys_rrhh_concepto")
        ->Where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes= '{$mes}'")
        ->andwhere("rol_mov.periodo = '{$periodo}'")
        ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->andwhere("conceptos.tipo= 'E'")
        ->scalar(SysRrhhEmpleados::getDb());
    
}

function getHistorialPrestamos($id_sys_rrhh_cedula){
    
    
    return  (new \yii\db\Query())->select("*")
        ->from("sys_rrhh_prestamos_cab")
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->andwhere("anulado = 0")
        ->all(SysRrhhEmpleados::getDb());
    
}

function getHistorialPrestamosExternos($id_sys_rrhh_cedula){
    
    
  return  (new \yii\db\Query())->select("*")
      ->from("sys_rrhh_prestamos_cab")
      ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
      ->andwhere("anulado = 0")
      ->all(SysRrhhEmpleados::getDb());
  
}
function getUltimaRemuneracion($anio,$mes,$cedula){
    
  $mes = $mes - 1;  

  return  (new \yii\db\Query())->select("*")
      ->from("sys_rrhh_empleados_rol_mov")
      ->where("id_sys_rrhh_cedula = '{$cedula}'")
      ->andwhere("periodo = '2'")
      ->andwhere("anio = '{$anio}'")
      ->andwhere("mes = '{$mes}'")
      ->all(SysRrhhEmpleados::getDb());
  
}
function getTotalProvisionesDesahucio($id_sys_rrhh_cedula,$fecha){
    
  $db  = $_SESSION['db'];
  $anio = date('Y',strtotime($fecha));
  $mes = date('n',strtotime($fecha));

  $fechaIngreso = getObtenerFechaIngreso($id_sys_rrhh_cedula);

  $conceptos = getUltimaRemuneracion($anio,$mes,$id_sys_rrhh_cedula);
  $sumTotal = 0;  

  foreach($conceptos as $concepto){
    if($concepto['id_sys_rrhh_concepto'] == 'SUELDO' || $concepto['id_sys_rrhh_concepto'] == 'PAGO_HORAS_50' || $concepto['id_sys_rrhh_concepto'] == 'PAGO_HORAS_25' || $concepto['id_sys_rrhh_concepto'] == 'PAGO_HORAS_100'){
      $sumTotal += floatval($concepto['valor']);
    }
  }

  $date1 = new \DateTime($fechaIngreso['fecha_ingreso']);
  $date2 = new \DateTime($fecha);
  $diff  = $date1->diff($date2);
  $anios = $diff->y;

  $porcentaje = $sumTotal * 0.25;

  $total = floatval($porcentaje) * $anios;
  
  return $total;
}

function getTotalProvisionesDecimoCuarto($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];

  $SueldoBasico = SysRrhhConceptos::find()->select('valor')->where(['concepto_sueldo'=> 'SU'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'V'])->scalar();

  $fechaActual = date('Y-m-d');
  $anioAct = date('Y',strtotime($fechaActual));
  $anioSig = date('Y',strtotime($fechaActual.'+ 1 years'));
    
  $decimo =  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioAct}' and  mes >= 3 and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();                              
  $dias   =  Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioAct}' and  mes >= 3 and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();
  $decimo =  $decimo +  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioSig}' and  mes <= 2 and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();
  $dias =    $dias +  Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioSig}' and  mes <= 2 and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();
                                
  if($dias == 360):
                                
    $decimo = $SueldoBasico;  

  else :

    $decimo = ($dias * $SueldoBasico) / 360;

  endif;

  return $decimo;
}

function getTotalProvisionesDecimoTercero($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];

  $fechaActual = date('Y-m-d');
  $anioAct = date('Y',strtotime($fechaActual));
  $anioAnt = date('Y',strtotime($fechaActual.'- 1 years'));
  
  $valorProvision = 0;
  $diasLaborados = 0;

  $valorProvision =   $valorProvision +  (new \yii\db\Query())
  ->select(["sum(valor)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'DECIMO_TERCERO'")
  ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
  ->andwhere("anio = {$anioAnt}")
  ->andwhere("mes >= 12")
  ->scalar(SysRrhhEmpleados::getDb());
                                             
  $diasLaborados =   $diasLaborados +  (new \yii\db\Query())
  ->select(["sum(cantidad)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'SUELDO'")
  ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
  ->andwhere("anio = {$anioAnt}")
  ->andwhere("mes >= 12")
  ->scalar(SysRrhhEmpleados::getDb());
                                             
  $valorProvision =   $valorProvision +  (new \yii\db\Query())
  ->select(["sum(valor)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'DECIMO_TERCERO'")
  ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
  ->andwhere("anio = {$anioAct}")
  ->andwhere("mes <= 11")
  ->scalar(SysRrhhEmpleados::getDb());
                                             
  $diasLaborados =   $diasLaborados +  (new \yii\db\Query())
  ->select(["sum(cantidad)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'SUELDO'")
  ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
  ->andwhere("anio = {$anioAct}")
  ->andwhere("mes <= 11")
  ->scalar(SysRrhhEmpleados::getDb());

  $decimo_ter = floatval($valorProvision);


  return $decimo_ter;
}

function getTotalProvisionesVacaciones($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];

  $mesIngreso = obtenerMesIngreso($id_sys_rrhh_cedula);
  $mesAnterior = $mesIngreso - 1;

  if($mesAnterior == 0){
    $mesAnterior = 12;
  }
  
  $periodosPendientes = obtenerPeriodosPendientes($id_sys_rrhh_cedula);

  $provisionAnio = 0;

  $total = 0;

  $aniovac = 0;
  $aniovach = 0;
  
  foreach($periodosPendientes as $periodo){
    $provisionAnio = obtenerProvisionAnual($periodo['id_sys_rrhh_cedula'],$periodo['anio_vac'],$periodo['anio_vac_hab']);
  
    $diasPendientes = $periodo['dias_disponibles'] - $periodo['dias_otorgados'];
    $total += ($provisionAnio / $periodo['dias_disponibles'])*$diasPendientes;
  }

  $ultP = obtenerUltimoPeriodo($id_sys_rrhh_cedula);

  if($ultP){

    $aniovac = $ultP['anio_vac'] + 1;
    $aniovach = $ultP['anio_vac_hab'] + 1;

    $total =   floatval($total) +  (new \yii\db\Query())
    ->select(["sum(valor)"])
    ->from("sys_rrhh_empleados_rol_mov")
    ->where("id_sys_rrhh_concepto = 'VACACIONES'")
    ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
    ->andwhere("anio = {$aniovac}")
    ->andwhere("mes >= {$mesIngreso}")
    ->scalar(SysRrhhEmpleados::getDb());                  
                                                
    $total =   floatval($total) +  (new \yii\db\Query())
    ->select(["sum(valor)"])
    ->from("sys_rrhh_empleados_rol_mov")
    ->where("id_sys_rrhh_concepto = 'VACACIONES'")
    ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
    ->andwhere("anio = {$aniovach}")
    ->andwhere("mes <= {$mesAnterior}")
    ->scalar(SysRrhhEmpleados::getDb());
  }else{

    $aniovac = date('Y') - 1;
    $aniovach = date('Y');

    $total =   floatval($total) +  (new \yii\db\Query())
    ->select(["sum(valor)"])
    ->from("sys_rrhh_empleados_rol_mov")
    ->where("id_sys_rrhh_concepto = 'VACACIONES'")
    ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
    ->andwhere("anio = {$aniovac}")
    ->andwhere("mes >= {$mesIngreso}")
    ->scalar(SysRrhhEmpleados::getDb());                  
                                                
    $total =   floatval($total) +  (new \yii\db\Query())
    ->select(["sum(valor)"])
    ->from("sys_rrhh_empleados_rol_mov")
    ->where("id_sys_rrhh_concepto = 'VACACIONES'")
    ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
    ->andwhere("anio = {$aniovach}")
    ->andwhere("mes <= {$mesAnterior}")
    ->scalar(SysRrhhEmpleados::getDb());
  }
  
  return floatval($total);
}

function obtenerPeriodosPendientes($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];
  return Yii::$app->$db->createCommand("select * from sys_rrhh_empleados_periodo_vacaciones vac
  inner join sys_adm_periodo_vacaciones per on per.id_sys_adm_periodo_vacaciones = vac.id_sys_adm_periodo_vacaciones
  where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'
  and estado = 'P'")->queryAll();                              
  
}   

function obtenerUltimoPeriodo($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];
  return Yii::$app->$db->createCommand("select top(1) * from sys_rrhh_empleados_periodo_vacaciones vac
  inner join sys_adm_periodo_vacaciones per on per.id_sys_adm_periodo_vacaciones = vac.id_sys_adm_periodo_vacaciones
  where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' order by per.id_sys_adm_periodo_vacaciones desc")->queryOne();                              
  
}   


function obtenerProvisionAnual($cedula,$anioAnt,$anioAct){
    
  $db  = $_SESSION['db'];

  $mesIngreso = obtenerMesIngreso($cedula);
  $mesAnterior = $mesIngreso - 1;

  if($mesAnterior == 0){
    $mesAnterior = 12;
  }

  $valorProvision = 0;

  $valorProvision =   $valorProvision +  (new \yii\db\Query())
  ->select(["sum(valor)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'VACACIONES'")
  ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
  ->andwhere("anio = {$anioAnt}")
  ->andwhere("mes >= {$mesIngreso}")
  ->scalar(SysRrhhEmpleados::getDb());                  
                                             
  $valorProvision =   $valorProvision +  (new \yii\db\Query())
  ->select(["sum(valor)"])
  ->from("sys_rrhh_empleados_rol_mov")
  ->where("id_sys_rrhh_concepto = 'VACACIONES'")
  ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
  ->andwhere("anio = {$anioAct}")
  ->andwhere("mes <= {$mesAnterior}")
  ->scalar(SysRrhhEmpleados::getDb());
                                             
  $decimo_ter = floatval($valorProvision);


  return $decimo_ter;                           
  
} 

function obtenerMesIngreso($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];
  return Yii::$app->$db->createCommand("select MONTH(fecha_ingreso) as mes_ingreso from sys_rrhh_empleados_contratos 
  where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'
  and activo = 1")->queryScalar();                              
  
}

function getObtenerFechaIngreso($id_sys_rrhh_cedula){
    
  $db  = $_SESSION['db'];
  return Yii::$app->$db->createCommand("select fecha_ingreso from sys_rrhh_empleados_contratos 
  where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'
  and activo = 1")->queryOne();                              
  
}

?>


