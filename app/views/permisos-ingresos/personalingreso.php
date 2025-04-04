<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysRrhhEmpleadosPermisosIngresosDet;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use app\models\SysRrhhEmpleados;
use yii\web\JsExpression;
use app\assets\AsistenciaAsset;
AsistenciaAsset::register($this);


$this->title = 'Ingreso General Visitas';
$this->params['breadcrumbs'][] = 'Ingreso General Visitas';

?>
<div class="site-contact">

    <h1><?= Html::encode($this->title) ?></h1>
     
    <?php $form = ActiveForm::begin(['id'=> 'formasistemp']); ?>
    <div class= 'row'>
          <div class= 'col-md-2 col-md-offset-4'>
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
        <?php  if($datos):?>
        <div class= 'row'>
           <div class = 'col-md-12'>
               <?=  Html::a('Exportar a PDF', ['personalingresopdf', 'fechaini'=> $fechaini, 'fechafin'=> $fechafin], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
               <?=  Html::a('Exportar a EXCEL', ['personalingresoxls', 'fechaini'=> $fechaini, 'fechafin'=> $fechafin], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
           </div>
        </div>
        <br>
        <div class = 'row'>
           <div class = 'col-md-12'>
                  <?php  echo $this->render('_tablevisitas', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
            </div>
          </div>
        <?php endif;?>
</div>


