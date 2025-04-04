<?php

use app\models\SysRrhhEmpleados;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhPermisos;
use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;
?>
<div class="sys-med-certficado-medico-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class = 'panel panel-default'>  
         <div class = 'panel-body'> 
            <div class = "row">
              <div class = "col-md-6">
                   <?php   $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                                                       '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                                                   
                                                   echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                                                       'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control','disabled' => $inputDisable],
                                                       'pluginOptions' => ['highlight'=>true],
                                                       'scrollable'=>true,
                                                       'dataset' => [
                                                           [
                                                               
                                                               'remote' => [
                                                                   'url' =>    Url::to(['consultas/listempleados2']) . '?q=%QUERY',
                                                                   'wildcard' => '%QUERY'
                                                               ],
                                                               'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                                               'display' => 'value',
                                                               'templates' => [
                                                                   'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                                                   'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                                                   ],
                                                                  
                                                           ]
                                                          
                                                           ],'pluginEvents' => [
                                                               'typeahead:select' => 'function(ev, suggestion) {
                                                                   $("#nombres").val(suggestion.nombres); }',
                                                           ]
                                                           
                                                        
                                                   ])->label('Cédula'); ?>
              </div>  
              <div class = "col-md-6">
                 <?= Html::label('Nombres')?>
                 <?= Html::textInput('nombres', $nombres, ['class'=> 'form-control', 'id'=> 'nombres', 'disabled'=> true])?>
               </div>
           </div>          
            <div class= "row">
                <div class= "col-md-2">
                    <?= $form->field($model, 'entidad_emisora')->dropDownList(['I'=> 'IESS', 'M'=> 'MPS', 'P'=> 'PARTICULAR', 'O'=> 'OTROS', 'E'=> 'PESPESCA'], ['maxlength' => true]) ?>
                </div>
                <div class= "col-md-2">
            	    <?= $form->field($model, 'tipo_ausentismo')->dropDownList(['E'=> 'ENFERMEDAD', 'A'=> 'ACCIDENTE'],['maxlength' => true]) ?>
            	</div>
            	<div class= "col-md-2">
            	    <?= $form->field($model, 'tipo')->dropDownList(['D'=> 'DIAS', 'H'=> 'HORAS'],['maxlength' => true]) ?>
            	</div>
                <div class = 'col-md-2'>
                      <?= $form->field($model, 'id_sys_rrhh_permiso')->dropDownList(ArrayHelper::map(SysRrhhPermisos::find()->where(['estado'=>'A'])->where(['id_sys_rrhh_permiso' => $listpermisos])->all(), 'id_sys_rrhh_permiso', 'permiso'), ['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm', $model->id_sys_rrhh_permiso => ['selected' => true]]) ?>
                   </div>
            	<div class= "col-md-3">
            	    <?= $form->field($model, 'fecha_ini')->widget(DateTimePicker::className(),[
            	        'options' => [
            	           
            	            'pluginOptions' => [
            	                //'autoclose'=>true,
            	                'format' => 'yyyy/mm/dd hh:ii:ss'
                            ]
            	        ],
            	        
            	    ]) ?>
            	</div>
            	<div class= "col-md-3">
            	       <?= $form->field($model, 'fecha_fin')->widget(DateTimePicker::className(),[
            	        'options' => [
            	           
            	            'pluginOptions' => [
            	                //'autoclose'=>true,
            	                'format' => 'yyyy/mm/dd hh:ii:ss'
                            ]
            	        ],
            	        
            	    ]) ?>
            	</div>
            </div>
            <div class= "row">
               <div class= "col-md-12">
                	<?= $form->field($model, 'diagnostico')->textInput(['maxlength' => true]) ?>
               </div>
            </div>  
            <div class="form-group">
                   <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
            </div>
     	</div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
