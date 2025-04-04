<?php
$this->title = 'Producción Edad';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Dashboard - Producción</h1>
<div class =  "row">
   <div class = "col-md-6">
      <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
             <?php if($datosHombres):
             
                 $promedioEdadHombres = 0;
                 $totalEdadHombres  = 0;
                 
                 foreach ($datosHombres as $item):
                 
                    $totalEdadHombres += $item[0];
                 
                 endforeach;
         
                 if($totalEdadHombres > 0):
                 
                     $promedioEdadHombres = intval($totalEdadHombres/count($datosHombres));
                    
                 endif;    
             ?>
             <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'column'
                       ],
                       'title' => [
                           'text' => 'Edad Hombres'
                       ],
                       'subtitle' => [
                           'text' => 'Edad Promedio: '.$promedioEdadHombres
                       ],
                       'credits'=> [
                           'enabled'=> false
                       ],
                       'xAxis'=> [
                           'type'=> 'category',
                           'labels' => [
                               'rotation'=> -45,
                               'style'=> [
                                   'fontSize'=> '13px',
                                   'fontFamily'=> 'Verdana, sans-serif'
                               ]
                           ]
                       ],
                             
                       'series' => [
                           [ 'name'=> 'Cantidad por Año',
                               'data' => $datosHombres,
                               'color' => '#6AF9C4',
                               'dataLabels'=> [
                                   'enabled'=> true]
                               
                           ],
                           
                       ]
                   ]
            
                ]);
            ?>
            <?php endif;?>
          </div>
      </div>
   </div>
   <div class = "col-md-6">
      <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
             <?php if($datosMujeres):
             
                 $promedioEdadMujeres = 0;
                 $totalEdadMujeres  = 0;
                 
                 foreach ($datosMujeres as $item):
                 
                    $totalEdadMujeres += $item[0];
                 
                 endforeach;
         
                 if($totalEdadMujeres > 0):
                 
                     $promedioEdadMujeres = intval($totalEdadMujeres/count($datosMujeres));
                    
                 endif;    
             ?>
             <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'column'
                       ],
                       'title' => [
                           'text' => 'Edad Mujeres'
                       ],
                       'subtitle' => [
                           'text' => 'Edad Promedio: '.$promedioEdadMujeres
                       ],
                       'credits'=> [
                           'enabled'=> false
                       ],
                       'xAxis'=> [
                           'type'=> 'category',
                           'labels' => [
                               'rotation'=> -45,
                               'style'=> [
                                   'fontSize'=> '13px',
                                   'fontFamily'=> 'Verdana, sans-serif'
                               ]
                           ]
                       ],
                             
                       'series' => [
                           [ 'name'=> 'Cantidad por Año',
                               'data' => $datosMujeres,
                               'color' => '#FFF263',
                               'dataLabels'=> [
                                   'enabled'=> true]
                               
                           ],
                           
                       ]
                   ]
            
                ]);
            ?>
            <?php endif;?>
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
                <th>Edad</th>
              </tr>
            </thead>
            <tbody>
               <?php foreach ($datosHombresDetalle as $index => $data):?>
                 <tr>
                    <td><?= $index + 1?></td>
                    <td><?= $data['departamento']?></td>
                    <td><?= $data['id_sys_rrhh_cedula']?></td>
                    <td><?= $data['nombres']?></td>
                    <td><?= $data['edad']?></td>
                 </tr>
               <?php endforeach;?>
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
                <th>Edad</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($datosMujeresDetalle as $index => $data):?>
               <tr>
                  <td><?= $index + 1?></td>
                  <td><?= $data['departamento']?></td>
                  <td><?= $data['id_sys_rrhh_cedula']?></td>
                  <td><?= $data['nombres']?></td>
                  <td><?= $data['edad']?></td>
               </tr>
               <?php endforeach;?>
            </tbody>
           </table>
        </div>
     </div>
  </div>
</div>