<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhContratos */

$this->title =  'Contratos';
$this->params['breadcrumbs'][] = ['label' => 'Contratos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-contratos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class= 'col-md-8'>
        <?= $form->field($model, 'contrato')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'plazo')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
       </div>
    </div>
    <div class= 'row'>
       <div class= 'col-md-12'>
       <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       
       </div>
    </div>  
    <?php ActiveForm::end(); ?>
   </div>
 </div>

</div>
