<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Inicio de sesión';
use app\models\SysEmpresa;
use app\assets\LoginAsset;
LoginAsset::register($this);

?>
   <div class="row">
       
        <div class="col-md-4 col-md-offset-4">
		   <h2 class= "text-center">Gestión de Nómina</h2>
            <div class="panel-default">
                    <div class="panel-heading">
                    <span class="glyphicon glyphicon-lock"></span> Inicio de sesión</div>
					<div class="panel-body">
						 <?php $form = ActiveForm::begin([
							'id' => 'login-form',
							'fieldConfig' => [
						     //'labelOptions' => ['class' => 'col-lg-1 control-label'],
							],
						]); ?>
						<?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'password')->passwordInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'empresa')->dropDownList(ArrayHelper::map(SysEmpresa::find()->all(), 'id_sys_empresa', 'razon_social'),  ['prompt' => 'Seleccione..', 'class'=> 'form-control input-sm']);?>
						
						<?= Html::submitButton('Iniciar', ['class' => 'btn btn-lg btn-success btn-block', 'name' => 'login-button']) ?>
							<?php ActiveForm::end(); ?>	
					</div>
            </div>
        </div>
	</div>


    
   

   

        
      