<?php
use app\models\SysRrhhEmpleadosMarcacionesReloj;
$totalEmpleadosPlanta = array_sum(array_column($empActivosPlanta, 'y'));
$totalEmpleadosPlantaVisitas = array_sum(array_column($empActivosPlantaVisitas, 'y'));
$this->title = 'Personal en Planta';
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>Dashboard - Personal en Planta</h1>
<div class =  "row">
   <div class = "col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
                <div class="panel-body">
      	        <?= \dosamigos\highcharts\HighCharts::widget([
               'clientOptions' => [
                   'chart' => [
                       'type' => 'pie'
                   ],
                   'title' => [
                       'text' => 'Personal en Planta - '.$totalEmpleadosPlanta
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
                               //'format'=> '<b>{point.percentage:.1f} %'
                               'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %'
                           ],
                           'showInLegend' => true
                       ]
                    ],
                    
                   
                   'series' => [
                       [ 'name'=> 'Brands',
                           'colorByPoint'=> true,
                           'data' => $empActivosPlanta
                       ]
                   ]
               ]
        
            ]);
        ?>
                </div>
        </div>
    </div>  
    <div class = "col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
                <div class="panel-body">
      	        <?= \dosamigos\highcharts\HighCharts::widget([
               'clientOptions' => [
                   'chart' => [
                       'type' => 'pie'
                   ],
                   'title' => [
                       'text' => 'Visitas en Planta - '.$totalEmpleadosPlantaVisitas
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
                               //'format'=> '<b>{point.percentage:.1f} %'
                               'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %'
                           ],
                           'showInLegend' => true
                       ]
                    ],
                    
                   
                   'series' => [
                       [ 'name'=> 'Brands',
                           'colorByPoint'=> true,
                           'data' => $empActivosPlantaVisitas
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
                <th>Ingreso</th>
              </tr>
            </thead>
            <tbody>
               <?php 
                  $cont = 0;
                  foreach ($entHombre as $index => $data):
                     $salida = obtenerSalida($data['id_sys_rrhh_cedula']);
                     if(!$salida):
                        $cont++;
                  ?>
               <tr>
                  <td><?= $cont?></td>
                  <td><?= $data['departamento']?></td>
                  <td><?= $data['id_sys_rrhh_cedula']?></td>
                  <td><?= $data['nombres']?></td>
                  <td><?= date('H:i:s',strtotime($data['fecha_marcacion']))?></td>
               </tr>
               <?php
                  endif; 
               endforeach;?>
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
                <th>Ingreso</th>
              </tr>
            </thead>
            <tbody>
               <?php 
                  $cont = 0;
                  foreach ($entMujer as $index => $data):
                     $salida = obtenerSalida($data['id_sys_rrhh_cedula']);
                     if(!$salida):
                        $cont++;
                  ?>
               <tr>
                  <td><?= $cont?></td>
                  <td><?= $data['departamento']?></td>
                  <td><?= $data['id_sys_rrhh_cedula']?></td>
                  <td><?= $data['nombres']?></td>
                  <td><?= date('H:i:s',strtotime($data['fecha_marcacion']))?></td>
               </tr>
               <?php
                  endif; 
               endforeach;?>
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
                <th>Ingreso</th>
              </tr>
            </thead>
            <tbody>
               <?php 
                  $cont = 0;
                  foreach ($entVisitas as $index => $data):
                     $cont++;
                  ?>
               <tr>
                  <td><?= $cont?></td>
                  <td><?= $data['departamento']?></td>
                  <td><?= $data['id_sys_rrhh_cedula']?></td>
                  <td><?= $data['nombres']?></td>
                  <td><?= date('H:i:s',strtotime($data['hora_ingreso']))?></td>
               </tr>
               <?php

               endforeach;?>
            </tbody>
           </table>
        </div>
     </div>
  </div>
</div>

<?php

function obtenerSalida($cedula){
   return SysRrhhEmpleadosMarcacionesReloj::find()->where(['id_sys_rrhh_cedula'=>$cedula])->andWhere(['tipo'=>'S'])->andWhere(['fecha_jornada'=>date('Y-m-d')])->one();
}

?>
