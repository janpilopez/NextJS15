<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\SysEmpresa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-empresa-form">  

    <?php $form = ActiveForm::begin([ 'options'=>['enctype'=>'multipart/form-data']]); ?>
    
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">1.- DATOS EMPRESA</a>
            </h4>
          </div>
          <div id="collapse1" class="panel-collapse collapse in">
          <div class="panel-body">
             <div class= 'row'>
               <div class= 'col-md-1'>
                  <?= $form->field($model, 'id_sys_empresa')->textInput(['maxlength' => true]) ?>
              </div>     
               <div class = 'col-md-2'>
                   <?= $form->field($model, 'ruc')->textInput(['maxlength' => true]) ?>
               </div>
               <div class = 'col-md-4'>
                   <?= $form->field($model, 'representante')->textInput(['maxlength' => true]) ?>
               </div>
                <div class= 'col-md-5'>
                 <?= $form->field($model, 'razon_social')->textInput(['maxlength' => true]) ?>
                 </div>
             </div>
              <div class= 'row'>
                <div class= 'col-md-3'>
                   <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>
                 </div>
                  <div class= 'col-md-3'>
                   <?= $form->field($model, 'celular')->textInput(['maxlength' => true]) ?>
                 </div>
                  <div class= 'col-md-3'>
                   <?= $form->field($model, 'pais')->textInput(['maxlength' => true]) ?>
                 </div>
                  <div class= 'col-md-3'>
                   <?= $form->field($model, 'ciudad')->textInput(['maxlength' => true]) ?>
                 </div>
             </div>
             <div class= 'row'>
                  <div class= 'col-md-6'>
                   <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>
                 </div>
                 <div class= 'col-md-6'>
                        <?= $form->field($model, 'vencimiento_credencial')->widget(DatePicker::classname(), [
                                                    'removeButton' => false,
                                                    'size'=>'md',
                                                   
                                                    'pluginOptions' => [
                                                        'autoclose'=>true,
                                                        'format' => 'yyyy-mm-dd',
                                                        //'startDate' => date('Y-m-d'),
                                                    ],
                    				                'options' => ['placeholder' => 'Fecha de Inicio']
                   ]);?>
                 </div>
          </div>
          <div class= 'row'>
              <div class= 'col-md-6'>  
                   
                   <?= $form->field($model, 'file')->widget(FileInput::classname(), [
                       'options' => ['accept' => 'file/*'],
                       'pluginOptions'=>['allowedFileExtensions'=>['jpg','png'],'showUpload' => false],
                     ]);?>
                     
              </div>
              <div class= 'col-md-6'>  
                   
                   <?= $form->field($model, 'cred')->widget(FileInput::classname(), [
                       'options' => ['accept' => 'cred/*', 'class'=> 'input-sm'],
                       'pluginOptions'=>['allowedFileExtensions'=>['jpg','png'],'showUpload' => false],
                     ]);?>
                     
              </div>
              </div>
           </div>
         </div>
       </div>
       <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">2- CONFIGURACIÓN BASE DE DATOS</a>
            </h4>
          </div>
          <div id="collapse2" class="panel-collapse collapse">
            <div class="panel-body">
                  <div class ="row">
                    <div class= "col-md-3">
                        <?= $form->field($model, 'db_dns')->textInput(['maxlength' => true]) ?>  
                    </div>          
                      <div class= "col-md-3">
                        <?= $form->field($model, 'db_name')->textInput(['maxlength' => true]) ?>  
                    </div>  
                      <div class= "col-md-3">
                        <?= $form->field($model, 'db_user')->textInput(['maxlength' => true]) ?>  
                    </div>  
                      <div class= "col-md-3">
                        <?= $form->field($model, 'db_password')->textInput(['maxlength' => true]) ?>  
                    </div>        
                  </div>
               </div>
            </div>
        </div>
       <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">3- CONFIGURACIÓN CORREO</a>
            </h4>
          </div>
          <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body">
                 <div class ="row">
                    <div class= "col-md-3">
                        <?= $form->field($model, 'mail_host')->textInput(['maxlength' => true]) ?>  
                    </div>          
                      <div class= "col-md-3">
                        <?= $form->field($model, 'mail_username')->textInput(['maxlength' => true]) ?>  
                    </div>  
                      <div class= "col-md-3">
                        <?= $form->field($model, 'mail_password')->textInput(['maxlength' => true]) ?>  
                    </div>  
                      <div class= "col-md-3">
                        <?= $form->field($model, 'mail_port')->textInput(['maxlength' => true]) ?>  
                    </div>        
                  </div>
            </div>
          </div>
        </div>
   </div>
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
