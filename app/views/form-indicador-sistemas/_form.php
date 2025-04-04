<?php

use app\models\SysAdmDepartamentos;
use app\models\SysIndicadores;
use kartik\date\DatePicker;
use kartik\number\NumberControl;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\FormIndicadorSistemasAsset;
FormIndicadorSistemasAsset::register($this);
$urlconsultas = Yii::$app->urlManager->createUrl(['form-indicador-sistemas']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "urlconsultas = '$urlconsultas', consultas = '$consultas';";
$this->registerJs($inlineScript, View::POS_HEAD);
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */
/* @var $form yii\widgets\ActiveForm */
$indicadores = [1=> '1%', 2=> '2%', 3=> '3%', 4=> '4%', 5=> '5%', 6=> '6%', 7=> '7%', 8=> '8%', 9=> '9%', 10=>'10%'];
?>
<div class="sys-rrhh-empleados-permisos-equipos-form">
 <div class = 'panel panel-default'>
     <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class= 'col-md-4'>
                <?= $form->field($model, 'id_sys_adm_departamento')->dropDownList(ArrayHelper::map(SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=>6])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'disabled'=> $update != 1  ?false: true,'id'=>'departamento', 'prompt' => 'Seleccione...'])
              ?>
           </div> 
           <div class = 'col-md-4'>
              <?= $form->field($model, 'tipo_indicador')->dropDownList(ArrayHelper::map(SysIndicadores::find()->all(), 'id_indicador', 'nombre_indicador') , ['class'=>'form-control input-sm', 'disabled'=> $update != 1  ?false: true,'id'=>'nombre_indicador', 'prompt' => 'Seleccione...'])
               ?>
           </div>
           <div class="col-md-2">
                 <?= $form->field($model, 'meta')->textInput(['class'=> 'form-control input-sm', 'id'=> 'meta','disabled'=> true]) ?>
            </div>
            <div class = 'col-md-2'>
                <?= $form->field($model, 'frecuencia')->dropDownList(['M'=>'Mensual', 'T'=> 'Trimestral', 'S' => 'Semestral', 'A' =>'Anual'], ['class'=> 'form-control input-sm', 'disabled'=> $update != 1  ?false: true,'id'=>'frecuencia']) ?>
            </div>
            <div class="col-md-5">
                 <?= $form->field($model, 'efecto_medir')->textInput(['class'=> 'form-control input-sm', 'disabled'=> $update != 1  ?false: true,'id'=> 'efecto_medir']) ?>
            </div>
            <div class="col-md-2">
                <?=  $form->field($model, 'anio')->widget(NumberControl::classname(), [
                        'displayOptions' =>  [
                            'placeholder' => '',
                            'class'=> 'form-control input-sm',
                            'disabled'=> $update != 1  ?false: true
                        ],
                        'maskedInputOptions' => [
                            'groupSeparator' => '',
                            'digits' => 0,
                            'min' => 2000,
                            'max' => 2060,
                            'rightAlign' => false
                        ]
                ]);?>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
 </div>
</div>
