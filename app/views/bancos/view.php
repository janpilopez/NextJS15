<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhBancos */

$this->title = 'Ver';
$this->params['breadcrumbs'][] = ['label' => 'Bancos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-bancos-view">

<h1><?= Html::encode($this->title) ?></h1>
<div class = 'panel panel-default'>
    <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <?= $form->field($model, 'banco')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
     
     <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true])?>

    <?php ActiveForm::end(); ?>
    
   </div>
  </div>
   

</div>
