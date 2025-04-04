<?php

use app\models\SysAccesoTipoVisitas;
use app\models\SysAdmDepartamentos;
use Mpdf\Tag\Time;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use app\models\SysAdmAreas;
use app\models\User;
use kartik\depdrop\DepDrop;
use kartik\typeahead\Typeahead;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisosIngresosDet;
use app\assets\PermisosIngresosAsset;
use yii\web\View;
use kartik\file\FileInput;
PermisosIngresosAsset::register($this);
use yii\web\JsExpression;
$url = Yii::$app->urlManager->createUrl(['permisos-ingresos']);
$inlineScript = "var update = {$update},esupdate = {$esupdate}, url = '{$url}';";
$this->registerJs($inlineScript, View::POS_HEAD);
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhpermisosingresos */
/* @var $form yii\widgets\ActiveForm */
$cont = 0;

$pluginOptions = [
    'autoclose'=>true,
    'format' => 'yyyy-mm-dd hh:ii',
    'todayHighlight' => true,
    
];

if($update != 0):

$cont =  SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $model->id])->count();

$iddetalle =
[
    'name' => 'id_sys_rrhh_cedula',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
else:

$iddetalle = [
    'name' => 'nombres',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
endif;
$template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
            '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
?>

<div class="sys-rrhh-permisos-ingresos-form">
 <?php $form = ActiveForm::begin(['id'=>'permisosingresosemp']); ?>
 <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'empresa')->textInput(['id'=> 'empresa','maxlength' => true, 'placeholder'=> 'Nombre Empresa'])?>   
        </div>
        <div class='col-md-2'>
            <br>
            <button id= 'abrir-modal' class= ' btn btn-primary input-sm'>
             <i class = 'glyphicon glyphicon-plus'></i> Agregar miembros
           </button>
        </div>
        <div class="col-md-3">
        <?php
            echo '<label>Fecha Ingreso</label>';
            echo DateTimePicker::widget([
                'name' => 'fechainicio', 
                'value' => $fechaini,
                'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..'],
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd H:ii',
                		//'todayHighlight' => true
                ]
            ]);?> 
        </div>
        <div class="col-md-3">
        <?php
            echo '<label>Fecha Salida</label>';
            echo DateTimePicker::widget([
                'name' => 'fechasalida', 
                'value' => $fechasalida,
                'options' => ['id'=>'fechasalida','placeholder' => 'Seleccione..'],
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd H:ii',
                		//'todayHighlight' => true
                ]
            ]);?>  
        </div>
        </div>
        <div class="row">
               <div class="col-md-6">
                 <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
               </div>
               <?php

                 if(User::hasRole('JEFESEGURIDAD') || User::hasRole('seguridad')):

                ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'tipo_visita')->dropDownList(ArrayHelper::map(SysAccesoTipoVisitas::find()->all(), 'id_tipo_visita', 'tipo_visita'), ['prompt'=> 'Seleccione..' , 'id'=>'visita']  ) ?>
                </div>

                <?php

                else:
                    ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'tipo_visita')->dropDownList(ArrayHelper::map(SysAccesoTipoVisitas::find()->andWhere('id_tipo_visita <> 8')->all(), 'id_tipo_visita', 'tipo_visita'), ['prompt'=> 'Seleccione..' , 'id'=>'visita']  ) ?>
                </div>
                <?php
                endif;
                ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_sys_adm_departamento')->dropDownList(ArrayHelper::map(SysAdmDepartamentos::find()->andWhere(['estado' => 'A'])->orderBy('departamento asc')->all(), 'id_sys_adm_departamento', 'departamento'), ['id'=>'departamento']  ) ?>
                </div>
         </div>
         <div class = 'row'>
            <div class = 'col-md-12'>
              <?=
                 $form->field($model, 'file')->widget(FileInput::classname(), [
                     'options' => ['accept' => 'pdf/*','disabled' => true,'id'=>'file'],
                     'pluginOptions' => [
                        
                     ]
                 ])->label(false);
                 ?>
            </div> 
        </div>
  <div class= 'row'>
     <div class= 'col-md-12'>
         <?= Html::hiddenInput('permisosingresosempleados', $cont, ['id'=> 'datapermisosingresos']); ?>
         <?php 
        echo  TabularInput::widget([
            'models' => $modeldet,
            'id'=> 'modeldet',
            'attributeOptions' => [
             // 'enableAjaxValidation'      => true,
                /*enableClientValidation'    => false,
                'validateOnChange'          => false,
                'validateOnSubmit'          => true,
                'validateOnBlur'            => false,*/
            ],
            
            'allowEmptyList' => true,
            'addButtonPosition' => MultipleInput::POS_HEADER,
            'addButtonOptions' => [
                'class' => 'btn btn-xs btn-info',
                'label' => '<i class="glyphicon glyphicon-plus"></i>'
            ],
            'removeButtonOptions' => [
                'class' => 'btn btn-xs btn-danger',
                'label' => '<i class="glyphicon glyphicon-remove"></i>'
            ],
               
            'columns'=> [
                
                [
                    'name' => 'id_sys_rrhh_empleados_permisos_ingresos',
                    'type' => TabularColumn::TYPE_HIDDEN_INPUT
                ],
                    
                [
                    'name' => 'id_sys_rrhh_cedula',
                    'title' => $modeldet[0]->getAttributeLabel('id_sys_rrhh_cedula'),
                    'type' => '\kartik\typeahead\Typeahead',
                    'options' => [
                        'options' => ['placeholder' => 'Buscar..'],
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
                                    asignarnombre(suggestion.nombres)
                                   }',
                            ]
                    ],

                ],
                
                [
                    'name' => 'nombres',
                    'title' => $modeldet[0]->getAttributeLabel('nombres'),
                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                    'enableError' => true,
                    'options' => [
                        'onchange' => 'imprimir.call(this,event)',
                    ]
                ],

                [
                    'name' => 'telefono',
                    'title' => $modeldet[0]->getAttributeLabel('telefono'),
                    'type' => TabularColumn::TYPE_CHECKBOX,
                    'enableError' => true,
                ],

                [
                    'name' => 'laptop',
                    'title' => $modeldet[0]->getAttributeLabel('laptop'),
                    'type' => TabularColumn::TYPE_CHECKBOX,
                    'enableError' => true,
                ],

                [
                    'name' => 'auto',
                    'title' => $modeldet[0]->getAttributeLabel('auto'),
                    'type' => TabularColumn::TYPE_CHECKBOX,
                    'enableError' => true,
                ],

                [
                    'name' => 'marca_auto',
                    'title' => $modeldet[0]->getAttributeLabel('marca_auto'),
                    'type' => TabularColumn::TYPE_TEXT_INPUT,
                    'enableError' => true,
                    'options' => ['disabled'=> true]
                ],

                [
                    'name' => 'otros',
                    'title' => $modeldet[0]->getAttributeLabel('otros'),
                    'type' => TabularColumn::TYPE_CHECKBOX,
                    'enableError' => true,
                ],
            ]
        ]) 
         ?>
     </div>
  </div>
  <br>
 <div class = 'row'>
     <div class = 'col-md-12'>
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success', 'id'=> 'btn-guardar']) ?>
        </div>
     </div>
  </div>
  
   <?php ActiveForm::end(); ?>
</div>

<?php 
    //modal empleados 
    Modal::begin([
        'id' => 'modalproveedores',
        'header' => '<h4 class="modal-title">Listado de Proveedores</h4>',
        'headerOptions'=>['style'=>"background-color:#EEE"],
        'size'=>'modal-md',
    ]);
    ?>
<?php Modal::end(); ?>



