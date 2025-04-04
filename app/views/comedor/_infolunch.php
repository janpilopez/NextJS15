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
$this->title = 'Informe Lunch';

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= "row">
     <div class = "col-md-2 col-md-offset-2">
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
     <div class = "col-md-2">
                <label>Tipo</label>
                <?=  html::dropDownList('tipo', $tipo, $lunch, ['class'=> 'form-control input-sm']) ?>
     </div>  
     <div class = "col-md-2">
                <label>Tipo</label>
                <?=  html::dropDownList('tipoinfo', $tipoinfo, $info, ['class'=> 'form-control input-sm']) ?>
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
         
      <div class= "row">
        <div class = "col-md-12">
           <?=  Html::a('Exportar a pdf', ['infolunchpdf','fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo'=> $tipo, 'tipoinfo'=> $tipoinfo], ['class'=>'btn btn-xs btn-danger pull-right', 'target'=> '_blank' ]);?>
        </div>
     </div>
     <br>
     <div class= "row">
        <div class= "col-md-12">
            <?php echo $this->render('_tableinfolunch', ['datos'=> $datos, 'tipo'=> $tipo, 'tipoinfo'=> $tipoinfo]);?>
        </div>
     </div>
     
  <?php endif;?> 

