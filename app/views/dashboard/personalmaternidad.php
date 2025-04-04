<?php
/* @var $this yii\web\View */
//$totalEmpleados = array_sum(  array_column($empActivos, 'y'));

$total = array_sum(  array_column($empXAreas, 'y'));
$this->title = 'Personal Lactancia';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Dashboard - Personal Lactancia</h1>
<div class =  "row">
   <div class= "col-md-12">
    <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
          	  <?= \dosamigos\highcharts\HighCharts::widget([
               'clientOptions' => [
                   'chart' => [
                       'type' => 'pie'
                   ],
                   'title' => [
                       'text' => 'Distribución por Areas : '.$total
                   ],
                   'credits'=> [
                       'enabled'=> false
                   ],
                  'plotOptions'=> [
                       'pie'=> [
                           /*'colors'=> [
                               
                               '#FFF263',
                               '#6AF9C4',
                               '#50B432',
                               '#ED561B',
                               '#DDDF00',
                               '#24CBE5',
                               '#64E572',
                               '#FF9655'
                           ],
                           */
                           'allowPointSelect' => true,
                           'cursor' => 'pointer',
                           'dataLabels' => [
                               'enabled'=> true,
                               'format'=> '<b>{point.name}</b>: {point.y}'
                           ],
                           'showInLegend' => true
                       ]
                    ],
                    
                   
                   'series' => [
                       [ 'name'=> 'Brands',
                           'colorByPoint'=> true,
                           'data' => $empXAreas
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
  <div class="col-md-12">
  	  <div class="panel panel-default">
        <div class="panel-heading"></div>
          <div class="panel-body">
          	<table class='table table-bordered table-condensed' style='background-color: white; font-size: 11px; width: 100%;'>
          	   <thead>
          	     <tr>
          	        <th>No.</th>
          	        <th>Area</th>
          	        <th>Departamento</th>
          	        <th>C.C</th>
          	        <th>Nombres</th>
          	        <th>Edad</th>
          	     </tr>
          	   </thead>
          	   <tbody>
          	      <?php foreach ($detalle as $index => $data): ?>
          	        <tr>
          	          <td><?=$index +1?></td>
          	          <td><?=$data['area']?></td>
          	          <td><?=$data['departamento']?></td>
          	          <td><?=$data['id_sys_rrhh_cedula']?></td>
          	          <td><?=$data['nombres']?></td>
          	          <td><?=$data['edad']. " Años"?></td>
          	        </tr>
          	      <?php endforeach;?>
          	   </tbody>
            </table>
        </div>
     </div>
  </div>
</div>

