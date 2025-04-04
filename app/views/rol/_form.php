<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\assets\RolpagoAsset;
RolpagoAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */
/* @var $form yii\widgets\ActiveForm */

$url = Yii::$app->urlManager->createUrl(['rol']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

?>

<div class="sys-rrhh-empleados-rol-cab-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
        
             <div class = 'row'>
                    <div class = 'col-md-2'>
                    </div>
                    <div class = 'col-md-2'>
                      <?= $form->field($model, 'anio')->textInput(['value'=> $anio,  'id'=> 'anio']);?>
                    </div>
                   <div class = 'col-md-2'>
                         <?= $form->field($model, 'mes')->dropDownList(['1'=> 'Enero', '2'=> 'Febrero', '3'=> 'Marzo', '4'=> 'Abril', '5'=> 'Mayo', '6'=> 'Junio', '7'=> 'Julio', '8'=> 'Agosto', '9'=> 'Septiembre', '10'=> 'Octubre', '11'=> 'Noviembre', '12'=> 'Diciembre'], ['class'=> 'form-control', 'prompt' => 'Seleccione..', 'id'=> 'mes',
                            'options' =>[ $mes => ['selected' => true]]
                  ])?>
                   </div>
                   <div class = 'col-md-2'>
                           <?= $form->field($model, 'periodo')->dropDownList($periodos, ['class'=> 'form-control', 'prompt' => 'Seleccione..','id'=> 'periodo',
                            'options' =>[ $periodo => ['selected' => true]]]);?>
                   </div>
                    <div class = 'col-md-2'>
                          <?= $form->field($model, 'fecha_registro')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                   //'startDate' => date('Y-m-d'),
                                                    'todayHighlight' => true,
                                                ],
                                               'options' => ['placeholder' => 'Fecha Registro',  'value'=> date('Y-m-d'), 'id'=> 'fechareg'],
                				                
                          ]);?>
               
                   </div>
                    <div class = 'col-md-2'>
                    </div>
             </div>   
             <div class = 'row'>
                 <div class = 'col-md-2'>
                 </div>
                 <div class = 'col-md-2'>
                        <?= $form->field($model, 'fecha_ini_liq')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                   //'startDate' => date('Y-m-d'),
                                                    'todayHighlight' => true,
                                                ],
                                               'options' => ['id'=> 'fechainiliq'],
                				                
                         ]);?>
                 </div>
                  <div class = 'col-md-2'>
                   <?= $form->field($model, 'fecha_fin_liq')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                   //'startDate' => date('Y-m-d'),
                                                    'todayHighlight' => true,
                                                ],
                                               'options' => ['id'=> 'fechafinliq'],
                				                
                         ]);?>
                 </div>
                  <div class = 'col-md-2'>
                    <?= $form->field($model, 'fecha_ini')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                   //'startDate' => date('Y-m-d'),
                                                   // 'todayHighlight' => true,
                                                ],
                                               'options' => ['id'=> 'fechaini'],
                				                
                         ]);?>
                 </div>
                  <div class = 'col-md-2'>
                    <?= $form->field($model, 'fecha_fin')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                   //'startDate' => date('Y-m-d'),
                                                   // 'todayHighlight' => true,
                                                ],
                                               'options' => ['id'=> 'fechafin'],
                				                
                         ]);?>
                 </div>
                  <div class = 'col-md-2'>
                  </div>
             </div>  
            
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Periodo', ['class' => 'btn btn-success', 'disabled']) ?>
        </div>
   
        <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
