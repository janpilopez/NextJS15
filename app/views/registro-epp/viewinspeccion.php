<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Detalle del EPP';
$this->params['breadcrumbs'][] = ['label' => 'Listado', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);

?>
<style>
    .clockdate-wrapper {
        background-color: #333;
        padding: 25px;
        max-width: 300px;
        width: 100%;
        text-align: center;
        border-radius: 5px;
        margin: 0 auto;
        margin-top: 15%;
    }

    #clock {
        background-color: #333;
        font-family: sans-serif;
        font-size: 40px;
        text-shadow: 0px 0px 1px #fff;
        color: #fff;
    }

    #text {
        background-color: #fff;
        font-family: sans-serif;
        font-size: 15px;
        text-shadow: 0px 0px 1px #fff;
        color: #000;
        ;
    }

    #clock span {
        color: #888;
        text-shadow: 0px 0px 1px #333;
        font-size: 30px;
        position: relative;
        top: -27px;
        left: -10px;
    }

    #date {
        letter-spacing: 10px;
        font-size: 14px;
        font-family: arial, sans-serif;
        color: #fff;
    }

    .titulo {

        font-size: 250px;
        font-weight: bold;
    }

    .table-wrapper {
        width: 100%;
        height: 300px;
        /* Altura de ejemplo */
        overflow: auto;
    }

    .table-wrapper table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-wrapper table thead {
        position: -webkit-sticky;
        /* Safari... */
        position: sticky;
        top: 0;
        left: 0;
    }

    .table-wrapper table thead th,
    .table-wrapper table tbody td {
        background-color: #FFF;
    }
</style>

<div class="sys-acceso-maestroepp-view">

    <h2><?= Html::encode($this->title) ?></h2>
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
            <p><?= ($model->fecha_registro ?? NULL) != NULL ? Yii::$app->formatter->asDate($model->fecha_registro, 'yyyy-MM-dd') : ''  ?></p>

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
            <p><?= $model->fecha_vencimiento != NULL ? Yii::$app->formatter->asDate($model->fecha_vencimiento, 'yyyy-MM-dd') : ''  ?></p>
        </div>
        <div class="col-md-3">
            <label >Observacion</label>
            <p><?= $model->estado ?></p>
        </div>
    </div>
    <h3><?= Html::encode("Detalle de inspecciones") ?></h3>
    <div class="sys-rrhh-comedor-form">
        <div class="row">
            <div class="col-md-12">
                <br>
                <div class="panel panel-default">
                    <div class="panel-heading"></div>
                    <div class="panel-body">
                        <div class="table-wrapper">
                            <table id="table" class="table" style="background-color: white; font-size: 16px; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Tipo Inspeccion</th>
                                        <th>Resultado Inspeccion</th>
                                        <th>Tiempo Estimado</th>
                                        <th>Fecha Registro</th>
                                        <th>Imagen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!isset($modelDetalle) ): ?>
                                        <tr>
                                            <td colspan="5">No hay inspecciones realizadas.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($modelDetalle as $key => $detalle): ?>
                                            <tr>
                                                <td><?= $detalle->tipo_inspeccion ?? '' ?></td>
                                                <td><?= $detalle->resultado_inspeccion ?? '' ?></td>
                                                <td><?= $detalle->tiempo_resultado_inspeccion ?? '' ?></td>
                                                <td><?= ($detalle->fecha_registro ?? NULL) != NULL ? Yii::$app->formatter->asDate($detalle->fecha_registro, 'yyyy-MM-dd') : ''  ?></td>
                                                <td>
                                                    <div class='d-flex flex-nowrap align-items-center'>
                                                        <?php
                                                            if (!empty($detalle->image_url)) {
                                                                $imagenUrl = URL.$detalle->image_url;
                                                                echo "<img src='$imagenUrl' class='img-thumbnail me-2' style='width: 50px; height: auto;' alt='Imagen previa'>";
                                                                echo "<a href='$imagenUrl' target='_blank' class='btn btn-xs btn-primary'>Abrir</a>";
                                                            }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>