<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysAdmAreas;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmDepartamentos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-departamentos-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <div class= 'row'>
        <div class= 'col-md-6'>
         <?= $form->field($model, 'departamento')->textInput(['maxlength' => true]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'siglas')->textInput(['maxlength' => true]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'id_sys_adm_area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'seleccione..']) ?>
        </div>
     </div>
     <div class= 'row'>
        <div class='col-md-3'>
           <?= $form->field($model, 'rango_ip_inicio')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-md-3'>
           <?= $form->field($model, 'rango_ip_fin')->textInput(['maxlength' => true]) ?>
        </div>
        <div class= 'col-md-2'>
          <?php // $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
          
          <?php echo $form->field($model, 'color')->widget(ColorInput::classname(), [
              'options' => ['placeholder' => 'Seleccione', 'class'=> 'form-control input-sm'],
            ]);?>
        </div>
        <div class= 'col-md-2'>
         <?php //$form->field($model, 'color_fuente')->textInput(['maxlength' => true]) ?>
          <?php echo $form->field($model, 'color_fuente')->widget(ColorInput::classname(), [
              'options' => ['placeholder' => 'Seleccione', 'class'=> 'form-control input-sm'],
            ]);?>
        </div>
        <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
        </div>
     </div>
   
   
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    </div>
  </div>
</div>
