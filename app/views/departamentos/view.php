<?php

use app\models\SysAdmAreas;
use kartik\color\ColorInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmDepartamentos */

$this->title = 'Departamentos';
$this->params['breadcrumbs'][] = ['label' => 'Departamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-departamentos-view">

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <div class= 'row'>
        <div class= 'col-md-6'>
         <?= $form->field($model, 'departamento')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'siglas')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'id_sys_adm_area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'seleccione..', 'disabled' => true]) ?>
        </div>
     </div>
     <div class= 'row'>
        <div class='col-md-3'>
           <?= $form->field($model, 'rango_ip_inicio')->textInput(['maxlength' => true,  'readonly'=> TRUE]) ?>
        </div>
        <div class='col-md-3'>
           <?= $form->field($model, 'rango_ip_fin')->textInput(['maxlength' => true,  'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
          <?php // $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
          
          <?php echo $form->field($model, 'color')->widget(ColorInput::classname(), [
              'options' => ['placeholder' => 'Seleccione', 'class'=> 'form-control input-sm',  'disabled' => true],
            ]);?>
        </div>
        <div class= 'col-md-2'>
         <?php //$form->field($model, 'color_fuente')->textInput(['maxlength' => true]) ?>
          <?php echo $form->field($model, 'color_fuente')->widget(ColorInput::classname(), [
              'options' => ['placeholder' => 'Seleccione', 'class'=> 'form-control input-sm', 'disabled' => true],
            ]);?>
        </div>
        <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
        </div>
     </div>
   
    <?php ActiveForm::end(); ?>
    
    </div>
  </div>

</div>
