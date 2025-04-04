<?php
/* @var $this yii\web\View */
$totalEmpleados = array_sum(  array_column($empActivos, 'y'));
$this->title = 'Personal Activo';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Dashboard - Personal Activo</h1>
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
                           'text' => 'Empleados Activos - '.$totalEmpleados
                       ],
                       'credits'=> [
                           'enabled'=> false
                       ],
                      'plotOptions'=> [
                           'pie'=> [
                               'colors'=> [
                                   
                                   '#FFF263',
                                   '#6AF9C4',
                                   '#50B432',
                                   '#ED561B',
                                   '#DDDF00',
                                   '#24CBE5',
                                   '#64E572',
                                   '#FF9655'
                               ],
                               'allowPointSelect' => true,
                               'cursor' => 'pointer',
                               'dataLabels' => [
                                   'enabled'=> true,
                                   //'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %'
                                   'format'=> '<b>{point.name}</b>: {point.y}'
                               ],
                               'showInLegend' => true
                           ]
                        ],
                        
                       
                       'series' => [
                           [ 'name'=> 'Brands',
                               'colorByPoint'=> true,
                               'data' => $empActivos
                           ]
                       ]
                   ]
            
                ]);
            ?>
          </div>
      </div>
   </div>
   <div class= "col-md-6">
    <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
          	  <?= \dosamigos\highcharts\HighCharts::widget([
               'clientOptions' => [
                   'chart' => [
                       'type' => 'pie'
                   ],
                   'title' => [
                       'text' => 'Empleados por Areas'
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
                               //'format'=> '<b>{point.percentage:.1f} %'
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
	     <?php 
	     
	     $categories = [];
	     $masculino = [];
	     $feminino = [];
	     
	     foreach ($generoXArea as $item):
	       
	       $categories[]= $item['area'];
	       $masculino[] =  intval($item['MASCULINO']);
	       $feminino[] = intval($item['FEMENINO']);
	     
	     endforeach;
	     
	     ?>
	     <?= \dosamigos\highcharts\HighCharts::widget([
	         'clientOptions' => [
	             'chart' => [
	                 'type' => 'bar'
	             ],
	             'title' => [
	                 'text' => 'Distribución por Area'
	             ],
	             'credits'=> [
	                 'enabled'=> false
	             ],
	             'xAxis' => [
	                 'categories'=> $categories
                 ],
	             'plotOptions' => [
	                 'bar'=> [
	                   
	                     'dataLabels' =>  [
	                         'enabled' => true,
	                        
	                     ]
	                 ],
	              
	              ],
	          
	             'legend'=> [
	                 'layout'=> 'vertical',
	                 'align'=> 'right',
	                 'verticalAlign'=> 'top',
	                 'x'=> -40,
	                 'y'=> 80,
	                 'floating'=> true,
	                 'borderWidth'=> 1,
	                 'backgroundColor'=>
	                 'Highcharts.defaultOptions.legend.backgroundColor' || '#FFFFFF',
	                 'shadow'=> true
	                     ],
	             'series' => [
	                 [
	                 'name'=> 'Hombres',
	                 'data' => $masculino,
	                 'color' => '#6AF9C4'
	                
	                 ], 
	                 [
	                  'name'=> 'Mujeres',
	                  'data'=> $feminino,
	                  'color' => '#FFF263'
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
     <?php if($edadMujeres): ?>
       <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">   
              <?php   $totalMujeresEdad = 0;
              foreach ($edadMujeres as $item):
               
                $totalMujeresEdad = $totalMujeresEdad + $item[0];
              
              endforeach;
              
              $promedioEdadMujeres = intval($totalMujeresEdad/count($edadMujeres));
              
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
                           [ 'name'=> '',
                               'data' => $edadMujeres,
                               'color' => '#FFF263',
                               'dataLabels'=> [
                                   'enabled'=> true]
                               
                           ],
                           
                       ]
                   ]
            
                ]);
            ?>
          </div>
      </div>
     <?php endif;?>   
    </div>
  </div>
<div class="row">
  	<div class="col-md-12">
     <?php if($edadHombres): ?>
       <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">   
              <?php   $totalHombresEdad = 0;
              foreach ($edadHombres as $item):
               
                 $totalHombresEdad = $totalHombresEdad + $item[0];
              
              endforeach;
              
              $promedioEdadHombres = intval($totalHombresEdad/count($edadHombres));
              
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
                           [ 'name'=> '',
                               'data' => $edadHombres,
                               'color' => '#6AF9C4',
                               'dataLabels'=> [
                                   'enabled'=> true]
                               
                           ],
                           
                       ]
                   ]
            
                ]);
            ?>
          </div>
      </div>
     <?php endif;?>   
    </div>
  </div>