<?php

use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use kartik\number\NumberControl;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInputColumn;
use app\models\SysRrhhRubrosGastos;
use kartik\file\FileInput;
use app\assets\GastosProyectadosAsset;
GastosProyectadosAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmActividades */
/* @var $form yii\widgets\ActiveForm */

$url = Yii::$app->urlManager->createUrl(['gastos-proyectados']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

$inputDisable = false;

$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;

if($update != 0):

  $inputDisable = true;

endif;

$anio = date('Y');
/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-gastos-proyectados-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class ="row">
            <div class="col-md-3">
                 <?php  $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                               '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                           
                           echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                               'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm','id'=>'cedula','disabled' => $inputDisable],
                               'pluginOptions' => ['highlight'=>true],
                               'scrollable'=>true,
                               'dataset' => [
                                   [
                                       
                                       'remote' => [
                                           'url' =>    Url::to(['consultas/listempleadossueldo']) . '?q=%QUERY',
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
                                         ObtenerUltimoLlamadoAtencion(suggestion.value);
                                          }',
                                   ]
                                   
                                
                           ])->label('Identificación');
                                   ?>
            </div>
            <div class="col-md-4">
                 <?php echo html::label('Nombres')?>
                 <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
            
            </div>

            <div class="col-md-2"> 
              <?=  $form->field($model, 'anio')->widget(NumberControl::classname(), [
                          'displayOptions' =>  [
                              'placeholder' => '',
                              'class'=> 'form-control input-sm',
                              'disabled' => $inputDisable,
                          ],
                          'maskedInputOptions' => [
                              'groupSeparator' => '',
                              'digits' => 0,
                              'min' => 2000,
                              'max' => 2060,
                              'rightAlign' => false
                          ],
                          'options' => ['value' => $anio],
            ]);?>
            </div>

            <div class = 'col-md-2'>
              <?= $form->field($model, 'cargas')->widget(NumberControl::classname(), [
                          'displayOptions' =>  [
                              'class'=> 'form-control input-sm',
                              'disabled' => $inputDisable,
                          ],
                          'maskedInputOptions' => [
                              'groupSeparator' => '',
                              'digits' => 0,
                              'min' => 0,
                              'max' => 20,
                              'rightAlign' => false
                          ], 
                        ]);?>
            </div>
          
    </div>
    <div class= 'row'>
      <div class= 'col-md-12'>
          <?= Html::hiddenInput('ids', '0',['id' => 'ids'])?>
      </div>
    </div>

    <div class = 'row'>
        <div class = 'col-md-12'>
              <?=
                 $form->field($model, 'file')->widget(FileInput::classname(), [
                     'options' => ['accept' => 'pdf/*'],
                     'pluginOptions' => [
                        
                     ]
                 ])->label(false);
                 ?>
        </div> 
    </div>

    <div class='row'> 
      <div class= 'col-md-4'>
        <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
          
             'models' => $modeldet,
             'id'=> 'modeldet',
            /* 'attributeOptions' => [
                 'enableAjaxValidation'      => true,
                 'enableClientValidation'    => false,
                 'validateOnChange'          => false,
                 'validateOnSubmit'          => true,
                 'validateOnBlur'            => false,
             ],
             */
                
             'allowEmptyList' => true,
             'addButtonPosition' => MultipleInput::POS_HEADER,
             'addButtonOptions' => [
                 'class' => 'btn btn-xs btn-info',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
    
             ],
                
             'columns'=> [
                 
                 [
                     'name' => 'id_gasto_proyectado_det',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                       
                 [
                     'name' => 'id_sys_rrhh_rubros_gastos',
                     'title' => 'Rubro',
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysRrhhRubrosGastos::find()->where(['id_sys_empresa'=> '001'])->all(), 'id_sys_rrhh_rubros_gastos', 'rubro'),
                     'options'=>['required'=> true, 'class'=> 'form-control input-sm'],
                  
                 ],
                 [
                     'name' => 'cantidad',
                     'title' => 'Cantidad',
                     'type' =>  kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         
                         'displayOptions' =>  ['class'=> 'form-control input-sm'],
                         'maskedInputOptions' => [
                             
                             'digits' => 2,
                             
                         ],
                         
                     ],
                  
                 ],
        
             ]
         ])?>
        </div>
      </div>
    </div>

     
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success','id'=> 'btn-guardar']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
<?php 
    //modal empleados 
    Modal::begin([
        'id' => 'modalempleadoscargas',
        'header' => '<h4 class="modal-title">Listado de Cargas Empleado</h4>',
        'headerOptions'=>['style'=>"background-color:#EEE"],
        'size'=>'modal-md',
    ]);
    ?>
    <?php Modal::end(); ?>