<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use kartik\number\NumberControl;
use app\models\SysRrhhEmpresaServicios;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpresaServiciosEmpleados*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empresa-servicios-empleados-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>

        <?php $form = ActiveForm::begin(); ?>
         
        <div class = "row">
           <div class = "col-md-2">
              <?= $form->field($model, 'id_sys_rrhh_cedula')->textInput(['maxlength' => true, 'placeholder'=> '9999999999', 'class'=> 'form-control']) ?>
           </div>
           <div class= "col-md-6">
             <?= $form->field($model, 'nombres')->textInput(['maxlength' => true, 'placeholder'=> 'Ingrese Nombres', 'class'=> 'form-control']) ?>
           </div>
           <div class = "col-md-2">
             <?= $form->field($model, 'genero')->dropDownList(['M'=>'Masculino', 'F'=> 'Femenino'], ['class'=> 'form-control']) ?>
           </div>
           <div class=  "col-md-2">
            <?= $form->field($model, 'estado')->dropDownList(['A'=>'Activo', 'I'=> 'Inactivo'], ['class'=> 'form-control']) ?>
           </div>
        </div>
        <div class ="row">
            <div class ="col-md-3">
              <?= $form->field($model, 'id_sys_rrhh_empresa_servicios')->dropDownList(ArrayHelper::map(SysRrhhEmpresaServicios::find()->all(), 'id_sys_rrhh_empresa_servicio', 'nombre'),['class'=> 'form-control', 'prompt'=> 'seleccione..']) ?>
            </div>
            <div class= "col-md-9">
             <?= $form->field($model, 'ocupacion')->textInput(['maxlength' => true, 'class'=> 'form-control']) ?>
            </div>
        </div>
        <div class="row">
           <div class= "col-md-6">
               <?=  $form->field($model, 'cargas_familiares')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Número de cargas',
                       'class'=> 'form-control'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 0,
                       'rightAlign' => false
                   ]
               ]);
               
               ?> 
           </div>
			<div class= "col-md-6">
			
                <?=  $form->field($model, 'dias_laborados')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => 'Número de faltas',
                       'class'=> 'form-control'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 0,
                       'rightAlign' => false
                   ]
               ]);
               
               ?> 
            
            
            </div>        
        </div>
        
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        
    </div>
  </div>
</div>
