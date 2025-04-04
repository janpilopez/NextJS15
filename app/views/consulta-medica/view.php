<?php

use app\models\SysMedCie10;
use app\models\SysMedPatologiaCategoria;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedConsultaMedica */

$this->title = 'Consulta Médica # '.$model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Consulta Médica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-consulta-medica-view">
 <?php $form = ActiveForm::begin(); ?>
    <h1><?= Html::encode($this->title) ?></h1>
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
                                     	  <?= $form->field($model, 'pulso')->textInput(['disabled'=> true]) ?>
                                       </div>
                                       <div class= "col-md-4">
                                     	  <?= $form->field($model, 'temperatura')->textInput(['disabled'=> true]) ?>  
                                       </div>
                                       <div class= "col-md-4">
                                     	  <?= $form->field($model, 'respiracion')->textInput(['disabled'=> true]) ?>
                                       </div>
                                    </div>  
                                    <div class= "row">
                                      <div class= "col-md-4">
                                        <?= $form->field($model, 'pa_max')->textInput(['disabled'=> true]) ?>    
                                      </div>
                                     <div class= "col-md-4">
                                     	<?= $form->field($model, 'pa_min')->textInput(['disabled'=> true]) ?>
                                     </div>
                                    </div> 
                                    <div class= "row">
                                    	<div class="col-md-12">
                                    		<?= $form->field($model, 'nota_enfermera')->textarea(['disabled'=> true]) ?>
                                    	</div>
                                    </div>
                                </div>
                    	   </div>
                    	   <div class= "row">
                    	      <div class="col-md-4">
                    	         <?= $form->field($model, 'tipo')->dropDownList(['N'=> 'NO APLICA', 'I'=> 'INCIDENTE', 'A' => 'ACCIDENTE'],['disabled'=> true]) ?>
                    	      </div>
                    	      <div class= "col-md-3">
                    	         <?= $form->field($model, 'categoria_patologia')->dropDownList(ArrayHelper::map(SysMedPatologiaCategoria::find()->orderBy(['categoria'=> SORT_ASC])->all(), 'id', 'categoria'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control', 'id'=> 'categoria_patologia', 'disabled'=> true, 'options'=>[ $id_categoria_patologia => ['selected' => true]]])?>
                    	      </div>
                    	      <div class= "col-md-5">
                    	        <?=  $form->field($model, 'id_sys_med_patologia')->widget(DepDrop::class,[
                    	            'disabled'=> true, 
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
                    	       		<?= $form->field($model, 'notas_evolucion')->textarea(['rows'=> '3', 'maxlength' => true, 'disabled'=> true]) ?>   
                    	       </div>
                    	   </div>
                    	   <div class= "row">
                    	      <div class= "col-md-12">
                    	      	<?= $form->field($model, 'prescripcion')->textarea(['rows'=> '3', 'maxlength' => true, 'disabled'=> true]) ?>  
                    	      </div>
                    	   </div>
                    	</div>
                    </div>   
  <?php ActiveForm::end(); ?>  
</div>
