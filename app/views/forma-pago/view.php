<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFormaPago */

$this->title = 'Forma de pagos';
$this->params['breadcrumbs'][] = ['label' => 'Forma de Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-forma-pago-view">

  <h1><?= Html::encode($this->title) ?></h1>

  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class= 'col-md-2'>
       <?= $form->field($model, 'id_sys_rrhh_forma_pago')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-8'>
        <?= $form->field($model, 'forma_pago')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
       </div>
    </div>
     
    <?php ActiveForm::end(); ?>
    
    
   </div>
 </div>

</div>
