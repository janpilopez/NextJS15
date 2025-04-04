<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCausaSalida */

$this->title = 'Causa Salidas';
$this->params['breadcrumbs'][] = ['label' => 'Causa Salidas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-causa-salida-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class = 'panel panel-default'>
    <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
    
        <div class= 'row'>
          <div class= 'col-md-1'>
             <?= $form->field($model, 'id_sys_rrhh_causa_salida')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
          </div>
          <div class= 'col-md-6'>
           <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
          </div>
          <div class= 'col-md-2'>
           <?= $form->field($model, 'indemnizacion')->textInput(['readonly'=> TRUE]) ?>
          </div>
            <div class= 'col-md-2'>
           <?= $form->field($model, 'bonificacion')->textInput(['readonly'=> TRUE]) ?>
          </div>
         </div>
     
        <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
