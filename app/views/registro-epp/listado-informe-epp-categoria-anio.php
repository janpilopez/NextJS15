<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;


// Preparamos los datos para el GridView
$data = [];
foreach ($registros as $registro) {
    $row = [
        'nombre' => $registro['nombreEpp'],
    ];
    
    // Agregamos los meses
    for ($i = 1; $i <= 12; $i++) {
        //Creamos la codificación del índice con un cero a la izquierda
        $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
        // $row['mes_'.$mes] = count($vencimientos[$registro['id_sys_ssoo_epp']][$mes] ?? []);
        // echo json_encode($registro['meses'][$i]);
        $row['mes_'.$mes] = $registro['meses'][$i] ?? 0;
    }
    // Calculamos el total
    $row['total'] = array_sum(array_slice($row, 1));//EMPIEZE DESDE EL 1
    
    $data[] = $row;
}


// Inicializamos array para totales por mes
$totalesMeses = array_fill(1, 12, 0);

// Calculamos los totales por columna (mes)
foreach ($data as $fila) {
    for ($i = 1; $i <= 12; $i++) {
        $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
        $totalesMeses[$i] += $fila['mes_'.$mes] ?? 0;
    }
}

// Agregamos la fila de totales
$rowTotal = [
    'nombre' => "TOTALES",
    'total' => array_sum($totalesMeses) // Suma de todos los totales
];

// Agregamos los totales por mes
for ($i = 1; $i <= 12; $i++) {
    $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
    $rowTotal['mes_'.$mes] = $totalesMeses[$i];
}

$data[] = $rowTotal;


$dataProvider = new ArrayDataProvider([
    'allModels' => $data,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => ['nombre', 'total'],
    ],
]);

// Registra los assets de DataTables
$this->registerJsFile('https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js');
$this->registerCssFile('https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
?>

<div class="container">
    <h2>Listado de Proyección Equipos de Protección Personal Agrupado | Anual <?= $anio ?> </h2>
    <form action="">
        <label>Año: </label>
        <input type="text" name="anio" value="<?= $anio?>" placeholder="año">
        <button type="submit">Consultar</button>
    </form>
    
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-hover table-bordered',
            'id' => 'eppTable',
            'showFooter' => true,
        ],
        'columns' => [
            [
                'attribute' => 'nombre',
                'label' => 'Nombre EPP',
                'contentOptions' => ['class' => 'col-wrap'],
            ],
            // Columnas para cada mes
            [
                'attribute' => 'mes_01',
                'label' => 'Enero',
                'contentOptions' => ['style' => 'text-align: center;'],
                'footer' => "2",       
            ],
            [
                'attribute' => 'mes_02',
                'label' => 'Febrero',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_03',
                'label' => 'Marzo',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_04',
                'label' => 'Abril',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_05',
                'label' => 'Mayo',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_06',
                'label' => 'Junio',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_07',
                'label' => 'Julio',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_08',
                'label' => 'Agosto',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_09',
                'label' => 'Septiembre',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_10',
                'label' => 'Octubre',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_11',
                'label' => 'Noviembre',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'mes_12',
                'label' => 'Diciembre',
                'contentOptions' => ['style' => 'text-align: center;'],
            ],          
            [
                'attribute' => 'total',
                'label' => 'Total',
                'contentOptions' => ['class' => 'success', 'style' => 'text-align: center; font-weight: bold;'],
            ],
        ],
    ]); ?>
</div>

<?php
// Script para inicializar DataTables
$this->registerJs(<<<JS
    $(document).ready(function() {
        $('#eppTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            pageLength: 25,
            responsive: true
        });
    });
JS
);
?>
