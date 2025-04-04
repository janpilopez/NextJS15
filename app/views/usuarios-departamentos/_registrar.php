<?php

use app\models\SysAdmAreas;
use app\models\SysEmpresa;
use app\models\User;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */
/* @var $form yii\widgets\ActiveForm */

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */
$this->title = 'Usuarios Departamentos';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios Departamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

use app\assets\UsuarioDepartamentoAsset;
UsuarioDepartamentoAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['usuarios-departamentos']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


?>
<div class="sys-adm-usuarios-dep-create">
    <div class="sys-adm-usuarios-dep-form">
        <?php $form = ActiveForm::begin(['id'=>'form']); ?>
         <div class = 'row'>
          <div class ='col-md-2'>
            <?= $form->field($model, 'id_usuario')->dropDownList(ArrayHelper::map(User::find()->where(['empresa'=> $empresa->id_sys_empresa])->andwhere(['status'=> '1'])->addOrderBy('username')->all(), 'id', 'username'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'user']  ) ?> 
           </div>
          <div class = 'col-md-2'>
             <?= $form->field($model, 'area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'Todos..' ,'class'=> 'form-control input-sm', 'id'=>'area']  ) ?> 
         </div>
          <div class = 'col-md-3'>
             <?= $form->field($model, 'departamento')->widget(DepDrop::classname(), [
                             'data'=> [$model->departamento => 'area'],
                             'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                             'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                'pluginOptions'=>[
                                    'depends'=>['area'],
                                     'initialize' => true,
                                      'initDepends' => ['area'], 
                                    'placeholder'=>'Todos',
                                    'url'=>Url::to(['/consultas/listadepartamento']),
                                   
                                ]])?>      
         </div>
          <div class = 'col-md-2'>
             <?= $form->field($model, 'usuario_tipo')->dropDownList(Yii::$app->params['tipo_usuarios'], ['class'=>'form-control input-sm'])?>
          </div>
          <div class = 'col-md-2'>
             <?= $form->field($model, 'estado')->dropDownList(['A'=>'Activo', 'I'=> 'Inactivo'], ['class'=>'form-control input-sm'])?>
          </div>
           <button class='btn btn-primary input-sm'  id= 'btn-pegar'><i class="glyphicon glyphicon-plus"></i></button>
         </div>
         <div class= 'row'>
                <div class= 'col-md-12'>
                   <table class = 'table' id='tabla' style='font-size: 11px;'>
                     <thead>
                     </thead>
                     <tbody>
                     
                     </tbody>
                   </table>
               </div>
         </div>
         <div class="form-group text-left">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success', 'id'=>'Guardar']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div id="loading"></div>
