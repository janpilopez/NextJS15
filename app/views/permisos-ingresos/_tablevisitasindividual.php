  
<?php  use yii\helpers\Html;
use yii\web\View;
use app\assets\PermisosIngresosFotosAsset;
PermisosIngresosFotosAsset::register($this);
use yii\bootstrap\Modal;
$url = Yii::$app->urlManager->createUrl(['permisos-ingresos']);
$inlineScript = "url = '{$url}';";
$this->registerJs($inlineScript, View::POS_HEAD);

$holgura =  15;
//listado de funciones de calculos


$meses =  Yii::$app->params['meses'];
$dias =   Yii::$app->params['dias'];

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
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
?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr>
        <th>No.</th>
        <th>Departamento Visitado</th>
        <th>Fecha Ingreso</th>
        <th>Hora Ingreso</th>
        <th>Fecha Salida</th>
        <th>Hora Salida</th>
        <th>Foto Documento</th>
        <th>Foto Firma</th>
      </tr>
    </thead>
    <tbody>
     <?php 
     $cont = 0;
     foreach ($datos as $index => $item):
        $cont++;
        $db =  $_SESSION['db'];

        $fotoFirma =   Yii::$app->$db->createCommand("select foto_firma, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_firma as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$item['id_sys_rrhh_empleados_permisos_ingresos']}")->queryOne();
        $fotoDocumento =   Yii::$app->$db->createCommand("select foto_documento, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_documento as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$item['id_sys_rrhh_empleados_permisos_ingresos']}")->queryOne();
        ?>
        <tr>
            <td width="5%"><?= $cont ?></td>
            <td width="10%"><?= $item['departamento']?></td>
            <td width="10%"><?= $item['fecha_ingreso'] != NULL ? $item['fecha_ingreso']: 'NA' ?></td>
            <td width="10%"><?= date('H:i:s', strtotime($item['hora_ingreso'])) != NULL ? date('H:i:s', strtotime($item['hora_ingreso'])): 'NA'?></td>
            <td width="10%"><?= $item['fecha_salida'] != NULL ? $item['fecha_salida']: 'NA'?></td>
            <td width="10%"><?= date('H:i:s', strtotime($item['hora_salida'])) != NULL ? date('H:i:s', strtotime($item['hora_salida'])): 'NA'?></td>
            <td width="20%" class='abrir-modal'><img width="10%" height ='10%' src="data:image/*;base64, <?= $fotoDocumento['baze64']?>" alt="" /></td>
            <td width="20%" class='abrir-modal'><img width="10%" height ='10%' src="data:image/*;base64, <?= $fotoFirma['baze64']?>" alt="" /></td>
        </tr>
        <?php endforeach;?>
    </tbody>
  </table>

  <?php 
    //modal empleados 
    Modal::begin([
        'id' => 'modalfotos',
        'header' => '<h4 class="modal-title">Foto</h4>',
        'headerOptions'=>['style'=>"background-color:#EEE"],
        'size'=>'modal-md',
    ]);
    ?>
    <?php Modal::end(); ?>


