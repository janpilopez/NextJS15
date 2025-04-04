<?php

use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
use kartik\typeahead\Typeahead;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhCuadrillas;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhCuadrillasEmpleados;
use app\assets\CuadrillasAsset;
use yii\web\View;
CuadrillasAsset::register($this);
use yii\web\JsExpression;
$url = Yii::$app->urlManager->createUrl(['cuadrillas']);
$inlineScript = "var update = {$update},esupdate = {$esupdate}, url = '{$url}';";
$this->registerJs($inlineScript, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillas */
/* @var $form yii\widgets\ActiveForm */
$cont = 0;

if($update != 0):

$cont =  SysRrhhCuadrillasEmpleados::find()->where(['id_sys_rrhh_cuadrilla'=> $model->id_sys_rrhh_cuadrilla, 'id_sys_empresa'=> $model->id_sys_empresa])->count();

$iddetalle =
[
    'name' => 'id_sys_rrhh_cuadrillas_empleados',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
else:

$iddetalle = [
    'name' => 'nombres',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
endif;

//grupos 


$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];

if(trim($userdeparta->area) != ''):

    $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();

endif;


?>
<div class="sys-rrhh-cuadrillas-form">
 <?php $form = ActiveForm::begin(['id'=>'cuadrillasemp']); ?>
 <div class = 'row'>
     <div class = 'col-md-4'>
         <?= $form->field($model, 'cuadrilla')->textInput(['maxlength' => true,  'class'=>'form-control input-sm']) ?>      
     </div>
     <div class = 'col-md-2'>
         <?= $form->field($model, 'id_sys_adm_area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->andFilterWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'area']  ) ?> 
     </div>
      <div class = 'col-md-3'>
         <?= $form->field($model, 'id_sys_adm_departamento')->widget(DepDrop::classname(), [
                           'data'=> [$model->id_sys_adm_departamento => 'area'],
                         'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                         'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions'=>[
                                'depends'=>['area'],
                                 'initialize' => true,
                                  'initDepends' => ['area'], 
                                'placeholder'=>'Select...',
                                'url'=>Url::to(['/consultas/listadepartamento']),
                               
                            ]])?>      
     </div>   
     <div class='col-md-3 text-right'>
            <br>
            <button id= 'abrir-modal' class= ' btn btn-primary input-sm'>
             <i class = 'glyphicon glyphicon-plus'></i> Agregar miembros
           </button>
     </div>
  </div>
  <div class= 'row'>
     <div class= 'col-md-12'>
         <?= Html::hiddenInput('cuadrillasempleados', $cont, ['id'=> 'datacuadrilla']); ?>
         <?php 
         $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
             '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
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
                 'class' => 'hidden',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                
             ],
                
             'columns'=> [
                 
                 $iddetalle,
                 
                 [
                     'name' => 'id_sys_rrhh_cedula',
                     'title' => 'Identificacion',
                     'type'  => Typeahead::className(),
                     'enableError' => true,
                     'options' => [
                         
                         'dataset' => [
                             [
                                 'remote' => [ 
                                     'url' =>    Url::to(['consultas/listempleados']) . '?q=%QUERY',
                                     'wildcard' => '%QUERY'
                                     
                                 ],
                                 'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                 'display' => "value",
                                 'templates' => [
                                     'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                     'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                     ]
                                     ]
                                     ],
                                     'pluginEvents'=>[
                                       'typeahead:select' => 'function(ev, suggestion) {
                                          var idinput = $(this).attr("id");
                                          typeahedasys(idinput, suggestion.nombres);
                                   
                                      }',                            
                                     /* "typeahead:asyncreceive" => "function(ev, query, dsName) {
                                        console.log(dsName)
                                        var idinput = $(this).attr('id');
                                       typeahedasys(idinput);

                                      }",*/
                                     ], 'options' => [
                                         'class'=>'input-sm'//,'style'=>'display:none'
                                     ]],
                                     'headerOptions'=>[
                                         'style'=>'width:20%',
                                     ],
                                     
                                     ],
                 
                 [
                     'name' => 'id_sys_empresa',
                     'title' => 'nombres',
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'enableError' => true,
                     'options' => [
                         
                         'class'=> 'input-sm',
                     ],
                 ],   
             ]
         ]) ?>
     </div>
  </div>
  <br>
 <div class = 'row'>
     <div class = 'col-md-12'>
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success', 'id'=> 'btn-guardar']) ?>
        </div>
     </div>
  </div>
  
   <?php ActiveForm::end(); ?>
</div>
  <?php 
    //modal empleados 
    Modal::begin([
        'id' => 'modalempleados',
        'header' => '<h4 class="modal-title">Listado de Empleado</h4>',
        'headerOptions'=>['style'=>"background-color:#EEE"],
        'size'=>'modal-md',
    ]);
    ?>
    <?php Modal::end(); ?>
