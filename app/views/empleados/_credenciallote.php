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
use app\assets\EmpleadosCredencialLoteAsset;
EmpleadosCredencialLoteAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */
/* @var $form yii\widgets\ActiveForm */

$url = Yii::$app->urlManager->createUrl(['empleados']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

?>
<div class="sys-rrhh-empleados-rol-cab-form">
       <div class = "row">
            <div class = "col-md-4">
                <div class = "row no-print">
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
                                 <td><?= utf8_encode($emp->nombres)?></td>
                                 <td><input  type="checkbox" id="<?= $emp->id_sys_rrhh_cedula ?>" value="<?= $emp->id_sys_rrhh_cedula?>" ></td>
                               </tr>
                              <?php endforeach;
                               endif;
                             ?>
                        </tbody>
                     </table>
                   </div>
                   	<?= Html::input("submit", "liquidar", "Consultar", ['id'=>'btnconsultar','class'=>"btn btn-success", 'style'=>'width:100%']) ?>
     
               </div>
            </div>
            <div class = "col-md-8">
               <div id= "detalle">
               
               </div>
            </div>
       </div>
      <div id= "loading"></div>
</div>




