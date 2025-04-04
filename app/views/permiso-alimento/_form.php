<?php

use app\models\SysRrhhEmpleados;
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisoAlimentos */
/* @var $form yii\widgets\ActiveForm */

$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;

?>


<div class="sys-rrhh-permiso-alimentos-form">
 <div class = 'panel panel-default'>
     <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
	
	 <div class ="row">
            <div class="col-md-3">
                 <?php  $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                               '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                           
                           echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                               'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm'],
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
                                           console.log(suggestion);
                                         $("#nombres").val(suggestion.nombres);
                                          }',
                                   ]
                                   
                                
                           ])->label('Identificación');
                                   ?>
            </div>
            <div class="col-md-9">
                 <?php echo html::label('Nombres')?>
                 <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
            
            </div>
     </div>
     <div class="row">
       <div class="col-md-6">
            <?= $form->field($model, 'inicio')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                              
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'todayHighlight' => true, 
                                                  
                                                ],
                				                'options' => ['placeholder' => 'Fecha de Inicio']
                                            ]);?>
        
       </div>
       <div class="col-md-6">
              <?= $form->field($model, 'fin')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                              
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'todayHighlight' => true, 
                                                  
                                                ],
                				                'options' => ['placeholder' => 'Fecha de Inicio']
                                            ]);?>
       </div>     
     </div>
     <div class="row">
        <div class="col-md-12">
         <?= $form->field($model, 'motivo')->textarea(['maxlength' => true, 'rows' => 10])?>
        </div>
     </div>
   
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
   
    </div>
  </div>
</div>
