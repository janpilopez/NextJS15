<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use app\models\SysRrhhComedor;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use app\models\SysAdmAreas;
use app\models\SysRrhhHorarioCab;
use yii\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
$this->render('../_alertFLOTADOR');
$this->title = 'Registro de Alimentación Manual';
$this->params['breadcrumbs'][] = 'Registro de Alimentación Manual';

$nombres = '';
?>
<div class="site-contact">

    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
             <div class= 'col-md-3'>
              <?php
                echo '<label>Fecha</label>';
                echo DateTimePicker::widget([
                	'name' => 'fechainicio', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd hh:ii',
                        'todayHighlight' => true,
                    ],
                ]);?>
           </div>
           <div class = 'col-md-2'>
             	<?php echo '<label>Alimento</label>';
             	      echo   Html::DropDownList('alimento', 'alimento', 
                       ArrayHelper::map(SysRrhhComedor::find()->all(), 'id_sys_rrhh_comedor', 'alimento'), ['class'=>'form-control input-sm', 'id'=>'alimento', 'prompt' => 'Seleccione Alimento',  'options'=>[ $alimento => ['selected' => true]]])
              ?>
           </div>
           <div class = 'col-md-3'>
                <?php  
                    $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                    '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                       
                    echo '<label>Cédula</label>' ;
                    echo Typeahead::widget([
                        'name' => 'cedula',
                        'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm', $cedula => ['selected' => true]],
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
                
                       ]);
                       
                       ?>
            </div>
            <div class = 'col-md-3'>
                <?php echo html::label('Nombres')?>
                <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
            </div>
    </div>
    <br>
    <div class= 'row'>
         <div class="form-group text-center">
              <?= Html::submitButton('Registrar Marcación', ['class' => 'btn btn-success input-sm', 'id'=> 'consultar']) ?>
        </div>
    </div>
      <?php $form = ActiveForm::end(); ?>
</div>
   
