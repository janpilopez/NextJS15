<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisos */

$this->title = 'Tipos de Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Tipos de permisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-permisos-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
       <div class= 'col-md-1'>
         <?= $form->field($model, 'id_sys_rrhh_permiso')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-6'>
         <?= $form->field($model, 'permiso')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo',  'I'=> 'Inactivo'], ['disabled' => true]) ?>
       </div>
        <div class= 'col-md-2'>
        <?= $form->field($model, 'descuento')->dropDownList(['S'=> 'Si', 'N'=> 'No'], ['disabled' => true]) ?>
       </div>
    </div>   
    <?php ActiveForm::end(); ?>
   </div>
 </div>

</div>
