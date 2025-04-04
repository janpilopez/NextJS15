<?php

use app\models\SysAdmCargos;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaMedica */

$this->title =  'Ficha Médica # '.$model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Ficha Médica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-ficha-medica-view">
 <?php $form = ActiveForm::begin(); ?>
   <h1><?= Html::encode($this->title) ?></h1>
	     <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title"><strong>1.- Datos Personales</strong></h4>
      </div>
      <div class="panel-body">
      	 <div class="row">
      		 <div class="col-md-6">
      		  	 <div class= "row">
                      <div class= "col-md-3">
                    	 <br>
                         <?php 
                          if ($fotos) :
                             echo  Html::img('data:image/jpeg;base64, '.$fotos['baze64'], ['style'=>"width:130px;height:130px;border-radius: 10px"]);
                          else :
                             echo  Html::img(Yii::$app->homeUrl."img/sin_foto.jpg", ['style'=>"width:140px;height:140px;"]);
                          endif;
                    	  ?>
                      </div>
                      <div class= "col-md-9">
                    	 <?= $form->field($empleado, 'id_sys_rrhh_cedula')->textInput(['disabled'=> true]) ?>
                    	 <?= $form->field($empleado, 'nombres')->textInput(['disabled'=> true]) ?>
                    	 <label>Edad:</label>
                         <?= html::textInput('edad',$empleado->getCalcularEdad($empleado->fecha_nacimiento), ['class'=>'form-control', 'id'=> 'edad', 'readonly'=> true] )?>
                      </div>
                 </div>
      		 </div>
      		 <div class= "col-md-6">
      		 		  <div class="row">
                    	 <div class="col-md-8">
                    	   <?=  $form->field($empleado, 'id_sys_adm_cargo')->widget(Select2::classname(), [
                                'size' => Select2::SMALL,
                    	        'disabled'=> true,
                                'data'=>  ArrayHelper::map(SysAdmCargos::find()->select("id_sys_adm_cargo, cargo")->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'),
                                'options'=> ['placeholder' => 'Seleccione'],
                                'pluginOptions'=> [
                                'allowClear'=> true 
                              ]]);?>
                    	 </div>
                    	  <div class= "col-md-4">
                    	      <?= $form->field($empleado, 'tipo_sangre')->dropDownList(['A+'=> 'A+',  'A-'=> 'A-', 'O+'=> 'O+', 'O-'=> 'O-', 'B+'=> 'B+', 'B-'=> 'B-', 'AB+'=> 'AB+', 'AB-', 'AB-'], ['class'=>'form-control', 'disabled'=> true])  ?>
                    	  </div>
                      </div>
                      <div class= "row">
                    	  <div class= "col-md-4">
                    	     <?= $form->field($empleado, 'genero')->dropDownList(['M'=> 'MASCULINO',  'F'=> 'FEMENINO'], ['class'=>'form-control', 'disabled'=> true])  ?>
                    	  </div>
                    	  <div class= "col-md-4">
                    	     <?= $form->field($empleado, 'estado_civil')->dropDownList(['S'=> 'Soltero',  'C'=> 'Casado', 'U'=> 'Unido', 'D'=> 'Divorciado'], ['class'=>'form-control', 'disabled'=> true])  ?>
                    	  </div>
                    	 <div class= "col-md-4">
                    	     <?= $form->field($empleado, 'discapacidad')->dropDownList(['N'=> 'NO', 'S'=> 'SI'], ['maxlenght'=> true, 'class'=> 'form-control', 'disabled'=> true]) ?>
                    	 </div>
                     </div>
                     <div class= "row">
                    	 <div class= 'col-md-4'>
                           <?= $form->field($empleado, 'tipo_discapacidad')->dropDownList(['F'=> 'Fisica', 'C'=> 'Cognitiva', 'S'=> 'Sensorial', 'I'=> 'Intelectual', 'P'=> 'Psicologica', 'V'=> 'Visual'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control', 'disabled'=> true]) ?>
                         </div>
                         <div class= 'col-md-4'>
                           <?= $form->field($empleado, 'por_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control', 'placeholder'=> '0 - 100 %', 'disabled'=> true]) ?>
                         </div>
                         <div class= 'col-md-4'>
                           <label># Carnét</label>
                           <?= $form->field($empleado, 'ide_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control', 'placeholder'=> '', 'disabled'=> true])->label(false) ?>
                         </div>
                   </div>
      		 </div>
      	 </div>
      </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>2.- Signos Vitales</strong></h4>
    </div>
      <div class="panel-body">
         <div class= "row">
               <div class= "col-md-3">
              	  <?= $form->field($model, 'pa_max')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
               </div>
               <div class= "col-md-3">
                  <?= $form->field($model, 'pa_min')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
               </div>
               <div class= "col-md-3">
                  <?= $form->field($model, 'pulso')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
               </div>
               <div class= "col-md-3">
                  <?= $form->field($model, 'respiracion')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
               </div>
         </div>
         <div class= "row">
              <div class="col-md-3">
                 <?= $form->field($model, 'temperatura')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
             </div>
             <div class= "col-md-3">
                 <?= $form->field($model, 'talla')->textInput(['maxlength' => true, 'type'=> 'number', 'step'=>'any', 'disabled'=> true]) ?>
             </div>
            <div class= "col-md-3">
                 <?= $form->field($model, 'peso')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
             </div>
              <div class= "col-md-3">
                 <?= $form->field($model, 'imc')->textInput(['maxlength' => true, 'type'=> 'number', 'disabled'=> true]) ?>
             </div>
         </div>       
      </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>3.- Datos Médicos</strong></h4>
    </div>
    <div class="panel-body">
       <div class= "row">
			<div class= "col-md-6">
				<?= $form->field($model, 'enf_cardiovasculares')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
			</div>
			<div class= "col-md-6">
				<?= $form->field($model, 'enf_metabolicos')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
			</div>       
       </div>
       <div class= "row">
           <div class= "col-md-6">
           	    <?= $form->field($model, 'enf_neurologicos')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>
           <div class= "col-md-6">
           		<?= $form->field($model, 'enf_oftalmologicos')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'enf_auditivas')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'traumatismos')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'cirugias')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'infecciones_contagiosas')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'enf_veneras')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'convulsiones')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'otras_patologias')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'alergias')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
           </div>      
       </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>3.- Datos Ginecólogicos</strong></h4>
    </div>
    <div class="panel-body">
      <div class="row">
         <div class="col-md-3">
               <?= $form->field($model, 'ultima_menarquia')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'disabled'=> true,
                                                'size'=>'md',
                                             
                				                'options' => ['placeholder' => '']
                                            ]);?>
         </div>
         <div class="col-md-3">
          <?= $form->field($model, 'partos')->textInput(['type'=> 'number', 'disabled'=> true]) ?>
         </div>
         <div class= "col-md-3">
           <?= $form->field($model, 'cesarea')->textInput(['type'=> 'number', 'disabled' => true]) ?>
         </div>
         <div class="col-md-3">
           <?= $form->field($model, 'abortos')->textInput(['type' => 'number', 'disabled'=> true]) ?>
         </div>
      </div>
      <div class= "row">
         <div class= "col-md-6">
          <?= $form->field($model, 'papanicolau')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
         <div class= "col-md-6">
          <?= $form->field($model, 'mamas')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>4.- Antecedentes Familiares </strong></h4>
    </div>
    <div class="panel-body">
      <div class="row">
         <div class="col-md-12">
           <?= $form->field($model, 'ant_familiar_padres')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
      <div class= "row">
         <div class= "col-md-12">
          <?= $form->field($model, 'ant_familiar_madre')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
       <div class= "row">
         <div class= "col-md-12">
          <?= $form->field($model, 'ant_familiar_otros')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>5.- Exámen Físico General</strong></h4>
    </div>
    <div class="panel-body">
      <div class="row">
         <div class="col-md-6">
           <?= $form->field($model, 'exa_craneo')->textInput(['maxlength' => true, 'disabled' => true]) ?>
         </div>
         <div class="col-md-6">
           <?= $form->field($model, 'exa_toraz_csps')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
      <div class= "row">
         <div class= "col-md-6">
          <?= $form->field($model, 'exa_ojos')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
          <div class= "col-md-6">
          <?= $form->field($model, 'exa_toraz_r1c1')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
      </div>
       <div class= "row">
         <div class= "col-md-6">
          <?= $form->field($model, 'exa_cabidad_oral')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
         <div class= "col-md-6">
          <?= $form->field($model, 'exa_cuello')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
         </div>
       </div>
       <div class="row">
			<div class="col-md-4">
			 <?= $form->field($model, 'exa_abdomen')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
			</div>  
			<div class="col-md-4">
			 <?= $form->field($model, 'exa_genital')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
			</div> 
			<div class= "col-md-4">
			 <?= $form->field($model, 'exa_extremidades')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
			</div>    
       </div>
       <div class= "row">
          <div class= "col-md-12">
           	<?= $form->field($model, 'exames_laboratorio')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
          </div>
       </div>
       <div class= "row">
          <div class= "col-md-12">
          	<?= $form->field($model, 'recomendacion')->textarea(['maxlength' => true, 'disabled'=> true]) ?>
          </div>
       </div>
    </div>
  </div>
  <div class= "row">
     <div class= "col-md-12">
          <div class="form-group">
                <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
          </div>
     </div>
  </div>
<?php ActiveForm::end(); ?>
</div>
