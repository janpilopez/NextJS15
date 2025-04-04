<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;


echo $this->render('funciones');
$url = Yii::$app->urlManager->createUrl(['soextras']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
AppAsset::register($this);
$con = 0;
$con1 = 0;
 if($datos): ?>  
    <table class="table table-bordered table-condensed" style="background-color: white; font-size: 12px; width: 100%">
         <thead>
             <tr>
               <th width = "5%">Id Solicitud</th>
               <th width = "8%">Día Laborado</th>
               <th>Área</th>
               <th>Total Empleados</th>
               <th>Total H50</th>
               <th>Total H100</th>
            </tr>
         </thead>
         <tbody>
           <?php 
           
           foreach ($datos as $data):
              
            $data_empleados = getDataSolicitudHoras($data['id_sys_rrhh_soextras']);
            $contador = count($data_empleados);
            $totalh50 = 0;
            $totalh100 = 0;

            foreach($data_empleados as $data2):

              //$contador = count($data2['id_sys_rrhh_cedula']);
              $totalh50 += $data2['horas50'];
              $totalh100 += $data2['horas100'];

            endforeach;

              ?>

              <tr>
                <td><?= $data['id_sys_rrhh_soextras'] ?></td>
                <td><?= $data['fecha_registro'] ?></td>
                <td><?= $data['area']?></td>
                <td><?= $contador ?></td>
                <td><?= DecimaltoHoras(number_format($totalh50, 2, '.', '')) ?></td>
                <td><?= DecimaltoHoras(number_format($totalh100, 2, '.', '')) ?></td>
            
              </tr>
            <?php endforeach;?>
         </tbody>    
    </table>
<?php endif;?>    
