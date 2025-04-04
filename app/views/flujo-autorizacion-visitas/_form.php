<?php

use app\models\SysAccesoTipoVisitas;
use app\models\SysEmpresa;
use app\models\SysGrupoAutorizacion;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysUserGrupoAutorizacion */
/* @var $form yii\widgets\ActiveForm */
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

if(!$updated):
    $datos = User::find()->where(['empresa'=> $empresa->id_sys_empresa])->andwhere(['status'=> 1])->addOrderBy('username')->all();
else:
    $datos = User::find()->where(['empresa'=> $empresa->id_sys_empresa])->addOrderBy('username')->all();
endif;

?>
<div class="sys-rrhh-asistencia-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
        <div class="sys-user-grupo-autorizacion-form">
        
            <?php $form = ActiveForm::begin(); ?>
        
            <?= $form->field($model, 'id_usuario')->dropDownList(ArrayHelper::map($datos, 'id', 'username'), ['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'user', 'disabled'=> $updated]  ) ?> 
        
            <?= $form->field($model, 'id_sys_grupo_autorizacion')->dropDownList(ArrayHelper::map(SysGrupoAutorizacion::find()->addOrderBy('nombre')->all(), 'id', 'nombre'), ['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'grupo']  ) ?>
        
            <?= $form->field($model, 'nivel_autorizacion')->dropDownList([1 => 'ALTA',2 => 'MEDIA',3 => 'BAJA'],['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm']) ?>
        
            <?= $form->field($model, 'tipo_visita')->dropDownList(ArrayHelper::map(SysAccesoTipoVisitas::find()->all(), 'id_tipo_visita', 'tipo_visita'), ['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'visita']  ) ?>

            <?= $form->field($model, 'activo')->dropDownList([1 => 'ACTIVO',0 =>'INACTIVO'],['prompt'=> 'Seleccione..' ,'class'=> 'form-control input-sm']) ?>
          
            <div class="form-group">
                <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
            </div>
        
            <?php ActiveForm::end(); ?>
        
        </div>
    </div>
  </div>
</div>
