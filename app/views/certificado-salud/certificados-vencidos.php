<?php 
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use SebastianBergmann\CodeCoverage\Report\PHP;
$this->title = 'Certificados de Salud Vencidos';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-4'>
             <label >Año</label>
             <input type="number" class="form-control" value = <?= $anio?> name = 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control', 'prompt' => 'Seleccione..',
                   'options' =>[ $mes => ['selected' => true]]
                  ]);
               ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <br>
   <?php ActiveForm::end(); ?>
   <?php if($datos): ?>
    <div class ="row" >
      <div class="col-md-12">
         <?=  Html::a('Exportar a PDF', ['certificadosvencidospdf','anio'=> $anio, 'mes'=> $mes], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
      </div>
    </div>
  	<br>
     <div class="row">
      	<div class="col-md-12">
            <?= $this->render('_tablecertificadosvencidos', ['datos'=> $datos,  'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
        </div>
     </div>
   <?php endif;?>
</div>
 