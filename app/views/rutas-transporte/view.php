<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmRutas */

$this->title = 'Rutas de Transporte';
$this->params['breadcrumbs'][] = ['label' => 'Rutas de Transporte', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-rutas-view">

   <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>

     <div class= 'row'>
       <div class= 'col-md-5'>
           <?= $form->field($model, 'ruta')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
        </div>
     </div>
     
    <?php ActiveForm::end(); ?>
  </div>
 </div>

</div>
