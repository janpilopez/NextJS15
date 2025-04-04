<?php

use app\models\SysRrhhEmpleados;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\assets\RegistroEppAsset;
use diggindata\signaturepad\SignaturePadWidget;
use diggindata\signaturepad\SignaturePadWidgetAsset;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;

Select2Asset::register($this);
RegistroEppAsset::register($this);
SignaturePadWidgetAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */
/* @var $form yii\widgets\ActiveForm */
$template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
  '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
?>

<div class="sys-ssoo-epp-form">
  <div class='panel panel-default'>
    <div class='panel-body'>
      <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
      <div class='row'>
        <div class='col-md-4'>
          <?= $form->field($model, 'nombre')->textInput(['readOnly' => true, 'required' => true, 'value' => 'Registro de Equipos de Protección', 'class' => 'form-control input-sm', 'maxlength' => true, 'placeholder' => 'nombre']) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'id_sys_rrhh_cedula')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(SysRrhhEmpleados::find()->all(), 'id_sys_rrhh_cedula', function ($model) {
              return $model->id_sys_rrhh_cedula . ' - ' . $model->nombres; // Concatenar ID y Nombre
            }),
            'options' => [
              'placeholder' => 'Seleccione un empleado...', // Placeholder que se muestra antes de seleccionar
              'id' => 'id_sys_rrhh_cedula',
              // 'required' => true,
            ],
            'pluginOptions' => [
              'allowClear' => true, // Permite limpiar la selección
              'minimumInputLength' => 3, // Empieza la búsqueda después de escribir 3 caracteres
              'language' => 'es', // Opcional: cambia el idioma de los mensajes del Select2 a español
              'width' => '100%', // Ocupar el 100% del ancho disponible
            ],
          ]) ?>

        </div>
        <div class='col-md-3'>
          <?= $form->field($model, 'fecha_registro')->hiddenInput([
            'value' => date('Y-m-d'), // Fecha actual en formato Y-m-d
            'class' => 'form-control input-sm ',
            'type' => 'date',
            'maxlength' => true,
            'placeholder' => 'FECHA REGISTRO',
            'readonly' => true, // Bloquear el campo para que no sea editable
            'required' => true,
            'autocomplete' => 'off', // Desactiva el autocompletado
          ]) ?>
        </div>

        <div class="col-md-6">
          <?= $form->field($model, 'observacion')->dropDownList(
            $listaActividades
          ) ?>
        </div>

        <div class="col-md-6">
          <?php if ($model->empleado->sysAdmCargo->cargo ?? false): ?>
            <label for="">CARGO</label>
            <div>
              <span> <?= $model->empleado->sysAdmCargo->cargo ?? ' ' ?> </span>
              <span> <?= " - " . ($model->empleado->sysAdmCargo->sysAdmDepartamento->departamento ?? 'loading...') ?> </span>
            </div>
        </div>

      <?php else: ?>
        <label id="cargo-label"></label> <!-- Este es el label donde se mostrará el cargo -->
      <?php endif; ?>      
    </div>
    

    <div class="row text-center">
      <div class="col-md-12">
        <div class="form-group">
          <canvas id="signature-pad" class="d-block" style="border: 1px solid coral;"></canvas>
          <?= Html::hiddenInput('signature_data', '', [
            'id' => 'signature-data',
            'class' => 'd-block mt-2'
          ]) ?>
        </div>
        <label class="d-block">Firma de la persona</label>
      </div>
    </div>
    <div class="row text-center">
      <!-- IMAGE DE EVIDENCIA -->

      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <video id="theVideo" autoplay muted style="display: none;"></video>
              <canvas id="evidencia-pad" style="display: none;"></canvas>
              <?= Html::hiddenInput('evidencia_data', '', [
                'id' => 'evidencia-data',
                'class' => 'd-block mt-2'
              ]) ?>
              <div class="d-grid d-md-block">
                <label class="d-block">Captura/Evidencia del producto </label>
              </div>
              <div class="d-grid gap-2 d-md-block">
                <button type="button" class="btn btn-primary" id="btnCapture">
                  Tomar foto
                </button>
                <button type="button" class="btn btn-primary" id="btnDownloadImage">
                  descargar imagen
                </button>
                <button type="button" class="btn btn-primary" id="btnStartCamera">
                  Iniciar camara
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class='row'>
      <div class='col-md-12'>
        <?php
        echo  TabularInput::widget([
          'models' => $modelDetalle,
          'id' => 'sys_ssoo_registro_entrega_detalle_id',
          'attributeOptions' => [
            // 'enableAjaxValidation'      => true,
            /*enableClientValidation'    => false,
            'validateOnChange'          => false,
            'validateOnSubmit'          => true,
            'validateOnBlur'            => false,*/],

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

          'columns' => [

            [
              'name' => 'id_sys_ssoo_epp',
              'type' => TabularColumn::TYPE_HIDDEN_INPUT
            ],

            [
              'name' => 'sys_ssoo_registro_entrega_detalle_id',
              // 'title' => $modelDetalle[0]->getAttributeLabel('id_sys_ssoo_epp'),
              'title' => 'Nombre EPP',
              'type' => '\kartik\typeahead\Typeahead',
              'options' => [
                'options' => ['placeholder' => 'Buscar..'],
                'pluginOptions' => ['highlight' => true],
                'scrollable' => true,
                'dataset' => [
                  [
                    'remote' => [
                      'url' =>    Url::to(['consultas/listepp']) . '?q=%QUERY',
                      'wildcard' => '%QUERY'
                    ],
                    'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                    'display' => 'value',
                    'templates' => [
                      'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                      'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                    ],

                  ]

                ],
                'pluginEvents' => [
                  // Este evento se ejecuta cuando el usuario selecciona una sugerencia
                  'typeahead:select' => new JsExpression("
                              function(ev, suggestion) {
                                  // Asignamos el ID del producto al campo oculto
                                  //$('#id_sys_ssoo_epp').val(suggestion.id_sys_ssoo_epp);  // Campo oculto para ID
                                  asignarEpp(suggestion.value, suggestion.id_sys_ssoo_epp, ev, suggestion.vida_util)
                                  
                              }
                          "),
                ],
              ],

            ],

            [
              'name' => 'estado',  // File input field
              'title' => 'Observacion',
              'type' => TabularColumn::TYPE_TEXT_INPUT,
              'options' => [
                'required' => true,
                'options' => [
                  'required' => true,
                  'autocomplete' => 'off',
                ],
              ],
            ],
            [
              'name' => 'vida_util',
              'title' => 'Vida útil',
              'type' => TabularColumn::TYPE_TEXT_INPUT,
              'value' => function ($model) {
                return $model->epp->vida_util ?? 50; // 50 es el valor por defecto
              },
              'options' => [
                // 'type' => 'number',
                'readonly' => true,  // Esto hace que el campo sea solo lectura
                'options' => [
                  'required' => true,
                  'readonly' => true,  // Esto hace que el campo sea solo lectura

                ]
              ],
            ],
            // [
            //   'name' => 'file',  // File input field
            //   'title' => 'Archivo',
            //   'type' => FileInput::classname(),
            //   'options' => [
            //     'options' => ['required' => true],
            //     'pluginOptions' => [
            //       'allowedFileExtensions' => ['jpg', 'png'], // Solo permite archivos JPG y PNG
            //       'showUpload' => false, // Oculta el botón de subida
            //       'showPreview' => false, // Desactiva la vista previa
            //       'browseClass' => 'btn btn-primary', // Clase CSS para el botón de selección
            //       'removeClass' => 'btn btn-danger', // Clase CSS para el botón de eliminar
            //       'browseLabel' => 'Seleccionar archivo', // Texto del botón de selección
            //       'removeLabel' => 'Eliminar', // Texto del botón de eliminar
            //       'elCaptionText' => '#customCaption', // Personaliza el texto del caption
            //       'layoutTemplates' => [
            //         'main1' => '{browse} {remove}', // Define el layout sin vista previa
            //       ],
            //     ],
            //   ],
            // ],
            [
              'name' => 'fecha_registro',
              'title' => "Fecha entrega EPP",
              'type'  => \kartik\date\DatePicker::className(),
              'enableError' => true,
              // 'value' => function($data) {
              //   return $data['day'];
              // },
              'options' => [
                'pluginOptions' => [
                  'format' => 'dd-mm-yyyy',
                  // 'format' => 'dd-mm-yyyy hh:ii:ss',
                  'todayHighlight' => true,
                  'endDate' => '0d',
                  'options' => [
                    'class' => 'form-control input-sm', // Clase CSS para hacer el input más pequeño
                    'placeholder' => 'Seleccione una fecha', // Placeholder opcional,
                    'autocomplete' => 'off'
                  ],
                ],

              ]
            ],

          ]
        ])
        ?>
      </div>
    </div>
    <br>
    <div class="form-group text-center">
      <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
  </div>