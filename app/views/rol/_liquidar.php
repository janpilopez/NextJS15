<?php
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */
/* @var $form yii\widgets\ActiveForm */

use app\models\SysAdmAreas;
use app\models\SysRrhhEmpleados;
use kartik\depdrop\DepDrop;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use app\assets\LiquidarRolAsset;
LiquidarRolAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */
/* @var $form yii\widgets\ActiveForm */

$url = Yii::$app->urlManager->createUrl(['rol']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

$meses      = Yii::$app->params['meses'];
$periodos   = Yii::$app->params['periodos'];

$haberes    =  getHaberes($model->anio, $model->mes, $model->periodo, 'I', $model->id_sys_empresa);
$descuentos =  getDescuentos($model->anio, $model->mes, $model->periodo, 'E', $model->id_sys_empresa);
$totalemp   =  getEmpleadosLiquidacion($model->anio, $model->mes, $model->periodo, $model->id_sys_empresa);

$neto    = 0;

if($model->periodo != 90):
    $neto =  $haberes - $descuentos; 
else:
    $neto =  $haberes + $descuentos; 
endif;

$this->title = 'Liquidación Periodo';
$this->params['breadcrumbs'][] = ['label' => 'Periodos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cont = 0;
?>
<style>
.margin {
   margin:1px;
   font-size: 12px;
}
</style>
<div class="sys-rrhh-empleados-rol-cab-form">
       <div class = "row">
            <div class = "col-md-4">
              	<div class = "panel panel-default">
                    <div class = "panel-heading">
                     <b>Parámetros de Busquedas:</b>
                    </div>
                   <div class = "panel-body">
                       <table class="table-condensed" style="width: 100%; font-size: 12px;">
                          <tr>
                             <td><b>Area</b><td>
                             <td><?= Html::DropDownList('area', 'area',  ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos'])?> <td>
                            </tr>
                            <tr>
                             <td><b>Departamento</b><td>
                             <td><?= DepDrop::widget([
                                       'name'=> 'departamento',
                                                 'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                                                 'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                                 'pluginOptions'=>[
                                                   'depends'=>['area'],
                                                   'initialize' => true,
                                                   'initDepends' => ['area'],
                                                   'placeholder'=>'Todos',
                                                   'url'=>Url::to(['/consultas/listadepartamento']),
                                                   
                                               ]
                                           ]);?>
                               <td>
                            </tr>
                       </table>
              
                   </div>
               </div>
            </div>
            <div class= "col-md-8">
            	<div class = "panel panel-default">
                    <div class = "panel-heading">
                      <b>Información General del Periodo de Liquidación:</b>
                    </div>
                   <div class = "panel-body">
                        <table class="table-condensed" style="width: 100%; font-size: 12px;">
                              <tr>
                                  <td style="vertical-align: top;"><b>Año:</b></td><td style="vertical-align: top;" ><?= html::hiddenInput('anio', $model->anio, ['id'=> 'anio'])?><?= $model->anio?></td>
                                  <td style="vertical-align: top;"><b>Mes:</b></td><td style="vertical-align: top;"><?= html::hiddenInput('mes',$model->mes, ['id'=> 'mes'])?><?= $meses[$model->mes]?></td>
                                  <td style="vertical-align: top;"><b>Periodo:</b></td><td style="vertical-align: top;"><?= html::hiddenInput('periodo',$model->periodo, ['id'=> 'periodo'])?><?= $periodos[$model->periodo]?></td>
                                  <td style="vertical-align: top;"><b>Estado:</b></td><td style="vertical-align: top;"><?= html::hiddenInput('estado',$model->estado, ['id'=> 'estado'])?><?= trim($model->estado != 'P') ? 'Liquidado': 'Procesado'?></td>
                              </tr>
                              <tr>
                                  <td style="vertical-align: top;"><b>F. Inicio Liq:</b></td><td style="vertical-align: top;"><?= $model->fecha_ini_liq ?></td>
                                  <td style="vertical-align: top;"><b>F. Fin Liq:</b></td><td style="vertical-align: top;"><?= $model->fecha_fin_liq ?></td>
                                  <td style="vertical-align: top;"><b>F. Inicio:</b></td><td style="vertical-align: top;"><?= $model->fecha_ini ?></td>
                                  <td style="vertical-align: top;"><b>F. Final:</b></td><td style="vertical-align: top;"><?= $model->fecha_fin ?></td>
                             </tr>
                              <tr>
                                  <td style="vertical-align: top;"><b>No Emp Liq:</b></td><td style="vertical-align: top;"><?= html::label($totalemp, 'numemp', ['id'=> 'numemp'])?></td>
                                  <td style="vertical-align: top;"><b>T. Ingresos:</b></td><td style="vertical-align: top;"><?= html:: label(number_format($haberes, 2, '.', ','), 'tingresos', ['id'=> 'tingresos'])?></td>
                                  <td style="vertical-align: top;"><b>T. Descuentos:</b></td><td style="vertical-align: top;"><?= html:: label(number_format($descuentos, 2, '.', ','), 'tdescuentos', ['id'=> 'tdescuentos'])?></td>
                                 
                                  <td style="vertical-align: top;"><b>T. Neto:</b></td><td style="vertical-align: top;"><?= html:: label(number_format($neto, 2, '.', ','), 'tneto', ['id'=> 'neto'])?></td>
                             </tr>
                        </table>
                     </div>
                </div>
            </div>
       </div>
       <div class = "row">
         <div class = "col-md-4">
          <div class = "row">
               <div class="col-xs-12 text-center">
               <div style="height: 350px; overflow: auto;">
                    <table id="tableempleados" class="table table-bordered" style="background-color: white; font-size: 11px; width: 100%;  text-align: left;">
                      <thead>
                           <tr class = "info">
                              <th width = "95%">Nombres</th>
                              <th width = "5%"><?= html::checkbox('chekemp', false, ['id'=> 'checkemp'])?></th>
                           </tr>
                       </thead>
                       <tbody>
                             <?php 
                              if($empleados):
                              foreach ($empleados as $emp):
                              ?>
                              <tr>
                                 <td><?=  $emp->nombres?></td>
                                 <td><input  type="checkbox" id="<?= $emp->id_sys_rrhh_cedula ?>" value="<?= $emp->id_sys_rrhh_cedula?>" ></td>
                               </tr>
                              <?php endforeach;
                               endif;
                             ?>
                        </tbody>
                     </table>
                </div>
              </div>
          </div>
          <div class = "row">
              	<div class="col-xs-12">
            		<?= Html::input("submit", "procesar", "Procesar", ['id'=>'btnprocesar','class'=>"btn btn-success", 'style'=>'width:30%', 'disabled'=> $model->estado != 'P'?false:true]) ?>
            		<?= Html::input("submit", "liquidar", "Liquidar", ['id'=>'btnliquidar','class'=>"btn btn-primary", 'style'=>'width:30%', 'disabled'=> $model->estado != 'P'?false:true]) ?>
            		<?= Html::input("submit", "liquidar", "Consultar", ['id'=>'btnconsultar','class'=>"btn btn-warning", 'style'=>'width:30%']) ?>
            	</div>
          </div>
         </div>
         <div class = "col-md-8">
              <div class = "panel panel-default"> 
              <div class = "panel-body">
               <div style="height: 350px; overflow: auto;" id= "detalle">
                 <?php  if($empleados):
                        
                        foreach ($empleados as $emp): 
                            $cont++;
                            //imprimir detalle rol 
                            echo $this->render('_detalleliquidacion', ['id_sys_rrhh_cedula'=> trim($emp->id_sys_rrhh_cedula), 'id_sys_empresa'=> trim($model->id_sys_empresa), 'anio'=> trim($model->anio), 'mes'=> trim($model->mes), 'periodo'=> trim($model->periodo)]);
                               
                         endforeach;
                     endif;
                ?>
                </div>
             </div>
           </div>
         </div>
      </div>
      <div id= "loading"></div>
</div>

<?php function getEmpleadosLiquidacion($anio, $mes, $periodo, $empresa){
    
    $concepto = '';
    
     if($periodo == 1):
      
         $concepto = 'ANTICIPO';
    
    elseif($periodo == 2):
    
         $concepto = 'SUELDO';
    
    elseif($periodo == 90):
    
         $concepto = 'VACACIONES';
    
    endif;
   
   return  (new \yii\db\Query())->select(['count(*)'])
    ->from('sys_rrhh_empleados_rol_mov rol_mov')
    ->where("rol_mov.id_sys_empresa = '{$empresa}'")
    ->andWhere("rol_mov.anio= '{$anio}'")
    ->andwhere("rol_mov.mes='{$mes}'")
    ->andwhere("rol_mov.periodo='{$periodo}'")
    ->andwhere("id_sys_rrhh_concepto = '{$concepto}'")
    ->scalar(SysRrhhEmpleados::getDb());
    
    
    
}?>
<?php function getHaberes($anio, $mes, $periodo, $tipo, $empresa){
    
  
   
   return  (new \yii\db\Query())->select(['isnull(sum(rol_mov.valor), 0)'])
    ->from('sys_rrhh_empleados_rol_mov rol_mov')
    ->innerJoin("sys_rrhh_conceptos conceptos","conceptos.id_sys_rrhh_concepto= rol_mov.id_sys_rrhh_concepto")
    ->where("rol_mov.id_sys_empresa = '{$empresa}'")
    ->andWhere("rol_mov.anio= '{$anio}'")
    ->andwhere("rol_mov.mes='{$mes}'")
    ->andwhere("rol_mov.periodo='{$periodo}'")
    ->andwhere("conceptos.tipo = '{$tipo}'")
    ->scalar(SysRrhhEmpleados::getDb());
    
    
    
}?>
<?php function getDescuentos($anio, $mes, $periodo, $tipo, $empresa){
    
   
   
   return  (new \yii\db\Query())->select(['isnull(sum(rol_mov.valor), 0)'])
    ->from('sys_rrhh_empleados_rol_mov rol_mov')
    ->innerJoin("sys_rrhh_conceptos conceptos","conceptos.id_sys_rrhh_concepto= rol_mov.id_sys_rrhh_concepto")
    ->where("rol_mov.id_sys_empresa = '{$empresa}'")
    ->andWhere("rol_mov.anio= '{$anio}'")
    ->andwhere("rol_mov.mes='{$mes}'")
    ->andwhere("rol_mov.periodo='{$periodo}'")
    ->andwhere("conceptos.tipo = '{$tipo}'")
    ->scalar(SysRrhhEmpleados::getDb());
    
    
    
}?>




