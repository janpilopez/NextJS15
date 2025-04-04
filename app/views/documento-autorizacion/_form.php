<?php

use app\models\SysAdmAreas;
use app\models\SysDocumento;
use app\models\SysEmpresa;
use app\models\User;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\SysGrupoAutorizacion;

/* @var $this yii\web\View */
/* @var $model app\models\SysDocumentoAutorizacion */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="sys-rrhh-asistencia-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'id_sys_documento')->dropDownList(ArrayHelper::map(SysDocumento::find()->orderBy('documento')->all(), 'id', 'documento'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'documento']  ) ?> 
    
        <?= $form->field($model, 'id_grupo_autorizacion')->dropDownList(ArrayHelper::map(SysGrupoAutorizacion::find()->addOrderBy('nombre')->all(), 'id', 'nombre'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'grupo']  ) ?> 
    
       <?= $form->field($model, 'id_sys_area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'Todos..' ,'class'=> 'form-control input-sm', 'id'=>'id_sys_area']  ) ?> 
       
       <?= $form->field($model, 'id_sys_departamento')->widget(DepDrop::classname(), [
                         'data'=> [$model->id_sys_departamento => 'area'],
                         'options'=>['id'=>'id_sys_departamento', 'class'=> 'form-control input-sm'],
                         'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions'=>[
                                'depends'=>['id_sys_area'],
                                 'initialize' => true,
                                  'initDepends' => ['id_sys_area'], 
                                'placeholder'=>'Todos',
                                'url'=>Url::to(['/consultas/listadepartamento']),
                               
                            ]])?>      
    
        <?= $form->field($model, 'estado')->dropDownList([1 => 'Activo',0 =>'Inactivo']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
   </div>
  </div>
</div>