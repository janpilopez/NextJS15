<?php
/* @var $this yii\web\View */
use app\models\SysRrhhEventos;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;

$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Informe de Asistencia a Eventos/Capacitaciones';
$this->render('../_alertFLOTADOR'); 

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-5 text-center'>
          <?php echo '<label>Eventos/Capacitaciones</label>';
                echo   Html::DropDownList('nombreEvento', 'nombreEvento', 
                       ArrayHelper::map(SysRrhhEventos::find()->all(), 'idEvento', 'nombreEvento'), ['class'=>'form-control input-sm', 'id'=>'idEvento', 'prompt' => 'Todos', 'options'=>[ $evento => ['selected' => true]]])
          ?>
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
        <?=  Html::a('Exportar a Excel', ['informecapacitacionesxls','evento'=> $evento], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px' ]);?>
      </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableinformecapacitaciones', ['datos'=> $datos,'evento'=> $evento]);?>
  </div>
  <?php endif;?> 

