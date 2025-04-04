<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmCcostos */

$this->title = 'Centro de Costos';
$this->params['breadcrumbs'][] = ['label' => 'Centro de Costos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-ccostos-view">

    <h1><?= Html::encode($this->title) ?></h1>
   <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
       <div class= 'col-md-2'>
          <?= $form->field($model, 'id_sys_adm_ccosto')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>    
        <div class = 'col-md-5'>
           <?= $form->field($model, 'centro_costo')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
 </div>

</div>
