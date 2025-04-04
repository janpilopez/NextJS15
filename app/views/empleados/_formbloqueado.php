<?php

use app\models\SysEmpresa;
use app\models\SysParroquias;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\EmpleadosAsset;
use yii\jui\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use kartik\select2\Select2;
use app\models\SysPaises;
use app\models\SysProvincias;
use app\models\SysRrhhContratos;
use app\models\SysAdmCargos;
use app\models\SysAdmActividades;
use app\models\SysAdmCcostos;
use app\models\SysAdmRutas;
use app\models\SysRrhhBancos;
use app\models\SysRrhhFormaPago;
use app\models\User;
EmpleadosAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */
/* @var $form yii\widgets\ActiveForm */
$this->render('../_alertFLOTADOR'); 


 $inputDisable = true;

 if(!User::hasRole('auditExterno')): 

   $inputDisable = true;
 
 endif;

 $tabNucleoFamiliar = false;
 $tabHorarios= false;
 $tabHaberes = false;
 $tabSueldos = false;
 $tabContratos = false;
 $tabCargos = false;
 $tabGastos = false;
 $tabExpediente = false;
 $tabFoto = false;
 
 if( User::hasRole('GERENTE') ||  User::hasRole('jefeDDOO') || User::hasRole('ASISTDDOO') || User::hasRole('jefeNomina') || User::hasRole('asistNomina') || User::hasRole('trabajadoraSocial')): 
 
       $tabNucleoFamiliar = true;
       $tabFoto = true;
       $tabCargos = true;
       $tabHorarios = true;
       $tabExpediente = true;
 
 endif;

 if(User::hasRole('GERENTE') ||  User::hasRole('jefeDDOO') || User::hasRole('ASISTDDOO') || User::hasRole('jefeNomina') || User::hasRole('asistNomina')):
 
     $tabSueldos = true;
     $tabContratos = true;
     $tabCargos = true;
     $tabGastos = true;
     $tabHaberes = true;
     $tabExpediente = true;
 
 endif;
 

 
?>

