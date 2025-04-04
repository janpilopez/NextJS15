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
use app\models\SysRrhhSoextrasEmpleados;
use app\assets\SoextrasAsset;
use yii\web\View;

SoextrasAsset::register($this);
use yii\web\JsExpression;
$url = Yii::$app->urlManager->createUrl(['soextras']);
$inlineScript = "var update = {$update},esupdate = {$esupdate}, url = '{$url}';";
$this->registerJs($inlineScript, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSoextras */
/* @var $form yii\widgets\ActiveForm */
$cont = 0;

if($update != 0):

$cont =  SysRrhhSoextrasEmpleados::find()->where(['id_sys_rrhh_soextras'=> $model->id_sys_rrhh_soextras, 'id_sys_empresa'=> $model->id_sys_empresa])->count();

$iddetalle =
[
    'name' => 'id_sys_rrhh_soextras_empleados',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
else:

$iddetalle = [
    'name' => 'nombres',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
endif;




//grupos 

$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];

if(trim($userdeparta->area) != ''):

    $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();

endif;


?>
<div class="sys-rrhh-soextras-form">
 <?php $form = ActiveForm::begin(['id'=>'soextrasemp']); ?>
  <div class = 'row'>
     <div class = 'col-md-12'>
        <?= $form->field($model, 'comentario_anulacion')->textarea(['maxlength' => true, 'row'=> 2]) ?>
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
