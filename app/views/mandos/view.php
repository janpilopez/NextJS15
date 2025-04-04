<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmMandos */

$this->title = 'Niveles Organizacionales';
$this->params['breadcrumbs'][] = ['label' => 'Niveles Organizacionales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-mandos-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
      <div class= 'col-md-6'>
       <?= $form->field($model, 'mando')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
      </div>
      <div class= 'col-md-3'>
        <?= $form->field($model, 'nivel')->dropDownList(['1'=> 'Nivel 1', '2'=> 'Nivel 2', '3'=> 'Nivel 3', '4'=> 'Nivel 4', '5'=> 'Nivel 5', '6'=> 'Nivel 6', '7'=> 'Nivel 7'], ['disabled' => true]) ?>
      </div>
     <div class= 'col-md-3'>
     <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled' => true]) ?>
     </div>
    </div>
    <div class= 'row'>
       <div class='col-md-6'>
         <?= $form->field($model, 't_cobertura')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
       </div>
       <div class='col-md-6'>
         <?= $form->field($model, 'n_entrevistas')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    
  </div>
 </div>

</div>
