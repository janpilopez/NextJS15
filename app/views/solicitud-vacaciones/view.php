<?php
use app\models\SysAdmDepartamentos;
use yii\helpers\Html;
use app\assets\AppAsset;
use app\models\SysAdmPeriodoVacaciones;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhVacacionesSolicitud;

AppAsset::register($this);
$this->title = 'Asistencia Laboral';

$periodo = SysAdmPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();

$empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();

$vacaciones = SysRrhhVacacionesSolicitud::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andwhere(['id_sys_rrhh_vacaciones_periodo'=> $periodo->id_sys_adm_periodo_vacaciones])->orderBy(['id_sys_rrhh_vacaciones_solicitud'=>SORT_DESC])->one();

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

$dias = [ 0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles' , 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

if($empresa->id_sys_empresa == '001'):

  $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> 8])->one();

else:

  $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> 4])->one();

endif;

?>
<div class= "row">
     <div class = "col-md-12">
      <table>
          <tr>
            <td style="text-align:right;"><img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="70" width="280" class ="text-center"></td>
          </tr>
      </table>
     </div>
</div>
<br>
<div class = "row margen-left">
   <div class = "col-md-12 margen-left">
     <br>
     <br>
     <p>Montecristi,  <?= date("d", strtotime($model->fecha_registro))?> de <?= $meses[date("n", strtotime($model->fecha_registro))]?> del <?= date("Y", strtotime($model->fecha_registro))?></p>
     <br>
     <p>COMUNICACIÓN AL TRABAJADOR</p>
     <p>Señor (a) (ita)</p>
     <p><?= strtoupper($empleado->nombres)?></p>
     <br>
     <br>
     <?php if($vacaciones->tipo == 'A'){?>
     <p>De mis Consideraciones:</p>
     <p style ="text-align: justify;">La compañía dentro de sus operaciones normales y habituales y dando  cumplimiento al  código de trabajo que en su artículo 69 dice textualmente  “Todo trabajador tendrá derecho a gozar anualmente de un periodo ininterrumpido de quince días de descanso, incluido  los días no laborables”,  dejo constancia que usted gozará de <span class = "negrita"><?= calculardias($model->fecha_inicio, $model->fecha_fin)?></span>  días de vacaciones anticipadas  correspondientes al  <span class = "negrita"><?= $periodo->periodo?></span> ,   desde el  <?= date('d', strtotime($model->fecha_inicio))?> de <?= $meses[date('n', strtotime($model->fecha_inicio))]?> del <?= date('Y', strtotime($model->fecha_inicio)) ?> hasta <?= date('d', strtotime($model->fecha_fin))?> de <?= $meses[date('n', strtotime($model->fecha_fin))]?> del <?=date('Y', strtotime($model->fecha_fin))?>.</p>
     <p>Debiéndose reintegrar a sus labores con normalidad el <?=  $dias[date("w", strtotime(fechafinalizacion($model->fecha_fin)))] ?> <?= date("d", strtotime(fechafinalizacion($model->fecha_fin)))?> de <?= $meses[date("n", strtotime(fechafinalizacion($model->fecha_fin)))]?> del <?= date("Y", strtotime(fechafinalizacion($model->fecha_fin)))?></p>
     <p>Para constancia del acuerdo antes expuesto  firman:</p>
     <?php }else{ ?>
     <p>De mis Consideraciones:</p>
     <p style ="text-align: justify;">La compañía dentro de sus operaciones normales y habituales y dando  cumplimiento al  código de trabajo que en su artículo 69 dice textualmente  “Todo trabajador tendrá derecho a gozar anualmente de un periodo ininterrumpido de quince días de descanso, incluido  los días no laborables”,  dejo constancia que usted gozará de <span class = "negrita"><?= calculardias($model->fecha_inicio, $model->fecha_fin)?></span>  días de vacaciones  correspondientes al  <span class = "negrita"><?= $periodo->periodo?></span> ,   desde el  <?= date('d', strtotime($model->fecha_inicio))?> de <?= $meses[date('n', strtotime($model->fecha_inicio))]?> del <?= date('Y', strtotime($model->fecha_inicio)) ?> hasta <?= date('d', strtotime($model->fecha_fin))?> de <?= $meses[date('n', strtotime($model->fecha_fin))]?> del <?=date('Y', strtotime($model->fecha_fin))?>.</p>
     <p>Debiéndose reintegrar a sus labores con normalidad el <?=  $dias[date("w", strtotime(fechafinalizacion($model->fecha_fin)))] ?> <?= date("d", strtotime(fechafinalizacion($model->fecha_fin)))?> de <?= $meses[date("n", strtotime(fechafinalizacion($model->fecha_fin)))]?> del <?= date("Y", strtotime(fechafinalizacion($model->fecha_fin)))?></p>
     <p>Para constancia del acuerdo antes expuesto  firman:</p>
     <?php } ?>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
   </div>
    <table>
       <tr>
         <td width = "50%" class = "text-center"><?= ucwords(strtolower($departamento->departamento))?></td>
         <td width = "50%" class = "text-center">El Trabajdor</td>
       <tr>
       <tr>
         <td width = "50%" class =  "text-center"><?= $empresa->razon_social?></td>
         <td width = "50%" class =  "text-center">C.I:<?=$empleado->id_sys_rrhh_cedula ?></td>
       <tr>
    </table>
</div>
<?php function calculardias($fechaini, $fechafin){
    
    
    $date1 = new \DateTime($fechaini);
    $date2 = new \DateTime($fechafin);
    $diff  = $date1->diff($date2);
    // will output 2 days
     return  $diff->days + 1;
    
}
function fechafinalizacion($fechafin){
    
  return    $fechafin = date("Y-m-d", strtotime($fechafin . " + 1 day"));
    
}


?>



