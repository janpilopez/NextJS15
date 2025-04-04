<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\models\SysAdmCargos;
use app\models\SysMedCie10;
use app\models\SysMedPatologiaCategoria;
use app\assets\ConsultaMedicaAsset;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
ConsultaMedicaAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['consulta-medica']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\SysMedConsultaMedica */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="sys-med-consulta-medica-form">
  <?php $form = ActiveForm::begin(); ?>
    <div class = 'panel panel-default'>  
         <div class = 'panel-body'> 
         	 <ul class="nav nav-tabs">
              <li class="active"><a href="#menu1" data-toggle="tab">Evolución Médica</a></li>
              <li><a  data-toggle="tab" href="#menu2">Ficha Médica</a></li>
              <li><a  data-toggle="tab" href="#menu3">Certificados Médico</a></li>
              <li><a  data-toggle="tab" href="#menu4">Historial Médico</a></li>
            </ul>
            <div class="tab-content">
               <br>
            	<div id="menu1" class="tab-pane fade in active">
                	 <div class = 'panel panel-default'>  
                	 	<div class="panel-heading"></div>
             			<div class = 'panel-body'> 
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
                             		   <?= html::textInput('edad',$empleado->getCalcularEdad($empleado->fecha_nacimiento), ['class'=>'form-control input-sm', 'id'=> 'edad', 'readonly'=> true] )?>
                    	       	    </div>
                    	       	 </div>
                    	       </div>
                    	       <div class="col-md-6">
                    	           <div class= "row">
                                       <div class= "col-md-4">
                                     	  <?= $form->field($model, 'pulso')->textInput() ?>
                                       </div>
                                       <div class= "col-md-4">
                                     	  <?= $form->field($model, 'temperatura')->textInput() ?>  
                                       </div>
                                       <div class= "col-md-4">
                                     	  <?= $form->field($model, 'respiracion')->textInput() ?>
                                       </div>
                                       
                                    </div>  
                                    <div class= "row">
                                      <div class= "col-md-4">
                                        <?= $form->field($model, 'pa_max')->textInput() ?>    
                                      </div>
                                     <div class= "col-md-4">
                                     	<?= $form->field($model, 'pa_min')->textInput() ?>
                                     </div>
                                    </div> 
                                    <div class= "row">
                                    	<div class="col-md-12">
                                    		<?= $form->field($model, 'nota_enfermera')->textarea(['maxlength'=> true]) ?>
                                    	</div>
                                    </div>
                                </div>
                    	   </div>
                    	   <div class= "row">
                    	     <div class="col-md-3">
                    	         <?= $form->field($model, 'tipo')->dropDownList(['N'=> 'NO APLICA', 'I'=> 'INCIDENTE', 'A' => 'ACCIDENTE', 'V' => 'NOVEDAD']) ?>
                    	      </div>
                    	       <div class="col-md-2">
                                  <?= $form->field($model, 'recurrencia')->dropDownList(['P'=> 'PRIMARIA', 'S'=> 'SUBSECUENTE']) ?>
                               </div>
                    	      <div class= "col-md-3">
                    	         <?= $form->field($model, 'categoria_patologia')->dropDownList(ArrayHelper::map(SysMedPatologiaCategoria::find()->orderBy(['categoria'=> SORT_ASC])->all(), 'id', 'categoria'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control', 'id'=> 'categoria_patologia', 'options'=>[ $id_categoria_patologia => ['selected' => true]]])?>
                    	      </div>
                    	      <div class= "col-md-4">
                    	        <?=  $form->field($model, 'id_sys_med_patologia')->widget(DepDrop::class,[
                    	            
                    	            'data'=> [$id_patologia => 'id_patologia'],
                    	            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                    	            'pluginOptions'=>[
                    	                'depends'=>['categoria_patologia'],
                    	                'initialize' => true,
                    	                'initDepends' => ['categoria_patologia'],
                    	                'placeholder'=>'Seleccione..',
                    	                'url'=>Url::to(['/consulta-medica/obtenerpatologias'])
                    	             ]
                    	          ]);
                    	        ?>
                               </div>
                              
                    	   </div>
                    	   <div class= "row">
                    	       <div class= "col-md-12">
                    	       		<?= $form->field($model, 'notas_evolucion')->textarea(['rows'=> '3', 'maxlength' => true]) ?>   
                    	       </div>
                    	   </div>
                    	   <div class= "row">
                    	      <div class= "col-md-12">
                    	      	<?= $form->field($model, 'prescripcion')->textarea(['rows'=> '5', 'maxlength' => true]) ?>  
                    	      </div>
                    	   </div>
                    	   <div class= "row">
                    	     <div class= "col-md-12">
                    	      	<div class="form-group">
                                    <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
                                </div>
                    	     </div>
                    	   </div>
                    	</div>
                    </div>   
            	</div>
            	
            	<div id="menu2" class="tab-pane fade">
            	  <div class= "row">
            	   <div class= "col-md-12">
            	   		<div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><strong>1.- Datos Empleado</strong></h4>
                          </div>
                          <div class="panel-body">
                                <div class="row">
                                	 <div class="col-md-6">
                                	   <?=  $form->field($empleado, 'id_sys_adm_cargo')->widget(Select2::classname(), [
                                            //'size' => Select2::SMALL,
                                	        'disabled'=> true,
                                            'data'=>  ArrayHelper::map(SysAdmCargos::find()->select("id_sys_adm_cargo, cargo")->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'),
                                            'options'=> ['placeholder' => 'Seleccione'],
                                            'pluginOptions'=> [
                                            'allowClear'=> true 
                                          ]]);?>
                                	 </div>
                                	  <div class= "col-md-6">
                                	      <?= $form->field($empleado, 'tipo_sangre')->dropDownList([
                                	          'A+'=> 'A+',  
                                	          'A-'=> 'A-', 
                                	          'O+'=> 'O+', 
                                	          'O-'=> 'O-', 
                                	          'B+'=> 'B+', 
                                	          'B-'=> 'B-', 
                                	          'AB+'=> 'AB+',
                                	          'AB-'=>'AB-'], ['class'=>'form-control', 'disabled'=> true])  ?>
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
            	 <?php if ($ficha_medica):?>
            	  <div class= "row">
            	      <div class= "col-md-12">
            	        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><strong>2.- Antecedentes Clínicos</strong></h4>
                            </div>
                          <div class="panel-body">
                               <div class= "row">
                        			<div class= "col-md-6">
                        				<?= $form->field($ficha_medica, 'enf_cardiovasculares')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                        			</div>
                        			<div class= "col-md-6">
                        				<?= $form->field($ficha_medica, 'enf_metabolicos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                        			</div>       
                               </div>
                               <div class= "row">
                                   <div class= "col-md-6">
                                   	    <?= $form->field($ficha_medica, 'enf_neurologicos')->textarea(['maxlength' => true, 'rows' => 3]) ?>
                                   </div>
                                   <div class= "col-md-6">
                                   		<?= $form->field($ficha_medica, 'enf_oftalmologicos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'enf_auditivas')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'traumatismos')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'cirugias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'infecciones_contagiosas')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'enf_veneras')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'convulsiones')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'otras_patologias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'alergias')->textarea(['maxlength' => true, 'rows'=> 3]) ?>
                                   </div>      
                               </div>
                            </div>
            	        </div>
            	      </div>
            	  </div>
            	  <div class= "row">
            	      <div class= "col-md-12">
            	        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><strong>3.- Antecedentes Familiares</strong></h4>
                            </div>
                          <div class="panel-body">
                          	  <div class="row">
                                 <div class="col-md-12">
                                   <?= $form->field($ficha_medica, 'ant_familiar_padres')->textarea(['maxlength' => true]) ?>
                                 </div>
                              </div>
                              <div class= "row">
                                 <div class= "col-md-12">
                                  <?= $form->field($ficha_medica, 'ant_familiar_madre')->textarea(['maxlength' => true]) ?>
                                 </div>
                              </div>
                               <div class= "row">
                                 <div class= "col-md-12">
                                  <?= $form->field($ficha_medica, 'ant_familiar_otros')->textarea(['maxlength' => true]) ?>
                                 </div>
                              </div>
                          </div>
            	       </div>
            	      </div>
            	  </div>
            	<?php endif;?>  
            	</div>
            	
                <div id="menu3" class="tab-pane fade">
                   <div class= "row">
                       <div class= "col-md-12">
                       	   <div class="panel panel-default">
                              <div class="panel-heading"></div>
                              <div class="panel-body">
                              		<table  class="table table-bordered table-condensed">
                               			<thead>
                                   		  <tr>
                                   		      <th>No</th>
                                   		      <th>Entidad Emisora</th>
                                   		      <th>Tiempo</th>
                                   		      <th>Tipo Ausentismo</th>
                                   		      <th>Desde</th>
                                   		      <th>Hasta</th>
                                   		      <th>Diagnóstico</th>
                                   		  </tr>
                               		 	</thead>
                               		 	<tbody>
                               		 	   <?php  foreach ($certificados as $index => $item): ?>
                               		 		 <tr>
                               		 		    <td><?= $index + 1 ?></td>
                               		 		    <td><?= $item['entidad_emisora'] ?></td>
                               		 		    <td><?= $item['tipo'] ?></td>
                               		 		    <td><?= $item['tipo_ausentismo']?></td>
                               		 		    <td><?= $item['fecha_ini']?></td>
                               		 		    <td><?= $item['fecha_fin']?></td>
                               		 		    <td><?= $item['diagnostico']?></td>
                               		 		 </tr>
                               		       <?php endforeach; ?>
                               		 	</tbody>
                               		</table>
                                </div>
                       		</div>
                   		</div>
                     </div>
                 </div>
                 
                 <div id="menu4" class="tab-pane fade">
                   <div class= "row">
                       <div class= "col-md-12">
                       	   <div class="panel panel-default">
                              <div class="panel-heading"></div>
                              <div class="panel-body">
                              		<table  class="table table-bordered table-condensed">
                               			<thead>
                                   		   <tr>
                                   		      <th># Consulta</th>
                                   		      <th>Fecha Atención</th>
                                   		      <th>Hora Atención</th>
                                   		      <th>Diagnóstico</th>
                                   		      <th>Ver</th>
                                   		   </tr>
                               		 	</thead>
                               		 	<tbody>
                               		 	   <?php  foreach ($historial_medico as $index => $item): ?>
                               		 		 <tr>
                               		 		    <td><?= $item['numero'] ?></td>
                               		 		    <td><?= $item['fecha_consulta'] ?></td>
                               		 		    <td><?= date("H:i:s", strtotime($item['hora_consulta']))?></td>
                               		 		    <td><?= $item['patologia']?></td>
                               		 		    <td><?=  Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view','id'=> $item["id"]], ['class'=>'btn btn-xs btn-info', "target" => "_blank" ]);?></td>
                               		 		 </tr>
                               		       <?php endforeach; ?>
                               		 	</tbody>
                               		</table>
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
