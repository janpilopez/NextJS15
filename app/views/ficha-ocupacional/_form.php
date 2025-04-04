<?php

use app\models\SysRrhhEmpleados;
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaOpupacional */
/* @var $form yii\widgets\ActiveForm */

$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;

?>

<div class="sys-med-ficha-opupacional-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class = 'panel panel-default'>  
      <div class = 'panel-body'> 
      
         <div class= "row">
               <div class= "col-md-6">
                      <?php   $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                                                       '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                                                   
                                                   echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                                                       'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control'],
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
               <div class= "col-md-6">
                 <?= Html::label('Nombres')?>
                 <?= Html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
               </div>
           </div>
      	   <div class= "row">
      	   		<div class= "col-md-4">
      	   			<?= $form->field($model, 'secuencial')->textInput(['type'=> 'number']) ?>
      	   		</div>
      	   		<div class= "col-md-4">
      	   		 	<?= $form->field($model, 'tipo')->dropDownList(['P'=> 'PRE-OCUPACIONAL', 'I'=>'INICIO', 'P'=> 'PERIODICA','R'=> 'RETIRO', 'G'=> 'REINTEGRO']); ?>
      	   		</div>
      	   		<div class= "col-md-4">
      	   		   <?= $form->field($model, 'fecha')->widget(DatePicker::classname(), [
                        'removeButton' => false,
                        'size'=>'md',
                       'options' => ['placeholder' => 'Fecha']
                     ]);?>
      	   		</div>
      	   </div>
      	   <div class= "row">
      	     <div class= "col-md-12">
      	   		  <?= $form->field($model, 'comentario')->textarea() ?>
      	     </div>
      	   </div>
    
        <div class="form-group">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        </div>
    
      </div>
   </div>
    <?php ActiveForm::end(); ?>

</div>
