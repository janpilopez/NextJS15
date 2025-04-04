<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Informe actualización de datos anual';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-5 text-center'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?=$anio?>" name = 'anio'>
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
        <?=  Html::a('Exportar a Excel', ['datosanualxls','anio'=> $anio], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px' ]);?>
      </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableactualizaciondatosanual', ['datos'=> $datos,'anio' => $anio]);?>
  </div>
  <?php endif;?> 

