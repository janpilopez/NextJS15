<?php

use app\models\SysMedTipoMotivo;
use kartik\typeahead\Typeahead;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */

$this->title = 'Registrar Turno';
$this->params['breadcrumbs'][] = ['label' => 'Turnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-turno-medico-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class = 'panel panel-default'>  
     	<div class = 'panel-body'>
            <?php $form = ActiveForm::begin(); ?>
             <div class= "row">
                <div class= "col-md-4">
                                   <?php  
                               $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                                   '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                               
                               echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                                   'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control'],
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
                                       
                                    
                               ])->label('Cédula Empleado');
                               
                               ?>
                </div>
                <div class = "col-md-8">
                      <?php echo html::label('Nombres')?>
                      <?php echo html::textInput('nombres', '', ['class'=> 'form-control', 'id'=> 'nombres', 'disabled'=> true])?>
                </div>
             </div>
             <div class= "row">
                <div class= "col-md-12">
                  <?= $form->field($model, 'id_sys_med_tipo_motivo')->dropDownList(ArrayHelper::map(SysMedTipoMotivo::find()->where(['activo'=>'1'])->andWhere(['id'=> ['1','2', '4', '5']])->all(), 'id', 'tipo'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control', 'options' => [ 1 => ['selected' => true]]]) ?>
                </div>
             </div>
             <div class= "row">
                <div class= "col-md-12">
                     <div class="form-group">
                    	<?= Html::submitButton('Registrar Turno', ['class' => 'btn btn-success']) ?>
                	</div>
                </div>
             </div>
            <?php ActiveForm::end(); ?>
       </div>
    </div>
</div>
