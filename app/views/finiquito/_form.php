<?php

use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use app\assets\FiniquitoAsset;
use app\models\SysRrhhEmpleados;
FiniquitoAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['finiquito']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFiniquitoCab */
/* @var $form yii\widgets\ActiveForm */
$nombres  = '';
$contrato = null;
if(!$model->isNewRecord):


    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();

    //buscar contrato
    $contrato  =  (new \yii\db\Query())->select(
        [
            
            "fecha_ingreso",
            "fecha_salida",
            "descripcion",
            "id_sys_rrhh_empleados_contrato_cod",
            "contratos.id_sys_rrhh_causa_salida"
        ])
        ->from("sys_rrhh_empleados_contratos contratos")
        ->innerJoin('sys_rrhh_causa_salida motivo','motivo.id_sys_rrhh_causa_salida=contratos.id_sys_rrhh_causa_salida')
        ->where("contratos.id_sys_empresa = '001'")
        ->andWhere("id_sys_rrhh_empleados_contrato_cod = '{$model->id_sys_rrhh_empleados_contrato_cod}'")
        ->one(SysRrhhEmpleados::getDb());

      
        
endif;


?>


<div class="sys-rrhh-finiquito-cab-form">

    <?php $form = ActiveForm::begin(); ?>
     <div class ="row">
        <div class= "col-md-3">
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
                                       'url' =>    Url::to(['consultas/empleadosrol']) . '?q=%QUERY',
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
                                        //mostrar datos de la salida
                                        getSalida(suggestion.value);
                                        $("#nombres").val(suggestion.nombres);
                                      }',
                               ]
                               
                            
                       ])->label('Cedula');
                       
                       ?>
        
        </div>
        <div class ="col-md-7">
              <?php echo html::label('Nombres')?>
              <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
        </div>
        <div class ="col-md-2">
           <?= $form->field($model, 'estado')->dropDownList(['G'=> 'Generado', 'L'=> 'Liquidado', 'A'=> 'Anulado']) ?>
        </div>
     </div>
     <div class = "row">
        <div class = "col-md-3">
          <?= $form->field($model, 'fechaing')->textInput(['disabled'=> true, 'id'=>'fechaing','value'=> $contrato != null ? $contrato['fecha_ingreso']: '']) ?>
        </div>
       <div class = "col-md-3">
          <?= $form->field($model, 'fechasal')->textInput(['disabled'=> true, 'id'=> 'fechasal', 'value' =>  $contrato != null ? $contrato['fecha_salida']: '']) ?>
        </div>
         <div class = "col-md-3">
          <?= $form->field($model, 'motivosal')->textInput(['disabled'=> true,  'id'=> 'motivosal', 'value'=> $contrato != null ? $contrato['descripcion']:'']) ?>
        </div>
         <div class = "col-md-3">
          <?= $form->field($model, 'sueldo')->textInput(['id'=> 'sueldo']) ?>
        </div>
     </div>
     <div class = "row">
        <div class = "col-md-12">
           <?= $form->field($model, 'comentario')->textInput(['maxlength' => true]) ?>
        </div>
     </div>
     <div class = "row">
          <div class = "col-md-12">
               <?= $form->field($model,'id_sys_rrhh_empleados_contrato_cod')->hiddenInput()->label(false)?>
          </div>
     </div>
     <div class ="row">
         <div class = "col-md-12">
              <div class = "row">
                    <div class ="col-md-10">
                       <?php //detalle del prestamo ?>
                           <?php 
                            echo  TabularInput::widget([
                             
                             'models' => $modeldet,
                             'id'=> 'detallefiniquito',
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
                                 'class' => 'btn btn-xs btn-info',
                                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                                 'id'    => 'addpresamo'
                             ],
                             'removeButtonOptions' => [
                                 'class' => 'btn btn-xs btn-danger',
                                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                                 'id'=> 'delprestamo'
                             ],
                                
                             'columns'=> [
                                 
                                 [
                                     'name' => 'id_sys_rrhh_finiquito_det',
                                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                 ],
                                      
                                 [
                                     'name' => 'descripcion',
                                     'title' => $modeldet[0]->getAttributeLabel('descripcion'),
                                     'type' =>  TabularColumn::TYPE_TEXT_INPUT,
                                     'options' => ['required'=> true,],
                                     'enableError' => true,
                                     
                                 ],
                                 [
                                     'name' => 'valor',
                                     'title' => $modeldet[0]->getAttributeLabel('valor'),
                                     'type' =>  kartik\number\NumberControl::className(),
                                     'enableError' => true,
                                     'options' => [
                                     
                                         'displayOptions' =>  ['class'=> 'form-control', 'onblur' => 'TotalLiquidacion.call()'],
                                         'maskedInputOptions' => [
                                             // 'prefix' => '%',
                                             'digits' => 2,
                                         ],
                                         
                                         
                                     ],
                                         
                                    
                                 ],
                         
                                 
                                [
                                     'name' => 'tipo',
                                     'title' => $modeldet[0]->getAttributeLabel('tipo'),
                                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                                     'enableError' => true,
                                     'items'=> ['I'=> 'Ingreso', 'E'=> 'Egreso']
                                 ],
                             
                              
                             ]
                         ])?>
                    </div>
                    <div class = "col-md-2">
                       <p style="font-size: 20px;"><strong>Neto a Recibir:</strong></p>
                       <p style="font-size: 20px;" id="total">0.00</p>
                    </div>
                </div>
         
         
         </div>
     </div>
     
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
