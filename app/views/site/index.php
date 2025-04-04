<?php

/* @var $this yii\web\View */
use app\models\SysEmpresa;
use app\models\User;
$this->title = 'Gestión';
?>
<?php $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();?>

<div class="site-index">

<?php if($empresa['razon_social'] == 'INDICADORES'): ?>

<div class=  "row">
    <div class= 'col-md-12 text-center'> 
        <img  alt="Fondo" width="50%" min-height= 100%; src=" <?= trim("logo/".$empresa->ruc."/".$empresa->logo)?>" >
    </div>
</div>

<?php elseif(User::hasRole('GERENTE') ||  User::hasRole('jefeNomina')):?>

<?php 
$totalEmpleados = array_sum(  array_column($empActivos, 'y'));

$anioAct = date('Y');
$anioAnt = date('Y') -1;

?>
<div class=  "row">
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
                               'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %'
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
                       'text' => 'Empleados por Areas'
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
                               'format'=> '<b>{point.percentage:.1f} %'
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
   <div class = "col-md-12">
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
			      <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'column'
                       ],
                       'title' => [
                           'text' => 'Ingreso de Personal '.$anioAnt.' Vs '.$anioAct
                       ],
                       'xAxis' => [
                           'categories'=> [
                               'Ene',
                               'Feb',
                               'Mar',
                               'Apr',
                               'May',
                               'Jun',
                               'Jul',
                               'Ago',
                               'Sep',
                               'Oct',
                               'Nov',
                               'Dec'
                           ],
                           'crosshair' => true
			              ],
                       
                      'plotOptions'=> [
                          
                          'column' => [
                              'pointPadding' => '0.2',
                              'borderWidth' => '0'
                           ],
                        ],
                       'series' => $empIngresos
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
                           'type' => 'column'
                       ],
                       'title' => [
                           'text' => 'Salida de Personal '.$anioAnt.' Vs '.$anioAct
                       ],
                       'xAxis' => [
                           'categories'=> [
                               'Ene',
                               'Feb',
                               'Mar',
                               'Apr',
                               'May',
                               'Jun',
                               'Jul',
                               'Ago',
                               'Sep',
                               'Oct',
                               'Nov',
                               'Dec'
                           ],
                           'crosshair' => true
			              ],
                       
                      'plotOptions'=> [
                          
                          'column' => [
                              'pointPadding' => '0.2',
                              'borderWidth' => '0'
                           ],
                        ],
                       'series' => $empSalida
                   ]
        
                    ]);
                  ?>
    			 </div>
		 </div>
	</div>
</div>
<?php else:?>
  <div class = 'row'>
       <br>
       <br>
         <div class= 'col-md-12 text-center'>
             <?php if($empresa): ?>
            	<img  alt="Fondo" width="50%" min-height= 100%; src=" <?= trim("logo/".$empresa->ruc."/".$empresa->logo)?>" >
              <?php endif;?>
         </div>
       </div>
<?php endif ?>
</div>
