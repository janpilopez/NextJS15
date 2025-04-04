<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhImpuestoRenta */

$this->title = 'Impuesto Renta';
$this->params['breadcrumbs'][] = ['label' => 'Impuesto Renta', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-impuesto-renta-view">

       <h1><?= Html::encode($this->title) ?></h1>

       <div class = 'panel panel-default'>
       <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
        
        <div class= 'row'>
          <div class= 'col-md-3'>
           <?= $form->field($model, 'fraccion_basica')->textInput(['maxlength' => true, 'placeholder'=> '0.00', 'readonly'=> TRUE]) ?>
          </div>
          <div class= 'col-md-3'>
            <?= $form->field($model, 'fraccion_excedente')->textInput(['maxlength' => true, 'placeholder'=> '0.00', 'readonly'=> TRUE]) ?>
          </div>
          <div class= 'col-md-3'>
            <?= $form->field($model, 'impuesto_fraccion_basica')->textInput(['maxlength' => true, 'placeholder'=> '0.00', 'readonly'=> TRUE]) ?>
          </div>
          <div class= 'col-md-3'>
            <?= $form->field($model, 'impuesto_fraccion_excedente')->textInput(['maxlength' => true, 'placeholder'=> '0.00', 'readonly'=> TRUE]) ?>
          </div>
        </div>  
   
        <?php ActiveForm::end(); ?>
       </div>
     </div>

</div>
