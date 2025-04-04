<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use app\models\SysAdmPeriodoVacaciones;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
$this->title = 'Liquidación de Vacaciones (General)';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= "row">
     <div class = "col-md-2 col-md-offset-4">
                    <label>Fecha Inicio</label>
                   <?php echo DatePicker::widget([
                	'name' => 'fechaini', 
                    'value' => $fechaini,
                	'options' => [ 'id'=> 'fechaini','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
     
     
     </div>   
     <div class = "col-md-2">
                    <label>Fecha Fin</label>
                   <?php echo DatePicker::widget([
                	'name' => 'fechafin', 
                    'value' => $fechafin,
                	'options' => [ 'id'=> 'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
     
     
     </div>     
   </div>
   <br>
   <div class ="row">
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <?php ActiveForm::end(); ?>
</div>
  <?php if($datos): ?>
    <div class= 'row'>
        <div class = 'col-md-12'>
           <?=  Html::a('Exportar a Excel', ['informegeneralpdf','fechaini'=> $fechaini, 'fechafin'=> $fechafin ], ['class'=>'btn btn-xs btn-success pull-right' ]);?>
        
        </div>
     </div>
     <div class= 'row'>
        <div class= 'col-md-12'>
             <?=  $this->render("_tableliquidacionvac.php",['datos'=>$datos]);?>
        </div>
     </div>
  
    
  <?php endif;?> 

