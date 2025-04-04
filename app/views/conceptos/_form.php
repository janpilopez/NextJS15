<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$inputDisable = false;

if($bloqueado != 0):

  $inputDisable = true;

endif;
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhConceptos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-conceptos-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>

    <div class= 'row'>
       <div class ='col-md-2'>
         <?= $form->field($model, 'id_sys_rrhh_concepto')->textInput(['maxlength' => true,'disabled' => $inputDisable]) ?>
       </div>
       <div class= 'col-md-8'>
         <?= $form->field($model, 'concepto')->textInput(['maxlength' => true,'disabled' => $inputDisable]) ?>
       </div>
        <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'],['disabled' => $inputDisable]) ?>
       </div>
    </div>
    <div class= 'row'>
      <div class= 'col-md-2'>
          <?= $form->field($model, 'tipo')->dropDownList(['I'=> 'Ingreso', 'E'=> 'Egreso'],['disabled' => $inputDisable]) ?>
      </div>
      <div class = 'col-md-2'>
          <?= $form->field($model, 'pago')->dropDownList(Yii::$app->params['periodos'],['disabled' => $inputDisable]) ?> 
      </div>
      <div class= 'col-md-2'>
              <?=  $form->field($model, 'valor')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => '0.00',
                       'class'=> 'form-control input-sm',
                       'disabled' => $inputDisable
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 2,
                       'rightAlign' => false
                   ]
               ]);
               
               ?> 
      </div>
      <div class = 'col-md-2'>
         <?= $form->field($model, 'tipo_valor')->dropDownList([ 'N'=> 'No Aplica','V'=> 'Valor', 'D'=> 'Días', 'M'=> 'Meses', 'A'=> 'Años', 'P'=> 'Porcentaje'],['disabled' => $inputDisable]) ?>   
      </div>
      <div class= 'col-md-2'>
          <?= $form->field($model, 'proceso')->dropDownList(['M'=> 'Manual', 'A'=> 'Automático', 'C'=> 'Configuración'],['disabled' => $inputDisable]) ?>   
      </div>
       <div class= 'col-md-2'>
          <?= $form->field($model, 'concepto_sueldo')->dropDownList(['NA'=> 'No aplica','SU'=> 'Sueldo Unificado', 'SE'=> 'Sueldo Empresa'],['disabled' => $inputDisable])?>   
      </div>
    </div>
    <div class= 'row'>
      <div class= 'col-md-2'>
          <?= $form->field($model, 'imprime')->dropDownList(['S'=> 'Si','N'=> 'No'],['disabled' => $inputDisable]) ?>   
      </div>
       <div class= 'col-md-1'>
          <?= $form->field($model, 'orden')->textInput(['maxlength' => true,'disabled' => $inputDisable]) ?>   
      </div>
         <div class= 'col-md-2'>
          <?= $form->field($model, 'aporta_iess')->dropDownList(['N'=> 'No', 'S'=> 'Si'],['disabled' => $inputDisable]) ?>   
      </div>
         <div class= 'col-md-2'>
         <?= $form->field($model, 'aporta_rentas')->dropDownList(['N'=> 'No', 'S'=> 'Si'],['disabled' => $inputDisable])?>   
      </div>
    </div>

    <br>
    <div class= 'row'>
       <div class= 'col-md-12'>
          <div class="form-group text-center">
            <?php if($bloqueado == 0): ?>
              <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
          </div>
       
       </div>
    </div>
 
    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
