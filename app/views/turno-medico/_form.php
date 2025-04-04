<?php

use app\models\SysMedTipoMotivo;
use app\models\SysRrhhEmpleados;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar(); 
?>
<div class="sys-med-turno-medico-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class = 'panel panel-default'>  
     	<div class = 'panel-body'>  	
                <div class="row">
                   <div class="col-md-4">
                    <?= $form->field($model, 'id_sys_rrhh_cedula')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
                   </div>
                   <div class="col-md-8">
                    <?= html::label('Nombres')?>
                    <?= html::textInput('nombres', $nombres, ['class'=> 'form-control', 'id'=> 'nombres', 'disabled'=> true])?>
                   </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                   	  <?= $form->field($model, 'id_sys_med_tipo_motivo')->dropDownList(ArrayHelper::map(SysMedTipoMotivo::find()->where(['activo'=>'1'])->all(), 'id', 'tipo'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control', 'disabled'=> true]) ?>
                    </div>
                </div>
                <div class= "row">
                  <div class= "col-md-12">
                   <?= $form->field($model, 'comentario')->textarea(['maxlength' => true]) ?>
                  </div>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
