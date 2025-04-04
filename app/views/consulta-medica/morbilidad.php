<?php 
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use SebastianBergmann\CodeCoverage\Report\PHP;
$this->title = 'Indicadores de Morbilidad';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-4'>
             <label >Año</label>
             <input type="number" class="form-control" value = "<?=  date('Y')?>" name = 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control', 'prompt' => 'Seleccione..',
                   'options' =>[ $mes => ['selected' => true]]
                  ]);
               ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <br>
   <?php ActiveForm::end(); ?>
 <div class="row">
  	<div class="col-md-6">
     <?php if($datos): ?>
       <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
          
          
              <?php  $total = array_sum(  array_column($datos, 'y'));?>
          
          
                <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'pie'
                       ],
                       'title' => [
                           'text' => 'Morbilidad por Patología - '.$meses[$mes]. " del  ".$anio
                       ],
                       'subtitle' => [
                           'text' => 'Total de Atenciones : '.$total
                       ],
                       'credits'=> [
                           'enabled'=> false
                       ],
                      'plotOptions'=> [
                           'pie'=> [
                              
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
                           [ 'name'=> 'Indice',
                               'colorByPoint'=> true,
                               'data' => $datos
                           ]
                       ]
                   ]
            
                ]);
            ?>
          </div>
      </div>
     <?php endif;?>   
     </div>
    <div class="col-md-6">
     <?php if($atencionesxArea): ?>
       <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
          
               <?php  $totalArea = array_sum(  array_column($atencionesxArea, 'y'));?>
          
          
                <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'pie'
                       ],
                       'title' => [
                           'text' => 'Atenciones por Área  - '.$meses[$mes]. " del  ".$anio
                       ],
                       'subtitle' => [
                           'text' => 'Total de Atenciones : '.$totalArea
                       ],
                       'credits'=> [
                           'enabled'=> false
                       ],
                      'plotOptions'=> [
                           'pie'=> [
                              
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
                           [ 'name'=> 'Indice',
                               'colorByPoint'=> true,
                               'data' => $atencionesxArea
                           ]
                       ]
                   ]
            
                ]);
            ?>
          </div>
      </div>
     <?php endif;?>   
     </div>
 </div>
 <div class= "row">
 	 <div class= "col-md-6">
     	  <?php if($atencionesxSexo):
     	  
     	   $totalAntecionxSexo = array_sum(array_column($atencionesxSexo, 'y'));

     	  ?>
           <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body">
                    <?= \dosamigos\highcharts\HighCharts::widget([
                       'clientOptions' => [
                           'chart' => [
                               'type' => 'column'
                           ],
                           'title' => [
                               'text' => 'Atenciones por Sexo  - '.$meses[$mes]. " del  ".$anio
                           ],
                           'subtitle' => [
                               'text' => 'Total de Atenciones : '.$totalAntecionxSexo
                           ],
                           'credits'=> [
                               'enabled'=> false
                           ],
                           'xAxis'=> [
                               'type'=> 'category'
                           ],
                           
                           'plotOptions'=> [
                               'series'=> [
                                   'borderWidth' => 0,
                                   'dataLabels' => [
                                       'enabled' => true,
                                       'format'=> '<span style="font-size:16px">{point.y}</span>'
                                   ]
                               ]
                           ],
                           'series' => [
                               [ 'name'=> 'Atenciones',
                                   'colorByPoint'=> true,
                                   'data' => $atencionesxSexo
                               ]
                           ]
                       ]
                    ]);
                ?>
              </div>
          </div>
         <?php endif;?> 
 	 </div>
 	 <div class= "col-md-6">
 	   <?php if($incidentesAccidentes):
 	   
 	      $totalIncidentesAccidentes = array_sum(array_column($incidentesAccidentes, 'y'));
 	   
 	   ?>
           <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body">
                    <?= \dosamigos\highcharts\HighCharts::widget([
                       'clientOptions' => [
                           'chart' => [
                               'type' => 'column'
                           ],
                           'title' => [
                               'text' => 'Incidentes y Accidentes  - '.$meses[$mes]. " del  ".$anio
                           ],
                           'subtitle' => [
                               'text' => 'Total Incidentes/Accidentes : '.$totalIncidentesAccidentes
                           ],
                           'credits'=> [
                               'enabled'=> false
                           ],
                           'xAxis'=> [
                               'type'=> 'category'
                           ],
                           
                           'plotOptions'=> [
                               'series'=> [
                                   'borderWidth' => 0,
                                   'dataLabels' => [
                                       'enabled' => true,
                                       'format'=> '<span style="font-size:16px">{point.y}</span>'
                                   ]
                               ]
                           ],
                           'series' => [
                               [ 'name'=> 'Incidentes/Accidentes',
                                   'colorByPoint'=> true,
                                   'data' => $incidentesAccidentes
                               ]
                           ]
                       ]
                    ]);
                ?>
              </div>
          </div>
         <?php endif;?> 
 	 </div>
 </div>
 <div class= "row">
   <div class= "col-md-6">
   		<?php if($incidentesXGenero):
     	  
   		  $totalincidentesXGenero = array_sum(array_column($incidentesXGenero, 'y'));

     	  ?>
           <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body">
                    <?= \dosamigos\highcharts\HighCharts::widget([
                       'clientOptions' => [
                           'chart' => [
                               'type' => 'column'
                           ],
                           'title' => [
                               'text' => 'Incidentes por Sexo  - '.$meses[$mes]. " del  ".$anio
                           ],
                           'subtitle' => [
                               'text' => 'Incidentes : '.$totalincidentesXGenero
                           ],
                           'credits'=> [
                               'enabled'=> false
                           ],
                           'xAxis'=> [
                               'type'=> 'category'
                           ],
                           
                           'plotOptions'=> [
                               'series'=> [
                                   'borderWidth' => 0,
                                   'dataLabels' => [
                                       'enabled' => true,
                                       'format'=> '<span style="font-size:16px">{point.y}</span>'
                                   ]
                               ]
                           ],
                           'series' => [
                               [ 'name'=> 'Incidentes',
                                   'colorByPoint'=> true,
                                   'data' => $incidentesXGenero
                               ]
                           ]
                       ]
                    ]);
                ?>
              </div>
          </div>
         <?php endif;?> 
   </div>
   <div class= "col-md-6">
    
     <?php if($accidentesXGenero):
     	  
        $totalaccidentesXGenero = array_sum(array_column($accidentesXGenero, 'y'));

     	  ?>
           <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body">
                    <?= \dosamigos\highcharts\HighCharts::widget([
                       'clientOptions' => [
                           'chart' => [
                               'type' => 'column'
                           ],
                           'title' => [
                               'text' => 'Accidentes por Sexo  - '.$meses[$mes]. " del  ".$anio
                           ],
                           'subtitle' => [
                               'text' => 'Accidentes : '.$totalaccidentesXGenero
                           ],
                           'credits'=> [
                               'enabled'=> false
                           ],
                           'xAxis'=> [
                               'type'=> 'category'
                           ],
                           
                           'plotOptions'=> [
                               'series'=> [
                                   'borderWidth' => 0,
                                   'dataLabels' => [
                                       'enabled' => true,
                                       'format'=> '<span style="font-size:16px">{point.y}</span>'
                                   ]
                               ]
                           ],
                           'series' => [
                               [ 'name'=> 'Accidentes',
                                   'colorByPoint'=> true,
                                   'data' => $accidentesXGenero
                               ]
                           ]
                       ]
                    ]);
                ?>
              </div>
          </div>
         <?php endif;?> 
   </div>
 </div>
</div>
 