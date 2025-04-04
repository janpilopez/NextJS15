<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
use app\models\SysAdmUsuariosDep;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

function getTipoUsuario($id_usuario){
        
  $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->one();
  
  if($usertipo):
  
  return $usertipo->usuario_tipo;
  
  endif;
  
  return 'N';
}

$tipousuario =  getTipoUsuario(Yii::$app->user->id);

echo $this->render('funciones');
$url = Yii::$app->urlManager->createUrl(['soextras']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
AppAsset::register($this);
$con = 0;
$con1 = 0;
 if($datos): ?>  
    <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 12px; width: 100%">
         <thead>
             <tr>
               <th width = "5%">Id Solicitud</th>
               <th width = "8%">Día Laborado</th>
               <th>Área</th>
               <th>Departamento</th>
               <th>Nombres</th> 
               <th>Hora Entrada</th>
               <th>Desayuno</th>
               <th>Almuerzo</th>
               <th>Merienda</th>
               <th>Hora Salida</th>
               <th>Total Horas</th>
               <th>Total Horas Efectivas</th>
               <th>H50</th>
               <th>H50($)</th>
               <th>H100</th>
               <th>H100($)</th>
               <th><input  type="checkbox" id="marca"> Marcar/Desmarcar</th>
            </tr>
         </thead>
         <tbody>
           <?php 
           
           foreach ($datos as $data):
              $con++;

              $totalh50 = 0;
              $totalh100 = 0;
              $totalp50 = 0;
              $totalp100 = 0;

              $data_empleados = getDataSolicitudHoras($data['id_sys_rrhh_soextras']);

              ?>

              <tr>
                <td><?= $data['id_sys_rrhh_soextras']?></td>
                <td><?= $data['fecha_registro']?></td>
                <td><?= $data['area']?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style = "text-align:center;"><input  type="checkbox" id="<?php echo $data['id_sys_rrhh_soextras'];?>"/></td>
              </tr>
             
              <?php
              foreach ($data_empleados as $dataEmp): 

                $data_marcacion = ObtenerDatosMarcacion($dataEmp['id_sys_rrhh_cedula'], $data['fecha_registro']);

                $thoras = getTotalhoras(date("Y-m-d H:i:s",strtotime($data_marcacion['entrada'])), date("Y-m-d H:i:s",strtotime($data_marcacion['salida'])));
                if(date("H:i:s",strtotime($data_marcacion['entrada'])) > date("H:i:s",strtotime($data_marcacion['hora_desayuno']))){
                  $tiempo = $data_marcacion['almuerzo'] + $data_marcacion['merienda'];
                }else{
                  $tiempo = $data_marcacion['desayuno'] + $data_marcacion['almuerzo'] + $data_marcacion['merienda'];
                }
                $thorasefectivas = getRendonminutos(restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras(date("Y-m-d H:i:s",strtotime($data_marcacion['entrada'])),date("Y-m-d H:i:s",strtotime($data_marcacion['salida'])))));
          
              ?>
           

             <tr>
                <td><?= $data['id_sys_rrhh_soextras']?></td>
                <td><?= $data['fecha_registro']?></td>
                <td><?= $data['area']?></td>
                <td><?= $data_marcacion['departamento'] ?></td>
                <td><?= $data_marcacion['nombres'] ?></td>
                <td><?= date("H:i:s",strtotime($data_marcacion['entrada'])) ?></td>
                <td bgcolor= "<?= $data_marcacion['hora_desayuno']  != ""  && date("H:i:s",strtotime($data_marcacion['hora_desayuno']))  >= date("H:i:s",strtotime($data_marcacion['entrada'])) ? '#f2bdfc': ''?>" ><?= $data_marcacion['hora_desayuno']  != "" && date("H:i:s",strtotime($data_marcacion['hora_desayuno']))  >= date("H:i:s",strtotime($data_marcacion['entrada'])) ? date('H:i:s', strtotime($data_marcacion['hora_desayuno'])): ''?></td>
                <td bgcolor= "<?= $data_marcacion['hora_almuerzo']  != "" ? '#bdc2fc': ''?>" ><?= $data_marcacion['hora_almuerzo']  != "" ? date('H:i:s', strtotime($data_marcacion['hora_almuerzo'])): ''?></td>
                <td bgcolor= "<?= $data_marcacion['hora_merienda']  != "" ? '#a3f98f': ''?>" ><?= $data_marcacion['hora_merienda']  != "" ? date('H:i:s', strtotime($data_marcacion['hora_merienda'])): ''?></td>
                <td><?= date("H:i:s",strtotime($data_marcacion['salida']))?></td>
                <td><?= $thoras  != "00:00:00" ? '<b>'.$thoras.'</b>' : $thoras ?></td>
                <td><?= $thorasefectivas  != "00:00:00" ? '<b>'.$thorasefectivas.'</b>' : $thorasefectivas ?></td>
                <td><?= DecimaltoHoras(number_format($dataEmp['horas50'], 2, '.', ''))?></td>
                <td><?= number_format($dataEmp['pago50'], 2, '.', '') ?></td>
                <td><?= DecimaltoHoras(number_format($dataEmp['horas100'], 2, '.', ''))?></td>
                <td><?= number_format($dataEmp['pago100'], 2, '.', '') ?></td>
                <td></td>
              </tr>
             
             <?php  

              $totalh50 += $dataEmp['horas50'];
              $totalh100 += $dataEmp['horas100'];
              $totalp50 += $dataEmp['pago50'];
              $totalp100 += $dataEmp['pago100'];
             
              endforeach;?>
             
             <tr>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th>TOTAL:</th>
               <th><?= DecimaltoHoras(number_format($totalh50, 2, '.', '')) ?></th>
               <th>$<?= number_format($totalp50, 2, '.', '') ?></th>
               <th><?= DecimaltoHoras(number_format($totalh100, 2, '.', '')) ?></th>
               <th>$<?= number_format($totalp100, 2, '.', '') ?></th>
               <th></th>
               
            </tr>
             <tr style="background-color: rgb(229 231 235); height:40px;">
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>

            </tr>
         
            <?php endforeach;?>
         </tbody>    
    </table>
    <?php if($tipousuario == 'G'): ?>
      <div class="form-group text-center">
        <?= Html::button('Desaprobar Solicitudes', ['class' => 'btn btn-danger',  'id'=> 'btn-desaprobar']) ?>
        <?= Html::button('Aprobar Solicitudes', ['class' => 'btn btn-success',  'id'=> 'btn-enviar']) ?>
     </div>
<?php endif; 
endif;?>    
