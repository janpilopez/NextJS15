<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Áreas';
$this->params['breadcrumbs'][] = ['label' => 'Áreas', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-areas-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <div class= 'row'>
       <div class= 'col-md-6'>
          <?= $form->field($model, 'area')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-3'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
       </div>
     </div>


    <?php ActiveForm::end(); ?>
    </div>
  </div>

</div>
