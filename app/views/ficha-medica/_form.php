<?php

use app\models\SysAdmCargos;
use app\models\SysPaises;
use app\models\SysRrhhEmpleados;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\FichaMedicaAsset;
FichaMedicaAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaMedica */
/* @var $form yii\widgets\ActiveForm */
$nombres = '';
if(!$model->isNewRecord){
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
}
?>

<div class="sys-med-ficha-medica-form">    
 <?php $form = ActiveForm::begin(['id'=> 'form']); ?>
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
                    	      <?= $form->field($empleado, 'tipo_sangre')->dropDownList([
                    	          'A+'=> 'A+',  
                    	          'A-'=> 'A-', 
                    	          'O+'=> 'O+', 
                    	          'O-'=> 'O-', 
                    	          'B+'=> 'B+', 
                    	          'B-'=> 'B-', 
                    	          'AB+'=> 'AB+', 
                    	          'AB-'=>'AB-'], 
                    	          ['class'=>'form-control', 
                    	          'disabled'=> true])  ?>
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
      	 <div class = "row">
      	   <div class= 'col-md-6'>
                 <?= $form->field($empleado, 'formacion_academica')->dropDownList(['P'=> 'PRIMARIA', 'S'=> 'SECUNDARIA', 'T'=> 'TERCER NIVEL', 'C'=> 'CUARTO NIVEL'],['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control', 'disabled'=> true]) ?>
           </div>
           <div class= 'col-md-6'>
                 <?= $form->field($empleado, 'titulo_academico')->textInput(['maxlength' => true, 'class'=> 'form-control', 'placeholder'=> 'Título Acádemico', 'disabled'=> true]) ?>
            </div>
      	 </div>
      	 <div class = "row">
      	   <div class= "col-md-6">
                <?= $form->field($empleado, 'telefono')->textInput(['maxlenght'=> true, 'class'=>'form-control', 'disabled'=> true, 'placeholder'=> 'Teléfono'])?>
            </div>
            <div class= "col-md-6">
                 <?= $form->field($empleado, 'celular')->textInput(['maxlenght'=> true, 'class'=>'form-control', 'disabled'=> true, 'placeholder'=> 'Celular'])?>
             </div>
      	 </div>
      	 <div class = "row">
      	    <div class= 'col-md-3'>
               <?= $form->field($empleado, 'pais')->dropDownList(ArrayHelper::map(SysPaises::find()->all(), 'id_sys_pais', 'pais'), [ 'value'=> $empleado->getObtenerpais(),'prompt'=> 'seleccione..' ,'class'=> 'form-control', 'id'=>'pais', 'disabled'=> true] )?>
            </div>
		    <div class= 'col-md-3'>
                <label>Provincia</label>
                <?= $form->field($empleado, 'provincia')->widget(DepDrop::classname(), [
                    'data' => [ $empleado->obtenerprovincia=> 'provincia'],
                     'options'=>['id'=>'provincia', 'class'=> 'form-control', 'disabled'=> true],
                     'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                     'pluginOptions'=>[
                           'depends'=>['pais'],
                           'initialize' => true,
                           'initDepends' => ['pais'], 
                           'placeholder'=>'Select...',
                           'url'=>Url::to(['/consultas/listeprovincias']),
                         
                     ]])->label(false)?>      
            </div>
            <div class= 'col-md-3'>
                             <label>Cantón</label>
                             <?= $form->field($empleado, 'canton')->widget(DepDrop::classname(), [
                                 'data' => [$empleado->getObtenercanto() => 'provincia'],
                                    'options'=>[ 'id'=> 'canton','class'=> 'form-control', 'disabled'=> true],
                                    'pluginOptions'=>[
                                        'depends'=>['provincia'],
                                        'initialize' => true,
                                        'initDepends' => ['provincia'], 
                                        'placeholder'=>'Select...',
                                        'url'=>Url::to(['/consultas/listcantones']),
                                        
                                    ]])->label(false)?>
            </div>
            <div class= 'col-md-3'>
                <label>Parroquia</label>
                 <?= $form->field($empleado, 'id_sys_parroquia')->widget(DepDrop::classname(), [
                'data' => [$empleado->id_sys_parroquia => 'provincia'],
                'options'=>['class'=> 'form-control', 'disabled'=> true],
                'pluginOptions'=>[
                'depends'=>['canton'],
                'initialize' => true,
                'initDepends' => ['canton'], 
                'placeholder'=>'Select...',
                'url'=>Url::to(['/consultas/listparroquias'])
             ]])->label(false)?>
           </div>
         </div>
      	 <div class="row">
      	   <div class="col-md-12">
      	      <?= $form->field($empleado, 'direccion')->textarea(['maxlenght'=> true, 'class'=> 'form-control', 'placeholder'=> 'Dirección', 'rows'=> 1, 'disabled'=> true]) ?>
      	   </div>
      	 </div>
      	 <div class= "row">
      	  <div class="col-md-3">
                <?= $form->field($empleado, 'fecha_nacimiento')->textInput(['class'=>'form-control', 'readonly'=> true]) ?>
             </div>
            <div class="col-md-3">
               <label>Fecha Ingreso</label>
               <?= html::textInput('fecha_ingreso', $contrato[0]['fecha_ingreso'], ['class'=>'form-control',  'readonly'=> true] )?>
             </div>
             <div class="col-md-6"> 
                <label>Años de Labor</label>
                <?= html::textInput('anio_laborado',$contrato[0]['anios'], ['class'=>'form-control',  'readonly'=> true] )?>
             </div>
         </div>
         <div class = "row">     
        	<div class="col-md-12">
        	 <?php if(count($nucleofamiliar) > 0):?>
        	    <br>
        	    <label>Núcleo Familiar</label>
        		<table  class="table table-bordered table-condensed" style="width:100%; background-color: white; font-size: 14px;">
        		   <thead>
        		      <tr>
        		        <th>Nombres</th>
        		        <th>Parentesco</th>
        		        <th>Edad</th>
        		        <th>Discapacidad</th>
        		      </tr>
        		   </thead>
        		   <tbody>
        		   <?php foreach($nucleofamiliar as $index => $dato){ ?>
        		     <tr>
        		        <td><?= $dato['nombres']?></td>
        		        <td><?= $dato['parentesco']== 'C' ? 'CÓNYUGE': 'HIJO(A)'?></td>
        		        <td><?= $dato['anios']?></td>
        		        <td><?= $dato['discapacidad']== 'S' ? 'SI': 'NO'?></td>
        		     <tr>
        		   <?php }?>
        		   </tbody>
                </table>
               <?php endif;?> 		
            </div>
        </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>1.- Antecedentes Clínicos</strong></h4>
    </div>
    <div class="panel-body">
       <div class="row">
          <div class="col-md-2">
          	<?= $form->field($model, 'talla')->textInput(['type' => 'number', 'class'=> 'form-control', 'id'=> 'talla']) ?>
          </div>
          <div class="col-md-2">
            <?= $form->field($model, 'peso')->textInput(['type' => 'number', 'class' => 'form-control', 'id' => 'peso']) ?>
          </div>
          <div class="col-md-8">
            <label>Imc</label>
            <?= Html::textInput('imc', '', ['class'=>' form-control', 'disabled' => true, 'id'=> 'imc'])?>
          </div>
       </div>
       <div class= "row">
			<div class= "col-md-6">
				<?= $form->field($model, 'enf_cardiovasculares')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
			</div>
			<div class= "col-md-6">
				<?= $form->field($model, 'enf_metabolicos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
			</div>       
       </div>
       <div class= "row">
           <div class= "col-md-6">
           	    <?= $form->field($model, 'enf_neurologicos')->textarea(['maxlength' => true, 'rows' => 3]) ?>
           </div>
           <div class= "col-md-6">
           		<?= $form->field($model, 'enf_oftalmologicos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'enf_auditivas')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'traumatismos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'cirugias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'infecciones_contagiosas')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'enf_veneras')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'convulsiones')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>      
       </div>
       <div class="row">
           <div clasS= "col-md-6">
             <?= $form->field($model, 'otras_patologias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>   
            <div clasS= "col-md-6">
             <?= $form->field($model, 'alergias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
           </div>      
       </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><strong>2.- Antecedentes Familiares </strong></h4>
    </div>
    <div class="panel-body">
      <div class="row">
         <div class="col-md-12">
           <?= $form->field($model, 'ant_familiar_padres')->textarea(['maxlength' => true]) ?>
         </div>
      </div>
      <div class= "row">
         <div class= "col-md-12">
          <?= $form->field($model, 'ant_familiar_madre')->textarea(['maxlength' => true]) ?>
         </div>
      </div>
       <div class= "row">
         <div class= "col-md-12">
          <?= $form->field($model, 'ant_familiar_otros')->textarea(['maxlength' => true]) ?>
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
