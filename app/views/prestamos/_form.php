<?php

use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\web\View;
use app\models\SysRrhhEmpleados;
use kartik\number\NumberControl;
use kartik\typeahead\Typeahead;
use app\assets\PrestamosAsset;
PrestamosAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPrestamosCab */
/* @var $form yii\widgets\ActiveForm */

$url = Yii::$app->urlManager->createUrl(['prestamos']);
$inlineScript = "var url='$url', update ='$update';";
$this->registerJs($inlineScript, View::POS_HEAD);

$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;
?>
<div class="sys-rrhh-prestamos-cab-form">

   <?php $form = ActiveForm::begin(['id'=> 'formprestamo']); ?>
           <div class=  "row">
             <div class =  "col-md-6">
                  <div class ="row">
                          <div class = 'col-md-4'>
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
                        <div class = "col-md-8">
                           <?php echo html::label('Nombres')?>
                           <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
                       </div>
                  </div>
                  <div class = "row">
                     <div class =  "col-md-4">       
                       <?=  $form->field($model, 'valor')->widget(NumberControl::classname(), [
                           
                           'maskedInputOptions' => [
                               'groupSeparator' => '',
                               'digits' => 2,
                               'rightAlign' => false
                           ]
                        
                       ]);?>
                     </div>
                     <div class = "col-md-4">
                       <?=  $form->field($model, 'cuotas')->widget(NumberControl::classname(), [
                          
                       
                           'maskedInputOptions' => [
                               'groupSeparator' => '',
                               'digits' => 0,
                               'rightAlign' => false
                           ]
                        
                       ]);
                       ?>
                     </div>
                     <div class =  "col-md-4">
                        <?= $form->field($model, 'nperiodo')->dropDownList($secuencia, ['disabled'=> true]) ?>
                     </div>
                 </div>
                 <div class = "row">
                     <div class =  "col-md-4">
                        <?= $form->field($model, 'anio_ini')->textInput(['maxlength' => true]) ?>
                     </div>
                     <div class = "col-md-4">
                       <?= $form->field($model, 'mes_ini')->dropDownList($meses) ?>
                     </div>
                     <div class =  "col-md-4">
                        <?= $form->field($model, 'periodo_rol')->dropDownList($periododes) ?>
                     </div>
                 </div>
                  <div class = "row">
                     <div class =  "col-md-12">
                        <?= $form->field($model, 'comentario')->textInput(['maxlength' => true]) ?>
                     </div>
                 </div>
                 <div class = "row">
                   <div class = "col-md-12">
                         <div class="form-group text-center">
                                <?= Html::submitButton('Guardar Préstamos', ['class' => 'btn btn-success']) ?>
                          </div>
                   </div>
                 </div>
            </div>
             <div class = "col-md-6">
                <div class =  "row">
                   <div class = "col-md-12">
                      	<?= Html::input("submit", "", "Generar Cuotas", ['id'=>'btngeneracuotas','class'=>"btn btn-primary input-sm", 'style'=>'text-align: left;',  'disabled'=> $update == '1'?true:false]) ?>
                  </div>
                </div>
                <br>
                <div class = "row">
                    <div class ="col-md-12">
                       <?php //detalle del prestamo ?>
                           <?php 
                            echo  TabularInput::widget([
                             
                             'models' => $modeldet,
                             'id'=> 'detalleprestamo',
                            /* 'attributeOptions' => [
                                 'enableAjaxValidation'      => true,
                                 'enableClientValidation'    => false,
                                 'validateOnChange'          => false,
                                 'validateOnSubmit'          => true,
                                 'validateOnBlur'            => false,
                             ],
                             */
                                
                             'allowEmptyList' => true,
                             'addButtonPosition' => MultipleInput::POS_HEADER,
                             'addButtonOptions' => [
                                 'class' => 'btn btn-xs btn-info hidden',
                                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                                 'id'    => 'addpresamo'
                             ],
                             'removeButtonOptions' => [
                                 'class' => 'btn btn-xs btn-danger hidden',
                                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                                 'id'=> 'delprestamo'
                             ],
                                
                             'columns'=> [
                                 
                                 [
                                     'name' => 'id_sys_rrhh_prestamos_det',
                                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                 ],
                               
                                 [
                                     'name' => 'anio',
                                     'title' => $modeldet[0]->getAttributeLabel('anio'),
                                     'type' => kartik\number\NumberControl::className(),
                                     'enableError' => true,
                                     'options' => [
                                         
                                         'maskedInputOptions' => [
                                             'groupSeparator' => '',
                                             'digits' => 0,
                                             'rightAlign' => false
                                         ]
                                         
                                     ],
                                     'headerOptions'=>[
                                         'style'=>'width:20%',
                                     ]
                                 ],
                                 
                                 [
                                     'name' => 'mes',
                                     'title' => $modeldet[0]->getAttributeLabel('mes'),
                                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                                     'enableError' => true,
                                     'items'=> $meses
                                 ],
                                 
                                 [
                                     'name' => 'valor',
                                     'title' => $modeldet[0]->getAttributeLabel('valor'),
                                     'type' =>  kartik\number\NumberControl::className(),
                                     'enableError' => true,
                                     'options' => [
                                         //'displayOptions' =>  ['onkeypress' => 'actualizaCuota.call(this,event)'],
                                         'maskedInputOptions' => [
                                            
                                         ],
                                         
                                     ],
                                     'headerOptions'=>[
                                         'style'=>'width:20%',
                                     ]
                                 ],
                                 
                                 [
                                     'name' => 'saldo',
                                     'title' => $modeldet[0]->getAttributeLabel('saldo'),
                                     'type' =>  kartik\number\NumberControl::className(),
                                     'enableError' => true,
                                     'options' => [
                                         
                                         'displayOptions' =>  ['readonly'=> true],
                                         'maskedInputOptions' => [
                                           
                                         ],
                                         
                                     ],
                                     'headerOptions'=>[
                                         'style'=>'width:20%',
                                     ]
                                 ],
                             ]
                         ])?>
                    </div>
                </div>
                <div class = "row">
                   <div class =  "col-md-12">
                     <table style="width: 100%;">
                     	<tr>
                     		<th width="17%" style="text-align: right;"> TOTAL: </th>
                     		<th width="10%"><span id= "total" style ="font-weight: bold;"></span></th>
                        </tr>
                     </table>
                   </div>
                </div>  
             </div>
          </div> 
    

    <?php ActiveForm::end(); ?>

</div>
