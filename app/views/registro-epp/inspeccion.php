<?php

use app\models\SysRrhhEmpleados;
use kartik\file\FileInput;
use kartik\select2\Select2;
use unclead\multipleinput\TabularInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Agregar inspeccion';
$this->params['breadcrumbs'][] = ['label' => 'Crear Registro de inspeccion de epp', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sys-adm-areas-create">

    <h2><?= Html::encode($this->title) ?></h2>
    <div class="sys-ssoo-epp-form">
        <div class='panel panel-default'>
            <div class='panel-body'>
            <div class="row">
        <div class="col-md-3">
            <label >Empleado</label>
            <p class=""><?= $model->registroepp->empleado->nombres ?></p>
            <p class=""><?= $model->registroepp->empleado->id_sys_rrhh_cedula ?></p>
        </div>
        <div class="col-md-3">
            <label >Nombre Equipo PP</label>
            <p class=""><?= $model->epp->nombre ?></p>
        </div>
        <div class="col-md-3">
            <label >Vida Util Inicial</label>
            <p class=""><?= $model->epp->vida_util ?></p>
        </div>
        <div class="col-md-3">
            <label >Vida Util Estimada</label>
            <p class=""><?= $model->vida_util ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label >Fecha Registro</label>
            <p class=""><?= $model->fecha_registro ?></p>
        </div>
        <div class="col-md-3">
            <label >Dias de Uso</label>
            <p class="">
                <?php echo $model->diasTranscurridos; ?>
            </p>
        </div>
        <div class="col-md-3">
            <label >Días Restantes</label>
            <p><?= $model->diasRestantes ?></p>
        </div>
        <div class="col-md-3">
            <label >Fecha Vencimiento</label>
            <p class=""><?= $model->fecha_vencimiento ?></p>
        </div>
    </div>

                <?php $form = ActiveForm::begin([ 'options'=>['enctype'=>'multipart/form-data']]); ?>

                <div class='row'>
                    <div class=''>
                        <?= $form->field($modelDetalle, 'id_sys_ssoo_registro_entrega_detalle')
                            ->textInput([
                                'required' => true,
                                'value' => 'Registro de Equipos de Protección',
                                'class' => 'hidden',
                                'maxlength' => true,
                                'readonly' => true,
                                'value' => $model->sys_ssoo_registro_entrega_detalle_id
                            ])
                            ->label(false); // Desactiva el label
                        ?>
                        <?= $form->field($modelDetalle, 'id_sys_ssoo_epp')
                            ->textInput([
                                'required' => true,
                                'value' => 'Registro de Equipos de Protección',
                                'class' => 'hidden',
                                'maxlength' => true,
                                'readonly' => true,
                                'value' => $model->id_sys_ssoo_epp
                            ])
                            ->label(false); // Desactiva el label
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelDetalle, 'id_sys_rrhh_cedula')->textInput(['required' => true, 'value'=> $model->registroepp->id_sys_rrhh_cedula, 'class' => 'form-control input-sm', 'type' => 'text', 'readOnly' => true,  'placeholder' => 'cedula']) ?>
                    </div>
                    <div class='col-md-3'>
                        <?= $form->field($modelDetalle, 'fecha_registro')->textInput([
                            'value' => date('Y-m-d'), // Fecha actual en formato Y-m-d
                            'class' => 'form-control input-sm',
                            'type' => 'date',
                            'maxlength' => true,
                            'placeholder' => 'observacion',
                            'readonly' => true, // Bloquear el campo para que no sea editable
                            'required' => true,
                        ]) ?>
                    </div>
                    <div class='col-md-4'>
                        <?= $form->field($modelDetalle, 'tipo_inspeccion')->dropDownList(['Mantener'=> 'Mantener', 'Acortar'=> 'Acortar', 'Alargar'=> 'Alargar'], ['class'=>'form-control input-sm'])?>
                    </div>
                    <div class='col-md-4'>
                        <?= $form->field($modelDetalle, 'resultado_inspeccion')->textInput(['required' => true, 'class' => 'form-control input-sm', 'type' => 'text', 'maxlength' => true, 'placeholder' => 'resultado']) ?>
                    </div>
                    <div class='col-md-4'>
                        <?= $form->field($modelDetalle, 'tiempo_resultado_inspeccion')->textInput(['required' => true, 'class' => 'form-control input-sm', 'type' => 'number', 'max' => 300,'min' => 0, 'placeholder' => 'tiempo']) ?>
                    </div>
                    <div class='col-md-4'>
                        <?= $form->field($modelDetalle, 'file')->widget(FileInput::classname(), [
                        'options' => ['accept' => 'file/*', 'required'=> true],
                        'pluginOptions'=>['allowedFileExtensions'=>['jpg','png'],'showUpload' => false],
                        ]);?>
                    </div>
                </div>

                <div class="form-group text-left">
                    <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
                </div>


                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>