<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\web\View;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
$url = Yii::$app->urlManager->createUrl(['funciones']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "var url='$url',consultas = '$consultas', departamento = '$departamento';";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\AprobarSolicitudHorasAsset;
use app\models\SysAdmDepartamentos;

AprobarSolicitudHorasAsset::register($this);
$this->title = 'Aprobación Solicitud Horas Extras';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
?>


<style>
#tablemodal_filter > label{
    width:100%;
}
.table__no-margin{
    margin-bottom: 0px;
}
tr.seleccion{
	background-color: #9ec4e2;
}
</style>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
        <div class= 'col-md-2 col-md-offset-2 text-center'>
                <?php
                echo '<label>Fecha Inicio</label>';
                echo DatePicker::widget([
                	'name' => 'fechaini', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechaini','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
       </div>
       <div class= 'col-md-2 text-center'>
                <?php
                echo '<label>Fecha Fin</label>';
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
       <div class= 'col-md-2 text-center'>
       <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos','options'=>[ $area => ['selected' => true]]])
              ?>
       </div>
       <div class= 'col-md-2 text-center'>
       <?php 
                   echo '<label>Departamento</label>';
                   echo DepDrop::widget([
                       'name'=> 'departamento',
                       'data'=> [$area => 'area'],
                       'value'=> '3',
                       'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                       'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                       'pluginOptions'=>[
                           'depends'=>['area'],
                           'initialize' => true,
                           'initDepends' => ['area'],
                           'placeholder'=>'Todos',
                          
                           'url'=>Url::to(['/consultas/listadepartamento']),
                           
                       ]
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
   <?php ActiveForm::end(); ?>
</div>
  
<?php if($datos):?>
   
   <?= $this->render('_tablesolicitudes', ['datos'=> $datos])?>
   
 
  <?php endif; ?>

<div id = "loading"></div>