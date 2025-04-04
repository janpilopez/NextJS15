<?php

use app\models\SysAdmAreas;
use app\models\SysRrhhPermisos;
use app\models\User;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\SysEmpresa;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */
/* @var $form yii\widgets\ActiveForm */

 $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
 


?>
<div class="sys-adm-usuarios-dep-form">
    <?php $form = ActiveForm::begin(); ?>
     <div class = 'row'>
      <div class = 'col-md-2'>
         <?= $form->field($model, 'usuario_tipo')->dropDownList(Yii::$app->params['tipo_usuarios'], ['class'=>'form-control input-sm'])?>
      </div>
      <div class = 'col-md-2'>
         <?= $form->field($model, 'permiso')->dropDownList(ArrayHelper::map(SysRrhhPermisos::find()->all(), 'id_sys_rrhh_permiso', 'permiso'), ['prompt'=> 'Todos..' ,'class'=> 'form-control input-sm', 'id'=>'permiso']  ) ?> 
     </div>
      <div class = 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=>'Activo', 'I'=> 'Inactivo'], ['class'=>'form-control input-sm'])?>
      </div>
     </div>
     <div class="form-group text-left">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
