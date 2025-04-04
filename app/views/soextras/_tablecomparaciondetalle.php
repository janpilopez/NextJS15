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
               <th><?= $area?></th>
               <th><?= $departamento ?></th>
               <th></th>
               <th><?= DecimaltoHoras(number_format($totalH50, 2, '.', '')) ?></th>
               <th><?= DecimaltoHoras(number_format($totalh50, 2, '.', '')) ?></th>
               <th><?= DecimaltoHoras(number_format($totalA50, 2, '.', '')) ?></th>
               <th><?= DecimaltoHoras(number_format($totalH100, 2, '.', '')) ?></th>
               <th><?= DecimaltoHoras(number_format($totalh100, 2, '.', '')) ?></th>
               <th><?= DecimaltoHoras(number_format($totalA100, 2, '.', '')) ?></th>
              </tr>   

              <tr>
                <th>Cédula</th>
                <th>Nombres</th>
                <th>Fecha</th>
                <th colspan="6"></th>
              </tr>

            <?php

              
              $dataFilterIdCedula =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));  
         
              foreach ($dataFilterIdCedula as $index => $row):
            
                $newData = obtenerDatosMarcacionySolicitudes($fechaini,$fechafin,$IDarea,$IDdepartamento,$row);
                $hT50 = 0;
                $hT100 = 0;
                $aT50 = 0;
                $aT100 = 0;
                $TotalH50 = 0;
                $TotalH100 = 0;
                $existe = false;
 
                foreach($newData as $new):

                  //if($new['solh100'] != 0 || $new['solh50'] != 0){

                    $salidadesayuno   = '00:00:00';
                    $salidaalmuerzo   = '00:00:00';
                    $salidamerienda   = '00:00:00';

                    if($new['salida_desayuno'] != NULL){
                      $salidadesayuno  =  date('H:i:s', strtotime($new['salida_desayuno']));
                    }
      
                    if($new['salida_almuerzo'] != NULL){
                      $salidaalmuerzo  =  date('H:i:s', strtotime($new['salida_almuerzo']));
                    }
                                                  
                    if($new['salida_merienda'] != NULL){
                      $salidamerienda  =  date('H:i:s', strtotime($new['salida_merienda']));
                    }
                
                    $existe = true;  
                    $h50  = getRendonminutos(gethoras50(date("Y-m-d H:i:s",strtotime($new['entrada'])),  date("Y-m-d H:i:s",strtotime($new['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$new['id_sys_rrhh_cedula'], $new['fecha'], $new['feriado']));
                    $h100  = getRendonminutos(gethoras100(date("Y-m-d H:i:s",strtotime($new['entrada'])),  date("Y-m-d H:i:s",strtotime($new['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$new['id_sys_rrhh_cedula'], $new['fecha'], $new['feriado'],$new['agendamiento']));
                  
                    $Dh50 = HorasToDecimal($h50);
                    $Dh100 = HorasToDecimal($h100);

                    $hT50 += $new['solh50'];
                    $hT100 += $new['solh100'];
                    $aT50 += $new['solA50'];
                    $aT100 += $new['solA100'];
                    $TotalH50 += $Dh50;
                    $TotalH100 +=  $Dh100;
                    ?>

                      <tr>
                        <td><?= $new['id_sys_rrhh_cedula']?></td>
                        <td><?= $new['nombres']?></td>
                        <td><?= $new['fecha']?></td>
                        <td><?= $h50?></td>
                        <td><?= DecimaltoHoras(number_format($new['solh50'], 2, '.', ''))?></td>
                        <td><?= DecimaltoHoras(number_format($new['solA50'], 2, '.', ''))?></td>
                        <td><?= $h100?></td>
                        <td><?= DecimaltoHoras(number_format($new['solh100'], 2, '.', ''))?></td>
                        <td><?= DecimaltoHoras(number_format($new['solA100'], 2, '.', ''))?></td>
                      </tr>

                    <?php

                  //}

                endforeach;

                if($existe == true){
                ?>

                      <tr>
                        <th colspan="3">TOTAL:</th>
                        <th><?= DecimaltoHoras(number_format($TotalH50, 2, '.', ''))?></t>
                        <th><?= DecimaltoHoras(number_format($hT50, 2, '.', ''))?></t>
                        <th><?= DecimaltoHoras(number_format($aT50, 2, '.', ''))?></th>
                        <th><?= DecimaltoHoras(number_format($TotalH100, 2, '.', ''))?></th>
                        <th><?= DecimaltoHoras(number_format($hT100, 2, '.', ''))?></th>
                        <th><?= DecimaltoHoras(number_format($aT100, 2, '.', ''))?></th>
                      </tr>

                <?php
                }
              endforeach;
            
              ?>
              <tr>
                <th colspan="9"></th>
              </tr>
            <?php
            endif;
            endforeach;?>
            
         </tbody>    
    </table>
<?php endif;?>    
