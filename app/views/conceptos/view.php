<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhConceptos */

$this->title = 'Conceptos de Nómina';
$this->params['breadcrumbs'][] = ['label' => 'Conceptos de Nómina', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-conceptos-view">

    <h1><?= Html::encode($this->title) ?></h1>

 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>

    <div class= 'row'>
       <div class ='col-md-2'>
         <?= $form->field($model, 'id_sys_rrhh_concepto')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-8'>
         <?= $form->field($model, 'concepto')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
        <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'],['disabled' => true]) ?>
       </div>
    </div>
    <div class= 'row'>
      <div class= 'col-md-2'>
          <?=  $form->field($model, 'tipo')->dropDownList(['I'=> 'Ingreso', 'E'=> 'Egreso'], ['disabled' => true]) ?>
      </div>
      <div class = 'col-md-2'>
          <?= $form->field($model, 'pago')->dropDownList(['1'=> 'Quincena', '2'=> 'Mensual', '90'=> 'Proviciones', '70'=> 'Décimo Tercero', '71'=> 'Décimo Cuarto'], ['disabled' => true]) ?> 
      </div>
      <div class= 'col-md-2'>
          <?= $form->field($model, 'imprime')->dropDownList(['S'=> 'Si','N'=> 'No'], ['disabled' => true]) ?>   
      </div>
       <div class= 'col-md-1'>
          <?= $form->field($model, 'orden')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>   
      </div>
         <div class= 'col-md-2'>
          <?= $form->field($model, 'aporta_iess')->dropDownList(['N'=> 'No', 'S'=> 'Si'], ['disabled' => true]) ?>   
      </div>
         <div class= 'col-md-2'>
          <?= $form->field($model, 'aporta_rentas')->dropDownList(['N'=> 'No', 'S'=> 'Si'], ['disabled' => true])?>   
      </div>
    </div>
    <br>
    <?php ActiveForm::end(); ?>
   </div>
 </div>

</div>
