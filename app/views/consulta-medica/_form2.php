<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\ConsultaMedicaAsset;
use app\models\SysAdmCargos;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
use yii\web\View;
use app\models\SysMedPatologiaCategoria;
ConsultaMedicaAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['consulta-medica']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
$img =  file_get_contents('img/sin_foto.jpg');
$data =  base64_encode($img);
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
              <li><a  data-toggle="tab" href="#menu3">Certificados Médicos</a></li>
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
                                                 echo  Html::img('data:image/jpeg;base64, '.$data, ['style'=>"width:130px;height:130px;border-radius: 10px", 'id'=>'foto']);
                            	       	      ?>
                            	       	    </div>
                        	       	    	<div class= "col-md-9">
                        	       	          <?php   $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                                                           '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                                                       
                                                       echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                                                           'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control'],
                                                           'pluginOptions' => ['highlight'=>true],
                                                           'scrollable'=>true,
                                                           'dataset' => [
                                                               [
                                                                   
                                                                   'remote' => [
                                                                       'url' =>    Url::to(['consultas/listempleados2']) . '?q=%QUERY',
                                                                       'wildcard' => '%QUERY'
                                                                   ],
                                                                   'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                                                   'display' => 'value',
                                                                   'templates' => [
                                                                       'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                                                       'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                                                       ],
                                                                      
                                                               ]
                                                              
                                                               ],'pluginEvents' => [
                                                                   'typeahead:select' => 'function(ev, suggestion) {
                                                                       $("#nombres").val(suggestion.nombres);
    
                                                                           $.get(url+"/datosconsulta?id_sys_rrhh_cedula="+suggestion.value, function (data) {
        				           
                                                                              if(data.length  > 1){
    
                                                                                   let today   = new Date();
                                                                                   let edad    = 0;
                                                                            	   result      = jQuery.parseJSON(data);	        	
                                                                            	   let array   = result.empleados["fecha_nacimiento"].split("-");
                                                                                   let anio    = parseInt(array[0]);
                                                                                   let mes     = parseInt(array[1]);
                                                                            	   let dia     = parseInt(array[1]);
                                                                                 
                                                                                 
                                                                                   edad =  today.getFullYear() - anio;
                                                                                   if(mes > today.getMonth()){
    
                                                                                      edad--;
    
                                                                                    }else if(mes == today.getMonth() && dia >= today.getDay()){
    
                                                                                       edad--;
                                                                                    }
                                                                                    
                                                                                  $("#edad").val(edad+" Años");
    
                                                                                  var  foto  = document.getElementById("foto");
    		                                                                      foto.src = result.foto;
                                                                                  
                                                                                  document.getElementById("id_cargo").value = result.empleados.id_sys_adm_cargo;
                                                                                  document.getElementById("tipo_sangre").value = result.empleados.tipo_sangre;
                                                                                  document.getElementById("genero").value = result.empleados.genero;
                                                                                  document.getElementById("estado_civil").value = result.empleados.estado_civil;
                                                                                  document.getElementById("discapacidad").value = result.empleados.discapacidad;
                                                                                  document.getElementById("tipo_discapacidad").value = result.empleados.tipo_discapacidad;
                                                                                  document.getElementById("por_discapacidad").value= result.empleados.por_discapacidad;
                                                                                  document.getElementById("ide_discapacidad").value = result.empleados.ide_discapacidad;
    
    
                                                                                  //FichaMedica
                                                                                  if(result.ficha_medica != false){
                                                                                   
                                                                                      document.getElementById("enf_cardiovasculares").value = result.ficha_medica.enf_cardiovasculares;
                                                                                      document.getElementById("enf_metabolicos").value = result.ficha_medica.enf_metabolicos;
                                                                                      document.getElementById("enf_neurologicos").value = result.ficha_medica.enf_neurologicos;
                                                                                      document.getElementById("enf_oftalmologicos").value = result.ficha_medica.enf_oftalmologicos;
                                                                                      document.getElementById("enf_auditivas").value = result.ficha_medica.enf_auditivas;
                                                                                      document.getElementById("traumatismos").value= result.ficha_medica.traumatismos;
                                                                                      document.getElementById("cirugias").value = result.ficha_medica.cirugias;
                                                                                      document.getElementById("infecciones_contagiosas").value = result.ficha_medica.infecciones_contagiosas;
                                                                                      document.getElementById("enf_veneras").value = result.ficha_medica.enf_veneras;
                                                                                      document.getElementById("convulsiones").value = result.ficha_medica.convulsiones;
                                                                                      document.getElementById("otras_patologias").value = result.ficha_medica.otras_patologias;
                                                                                      document.getElementById("alergias").value = result.ficha_medica.alergias;
                                                                                      document.getElementById("ant_familiar_padres").value = result.ficha_medica.ant_familiar_padres;
                                                                                      document.getElementById("ant_familiar_madre").value = result.ficha_medica.ant_familiar_madre;
                                                                                      document.getElementById("ant_familiar_otros").value = result.ficha_medica.ant_familiar_otros;
    
      
                                                                                  }
                                                                                  
                                                                                  //Certificados Médicos 
                                                                                  if(result.certificados.length > 0){
                                                                                       
                                                                                      
    
                                                                                       let tabla = document.querySelector("#table > tbody");
                                                                                       tabla.innerHTML = ""
                                                                                       result.certificados.forEach(function(item, index){
                                                                                          
                                                                                             var entidadEmisora = "";                                                                             
                                                                                             var tr = document.createElement("tr");
    
                 	     	                                                                 var td0 = document.createElement("td"); 
                                                                                             td0.innerHTML = index +1;
                                                                                             tr.appendChild(td0);
    
                                                                                             var td1 = document.createElement("td"); 
                                                                                             td1.innerHTML = item["entidad_emisora"];
                                                                                             tr.appendChild(td1);
    
                                                                                             var td2 = document.createElement("td"); 
                                                                                             td2.innerHTML = item["tipo"];
                                                                                             tr.appendChild(td2);
    
                                                                                             var td3 = document.createElement("td"); 
                                                                                             td3.innerHTML = item["tipo_ausentismo"];
                                                                                             tr.appendChild(td3);
    
                                                                                             var td4 = document.createElement("td"); 
                                                                                             td4.innerHTML = item["fecha_ini"];
                                                                                             tr.appendChild(td4);
    
                                                                                             var td5 = document.createElement("td"); 
                                                                                             td5.innerHTML = item["fecha_fin"];
                                                                                             tr.appendChild(td5);
    
                                                                                             var td6 = document.createElement("td"); 
                                                                                             td6.innerHTML = item["diagnostico"];
                                                                                             tr.appendChild(td6);
    
                                                                                             tabla.appendChild(tr);
                                                            
                                                                                             
     	     	        	    
                                                                                      });
    
                                                                                  }
                                                                                  //Historial de atenciones 
                                                                                  if(result.historial_atenciones.length > 0 ){

                                                                                      
 
                                                                                       let tableHistorial = document.querySelector("#table_historial > tbody");
                                                                                       tableHistorial.innerHTML = "";
                                                                                       result.historial_atenciones.forEach(function(item, index){
                                                                                            
                                                                                             var tr = document.createElement("tr"); 
                    
                                                                                             var td0 = document.createElement("td"); 
                                                                                             td0.innerHTML = item["numero"];
                                                                                             tr.appendChild(td0);
    
                                                                                             var td1 = document.createElement("td"); 
                                                                                             td1.innerHTML = item["fecha_consulta"];
                                                                                             tr.appendChild(td1);
    
                                                                                             var td2 = document.createElement("td"); 
                                                                                             td2.innerHTML = item["hora_consulta"];
                                                                                             tr.appendChild(td2);
    
                                                                                             var td3 = document.createElement("td"); 
                                                                                             td3.innerHTML = item["patologia"];
                                                                                             tr.appendChild(td3);
    
                                                                                             var td4 = document.createElement("td"); 

                                                                                             let a  = document.createElement("a");
                                                                                             let span = document.createElement("span");

                                                                                             span.setAttribute("class", "glyphicon glyphicon-eye-open");
                                                                                             a.appendChild(span)

                                                                                             a.href = url+"/view?id="+item["id"];
                                                                                             a.setAttribute("class", "btn btn-xs btn-info");
                                                                                             a.setAttribute("target", "_blank");

             	     	                                                                     td4.appendChild(a);



                                                                                             tr.appendChild(td4);
    
                                                                                             tableHistorial.appendChild(tr);

                                                                                       });





                                                                                  }
                                                                                 
                                                                               
                                                                               }else{
                                                                            				    	
                                                                            	 alert("Ha ocurrido un error. Comuniquese con su Administrador!")
                                                                            	 return false;
                                                                               }
                                                                          });
                                                                       
                                                                      }',
                                                               ]
                                                               
                                                            
                                                       ])->label('Cedula');?>
                        	       	       <?= $form->field($model, 'nombres')->textInput(['disabled'=> true, 'id'=> 'nombres']) ?>
                        	       	       <label>Edad:</label>
                                           <?= html::textInput('edad','', ['class'=>'form-control', 'id'=> 'edad', 'readonly'=> true] )?>
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
                                      <div class= "col-md-4">
                                     	<?= $form->field($model, 'tipo_atencion')->dropDownList([1 => 'JEFATURA', 3 => 'EMERGENCIA'], ['class'=>'form-control']) ?>
                                     </div>
                                    </div> 
                                    <div class= "row">
                                    	<div class="col-md-12">
                                    		<?= $form->field($model, 'nota_enfermera')->textarea(['maxlength'=> true]) ?>
                                    	</div>
                                    </div>
                                </div>
                    	   </div>
                    	   <div class="row">
                    	      <div class="col-md-3">
                    	         <?= $form->field($model, 'tipo')->dropDownList(['N'=> 'NO APLICA', 'I'=> 'INCIDENTE', 'A' => 'ACCIDENTE','V' => 'NOVEDAD']) ?>
                    	      </div>
                    	      <div class="col-md-2">
                                  <?= $form->field($model, 'recurrencia')->dropDownList(['P'=> 'PRIMARIA', 'S'=> 'SUBSECUENTE']) ?>
                               </div>
                    	      <div class= "col-md-3">
                    	         <?= $form->field($model, 'categoria_patologia')->dropDownList(ArrayHelper::map(SysMedPatologiaCategoria::find()->orderBy(['categoria'=> SORT_ASC])->all(), 'id', 'categoria'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control', 'id'=> 'categoria_patologia'])?>
                    	      </div>
                    	      <div class= "col-md-4">
                    	         <?php echo '<label>Patología</label>';
                                     echo DepDrop::widget([
                                       'name'=> 'id_sys_med_patologia',
                                       'options'=>['id'=>'id_sys_med_patologia', 'class'=> 'form-control'],
                                       'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                       'pluginOptions'=>[
                                           'depends'=>['categoria_patologia'],
                                           'initialize' => true,
                                           'initDepends' => ['categoria_patologia'],
                                           'placeholder'=>'Seleccione..',
                                           'url'=>Url::to(['/consulta-medica/obtenerpatologias']),
                                           
                                       ]
                                   ]);?>
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
                                         <?= $form->field($empleado, 'id_sys_adm_cargo')->dropDownList(ArrayHelper::map(SysAdmCargos::find()->select("id_sys_adm_cargo, cargo")->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'), ['class'=>'form-control', 'disabled'=> true, 'id'=> 'id_cargo'])  ?>
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
                                	          'AB-'=>'AB-'], ['class'=>'form-control', 'disabled'=> true, 'id'=> 'tipo_sangre'])  ?>
                                	  </div>
                                  </div>
                                  <div class= "row">
                                	  <div class= "col-md-4">
                                	     <?= $form->field($empleado, 'genero')->dropDownList(['M'=> 'MASCULINO',  'F'=> 'FEMENINO'], ['class'=>'form-control', 'disabled'=> true, 'id'=> 'genero'])  ?>
                                	  </div>
                                	  <div class= "col-md-4">
                                	     <?= $form->field($empleado, 'estado_civil')->dropDownList(['S'=> 'Soltero',  'C'=> 'Casado', 'U'=> 'Unido', 'D'=> 'Divorciado'], ['class'=>'form-control', 'disabled'=> true, 'id'=>'estado_civil'])  ?>
                                	  </div>
                                	 <div class= "col-md-4">
                                	     <?= $form->field($empleado, 'discapacidad')->dropDownList(['N'=> 'NO', 'S'=> 'SI'], ['maxlenght'=> true, 'class'=> 'form-control', 'disabled'=> true, 'id'=> 'discapacidad']) ?>
                                	 </div>
                                 </div>
                                 <div class= "row">
                                	 <div class= 'col-md-4'>
                                       <?= $form->field($empleado, 'tipo_discapacidad')->dropDownList(['F'=> 'Fisica', 'C'=> 'Cognitiva', 'S'=> 'Sensorial', 'I'=> 'Intelectual', 'P'=> 'Psicologica', 'V'=> 'Visual'], ['prompt'=> 'seleccione..','maxlenght'=> true, 'class'=> 'form-control', 'disabled'=> true, 'id'=> 'tipo_discapacidad']) ?>
                                     </div>
                                     <div class= 'col-md-4'>
                                       <?= $form->field($empleado, 'por_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control', 'placeholder'=> '0 - 100 %', 'disabled'=> true, 'id'=> 'por_discapacidad']) ?>
                                     </div>
                                     <div class= 'col-md-4'>
                                       <label># Carnét</label>
                                       <?= $form->field($empleado, 'ide_discapacidad')->textInput(['maxlength' => true, 'class'=> 'form-control', 'placeholder'=> '', 'disabled'=> true, 'id' =>'ide_discapacidad'])->label(false) ?>
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
                              <h4 class="panel-title"><strong>2.- Antecedentes Clínicos</strong></h4>
                            </div>
                          <div class="panel-body">
                               <div class= "row">
                        			<div class= "col-md-6">
                        				<?= $form->field($ficha_medica, 'enf_cardiovasculares')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'enf_cardiovasculares']) ?>
                        			</div>
                        			<div class= "col-md-6">
                        				<?= $form->field($ficha_medica, 'enf_metabolicos')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'enf_metabolicos']) ?>
                        			</div>       
                               </div>
                               <div class= "row">
                                   <div class= "col-md-6">
                                   	    <?= $form->field($ficha_medica, 'enf_neurologicos')->textarea(['maxlength' => true, 'rows' => 3, 'id' => 'enf_neurologicos']) ?>
                                   </div>
                                   <div class= "col-md-6">
                                   		<?= $form->field($ficha_medica, 'enf_oftalmologicos')->textarea(['maxlength' => true, 'rows'=> 3, 'id' => 'enf_oftalmologicos']) ?>
                                   </div>
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'enf_auditivas')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=>'enf_auditivas']) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'traumatismos')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'traumatismos']) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'cirugias')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'cirugias']) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'infecciones_contagiosas')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'infecciones_contagiosas']) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'enf_veneras')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'enf_veneras']) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'convulsiones')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'convulsiones']) ?>
                                   </div>      
                               </div>
                               <div class="row">
                                   <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'otras_patologias')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'otras_patologias']) ?>
                                   </div>   
                                    <div clasS= "col-md-6">
                                     <?= $form->field($ficha_medica, 'alergias')->textarea(['maxlength' => true, 'rows'=> 3, 'id'=> 'alergias']) ?>
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
                                   <?= $form->field($ficha_medica, 'ant_familiar_padres')->textarea(['maxlength' => true, 'id'=> 'ant_familiar_padres']) ?>
                                 </div>
                              </div>
                              <div class= "row">
                                 <div class= "col-md-12">
                                  <?= $form->field($ficha_medica, 'ant_familiar_madre')->textarea(['maxlength' => true, 'id' => 'ant_familiar_madre']) ?>
                                 </div>
                              </div>
                               <div class= "row">
                                 <div class= "col-md-12">
                                  <?= $form->field($ficha_medica, 'ant_familiar_otros')->textarea(['maxlength' => true, 'id'=> 'ant_familiar_otros']) ?>
                                 </div>
                              </div>
                          </div>
            	       </div>
            	      </div>
            	  </div>
               </div>
   				
   				<div id="menu3" class="tab-pane fade">
               		<div class= "row">
               			<div class= "col-md-12">
               			   	   <div class="panel panel-default">
                              <div class="panel-heading"></div>
                              <div class="panel-body">
                              		<table  class="table table-bordered table-condensed" id="table">
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
                              		<table  class="table table-bordered table-condensed" id="table_historial">
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
