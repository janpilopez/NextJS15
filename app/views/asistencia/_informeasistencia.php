<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhJornadasCab;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use app\assets\AgendamientoAsset;
use app\assets\CuadrillasAsset;
use app\models\SysRrhhHorarioCab;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use app\assets\AsistenciaAsset;
AsistenciaAsset::register($this);
$urlconsultas = Yii::$app->urlManager->createUrl(['asistencia']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "urlconsultas = '$urlconsultas', consultas = '$consultas', departamento = '$departamento';";
$this->registerJs($inlineScript, View::POS_HEAD);


$this->title = 'Asistencia Laboral';
$this->params['breadcrumbs'][] = 'Asistencia Laboral';


$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];



if($userdeparta):

    if(trim($userdeparta->area) != ''):
    
    $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
    
    endif;

endif;

?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1> 
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
          <div class= 'col-md-2'>
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
             <div class= 'col-md-3'>
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->andFilterWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos',  'options'=>[ $area => ['selected' => true]]])
                       
              ?>
           </div> 
           <div class = 'col-md-3'>
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
           <div class = 'col-md-2'>
               <?php 
                  echo '<label>Filtrar por</label>';
                  echo  html::dropDownList('tipo', null, ['T'=> 'Todas', 'F'=> 'Faltas', 'P'=> 'Permisos', 'V'=> 'Vacaciones', 'L'=>'Dia Libre', 'E' => 'Error Marcación'], ['class'=> 'form-control input-sm',  'options' =>[ $tipo => ['selected' => true]]]);
               ?>
           </div>
    </div>
    <div class= 'row'>
       <div class = 'col-md-12'>
              <?php 
                 echo '<label>Nombres</label>';
                 echo  Html::textInput('nombres', $filtro, ['class'=> 'form-control input-sm']);
              ?>
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
         <?=  Html::a('Exportar a PDF', ['informeasistenciapdf','fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'filtro'=> $filtro, 'tipo'=> $tipo], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
      </div>
  </div>
  <br>
 <div class= 'row'>
    <div class= 'col-md-12'>
       <?php  echo $this->render('_tableinforme', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo'=> $tipo,  'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
    </div>
</diV>
   <?php endif; ?>
</div>




