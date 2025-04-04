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


$this->title = 'Ingreso Individual Visitas';
$this->params['breadcrumbs'][] = 'Ingreso Individual Visitas';

$empleado = '';

if($cedula != ''){
    
    $empleado =  SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_cedula'=> trim($cedula)])
    ->one();
}
?>
<div class="site-contact">

    <h1><?= Html::encode($this->title) ?></h1>
     
    <?php $form = ActiveForm::begin(['id'=> 'formasistemp']); ?>
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
              <div class = 'col-md-3'>
                       <label>Cédula</label>
                       <?php  
                       $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                           '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                       
                       echo Typeahead::widget([ 
                           'name' => 'cedula',
                           'value'=> $cedula,
                           'options' => [ 'id'=> 'cedula', 'placeholder' => 'Buscar..',  'class'=> 'form-control input-sm'],
                           'pluginOptions' => ['highlight'=>true],
                           'scrollable'=>true,
                           'dataset' => [
                               [
                                   
                                   'remote' => [
                                       'url' =>    Url::to(['consultas/listpersonalvisitas']) . '?q=%QUERY',
                                       'wildcard' => '%QUERY'
                                   ],
                                   'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                   'display' => 'value',
                                   'templates' => [
                                       'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                       'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                       ],
                                      
                               ]
                              
                               ],'pluginEvents' => [
                                   'typeahead:select' => 'function(ev, suggestion) {
                                       console.log(suggestion);
                                     $("#nombres").val(suggestion.nombres);
                                      }',
                               ]
                       ]); 
                    ?>
                   </div>
                    <div class = 'col-md-5'>
                       <?php echo html::label('Nombres')?>
                       <?php echo html::textInput('nombres', $empleado != null ? $empleado->nombres: '', ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
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
  
      <?php  if($cedula != ''):?>
      
        <div class= 'row'>
           <div class = 'col-md-12'>
               <?=  Html::a('Exportar a PDF', ['personalingresoindividualpdf','cedula'=> $empleado->id_sys_rrhh_cedula, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
               <?=  Html::a('Exportar a EXCEL', ['personalingresoindividualxls','cedula'=> $empleado->id_sys_rrhh_cedula, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
           </div>
        </div>
        <br>
        <div class = 'row'>
           <div class = 'col-md-12'>
                  <?php  echo $this->render('_tablevisitasindividual', ['datos'=> $datos,'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
            </div>
          </div>
         <?php endif;?>

</div>


