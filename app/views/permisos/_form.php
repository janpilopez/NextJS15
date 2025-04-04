<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\models\SysRrhhPermisos;
use kartik\datetime\DateTimePicker;
use kartik\typeahead\Typeahead;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisos */
/* @var $form yii\widgets\ActiveForm */
use app\assets\PermisosEmpleadosAsset;
use app\models\SysRrhhEmpleados;
PermisosEmpleadosAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['permisos']);
$urlconsultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "var url='$url',permisolote=false,urlconsultas ='$urlconsultas';";
$this->registerJs($inlineScript, View::POS_HEAD);



if($tipousuario == 'A' || $tipousuario == 'D'):

$pluginOptions = [
    'autoclose'=>true,
    'format' => 'yyyy-mm-dd hh:ii',
    'todayHighlight' => true,
    
];

else:

    $pluginOptions = [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd hh:ii',
        'todayHighlight' => true,
        'startDate' => date('Y-m-d'),
    ];

endif;


?>

<div class="sys-rrhh-empleados-permisos-form">

     <?php $form = ActiveForm::begin(['id'=>'permisoform']);?>
    
     <?php 
    
     $nombres = '';
     if(!$model->isNewRecord){ 
          $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();        
     }?>
     
    <div class = 'panel panel-default'>
     <div class = 'panel-body'>
                <div class= 'row'>
                   <div class = 'col-md-3'>
                       <?php  
                       $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                           '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                       
                       echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                           'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm'],
                           'pluginOptions' => ['highlight'=>true],
                           'scrollable'=>true,
                           'dataset' => [
                               [
                                   
                                   'remote' => [
                                       'url' =>    Url::to(['consultas/listempleados2']) . '?q=%QUERY',
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
                               
                            
                       ])->label('Cedula');
                       
                       ?>
                   </div>
                    <div class = 'col-md-5'>
                       <?php echo html::label('Nombres')?>
                       <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
                   </div>
                   <div class = 'col-md-2'>
                      <?= $form->field($model, 'id_sys_rrhh_permiso')->dropDownList(ArrayHelper::map(SysRrhhPermisos::find()->where(['estado'=>'A'])->where(['id_sys_rrhh_permiso' => $listpermisos])->all(), 'id_sys_rrhh_permiso', 'permiso'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm', 'id'=>'tipopermiso',$model->id_sys_rrhh_permiso => ['selected' => true]]) ?>
                   </div>
                   <div class = 'col-md-2'>
                    <?= $form->field($model, 'tipo')->dropDownList(['C'=>'Completa', 'P'=> 'Parcial', 'D' => 'Completa-Laboral', 'O' =>'Parcial-Laboral'], ['class'=> 'form-control input-sm', 'id'=>'tipojornada']) ?>
                   </div>
                </div>
                <div class = 'row'>
                   <div class = 'col-md-6'>
                      <?= $form->field($model, 'fecha_ini')->widget(DateTimePicker::classname(), [
                          'value' => $model->fecha_ini,
                          'options' => ['placeholder' => 'Inicio del permiso', 'autocomplete'=>'off', 'id'=> 'fecha_ini'],
                          'pluginOptions' => $pluginOptions,
                        ]);
                      ?>            
                   </div>
                   <div class = 'col-md-6'>
                   
                   	   <?= $form->field($model, 'fecha_fin')->widget(DateTimePicker::classname(), [
                   	       'value' => $model->fecha_fin,
                   	       'options' => ['placeholder' => 'Fin del permiso', 'autocomplete'=>'off', 'id'=> 'fecha_fin'],
                   	       'pluginOptions' => $pluginOptions,
                        ]);
                      ?>  
                   
                   </div>
                   <?php 
                   /*
                   <div class= 'col-md-3'>
                   
                      <?= $form->field($model, 'fecha_ini')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => $pluginOptions,
                				                'options' => ['placeholder' => 'Fecha de Inicio']
                                            ]);?>
                                            
                   </div>
                   <div class = 'col-md-3'>
                     <?= $form->field($model, 'hora_ini')->widget(TimePicker::classname(), 
                			                          [
                			                              'disabled'=> $model->tipo == 'C' ? true : false,
                			                              'options'=> ['id'=> 'horainicio'],
                			                          
                			                              'pluginOptions' => [
                			                                                     'minuteStep' => 30,
                			                                                     'showMeridian' => false,
                			                                                     'defaultTime' => '00:00',
                			                                                 ],
                			                          ]
            			                         );?>
                   </div>

                   
                   <div class ='col-md-3'>
                   
                     <?= $form->field($model, 'fecha_fin')->widget(DatePicker::classname(), [
                                                'removeButton' => false,
                                                'size'=>'md',
                                                'pluginOptions' => $pluginOptions,
                				                'options' => ['placeholder' => 'Fecha Fin']
                                            ]);?>
                   </div>
                   <div class ='col-md-3'>
                     <?= $form->field($model, 'hora_fin')->widget(TimePicker::classname(), 
                			                          [
                			                              'disabled'=> $model->tipo == 'C' ? true : false,
                			                              'options'=> ['id'=> 'horafin'],
                			                              'pluginOptions' => [
                			                                                     'minuteStep' => 30,
                			                                                     'showMeridian' => false,
                			                                                      //'defaultTime' => date('H:i'),
                			                                                      'defaultTime' => '00:00',
                			                                                 ],
                			                          ]
            			                         );?>
                   </div>
                  */?>
                </div>
                <div class = 'row'>
                   <div class = 'col-md-12'>
                      <?= $form->field($model, 'comentario')->textInput() ?>
                   </div>
                </div>
                <div class="form-group text-center">
                    <?= Html::submitButton('Guardar Datos ', ['class' => 'btn btn-success', 'data-confirm'=> 'Está usted seguro que desea realizar continuar?']) ?>
                </div>
        </div>
        <div class = 'panel-footer'>
          <div class= 'row'>
            <div class = 'col-md-12'>
               <p>Nota:</p>
               <p>* Para los permisos de <strong>tipo completa </strong> especificar bajo la  forma  <strong> <?= 'Año-Mes-Día'. ' 00:00'?></strong>. Recuerde que este tipo de permiso es por día y,  sólo validará la fecha inicio y fin del permiso. En caso de haber especificado horas este las marginará.</p>
  			   <p>* Para los permisos de <strong>tipo parcial</strong>  especificar bajo la  forma  <strong> <?= 'Año-Mes-Día'. ' 08:30'?></strong>. Recuerde que este tipo de permiso es por hora y, sólo validará la hora de inicio y fin del permiso. </p>
            </div>
          </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>


