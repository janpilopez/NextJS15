<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
$this->title = 'Ausentismo Laboral';
$this->params['breadcrumbs'][] = 'Ausentismo Laboral';
echo $this->render('funciones');
$asistencia = 0;
$faltas     = 0;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1> 
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
          <div class= 'col-md-2  col-md-offset-4'>
              <?php
                echo '<label>Desde</label>';
                echo DatePicker::widget([
                	'name' => 'fechaini', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-2'>
              <?php
                echo '<label>Hasta</label>';
                echo DatePicker::widget([
                	'name' => 'fechafin', 
                	'value' => $fechafin,
                    'options' => ['id'=>'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
    </div>
    <br>
    <div class ='row'>
       <div class ='col-md-12'>
              <div class="form-group text-center">
                    <?= Html::submitButton('Consultar', ['class' => 'btn btn-success', 'id'=> 'btnconsultar']) ?>
               </div>    
       </div>
    </div>  
 <?php ActiveForm::end(); ?>
 
 
 <?php 
     if($empleados):
     
    
        echo $this->render('_ausentismoxls', ['fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'empleados'=> $empleados]);
     
     endif;
 ?>
</div>




