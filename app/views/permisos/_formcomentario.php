<?php

use Mpdf\Tag\Time;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
use kartik\typeahead\Typeahead;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhpermisosEmpleados;
use yii\web\View;

use yii\web\JsExpression;
$url = Yii::$app->urlManager->createUrl(['permisos']);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhpermisos */
/* @var $form yii\widgets\ActiveForm */



?>
<div class="sys-rrhh-permisos-form">
 <?php $form = ActiveForm::begin(['id'=>'permisosemp']); ?>
  <div class = 'row'>
     <div class = 'col-md-12'>
        <?= $form->field($model, 'comentario')->textarea(['maxlength' => true, 'row'=> 2]) ?>
     </div>
  </div>
  <div class = 'row'>
     <div class = 'col-md-12'>
        <div class="form-group text-center">
            <?= Html::submitButton('Anular', ['class' => 'btn btn-danger', 'id'=> 'btn-guardar']) ?>
        </div>
     </div>
  </div>
   <?php ActiveForm::end(); ?>
</div>
