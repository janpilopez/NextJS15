<?php

use app\models\SysAdmAreas;
use app\models\SysRrhhEmpleados;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use kartik\typeahead\Typeahead;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\SysSsooIncidente */
/* @var $form yii\widgets\ActiveForm */
$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;

$url = Yii::$app->urlManager->createUrl(['incidente']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
?>
<div class="sys-ssoo-incidente-form">

    <?php $form = ActiveForm::begin(); ?>

   <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>1.- Datos Generales</strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
               <div class= 'col-md-3'>
               	      <?php   $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                                                           '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                                                       
                                                       echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                                                           'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control'],
                                                           'pluginOptions' => ['highlight'=>true],
                                                           'scrollable'=>true,
                                                           'dataset' => [
                                                               [
                                                                   
                                                                   'remote' => [
                                                                       'url' =>    Url::to(['incidente/obtenerempleado']) . '?q=%QUERY',
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
                                                                       
                                                                     }',
                                                               ]
                                                               
                                                            
                                                       ])->label('Identificación');?>
               </div>
               <div class = 'col-md-7'>
                     <?= Html::label('Nombres')?>
                     <?= Html::textInput('nombres', $nombres, ['class'=> 'form-control', 'id'=> 'nombres', 'disabled'=> true])?>
               </div>
               <div class = 'col-md-2'>
                  <?= $form->field($model, 'secuencial')->textInput(['maxlength' => true, 'disabled'=> true]) ?>
               </div>
         </div>
         <div class = 'row'>
            <div class = 'col-md-4'>
               	      <?php   $template = '<div style="font-size:10px;"><div class="repo-language">Consulta No: {{numero}}</div>' .
                                                           '';
                                                       
                                                       echo $form->field($model, 'numero_consulta')->widget(Typeahead::classname(), [
                                                           'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control'],
                                                           'pluginOptions' => ['highlight'=>true],
                                                           'scrollable'=>true,
                                                           'dataset' => [
                                                               [
                                                                   
                                                                   'remote' => [
                                                                       'url' =>    Url::to(['incidente/obtenerconsulta']) . '?q=%QUERY',
                                                                       'wildcard' => '%QUERY'
                                                                   ],
                                                                   'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('numero')",
                                                                   'display' => 'value',
                                                                   'templates' => [
                                                                       'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                                                       'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                                                       ],
                                                                      
                                                               ]
                                                              
                                                               ],'pluginEvents' => [
                                                                   'typeahead:select' => 'function(ev, suggestion) {
                                                                       $("#id_sys_med_consulta_medica").val(suggestion.value);
                                                                  
                                                                            $.get(url+"/obtenerprescripcionmedica?id_consulta_medica="+suggestion.value, function (data) {
                                                                                  
                                                                                    if(data.length  > 1){

                                                                                         var result = jQuery.parseJSON(data);
                                                                                         document.getElementById("correcion").value = result[0].prescripcion;
                                                                                    }
                                                                            });
                                                                   }',
                                                               ]
                                                               
                                                            
                                                       ])->label('Número Consulta');?>
                                                       
              <?= $form->field($model, 'id_sys_med_consulta_medica')->textInput(['type'=> 'hidden', 'id'=>'id_sys_med_consulta_medica'])->label(false) ?>
            </div>
         	<div class = 'col-md-4'>
         	   <?= $form->field($model, 'turno')->dropDownList(['M'=> 'Matutino', 'V'=> 'Vespertino', 'N' => 'Nocturno']) ?>
         	</div>
         	<div class = 'col-md-4'>
         	     <?= $form->field($model, 'fecha')->widget(DateTimePicker::className(),[
            	        'options' => [
            	           
            	            'pluginOptions' => [
            	                //'autoclose'=>true,
            	                'format' => 'yyyy/mm/dd hh:ii:ss'
                            ]
            	        ],
            	        
            	    ]) ?>
         	</div>
         </div>
         <div class = 'row'>
         	<div class = 'col-md-4'>
         	   <?= $form->field($model, 'id_sys_adm_area')->dropDownList(ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'seleccione..']) ?>
         	</div>
            <div class = 'col-md-4'>
              <?= $form->field($model, 'lugar')->textInput() ?>
            </div>
             <div class = 'col-md-4'>
             <?= $form->field($model, 'puesto_trabajo')->textInput(['maxlength' => true]) ?>
            </div>
         </div>
      </div>
    </div>
    
   <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>2.- El Incidente Produjo</strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
              <div class= 'col-md-6'>
                <?= $form->field($model, 'lesion_corporal')->textInput(['maxlength' => true]) ?>
              </div>
              <div class= 'col-md-6'>
               <?= $form->field($model, 'danio_maquinaria')->textInput(['maxlength' => true]) ?>
              </div>
         </div>
         <div class= 'row'>
             <div class= 'col-md-6'>
              <?= $form->field($model, 'danio_instalaciones')->textInput(['maxlength' => true]) ?>
             </div>
             <div class = 'col-md-6'>
              <?= $form->field($model, 'danio_epp')->textInput(['maxlength' => true]) ?>
             </div>
         </div>
         <div class = 'row'>
            <div class ='col-md-12'>
             <?= $form->field($model, 'observacion')->textInput(['maxlength' => true]) ?>
            </div>
         </div>
      </div>
    </div>
    
   <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>3.- Descripción del Incidente</strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
            <div class= 'col-md-12'>
               <?= $form->field($model, 'descripcion_incidente')->textarea(['maxlength' => true, 'rows' => 6])->label(false) ?>
            </div>
         </div>
      </div>
    </div>
    
    <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>4.- Análisis del problema </strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
           <div class = 'col-md-12'>
             <?= $form->field($model, 'analisis_problema')->textarea(['maxlength' => true, 'rows' => 3])->label(false) ?>
           </div>
         </div>
      </div>
    </div>
    
    <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong> 6.- Corrección </strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
             <div class ='col-md-12'>
          	<?= $form->field($model, 'correcion')->textarea(['maxlength' => true, 'rows' => 3, 'id'=> 'correcion'])->label(false) ?>
             </div>
         </div>
      </div>
    </div>
    
    <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>7.- Acción Preventiva</strong></div>
          <div class = 'panel-body'>
                 <div class= 'row'>
                     <div class = 'col-md-12'>
                  		<?= $form->field($model, 'accion_preventiva')->textarea(['maxlength' => true, 'rows' => 3])->label(false) ?>
                     </div>
                 </div>
          </div>
    </div>
    
    <div class = 'panel panel-default'>  
      <div class="panel-heading"><strong>8.- Notifica el Incidente</strong></div>
      <div class = 'panel-body'> 
         <div class= 'row'>
         	<div class = 'col-md-6'>
         	 <?= $form->field($model, 'notifica_incidente_nombre')->textInput(['maxlength' => true]) ?>
         	</div>
         	<div class = 'col-md-6'>
         	  <?= $form->field($model, 'notifica_incidente_cargo')->textInput(['maxlength' => true]) ?>
         	</div>
         </div>
      </div>
    </div>
    
    <div class= 'panel panel-default'>
       <div class= 'panel-heading'><strong>9.- Adjunto</strong>
       <div class = 'panel-body'>
          <div class = 'row'>
              <div class = 'col-md-12'>
                 <?=
                 $form->field($model, 'file')->widget(FileInput::classname(), [
                     'options' => ['accept' => 'image/*'],
                     'pluginOptions' => [
                        
                     ]
                 ])->label(false);
                 ?>
              </div>
          </div>
       </div>
       </div>
    </div>
    
   
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
