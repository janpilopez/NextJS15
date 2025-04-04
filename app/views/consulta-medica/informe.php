<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\bootstrap\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
$this->title = 'Atención Médica -  Dpto Médico';
$this->params['breadcrumbs'][] = 'Consultas Médicas';
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1> 
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
          <div class= 'col-md-3 col-md-offset-3'>
              <?php
                echo '<label>Desde</label>';
                echo DatePicker::widget([
                	'name' => 'fechaini', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-3'>
              <?php
                echo '<label>Hasta</label>';
                echo DatePicker::widget([
                	'name' => 'fechafin', 
                	'value' => $fechafin,
                    'options' => ['id'=>'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control'],
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
<?php if($datos): ?>
  <div class ="row" >
      <div class="col-md-12">
         <?=  Html::a('Exportar a PDF', ['informepdf','fechaini'=> $fechaini, 'fechafin'=> $fechafin], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
      </div>
  </div>
  <br>
 <div class= 'row'>
    <div class= 'col-md-12'>
       <?php  echo $this->render('_tableinforme', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
    </div>
</diV>
<?php endif; ?>
</div>




