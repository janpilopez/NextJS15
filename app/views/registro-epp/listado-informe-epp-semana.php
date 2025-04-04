<?php

use phpDocumentor\Reflection\Types\Integer;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

$this->title = 'Listado Compras Epp';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
setlocale(LC_ALL, 'es_ES');

$date = new DateTime();
$date->setISODate($anio, 53); // Intentamos obtener la semana 53
$numeroDeSemanas = ($date->format("W") === "53") ? 53 : 52;

// Función para obtener el mes de una semana específica del año
function getMonthFromWeek($year, $week) {
    $date = new DateTime();
    $date->setISODate($year, $week);
    return $date->format('M'); // Devuelve el mes abreviado (Ene, Feb, etc.)
}
?>
<style>
    .col-wrap {
        max-width: 200px; /* Ajusta el tamaño de la celda */
        word-wrap: break-word; /* Permite que las palabras largas se dividan */
        font-size: 10px; 
        white-space: normal; /* Asegura que el texto largo pueda romperse en varias líneas */
        overflow: hidden; /* Evita que el contenido se desborde */
        /* word-break: break-all; */
    }
</style>

<div class="">
<h2>Listado de Proyección Equipos de Protección Personal | Por Semanas <?= $anio ?> </h2>
<form action="">
        <input type="text" name="anio" value="<?= $anio?>" placeholder="año">
        <input type="text" name="empleado" value="<?= $empleado?>" placeholder="nombre empleado">
        <!-- <input type="text" name="semana" value="<?= $semana?>" placeholder="semana"> -->
        <input type="text" name="nombre_epp" value="<?= $nombre_epp?>" placeholder="nombre epp">
        <select name="selectedDepartamento" id="selectedDepartamento">
            <option value="">Todos</option>
            <?php foreach ($departamentos as $departamento): ?>
                <option value="<?= Html::encode($departamento->departamento) ?>" 
                    <?= $departamento->departamento == $selectedDepartamento ? 'selected' : '' ?>>
                    <?= Html::encode($departamento->departamento) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Consultar</button>
    </form>
    <br>
    <!-- <table class="table" style="">
        <thead>
        <tr>
                <th rowspan="2">Empleado</th>
                <th rowspan="2">Departamento</th>
                <th rowspan="2">EPP</th>
                <th style="border: 0.5px solid black; width: 140px;" colspan="4" class="text-center">Enero</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Febrero</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Marzo</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Abril</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Mayo</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Junio</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Julio</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Agosto</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Septiembre</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Octubre</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Noviembre</th>
                <th style="border: 0.5px solid black;  width: 140px;" colspan="4" class="text-center">Diciembre</th>
            </tr>

        </thead>
    </table> -->
    <table class="table">
        <thead>
        <tr >
                <th rowspan="2">Empleado</th>
                <th rowspan="2">Departamento</th>
                <th rowspan="2">EPP</th>
                <?php for ($i = 1; $i <= $numeroDeSemanas; $i++): ?>
                    <td class="text-center" style="margin: 0px; padding: 0px; border: 1px solid black;">
                        <?= getMonthFromWeek($anio, $i) ?> 
                    <?= $i ?>
                        </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($registros as $registro): ?>

                <tr>
                    <td rowspan="1" class="col-wrap">
                        <?= Html::encode($registro['nombreEmpleado']) ?><br>
                        <?= Html::encode($registro['id_sys_rrhh_cedula']) ?>
                    </td>
                    <td rowspan="1" class="col-wrap"><?= Html::encode($registro['departamento']) ?></td>
                    <td rowspan="1" class="col-wrap"><?= Html::encode($registro['nombreEpp']) ?></td>
                    <?php for ($i = 1; $i <= $numeroDeSemanas; $i++): ?>
                        <td colspan="1" class="text-center" style="border: 1px solid black;">
                            <?= $i == intval($registro['numeroSemana']) ? 'X' : '' ?>
                            
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <td colspan="4" class="text-center">
                        Mes #<?= $i." : ".$totales[$i] ?? 0 ?>
                    </td>
                <?php endfor; ?>
            </tr>
        </tfoot>
    </table>
</div>

