<?php

use app\models\SysCantones;
use app\models\SysProvincias;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFeriados */

$this->title = 'Feriados';
$this->params['breadcrumbs'][] = ['label' => 'Feriados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-feriados-view">

    <h1><?= Html::encode($this->title) ?></h1>

 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class = 'col-md-3'>
  
         <?= $form->field($model, 'fecha')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                               
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd',
                                                    //'startDate' => date('Y-m-d'),
                                                    
                                                ],
                                              'options' => ['placeholder' => 'Fecha de Inicio', 'disabled' => true]
                                            ]);?>
       </div>
        <div class= 'col-md-6'>
             <?= $form->field($model, 'feriado')->textInput(['maxlength' => true,  'readonly'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
              <?= $form->field($model, 'nacional')->dropDownList(['S'=> 'Si','N'=> 'No'], ['disabled' => true]) ?>
        </div>
    </div>
   <div class= 'row'>
      <div class= 'col-md-3'>
       <?= $form->field($model, 'id_sys_provincia')->dropDownList(ArrayHelper::map(SysProvincias::find()->all(), 'id_sys_provincia', 'provincia'), ['prompt'=> 'seleccione..', 'disabled' => true]) ?>
      </div>
      <div class= 'col-md-3'>
       <?= $form->field($model, 'id_sys_canton')->dropDownList(ArrayHelper::map(SysCantones::find()->all(), 'id_sys_canton', 'canton'), ['prompt'=> 'seleccione..', 'disabled' => true]) ?>
      </div>
   </div>
    <?php ActiveForm::end(); ?>
   </div>
  </div>

</div>
