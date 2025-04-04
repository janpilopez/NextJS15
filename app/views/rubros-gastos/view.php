<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhRubrosGastos */

$this->title = 'Rubros y Gastos';
$this->params['breadcrumbs'][] = ['label' => 'Rubros Gastos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-rubros-gastos-view">

    <h1><?= Html::encode($this->title) ?></h1>

  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
      <div class= 'col-md-6'>
        <?= $form->field($model, 'rubro')->textInput(['maxlength' => true, 'placeholder'=> 'Rubro', 'readonly'=> TRUE]) ?>
        <?= $form->field($model, 'max_gasto')->textInput(['maxlength' => true, 'placeholder'=> '0.00', 'readonly'=> TRUE]) ?>
      </div>
      <div class ='col-md-6'>
         <?= $form->field($model, 'detalle')->textarea(['maxlength' => true,'rows'=> '5', 'readonly'=> TRUE]) ?>
      </div>
    </div>
  
    <?php ActiveForm::end(); ?>
    
   </div>
 </div>

</div>
