<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Resumen Epp';
$this->params['breadcrumbs'][] = ['label' => 'Lista Registro EPP', 'url' => ['index']];
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

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nombre',
            'id_sys_rrhh_cedula',
            [
                'label' => 'Nobres',
                'value' => $model->empleado ? $model->empleado->nombres : 'No asignado',  // Accede al nombre del empleado
            ],
            'observacion',

        ],
    ]) ?>
    <h1>Detalle del registro de EPP</h1>
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
                                        <th>Nombre del EPP</th>
                                        <th>Vida Util Inicial</th>
                                        <th>Vida Util Estimada</th>
                                        <th>F. de Registro</th>
                                        <th>Estado Asignacion</th>
                                        <th>F. Retiro</th>
                                        <th>Dias de Uso</th>
                                        <th>Firma</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modelDetalle as $key => $detalle): ?>
                                        <tr>
                                            <td><?= $detalle->epp->nombre ?></td>  <!-- Suponiendo que 'nombre_epp' es un atributo de modelDetalle -->
                                            <td><?= $detalle->epp->vida_util ?></td>
                                            <td><?= $detalle->vida_util ?></td>
                                            <td><?= Yii::$app->formatter->asDate($detalle->fecha_registro, 'yyyy-MM-dd') ?></td>
                                            <td><?= $detalle->estado_asignacion ?></td>
                                            <td><?= $detalle->fecha_fin != NULL ? Yii::$app->formatter->asDate($detalle->fecha_fin, 'yyyy-MM-dd') : 'NO'  ?></td>
                                            <td>
                                              <?php
                                                    
                                                    if ($detalle->fecha_registro && $detalle->fecha_fin) {
                                                        $fecha_registro = new \DateTime($detalle->fecha_registro);
                                                        $fecha_fin = new \DateTime($detalle->fecha_fin);
                                                        $diferencia = $fecha_registro->diff($fecha_fin);  // Calcula la diferencia entre las dos fechas
                                                        echo $diferencia->days;  // Muestra la diferencia en días
                                                    } else {
                                                        $fecha_registro = new \DateTime($detalle->fecha_registro);
                                                        $fecha_fin = new \DateTime();
                                                        $diferencia = $fecha_registro->diff($fecha_fin);  // Calcula la diferencia entre las dos fechas
                                                        echo $diferencia->days; // Si alguna de las fechas no existe, no mostrar nada
                                                    }
                                                ?>
                                            </td>
                                            <td><?= \yii\helpers\Html::a('Firma', URL . $detalle->firma_empleado_url, ['target' => '_blank']) ?></td>
                                            </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>