<?php
/* @var $this yii\web\View */

$anioAct = date('Y');
$anioAnt = date('Y') -1;

$this->title = 'Salida Empleados';
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>Dashboard - Salida Personal</h1>
<div class =  "row">
   <div class = "col-md-12">
      <div class="panel panel-default">
          <div class="panel-heading"></div>
          <div class="panel-body">
             	     <?= \dosamigos\highcharts\HighCharts::widget([
                   'clientOptions' => [
                       'chart' => [
                           'type' => 'column'
                       ],
                       'credits'=> [
                           'enabled'=> false
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
                          'series' => [
                              'dataLabels' => [
                                  'enabled'=> true,
                                  //'format'=> '<b>{point.percentage:.1f} %'
                                  'format'=> '<b>{point.y}</b>'
                              ],
                          ]
                        ],
                       'series' => $empSalida
                   ]
        
                    ]);
                  ?>
          </div>
      </div>
   </div>
</div>

