<?php

use app\models\SysAdmDepartamentos;
use app\models\SysIndicadores;
use kartik\date\DatePicker;
use kartik\number\NumberControl;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\FormIndicadorSistemasDetalleAsset;
FormIndicadorSistemasDetalleAsset::register($this);
$urlconsultas = Yii::$app->urlManager->createUrl(['form-indicador-sistemas-detalle']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "urlconsultas = '$urlconsultas', consultas = '$consultas', id_encabezado_indicador = '$id_encabezado_indicador';";
$this->registerJs($inlineScript, View::POS_HEAD);
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */
/* @var $form yii\widgets\ActiveForm */
$indicadores = [1=> '1%', 2=> '2%', 3=> '3%', 4=> '4%', 5=> '5%', 6=> '6%', 7=> '7%', 8=> '8%', 9=> '9%', 10=>'10%'];
?>
<div class="sys-rrhh-empleados-permisos-equipos-form">
 <div class = 'panel panel-default'>
     <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'imp_departamento')->dropDownList(ArrayHelper::map(SysAdmDepartamentos::find()->orderBy(['departamento' => 'asc'])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'disabled'=> $update != 1  ?false: true,'id'=>'impDepartamento', 'prompt' => 'Seleccione...'])
                ?>
            </div> 
            <div class="col-md-4">
                <?= $form->field($model, 'departamental')->textInput(['class'=> 'form-control input-sm', 'disabled'=> true,'id'=> 'departamental']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'tipo_indicador')->textInput(['class'=> 'form-control input-sm', 'disabled'=> true, 'id'=> 'tip_indicador']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fecha')->widget(DatePicker::classname(), [
                    'removeButton' => false,
                                              
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,                           
                    ],
                	'options' => ['class'=>'form-control input-sm','placeholder' => 'Fecha', 'id'=>'fecha']
                ]);?>
            </div> 
        </div>

        <div class="row"> 
            <div class= "col-md-12">
               <div style="height: 300px; overflow: auto; font-size:11px;">
                    <?php 
                        echo  TabularInput::widget([
                         
                            'models' => $modeldet,
                            'id'=> 'modeldet',
                            'attributeOptions' => [
                          // 'enableAjaxValidation'      => true,
                             /*enableClientValidation'    => false,
                             'validateOnChange'          => false,
                             'validateOnSubmit'          => true,
                             'validateOnBlur'            => false,*/
                            ],
                         
                            'allowEmptyList' => true,
                            'addButtonPosition' => MultipleInput::POS_HEADER,
                            'addButtonOptions' => [
                                'class' => 'btn btn-xs btn-info',
                                'label' => '<i class="glyphicon glyphicon-plus"></i>'
                            ],
                         
                            'removeButtonOptions' => [
                                'class' => 'btn btn-xs btn-danger',
                                'label' => '<i class="glyphicon glyphicon-remove"></i>'
                            ],
                            
                            'columns'=> [
                             
                                [
                                    'name' => 'id_detalle_indicador',
                                    'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                ],

                                [
                                    'name' => 'id_cuerpo_indicador',
                                    'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                ],
                                 
                                [
                                    'name' => 'usuario',
                                    'title' => $modeldet[0]->getAttributeLabel('usuario'),
                                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                                    'enableError' => true,
                                    'options' => [    
                                        'class'=> 'input-sm'  
                                    ],
                                ],
                            
                                [
                                    'name' => 'can_negro',
                                    'title' => $modeldet[0]->getAttributeLabel('can_negro'),
                                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                                    'enableError' => true,
                                    'options' => [ 
                                        'class'=> 'input-sm' 
                                    ],
                                ],

                                [
                                    'name' => 'can_color',
                                    'title' => $modeldet[0]->getAttributeLabel('can_color'),
                                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                                    'enableError' => true,
                                    'options' => [
                                        
                                        'class'=> 'input-sm'
                                        
                                        
                                    ],
                                ],

                                [
                                    'name' => 'rem_sol',
                                    'title' => $modeldet[0]->getAttributeLabel('rem_sol'),
                                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                                    'enableError' => true,
                                    'options' => [
                                        
                                        'class'=> 'input-sm'
                                    ],
                                ],
             
                            ]
                        ])
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
 </div>
</div>
