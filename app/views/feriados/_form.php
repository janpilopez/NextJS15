<?php

use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysProvincias;
use app\models\SysCantones;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFeriados */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="sys-rrhh-feriados-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class = 'col-md-3'>
  
         <?= $form->field($model, 'fecha')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                               
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                    //'startDate' => date('Y-m-d'),
                                                ],
                				                'options' => ['placeholder' => 'Fecha de Inicio']
                                            ]);?>
       </div>
        <div class= 'col-md-6'>
             <?= $form->field($model, 'feriado')->textInput(['maxlength' => true]) ?>
        </div>
        <div class= 'col-md-2'>
              <?= $form->field($model, 'nacional')->dropDownList(['S'=> 'Si','N'=> 'No']) ?>
        </div>
    </div>
   <div class= 'row'>
      <div class= 'col-md-3'>
       <?= $form->field($model, 'id_sys_provincia')->dropDownList(ArrayHelper::map(SysProvincias::find()->all(), 'id_sys_provincia', 'provincia'), ['prompt'=> 'seleccione..']) ?>
      </div>
      <div class= 'col-md-3'>
       <?= $form->field($model, 'id_sys_canton')->dropDownList(ArrayHelper::map(SysCantones::find()->all(), 'id_sys_canton', 'canton'), ['prompt'=> 'seleccione..']) ?>
      </div>
   </div>

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
   </div>
  </div>
</div>
