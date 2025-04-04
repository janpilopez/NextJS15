<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhUtilidadesCab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-utilidades-cab-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class = "row">
     	<div class = "col-md-6">
     
     	     <?=  $form->field($model, 'anio')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Año Utilidades',
                       'disabled'=> $model->estado == 'P' ? true: false,
                       'class'=> 'form-control input-sm'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 0,
                       'rightAlign' => false
                   ]
               ]);?>
     	    
     	    
     	</div>
        <div class = "col-md-6">
     	</div>
    </div>
    <div class = "row">
     	<div class = "col-md-6">
     	    <?=  $form->field($model, 'valor_uti')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Valor Utilidades',
                       'disabled'=> $model->estado == 'P' ? true: false,
                       'class'=> 'form-control input-sm'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 2,
                       'rightAlign' => false
                   ]
               ]);?>
      
     	</div>
        <div class = "col-md-6">
     	</div>
    </div>
    <div class = "row">
     	<div class = "col-md-6">
     	    <?=  $form->field($model, 'valor_uti_empleado')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Porcentaje Empleados',
                       'disabled'=> $model->estado == 'P' ? true: false,
                       'class'=> 'form-control input-sm'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 2,
                       'min' => 1,
                       'max' => 100,
                       'rightAlign' => false
                   ]
               ]);?>
     	    
     	</div>
        <div class = "col-md-6">
     	</div>
    </div>
    <div class = "row">
     	<div class = "col-md-6">
     	      <?=  $form->field($model, 'valor_uti_carga')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Porcentaje Carga',
                       'class'=> 'form-control input-sm'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 2,
                       'min' => 1,
                       'max' => 100,
                       'rightAlign' => false
                   ]
               ]);?>
     	</div>
        <div class = "col-md-6">
     	</div>
    </div>

  
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
