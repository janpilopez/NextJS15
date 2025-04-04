<?php

use app\models\SysAdmActividades;
use app\models\SysAdmCargos;
use app\models\SysAdmCcostos;
use app\models\SysAdmRutas;
use app\models\SysPaises;
use app\models\SysRrhhBancos;
use app\models\SysRrhhContratos;
use app\models\SysRrhhFormaPago;
use yii\jui\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = $model->id_sys_rrhh_cedula;
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-view">
   <?php $form = ActiveForm::begin([ 'id'=>'formempleados']); ?>

 <div class = 'panel panel-default'>  
     <div class = 'panel-body'>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Datos Generales</a></li>
        </ul>
        <div class="tab-content">
         <div id="home" class="tab-pane fade in active">
           <div class = 'panel panel-default'>  
            <div class = 'panel-body'>
                    <div class= 'row'>
                       <div class= 'col-md-2'>
                          <label>Cédula</label>
                          <?= $form->field($model, 'id_sys_rrhh_cedula')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Identificación'])->label(false) ?>
                       </div>
                       <div class= 'col-md-3'>
                          <?= $form->field($model, 'nombre')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Nombres']) ?>
                       </div>
                       <div class= 'col-md-3'>
                          <?= $form->field($model, 'apellidos')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Apellidos']) ?>
                       </div>
                       <div class= 'col-md-4'>
                                <?=
                                
                                $form->field($model, 'id_sys_adm_cargo')->widget(Select2::classname(), [
                                    'size' => Select2::SMALL,
                                    'data'=>  ArrayHelper::map(SysAdmCargos::find()->select("id_sys_adm_cargo, cargo")->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'),
                                    'options'=> ['placeholder' => 'Seleccione'],
                                    'pluginOptions'=> [
                                      
                                        'allowClear'=> true 
                              ]]);
                                ?>
                        </div>
                     
                    </div>
                    <div class= 'row'>
                       <div class='col-md-2'>
                          <?=  $form->field($model, 'fecha_nacimiento')->widget(DatePicker::classname(), [
                                                            'dateFormat' => 'yyyy-MM-dd',
                                                             'clientOptions' => [
                                                              'yearRange' => '-115:+0',
                                                              'changeYear' => true],
                              
                            				                'options' => ['placeholder' => 'Fecha', 'class'=> 'form-control input-sm', 'id'=> 'fechanacimiento', 'type'=> 'date']
                                                        ]);
                          ?>
                       
                       </div>
                       <div class= 'col-md-1'>
                           <label>Edad</label>
                           <?= html::textInput('edad',$model->getCalcularEdad($model->fecha_nacimiento), ['class'=>'form-control input-sm', 'id'=> 'edad', 'readonly'=> true] )?>
                       </div>
                       <div class= 'col-md-1'>
                           <?= $form->field($model, 'tipo_sangre')->dropDownList(['A+'=> 'A+',  'A-'=> 'A-', 'O+'=> 'O+', 'O-'=> 'O-', 'B+'=> 'B+', 'B-'=> 'B-', 'AB+'=> 'AB+', 'AB-', 'AB-'], ['class'=>'form-control input-sm'])  ?>
                        </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'genero')->dropDownList(['M'=> 'MASCULINO',  'F'=> 'FEMENINO'], ['class'=>'form-control input-sm'])  ?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'estado_civil')->dropDownList(['S'=> 'Soltero',  'C'=> 'Casado', 'U'=> 'Unido', 'D'=> 'Divorciado'], ['class'=>'form-control input-sm'])  ?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'telefono')->textInput(['maxlenght'=> true, 'class'=>'form-control input-sm', 'placeholder'=> 'Teléfono'])?>
                       </div>
                       <div class= 'col-md-2'>
                           <?= $form->field($model, 'celular')->textInput(['maxlenght'=> true, 'class'=>'form-control input-sm', 'placeholder'=> 'Celular'])?>
                       </div>
                      
                     
                    </div>
                    <div class= 'row'>
                       <div class= 'col-md-2'>
                           <?= $form->field($model, 'pais')->dropDownList(ArrayHelper::map(SysPaises::find()->all(), 'id_sys_pais', 'pais'), [ 'value'=> $model->getObtenerpais(),'prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'pais'] )?>
                       </div>
                      
                       <div class= 'col-md-2'>
                             <label>Provincia</label>
                             <?= $form->field($model, 'provincia')->widget(DepDrop::classname(), [
                                 'data' => [ $model->obtenerprovincia=> 'provincia'],
                                 'options'=>['id'=>'provincia', 'class'=> 'form-control input-sm'],
                                 'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                    'pluginOptions'=>[
                                        'depends'=>['pais'],
                                        'initialize' => true,
                                        'initDepends' => ['pais'], 
                                        'placeholder'=>'Select...',
                                        'url'=>Url::to(['/consultas/listeprovincias']),
                                       
                                    ]])->label(false)?>      
                        </div>
                     
                         <div class= 'col-md-2'>
                             <label>Cantón</label>
                             <?= $form->field($model, 'canton')->widget(DepDrop::classname(), [
                                    'data' => [$model->getObtenercanto() => 'provincia'],
                                    'options'=>[ 'id'=> 'canton','class'=> 'form-control input-sm'],
                                    'pluginOptions'=>[
                                        'depends'=>['provincia'],
                                        'initialize' => true,
                                        'initDepends' => ['provincia'], 
                                        'placeholder'=>'Select...',
                                        'url'=>Url::to(['/consultas/listcantones']),
                                        
                                    ]])->label(false)?>
                        </div>
                        <div class= 'col-md-2'>
                             <label>Parroquia</label>
                             <?= $form->field($model, 'id_sys_parroquia')->widget(DepDrop::classname(), [
                                    'data' => [$model->id_sys_parroquia => 'provincia'],
                                    'options'=>['class'=> 'form-control input-sm'],
                                    'pluginOptions'=>[
                                        'depends'=>['canton'],
                                        'initialize' => true,
                                        'initDepends' => ['canton'], 
                                        'placeholder'=>'Select...',
                                        'url'=>Url::to(['/consultas/listparroquias'])
                                    ]])->label(false)?>
                        </div>
                        <div class= 'col-md-4'>
                           <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'example@email.com']) ?>
                       </div>
                    </div>
                
                    <div class= 'row'>
                       <div class= 'col-md-2'>
                          <?= $form->field($model, 'formacion_academica')->dropDownList(['P'=> 'PRIMARIA', 'S'=> 'SECUNDARIA', 'T'=> 'TERCER NIVEL', 'C'=> 'CUARTO NIVEL'],['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm']) ?>
                       </div>
                       <div class= 'col-md-3'>
                         <?= $form->field($model, 'titulo_academico')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'Título Acádemico']) ?>
                       </div>
                     
                       <div class= 'col-md-1'>
                           <?= $form->field($model, 'discapacidad')->dropDownList(['N'=> 'NO', 'S'=> 'SI'], ['maxlenght'=> true, 'class'=> 'form-control input-sm']) ?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'tipo_discapacidad')->dropDownList(['F'=> 'Fisica', 'C'=> 'Cognitiva', 'S'=> 'Sensorial', 'I'=> 'Intelectual', 'P'=> 'Psicologica', 'V'=> 'Visual'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm']) ?>
                       </div>
                         <div class= 'col-md-2'>
                          <?= $form->field($model, 'por_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> '0 - 100 %']) ?>
                       </div>
                         <div class= 'col-md-2'>
                          <label># Carnét</label>
                          <?= $form->field($model, 'ide_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'Número de Carnet'])->label(false) ?>
                       </div>
                    </div>
                    <div class= 'row'>
                        <div class= 'col-md-12'>
                           <?= $form->field($model, 'direccion')->textarea(['maxlenght'=> true, 'class'=> 'form-control', 'placeholder'=> 'Dirección', 'rows'=> 1]) ?>
                        </div>
                    </div>
                    <div class= 'row'>
                       <div class='col-md-6'>
                        	<label>Fecha Ingreso</label>
                            <?= html::textInput('fecha_ingreso', $contrato[0]['fecha_ingreso'], ['class'=>'form-control input-sm',  'readonly'=> true] )?>
                      </div>
                    
                       <div class='col-md-6'> 
                       	   <label>Años Laborados</label>
                           <?= html::textInput('anio_laborado',$contrato[0]['anios'], ['class'=>'form-control input-sm',  'readonly'=> true] )?>
                       </div>
                    </div>
               </div>
            </div>
          </div>
        </div>
       </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
