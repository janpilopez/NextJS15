<?php

use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\models\SysRrhhBarcos;
use kartik\number\NumberControl;
use app\assets\MareasAsset;
use app\models\SysAdmDepartamentos;
MareasAsset::register($this);

$url = Yii::$app->urlManager->createUrl(['mareas']);
$inlineScript = "var url = '{$url}', update = '{$update}';";
$this->registerJs($inlineScript, View::POS_HEAD);


/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhMareasCab */
/* @var $form yii\widgets\ActiveForm */
?>
<?= Html::hiddenInput('update', $update, ['id'=> 'update'])?>
<div class="sys-rrhh-mareas-cab-form">
<div class = 'panel panel-default'>
    <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class = "row">
       <div class = "col-md-6">
          <?= $form->field($model, 'id_sys_rrhh_barcos')->dropDownList(ArrayHelper::map(SysAdmDepartamentos::find()->select("id_sys_adm_departamento, departamento")->all(), 'id_sys_adm_departamento', 'departamento'), ['prompt'=> 'seleccione..'])?>
       </div>
       <div class=  "col-md-3">
              <?=  $form->field($model, 'fecha_inicio')->widget(DatePicker::classname(), [
                                                            'dateFormat' => 'yyyy-MM-dd',
                                                             'clientOptions' => [
                                                              'yearRange' => '-115:+0',
                                                              'changeYear' => true],
                              
                            				                'options' => ['placeholder' => 'Fecha', 'class'=> 'form-control input-sm', 'type'=> 'date']
                                                        ]);
              ?>
       </div>    
       <div class = "col-md-3">
           <?=  $form->field($model, 'fecha_fin')->widget(DatePicker::classname(), [
                                                            'dateFormat' => 'yyyy-MM-dd',
                                                             'clientOptions' => [
                                                              'yearRange' => '-115:+0',
                                                              'changeYear' => true],
                              
                            				                'options' => ['placeholder' => 'Fecha', 'class'=> 'form-control input-sm', 'type'=> 'date']
                                                        ]);
              ?>
       </div>
    </div>
    <div class = "row">
        <div class = "col-md-4"> 
              <?=  $form->field($model, 'tonelada')->widget(NumberControl::classname(), [
                 
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 3,
                       'rightAlign' => false
                   ]
               ]);?>
           
        </div>
        <div class = "col-md-4">
            <?=  $form->field($model, 'valor_tonelada')->widget(NumberControl::classname(), [
                 
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 3,
                       'rightAlign' => false
                   ]
               ]);?>
        </div>
        <div class = "col-md-4">
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Abierta', 'C'=> 'Cerrada']) ?>
        </div>
    </div>
    
    <div class ="row">
       <div class = "col-md-12">
             <button class ="btn btn-primary pull-right" id ="addtripulante">Agregar Tripulantes</button>
             <p><b>Listado Tripulantes</b></p>
       </div>
    </div>
    <div class = "row">
       <div class = "col-md-12">
         <?php 
         echo  TabularInput::widget([
             
             'models' => $modeldet,
             'id'=> 'mareas',
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
                 'class' => 'btn btn-xs btn-info hidden',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                 'id'    => 'addtripulante'
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger'.$model->estado = 'A' ? '': 'hidden',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                 'id'=> 'deltripulante'
             ],
             
             'columns'=> [
                 
                 [
                     'name' => 'id_sys_rrhh_marea_det',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                 [
                     'name' => 'id_sys_rrhh_cedula',
                     'title' => 'Identificación',
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'options' => [
                         'class'=>'cedula', 'readonly' => true//,'style'=>'display:none'
                                                                 
                     ],
                     
                 ],
                 [
                     'name' => 'id_sys_rrhh_marea_cab',
                     'title' => 'nombres',
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'options' => ['readonly'=> true]
                 ],
                 
               
      
             ]
         ])?>
         
         
      
       </div>
    </div>
    <br>
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    </div>
  </div>
</div>
 <?php 
    //modal empleados 
    Modal::begin([
        'id' => 'modal',
        'header' => '<h4 class="modal-title">Listado de Empleado</h4>',
        'headerOptions'=>['style'=>"background-color:#EEE"],
        'size'=>'modal-md',
    ]);
    ?>
    <?php Modal::end(); ?>
