<?php

use app\models\SysRrhhEmpleados;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;


$template = '<div style="font-size:10px;"><div class="repo-language">{{nombre}}</div>' .
  '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
?>

<div class="sys-ssoo-epp-form">
  <div class='panel panel-default'>
    <div class='panel-body'>
      <?php $form = ActiveForm::begin(); ?>

      <div class='row'>
        <div class='col-md-4'>
          <?= $form->field($model, 'nombre')->textInput(['required' => true, 'value' => 'Registro de Equipos de Protección', 'class' => 'form-control input-sm', 'maxlength' => true, 'placeholder' => 'nombre', 'readonly' => true]) ?>
        </div>
        <div class="col-md-3">
          <label> Usuario</label>
          <p>
             <?= $model->empleado->id_sys_rrhh_cedula ." ".$model->empleado->nombres ?>
            </p>
        </div>
        <div class='col-md-3'>
          <?= $form->field($model, 'fecha_registro')->textInput([
            'value' => date('Y-m-d'), // Fecha actual en formato Y-m-d
            'class' => 'form-control input-sm',
            'type' => 'date',
            'maxlength' => true,
            'placeholder' => 'observacion',
            'readonly' => true, // Bloquear el campo para que no sea editable
            'required' => true,
          ]) ?>
        </div>
        <div class="col-md-6">
          <label for="">CARGO</label>
          <div>
            <span> <?=$model->empleado->sysAdmCargo->cargo ?? ' ' ?> </span>
            <span> <?= " - " .($model->empleado->sysAdmCargo->sysAdmDepartamento->departamento ?? 'loading...' )?> </span>
          </div>
        </div>
        <div class="col-md-6">
            <label for="">Actividad</label>
            <br>
            <?= $model->observacion ?>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12'>
          <?php if (count($modelDetalle) > 0 && $modelDetalle[0]->sys_ssoo_registro_entrega_detalle_id ): ?> 
            
            <?=  TabularInput::widget([
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
                'class' => 'hidden',
                'label' => '<i class="glyphicon glyphicon-plus"></i>'
              ],
              'removeButtonOptions' => [
                'class' => 'hidden',
                'label' => '<i class="glyphicon glyphicon-remove"></i>'
              ],
  
              'columns' => [
  
                [
                  'name' => 'sys_ssoo_registro_entrega_detalle_id',
                  'type' => TabularColumn::TYPE_STATIC
                ],
  
                [
                  'type' => TabularColumn::TYPE_STATIC,
                  'name' => 'id_sys_ssoo_epp',
                  'title' => 'PRODUCTO',  // Puedes cambiar el nombre de la columna si es necesario
                  'value' => function ($model) {
                    $result = \app\models\SysSsooEPP::findOne(['id_sys_ssoo_epp' => $model->id_sys_ssoo_epp]);
                    return $result ? $result->nombre : null;  // Devolvemos el ID, o null si no existe
                  },
                ],
                // [
                //   'name' => 'estado_asignacion',
                //   'title' => ($modelDetalle[0]->getAttributeLabel('estado_asignacion')),
                //   'type' => TabularColumn::TYPE_DROPDOWN,
                //   'enableError' => true,
                //   // 'items' => ArrayHelper::map( Books::find()->asArray()->all (),'id','name'),
                //   'items' => [
                //     'ASIGNADO' => 'ASIGNADO',
                //     'RETIRADO' => 'RETIRADO'
                //   ],
                //   'options' => function ($modelDetalle) {
                //     // Si el estado es "RETIRADO", deshabilitar el dropdown
                //     return $modelDetalle->estado_asignacion === 'RETIRADO' ? ['disabled' => true] : [];
                //   }
                // ],
                [
                  'name' => 'fecha_registro',
                  'type' => TabularColumn::TYPE_STATIC,
                  'title' => $modelDetalle[0]->getAttributeLabel('fecha_registro'),
                  'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->fecha_registro, 'dd-MM-yyyy'); // Formatear la fecha
                  },
                ],
                [
                  'title' => 'Dias de Uso',  // Puedes cambiar el nombre de la columna si es necesario
                  'type' => TabularColumn::TYPE_STATIC,
                  'value' => function ($model) {
                    return $model->diasTranscurridos;
                  },
  
                ],
                [
                  'title' => 'Dias Restantes',  // Puedes cambiar el nombre de la columna si es necesario
                  'type' => TabularColumn::TYPE_STATIC,
                  'value' => function ($model) {
                    return $model->diasRestantes;
                  },
  
                ],
                [
                  'title' => 'Archivo',
                  'name' => 'image_url',
                  'type' => TabularColumn::TYPE_STATIC,
                  'value' => function ($model) {
                    if (!empty($model->image_url)) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Ver', 
                      URL.$model->image_url, [
                          'class' => 'btn btn-info btn-xs',
                          'title' => 'Ver',
                          'target' => '_blank'  // Agregar target="_blank" para abrir en nueva pestaña
                      ]);
                    }
                  },
                ],
                [
                  'title' => 'Firma',
                  'name' => 'firma_empleado_url',
                  'type' => TabularColumn::TYPE_STATIC,
                  'value' => function ($model) {
                    if (!empty($model->firma_empleado_url)) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Ver', 
                      URL.$model->firma_empleado_url, [
                          'class' => 'btn btn-info btn-xs',
                          'title' => 'Ver',
                          'target' => '_blank'  // Agregar target="_blank" para abrir en nueva pestaña
                      ]);
                    }
                  },
                ],
                [
                  'title' => 'Inspeccion', // Nueva columna para las acciones
                  'type' => TabularColumn::TYPE_STATIC,
                  'value' => function ($model) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Ver', 
                          ['registro-epp/viewinspeccion', 'id' => $model->sys_ssoo_registro_entrega_detalle_id], [
                              'class' => 'btn btn-info btn-xs',
                              'title' => 'Ver'
                          ])
                          . ' ' .
                          (!isset($model->fecha_fin) ? 
                            Html::a('<span class="glyphicon glyphicon-pencil"></span> Agregar', 
                            ['registro-epp/inspeccion', 'id' => $model->sys_ssoo_registro_entrega_detalle_id], [
                                'class' => 'btn btn-warning btn-xs',
                                'title' => 'Agregar'
                            ]) 
                        : '');
                      },
                  ],
  
  
              ]
            ])
            ?>
          <?php endif; ?>
        </div>
      </div>


      <div class="form-group text-left">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
      </div>


      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>