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
use kartik\time\TimePicker;
use app\models\SysAdmAreas;
use app\models\SysRrhhPermisos;
use app\assets\PermisosEmpleadosloteAsset;
PermisosEmpleadosLoteAsset::register($this);
$urlconsultas = Yii::$app->urlManager->createUrl(['consultas']);
$consultas    = Yii::$app->urlManager->createUrl(['permisos']);
$inlineScript = "urlconsultas = '$urlconsultas', url = '$consultas';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Permisos por Lotes';
$this->params['breadcrumbs'][] = 'Permisos por Lotes';
?>

<div class="site-contact">
   <h1><?= Html::encode($this->title) ?></h1>
   <div class = 'panel panel-default'>
    <div class = 'panel-body'>
        <div class ='row'>
           <div class = 'col-md-3'>
               <?php
               echo '<label>Fecha</label>';
               echo DatePicker::widget([
                   'name' => 'fechaini',
                   'value' => date('Y-m-d'),
                   'options' => ['placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm', 'id'=> 'fechaini'],
                   'pluginOptions' => [
                       'format' => 'yyyy-mm-dd',
                       //'todayHighlight' => true
                   ]
               ]);?>
             </div>
             
            <div class ='col-md-2'>
               <?php 
               echo '<label>Hora</label>';
               echo  TimePicker::widget([
                                  'name'=> 'horaini',
                               'options'=> ['id'=> 'horaini', 'class'=> 'form-control input-sm'],
                                   'pluginOptions' => [
                                       'minuteStep' => 30,
                                       'showMeridian' => false,
                                       //'defaultTime' => date('H:i'),
                                       'defaultTime' => '00:00',
                                   ],   
                  ]);?>
           </div>
           <div class = 'col-md-3'>
               <?php
               echo '<label>Fecha</label>';
               echo DatePicker::widget([
                   'name' => 'fechafin',
                   'value' => date('Y-m-d'),
                   'options' => ['id'=>'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                   'pluginOptions' => [
                       'format' => 'yyyy-mm-dd',
                       //'todayHighlight' => true
                   ]
               ]);?>
             </div>
             
            <div class ='col-md-2'>
               <?php 
               echo '<label>Hora</label>';
               echo  TimePicker::widget([
                               'name'=> 'horafin',
                               'options'=> ['id'=> 'horafin', 'class'=> 'form-control input-sm'],
                                   'pluginOptions' => [
                                       'minuteStep' => 30,
                                       'showMeridian' => false,
                                       //'defaultTime' => date('H:i'),
                                       'defaultTime' => '00:00',
                                   ],   
                  ]);?>
           </div>
           
           <div class = 'col-md-2'>
                <?php echo '<label>Tipo</label>';?>
                <?php echo html::dropDownList('tipo', 'tipo',['C'=>'Completa', 'P'=> 'Parcial'], ['class'=> 'form-control input-sm', 'id'=>'tipo']);?>
           </div>
        </div>
        <br>
        <div class ='row'>
           <div class ='col-md-4'>
               <?php echo '<label>Permiso</label>'?>
               <?php echo html::dropDownList('permiso', null,ArrayHelper::map(SysRrhhPermisos::find()->all(), 'id_sys_rrhh_permiso', 'permiso'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'permiso'] )?>
           </div>
           <div class ='col-md-8'>
               <?php echo '<label>Comentario</label>'?>
               <?php echo html::textInput('comentario',null,['class'=> 'form-control input-sm', 'id'=> 'comentario']);?>
           </div>
        </div>
        <br>
        <div class = 'row'>
           <div class = 'col-md-12'>
              <div class="form-group text-center">
                  <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success input-sm', 'id'=> 'guardarlote']) ?>
              </div>
           </div>
        </div>
   </div>
  </div>
  <div class = 'row'>
     <div class= 'col-md-3'>
      <?php echo '<label>Area</label>'?>
      <?php echo html::dropDownList('area', null,ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'area'] )?>
     </div>
     <div class = 'col-md-3'>
      <?php echo '<label>Departamento</label>'?>              
      <?php echo  DepDrop::widget([
                          'name'=> 'departamento',
                          'options' => ['id'=> 'departamento','class'=> 'form-control input-sm'],
                          'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                          'pluginOptions'=>[
                             'depends'=>['area'],
                             'initialize' => true,
                             'initDepends' => ['area'],
                             'placeholder'=>'Select...',
                             'url'=>Url::to(['/consultas/listadepartamento']),
                             
                         ]
                         
        ]); ?> 
     
     </div>
      <div class = 'col-md-1'>
        <br>
        <div class="text-left">
         <?= Html::submitButton("Buscar <i class = 'glyphicon glyphicon-search'></i>", ['class' => 'btn btn-info input-sm', 'id'=> 'agregaremp']) ?>
        </div>
      </div>
     
     <div class = 'col-md-12'>
     <br>
       <table id= 'tabla' class ='table table-condensed'>
          <thead>
            <tr class = 'info'>
               <th>Cedula</th>
               <th>Nombres</th>
               <th style="text-align: center"><button onclick= 'Eliminarfila()' class= 'btn btn btn-danger input-sm'><i class = 'glyphicon glyphicon-trash'></i></button></th>
            </tr>
          </thead>
          <tbody>
          
          </tbody>
       </table>
     </div>
  </div>
  <div id="loading"></div>
</div>
<?php 
   Modal::begin([
    'id' => 'modal',
    'header' => '<h4 class="modal-title">Listado de Empleados</h4>',
    'headerOptions'=>['style'=>"background-color:#EEE"],
    'size'=>'modal-lg',
    ]); ?>
<?php Modal::end(); ?>