<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
use app\models\SysRrhhCausaSalida;
$this->title = 'Informe de Ajuste Salarial';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-5'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?= $anio?>" name = 'anio'>
       </div> 
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   
   <?php ActiveForm::end(); ?>
</div>
  <?php if($datos): ?>
  <div class ="row" >
      <div class="col-md-12">
        <?=  Html::a('Exportar a PDF', ['ajustesalarialpdf', 'anio'=> $anio], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
        <?=  Html::a('Exportar a Excel', ['ajustesalarialxls', 'anio'=> $anio], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
       </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableajustesalarial', ['datos'=> $datos, 'anio'=> $anio]);?>
  </div>
  <?php endif;?> 

