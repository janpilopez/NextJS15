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
class FilterColumn {
  private $colName;
  
  function __construct($colName) {
      $this->colName = $colName;
  }
  
  function getValues($i) {
      
      return $i[$this->colName] ;
  }
}
class FilterData {
  private $colName;
  private $value;
  
  function __construct($colName, $value) {
      $this->colName = $colName;
      $this->value = $value;
  }
  function getFilter($i) {
      return $i[$this->colName] == $this->value;
  }
}
 
if($datos): ?>  
    <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 12px; width: 100%">
         <thead>
             <tr>
               <th>Área</th>
               <th>Departamento</th>
               <th></th>
               <th>Horas 50 Generadas</th>
               <th>Horas 50 Solicitadas</th>
               <th>Horas 50 Aprobadas</th>
               <th>Horas 100 Generadas</th>
               <th>Horas 100 Solicitadas</th>
               <th>Horas 100 Aprobadas</th>
            </tr>
         </thead>
         <tbody>
           <?php 
          
            $dataFilterIdSysAdmArea =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $datos));  
            $con = 0;
            

            foreach ($dataFilterIdSysAdmArea as $index => $departamento):
              $area = "";
              $con++;
              $totalh50 = 0;
              $totalH50 = 0;
              $totalNh50 = 0;
              $totalh100 = 0;
              $totalH100 = 0;
              $totalNh100 = 0;
              $totalA50 = 0;
              $totalA100 = 0;

              $arrayData   = array_filter($datos, array(new FilterData("departamento", $departamento), 'getFilter'));

              foreach ($arrayData as $index => $row):
              
                //if($row['solh50'] != null){

                
                  $salidadesayuno   = '00:00:00';
                  $salidaalmuerzo   = '00:00:00';
                  $salidamerienda   = '00:00:00';
                
                  if($row['salida_desayuno'] != NULL){
                    $salidadesayuno  =  date('H:i:s', strtotime($row['salida_desayuno']));
                  }

                  if($row['salida_almuerzo'] != NULL){
                    $salidaalmuerzo  =  date('H:i:s', strtotime($row['salida_almuerzo']));
                  }
                  

                  if($row['salida_merienda'] != NULL){
                    $salidamerienda  =  date('H:i:s', strtotime($row['salida_merienda']));
                  }
                
                  $area = $row['area'];
                  $departamento = $row['departamento']; 
                  $h50  = getRendonminutos(gethoras50(date("Y-m-d H:i:s",strtotime($row['entrada'])),  date("Y-m-d H:i:s",strtotime($row['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$row['id_sys_rrhh_cedula'], $row['fecha'], $row['feriado']));
                  $h100  = getRendonminutos(gethoras100(date("Y-m-d H:i:s",strtotime($row['entrada'])),  date("Y-m-d H:i:s",strtotime($row['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$row['id_sys_rrhh_cedula'], $row['fecha'], $row['feriado'],$row['agendamiento']));
                
                  $Dh50 = HorasToDecimal($h50);
                  $Dh100 = HorasToDecimal($h100);
  
                  $totalH50 += $Dh50;
                  $totalH100 += $Dh100;
                  $totalh50 += $row['solh50'];
                  $totalh100 += $row['solh100'];
                  $totalA50 += $row['solA50'];
                  $totalA100 += $row['solA100'];
            
                //}

              endforeach;


              if($area != ""):
              ?>

              <tr>
               <td><?= $area?></td>
               <td><?= $departamento ?></td>
               <td></td>
               <td><?= DecimaltoHoras(number_format($totalH50, 2, '.', '')) ?></t>
               <td><?= DecimaltoHoras(number_format($totalh50, 2, '.', '')) ?></td>
               <td><?= DecimaltoHoras(number_format($totalA50, 2, '.', '')) ?></td>
               <td><?= DecimaltoHoras(number_format($totalH100, 2, '.', '')) ?></td>
               <td><?= DecimaltoHoras(number_format($totalh100, 2, '.', '')) ?></td>
               <td><?= DecimaltoHoras(number_format($totalA100, 2, '.', '')) ?></td>
               <td></td>
              </tr>   
            <?php
              endif;
            endforeach;?>
            
         </tbody>    
    </table>
<?php endif;?>    
