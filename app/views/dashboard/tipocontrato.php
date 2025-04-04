<?php

$total = array_sum(array_column($totalContrato, 'y'));

$this->title = 'Tipo de Contrato';
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>Dashboard - Personal Tipo de Contrato</h1>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <?= \dosamigos\highcharts\HighCharts::widget([
               'clientOptions' => [
                  'chart' => [
                     'type' => 'pie'
                  ],
                  'title' => [
                     'text' => 'Distribución por Tipo de Contrato'
                  ],
                  'credits' => [
                     'enabled' => false
                  ],
                  'plotOptions' => [
                     'pie' => [
                        'colors' => [

                           '#F1C40F',
                           '#2980B9',
                           '#C0392B',
                           '#1ABC9C',
                           '#9B59B6'

                        ],

                        'allowPointSelect' => true,
                        'cursor' => 'pointer',
                        'dataLabels' => [
                           'enabled' => true,
                           //'format'=> '<b>{point.percentage:.1f} %'
                           'format' => '<b>{point.name}</b>: {point.y}'

                        ],
                        'showInLegend' => true
                     ]
                  ],


                  'series' => [
                     [
                        'name' => 'Brands',
                        'colorByPoint' => true,
                        'data' => $tipoContrato
                     ]
                  ]
               ]

            ]);
            ?>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosIndefinido as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosEmergente as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosEventual as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>

   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosPasantia as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosTemporada as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading"></div>
         <div class="panel-body">
            <table class="table" style="background-color: white; font-size: 10px; width: 100%;">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Departamento</th>
                     <th>C.I</th>
                     <th>Nombres</th>
                     <th>Tipo Contrato</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($datosProduccion as $index => $data): ?>
                     <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['departamento'] ?></td>
                        <td><?= $data['id_sys_rrhh_cedula'] ?></td>
                        <td><?= $data['nombres'] ?></td>
                        <td><?= $data['contrato'] ?></td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>