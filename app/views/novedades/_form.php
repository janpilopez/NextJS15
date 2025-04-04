<?php
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleados;
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosNovedades */
/* @var $form yii\widgets\ActiveForm */
use app\assets\NovedadesEmpleadosAsset;
NovedadesEmpleadosAsset::register($this);

$url = Yii::$app->urlManager->createUrl(['novedades']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
?>
<div class="sys-rrhh-empleados-novedades-form">

    <?php $form = ActiveForm::begin(['id'=>'novedadform']); ?>
   
     <?php 
     $nombres = '';
     if(!$model->isNewRecord){ 
          $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();        
     }?>
    <div class = 'panel panel-default'>
     <div class = 'panel-body'>
        <div class = 'row'>
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
                                       'url' =>    Url::to(['consultas/listempleados']) . '?q=%QUERY',
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
            <div class = 'col-md-4'>
             <?= $form->field($model, 'id_sys_rrhh_concepto')->dropDownList(ArrayHelper::map(SysRrhhConceptos::find()->where(['estado'=>'A'])->all(), 'id_sys_rrhh_concepto', 'concepto'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control input-sm']) ?>
            </div>
        </div>
        <div class= 'row'>
            <div class= 'col-md-6'>
              <?= $form->field($model, 'fecha')->widget(DatePicker::classname(), [
                                        'removeButton' => false,
                                        'size'=>'md',
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'yyyy-mm-dd',
                                            //'startDate' => date('Y-m-d'),
                                        ],
                  'options' => ['class'=> 'form-control input-sm','placeholder' => 'Fecha de Registro']
                                    ]);?>
            </div>
            <div class= 'col-md-6'>
              <?= $form->field($model, 'cantidad')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm', 'placeholder'=> '00.00.00']) ?>
            </div>
        </div>
        <div class = 'row'>
           <div  class = 'col-md-12 '>
              <?= $form->field($model, 'comentario')->textInput(['class'=> 'form-control input-sm']) ?>
            </div>
        </div>  
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success', 'data-confirm'=> 'Está usted seguro que desea realizar continuar?',]) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
