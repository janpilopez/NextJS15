<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysAdmDepartamentos;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedorVisitas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-comedor-visitas-form">
  <?php $form = ActiveForm::begin(); ?>
   <div class = "row">
       <div class = "col-md-6" >
             <?=   $form->field($model, 'id_sys_adm_departamento')->widget(Select2::classname(), [
                        'data'=>  ArrayHelper::map( SysAdmDepartamentos::find()->select("id_sys_adm_departamento, departamento")->orderBy(['departamento'=>'asc'])->all(), 'id_sys_adm_departamento', 'departamento'),
                        'options'=> ['placeholder' => 'Seleccione'],
                        'class'=> 'form-control input-sm',
                        'pluginOptions'=> [
                          
                            'allowClear'=> true 
                  ]]);?>
       </div>
       <div class =  "col-md-3">
          <?= $form->field($model, 'tipo_visita')->dropDownList(['C'=> 'Cerrada', 'A'=> 'Abierta']) ?>
       </div>
       <div class =  "col-md-3">
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activa', 'I'=> 'Inactiva']) ?>
       </div>
   </div>
   <div class = "row">
       <div class ="col-md-3" >
        	<?= $form->field($model, 'desayuno')->dropDownList(['N'=> 'No', 'S'=> 'Si']); ?>
       </div>
        <div class ="col-md-3" >
       		 <?= $form->field($model, 'almuerzo')->dropDownList(['N'=> 'No', 'S'=> 'Si']); ?>
       </div>
        <div class ="col-md-3" >
              <?= $form->field($model, 'merienda')->dropDownList(['N'=> 'No', 'S'=> 'Si']);?>
       </div>
   </div>



 
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