<div class="sys-rrhh-empleados-form">

    <?php $form = ActiveForm::begin([ 'id'=>'formempleados']); ?>
    
    <div class = 'panel panel-default'>  
     <div class = 'panel-body'>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Datos Generales</a></li>
          <li><a data-toggle="tab" href="#menu1">Núcleo Familiar</a></li>
          <li><a data-toggle="tab" href="#menu2">Horarios Laborales</a></li>
          <li><a data-toggle="tab" href="#menu3">Haberes y Descuentos</a></li>
          <li><a data-toggle="tab" href="#menu4">Historico de Sueldos</a></li>
          <li><a data-toggle="tab" href="#menu5">Contratos</a></li>
          <li><a data-toggle="tab" href="#menu6">Historico Departamentales</a></li>
          <li><a data-toggle="tab" href="#menu7">Gastos Deducibles</a></li>
          <li><a data-toggle="tab" href="#menu8">Expediente</a></li>
          <li><a data-toggle="tab" href="#menu9">Foto</a></li>
        </ul>
        <br>
        <div class="tab-content">
         <div id="home" class="tab-pane fade in active">
           <div class = 'panel panel-default'>  
              <p class = 'separador'>1.- Datos Personales</p>
            <div class = 'panel-body'>
                    <div class= 'row'>
                       <div class= 'col-md-1'>
                          <?= $form->field($model, 'tipo_identificacion')->dropDownList(['C'=> 'Cédula', 'P'=> 'Pasaporte'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable])?>
                       </div>
                       <div class= 'col-md-2'>
                          <?= $form->field($model, 'id_sys_rrhh_cedula')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Identificación', 'disabled' => $inputDisable]) ?>
                       </div>
                       <div class= 'col-md-3'>
                          <?= $form->field($model, 'nombre')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Nombres', 'disabled' => $inputDisable]) ?>
                       </div>
                         <div class= 'col-md-3'>
                          <?= $form->field($model, 'apellidos')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Apellidos', 'disabled' => $inputDisable]) ?>
                       </div>
                         <div class= 'col-md-2'>
                          <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo',  'I'=> 'Inactivo'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable]) ?>
                       </div>
                        <div class= 'col-md-1'>
                           <?= $form->field($model, 'nivel_riesgo')->dropDownList([1=> 'Leve',  2=> 'Medio', 3=> 'Alto'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable]) ?>
                       </div>
                    </div>
                    <div class= 'row'>
                       <div class='col-md-2'>
                          <?=  $form->field($model, 'fecha_nacimiento')->widget(DatePicker::classname(), [
                                                            'dateFormat' => 'yyyy-MM-dd',
                                                             'clientOptions' => [
                                                              'yearRange' => '-115:+0',
                                                              'changeYear' => true],
                              
                              'options' => ['placeholder' => 'Fecha', 'class'=> 'form-control input-sm', 'id'=> 'fechanacimiento', 'type'=> 'date', 'disabled' => $inputDisable]
                                                        ]);
                          ?>
                       
                       </div>
                       <div class= 'col-md-1'>
                           <label>Edad</label>
                           <?= html::textInput('edad',$model->getCalcularEdad($model->fecha_nacimiento), ['class'=>'form-control input-sm', 'id'=> 'edad', 'readonly'=> true] )?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'genero')->dropDownList(['M'=> 'MASCULINO',  'F'=> 'FEMENINO'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable])  ?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'estado_civil')->dropDownList(['S'=> 'Soltero',  'C'=> 'Casado', 'U'=> 'Unido', 'D'=> 'Divorciado'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable])  ?>
                       </div>
                        <div class= 'col-md-1'>
                           <?= $form->field($model, 'tipo_sangre')->dropDownList([
                               'A+'=> 'A+',  
                               'A-'=> 'A-', 
                               'O+'=> 'O+', 
                               'O-'=> 'O-', 
                               'B+'=> 'B+', 
                               'B-'=> 'B-', 
                               'AB+'=> 'AB+', 
                               'AB-'=> 'AB-'], ['class'=>'form-control input-sm', 'disabled' => $inputDisable])  ?>
                        </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'telefono')->textInput(['maxlenght'=> true, 'class'=>'form-control input-sm', 'placeholder'=> 'Teléfono', 'disabled' => $inputDisable])?>
                       </div>
                       <div class= 'col-md-2'>
                           <?= $form->field($model, 'celular')->textInput(['maxlenght'=> true, 'class'=>'form-control input-sm', 'placeholder'=> 'Celular', 'disabled' => $inputDisable])?>
                       </div>
                  
                     
                    </div>
                    <div class= 'row'>
                       <div class= 'col-md-2'>
                           <?= $form->field($model, 'pais')->dropDownList(ArrayHelper::map(SysPaises::find()->all(), 'id_sys_pais', 'pais'), [ 'value'=> $model->getObtenerpais(),'prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'pais', 'disabled' => $inputDisable] )?>
                       </div>
                      
                       <div class= 'col-md-2'>
                             <label>Provincia</label>
                             <?= $form->field($model, 'provincia')->widget(DepDrop::classname(), [
                                 'data' => [ $model->obtenerprovincia=> 'provincia'],
                                 'options'=>['id'=>'provincia', 'class'=> 'form-control input-sm', 'disabled' => $inputDisable],
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
                                 'options'=>[ 'id'=> 'canton','class'=> 'form-control input-sm', 'disabled' => $inputDisable],
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
                                 'options'=>['class'=> 'form-control input-sm', 'disabled' => $inputDisable],
                                    'pluginOptions'=>[
                                        'depends'=>['canton'],
                                        'initialize' => true,
                                        'initDepends' => ['canton'], 
                                        'placeholder'=>'Select...',
                                        'url'=>Url::to(['/consultas/listparroquias'])
                                    ]])->label(false)?>
                        </div>
                        <div class= 'col-md-4'>
                           <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'example@email.com', 'disabled' => $inputDisable]) ?>
                       </div>
                    </div>
                    <div class= 'row'>
                        <div class= 'col-md-12'>
                           <?= $form->field($model, 'direccion')->textarea(['maxlenght'=> true, 'class'=> 'form-control', 'placeholder'=> 'Dirección', 'rows'=> 1, 'disabled' => $inputDisable]) ?>
                        </div>
                    </div>
                    <div class= 'row'>
                       <div class= 'col-md-2'>
                          <?= $form->field($model, 'formacion_academica')->dropDownList(['P'=> 'PRIMARIA', 'S'=> 'SECUNDARIA', 'T'=> 'TERCER NIVEL', 'C'=> 'CUARTO NIVEL'],['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                       </div>
                       <div class= 'col-md-3'>
                         <?= $form->field($model, 'titulo_academico')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'Título Acádemico', 'disabled' => $inputDisable]) ?>
                       </div>
                       <div class= 'col-md-1'>
                           <?= $form->field($model, 'discapacidad')->dropDownList(['N'=> 'NO', 'S'=> 'SI'], ['maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                       </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'tipo_discapacidad')->dropDownList(['F'=> 'Fisica', 'C'=> 'Cognitiva', 'S'=> 'Sensorial', 'I'=> 'Intelectual', 'P'=> 'Psicologica', 'V'=> 'Visual', 'A' => 'Auditiva'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                       </div>
                         <div class= 'col-md-1'>
                          <?= $form->field($model, 'por_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> '0 - 100 %', 'disabled' => $inputDisable]) ?>
                       </div>
                         <div class= 'col-md-2'>
                          <?= $form->field($model, 'ide_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> 'Número de Carnet', 'disabled' => $inputDisable]) ?>
                       </div>
                    </div>
               </div>
            </div>
            <div class = 'panel panel-default'>  
               <p class = 'separador'>2.- Datos Laborales</p>
            <div class = 'panel-body'>
               
                 <div class= 'row'>
                   <div class= 'col-md-2'>
                    <?= $form->field($model, 'tipo_empleado')->dropDownList(['A'=> 'ADMINISTRATIVO', 'O'=> 'OPERACIONAL', 'T'=> 'TRIPULANTE'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                   </div>
                    <div class= 'col-md-2'>
                    <?= $form->field($model, 'id_sys_rrhh_contrato')->dropDownList(ArrayHelper::map(SysRrhhContratos::find()->orderBy('')->all(), 'id_sys_rrhh_contrato', 'contrato'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                   </div>
                   <div class= 'col-md-3'>
                    <?=
                    
                    $form->field($model, 'id_sys_adm_cargo')->widget(Select2::classname(), [
                        'size' => Select2::SMALL,
                        'data'=>  ArrayHelper::map(SysAdmCargos::find()->select("id_sys_adm_cargo, cargo")->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'),
                        'options'=> ['placeholder' => 'Seleccione','disabled' => $inputDisable],
                        'pluginOptions'=> [
                          
                            'allowClear'=> true 
                  ]]);
                    ?>
                   </div>
                   <div class= 'col-md-2'>
                    <?= $form->field($model, 'id_sys_adm_actividad')->dropDownList(ArrayHelper::map(SysAdmActividades::find()->all(), 'id_sys_adm_actividad', 'actividad'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                   </div>
                   <div class= 'col-md-3'>
                    <?= $form->field($model, 'id_sys_adm_ccosto')->dropDownList(ArrayHelper::map(SysAdmCcostos::find()->all(), 'id_sys_adm_ccosto', 'centro_costo'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                   </div> 
                 </div>
                 <div class='row'>
                    <div class= 'col-md-2'>
                      <?= $form->field($model, 'valor_transporte')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> '0.00', 'disabled' => $inputDisable]) ?>     
                      <?= $form->field($model, 'transporte')->checkbox(['checked'=> $model->transporte== '1' ? true: false,],['class'=> 'input-sm']) ?>
                      <?= $form->field($model, 'id_sys_adm_ruta')->dropDownList(ArrayHelper::map(SysAdmRutas::find()->all(), 'id_sys_adm_ruta', 'ruta'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                    </div>
                     <div class= 'col-md-2'>
                      <?= $form->field($model, 'valor_lunch')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm' , 'placeholder'=> '0.00', 'disabled' => $inputDisable]) ?>   
                      <?= $form->field($model, 'desayuno')->checkbox(['checked'=> $model->desayuno == '1'? true: false],['class'=> 'input-sm', 'disabled' => $inputDisable])?>
                      <?= $form->field($model, 'almuerzo')->checkbox(['checked'=> $model->almuerzo== '1'? true: false],  ['class'=> 'input-sm', 'disabled' => $inputDisable])?>
                      <?= $form->field($model, 'merienda')->checkbox(['checked'=> $model->merienda == '1'? true: false], ['class'=> 'input-sm', 'disabled' => $inputDisable])?>
                    </div>
                    <div class= 'col-md-3'>
                      <?= $form->field($model, 'tipo_jornada')->dropDownList(['N'=> 'NORMAL', 'R'=> 'ROTATIVA'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                      <?= $form->field($model, 'decimo')->dropDownList(['N'=> 'No', 'S'=> 'Si'], ['maxlenght'=> true, 'class'=> 'input-sm', 'disabled' => $inputDisable]) ?>
                      <?= $form->field($model, 'freserva')->dropDownList(['N'=> 'No', 'S'=> 'Si'], ['maxlenght'=> true, 'class'=> 'input-sm', 'disabled' => $inputDisable]) ?>
                      <?= $form->field($model, 'provision_freserva')->dropDownList(['S'=> 'SI', 'N'=> 'NO'], ['maxlenght'=> true, 'class'=> 'input-sm', 'disabled' => $inputDisable]) ?>
                    </div>
                   <div class= 'col-md-2'>
                     <?= $form->field($model, 'id_sys_rrhh_banco')->dropDownList(ArrayHelper::map(SysRrhhBancos::find()->all(), 'id_sys_rrhh_banco', 'banco'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm',  'placeholder'=> 'Número de Cuenta', 'disabled' => $inputDisable]) ?>
                     <?= $form->field($model, 'cta_banco')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm',  'placeholder'=> 'Número de Cuenta', 'disabled' => $inputDisable]) ?>
                   </div>
                   <div class= 'col-md-2'>
                      <?= $form->field($model, 'id_sys_rrhh_forma_pago')->dropDownList(ArrayHelper::map(SysRrhhFormaPago::find()->all(), 'id_sys_rrhh_forma_pago', 'forma_pago'), ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control input-sm', 'disabled' => $inputDisable]) ?>
                      <?= $form->field($model, 'num_tar')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm',  'placeholder'=> 'Número de Tarjeta', 'disabled' => $inputDisable]) ?>
                   </div>
                   <div class= 'col-md-2'>
                     <?= $form->field($model, 'numero_uniforme')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm',  'placeholder'=> 'Número de Uniforme', 'disabled' => $inputDisable])?>
                   </div>
                 </div> 
            </div>
           </div>
          </div>
          
          <?php if($tabNucleoFamiliar):?>
              <div id="menu1" class="tab-pane fade">
                <div class = 'panel panel-default'>  
                <p class = 'separador'>Presentar los respectivos certificados de nacimiento que comprueben su grado de parentesco</p>
                <div class = 'panel-body'>
                  <?php  echo $this->render('_nucleofamiliar',[ 'model'=> $model,  'nucleofamiliar'=> $nucleofamiliar, 'update'=> $update, 'disabled' => $inputDisable]); ?>
                 </div>
               </div>
              </div>
          <?php endif;?>
          
          <?php if($tabHorarios):?>
             <div id="menu2" class="tab-pane fade">
                 <div class = 'panel panel-default'>  
                    <p class = 'separador'>Asignar un horario en caso de que empleador tenga una jornada laboral normal</p>
                    <div class = 'panel-body'>
                       <?php  echo $this->render(' _jornadalaboral',[ 'model'=> $model,  'horarios'=> $horarios, 'update'=> $update, 'disabled' => $inputDisable]); ?>
                     </div>
                  </div>
            </div>
           <?php endif;?>
           
           <?php if($tabHaberes):?>
             <div id="menu3" class="tab-pane fade">
                <div class = 'panel panel-default'>  
                  <p class = 'separador'>Registro de Novedades y Descuentos</p>
                <div class = 'panel-body'>
                <?php  echo $this->render('_haberes',[ 'model'=> $model,  'haberes'=> $haberes, 'update'=> $update, 'disabled' => $inputDisable]); ?>
                </div>
               </div>
             </div>
          <?php endif;?>

          <?php if($tabSueldos):?>
          <div id="menu4" class="tab-pane fade">
           <div class = 'panel panel-default'>  
              <p class = 'separador'>Histórico de Sueldos</p>
            <div class = 'panel-body'>
            <?php  echo $this->render('_sueldos',[ 'model'=> $model,  'sueldos'=> $sueldos, 'update'=> $update, 'disabled' => $inputDisable]); ?>
            </div>
           </div>
          </div>
          <?php endif;?>
          
          <?php if($tabContratos):?>
           <div id="menu5" class="tab-pane fade">
                <div class = 'panel panel-default'>  
                  <p class = 'separador'>Contratos</p>
                <div class = 'panel-body'>
                <?php  echo $this->render('_contratos',[ 'model'=> $model,  'contratos'=> $contratos, 'update'=> $update, 'disabled' => $inputDisable]); ?>
                </div>
               </div>
          </div>
          <?php endif;?>

          <?php if($tabCargos):?>
           <div id="menu6" class="tab-pane fade">
                <div class = 'panel panel-default'>  
                  <p class = 'separador'>Historico Departamentales</p>
                <div class = 'panel-body'>
                <?php  echo $this->render('_cargos',[ 'model'=> $model,  'cargos'=> $cargos, 'update'=> $update, 'disabled' => $inputDisable]); ?>
                </div>
               </div>
          </div>
          <?php endif;?>
          
   		 <?php  if($tabGastos):?>
           <div id="menu7" class="tab-pane fade">
            <div class = 'panel panel-default'>  
              <p class = 'separador'>Gastos Personales</p>
            <div class = 'panel-body'>
            <?php  echo $this->render('_gastos',[ 'model'=> $model,  'gastos'=> $gastos, 'update'=> $update, 'disabled' => $inputDisable]); ?>
            </div>
           </div>
          </div>
          <?php endif;?>

          <?php  if($tabExpediente):?>
           <div id="menu8" class="tab-pane fade">
           <div class = 'panel panel-default'>  
            <div class = 'panel-body'>

               <?php if ($documentos) :

                  $cont = 0;
                  
                  foreach($documentos as $document){
                     $cont++;
                     ?>
                     <a href='<?= Yii::$app->homeUrl.$document['ruta']?>'>Documento <?= $cont ?></a>.<br>
                     <?php 
                  }
                  
               endif;?>
                   
                <?= $form->field($model, 'file2[]')->fileInput(['multiple'=>true]);?>  
            </div>
           </div>
          </div>
          <?php endif;?>
          
          <?php if($tabFoto):?>
          <div id="menu9" class="tab-pane fade">
            <div class = 'panel panel-default'>  
            <div class = 'panel-body'>
            
                <?php if ($fotos) :?>
                
                <img width="20%" height ='20%' src="data:image/jpeg;base64, <?= $fotos?>" alt="" />
                
                <?php else : ?>
                 
                  <img width="20%" height ='20%' src='<?= Yii::$app->homeUrl.'img/sin_foto.jpg'?>' alt="" />
                     
                <?php endif ?>
                
                <?= $form->field($model, 'file')->fileInput();?>  
            </div>
           </div>
          </div>
          <?php endif;?>
          
        </div>
        <br>
       </div>
    </div>
      
    <?php ActiveForm::end(); ?>

</div>
