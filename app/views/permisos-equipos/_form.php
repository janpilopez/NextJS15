<?php

use app\models\SysRrhhEmpleados;
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */
/* @var $form yii\widgets\ActiveForm */


$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;

?>
<div class="sys-rrhh-empleados-permisos-equipos-form">
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
                      <?= $form->field($model, 'fecha_inicio')->widget(DatePicker::classname(), [
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
                      <?= $form->field($model, 'fecha_fin')->widget(DatePicker::classname(), [
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
                 <?= $form->field($model, 'motivo')->textarea(['maxlength' => true]) ?>
               </div>
         </div>
     
     	<div class="row"> 
              <div class= "col-md-12">
               <div style="height: 300px; overflow: auto; font-size:11px;">
                     <?php 
                        echo  TabularInput::widget([
                         
                         'models' => $modeldet,
                         'id'=> 'modeldet',
                         'attributeOptions' => [
                          // 'enableAjaxValidation'      => true,
                             /*enableClientValidation'    => false,
                             'validateOnChange'          => false,
                             'validateOnSubmit'          => true,
                             'validateOnBlur'            => false,*/
                         ],
                         
                         'allowEmptyList' => true,
                         'addButtonPosition' => MultipleInput::POS_HEADER,
                         'addButtonOptions' => [
                             'class' => 'btn btn-xs btn-info',
                             'label' => '<i class="glyphicon glyphicon-plus"></i>'
                         ],
                         'removeButtonOptions' => [
                             'class' => 'btn btn-xs btn-danger',
                             'label' => '<i class="glyphicon glyphicon-remove"></i>'
                         ],
                            
                            
                            
                         'columns'=> [
                             
                                 [
                                     'name' => 'id',
                                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                 ],
                                 
                                 [
                                     'name' => 'tipo',
                                     'title' => $modeldet[0]->getAttributeLabel('tipo'),
                                     'type'  => 'dropDownList',
                                     'items' => [
                                         'P'=>'PC',
                                         'L'=> 'LAPTO',
                                         'I'=> 'IMPRESORA',
                                         'O'=> 'OTROS',
                                     ],
                                     'enableError' => true,
                                     'options' => [
                                         
                                         'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm'
                                         
                                     ],
                              ],
                            
                             [
                                 'name' => 'marca',
                                 'title' => $modeldet[0]->getAttributeLabel('marca'),
                                 'type' => TabularColumn::TYPE_TEXT_INPUT,
                                 'enableError' => true,
                                 'options' => [
                                     
                                     'class'=> 'input-sm' 
                                 ],
                             ],
                             [
                                 'name' => 'modelo',
                                 'title' => $modeldet[0]->getAttributeLabel('modelo'),
                                 'type' => TabularColumn::TYPE_TEXT_INPUT,
                                 'enableError' => true,
                                 'options' => [
                                     
                                    'class'=> 'input-sm'
                                     
                                     
                                 ],
                             ],
                             [
                                 'name' => 'serie',
                                 'title' => $modeldet[0]->getAttributeLabel('serie'),
                                 'type' => TabularColumn::TYPE_TEXT_INPUT,
                                 'enableError' => true,
                                 'options' => [
                                     
                                     'class'=> 'input-sm'
                                 ],
                             ],
                                 
                         ]
                     ])?>
                  </div>
              </div>
         </div>
     
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
 </div>
</div>
