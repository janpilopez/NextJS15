<?php 

use yii\helpers\Html;
use app\models\SysRrhhPermisos;
use app\assets\PermisosAsset;
//PermisosAsset::register($this);

$holgura =  15;
//listado de funciones de calculos
echo $this->render('funciones');


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
<table id ="table" class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>Fecha Inicio/Fecha Fin</th>
        <th>Área</th>
        <th>Departamento</th>
        <th>Cargo</th>
        <th>Permiso</th>
        <th>Jornada</th>
        <th>Entidad Emisora</th>
      </tr>
    </thead>
    <tbody>
    <?php 
        $data =  array_unique(array_map(array(new FilterColumn("nombres"), 'getValues'), $datos));
        
        

        foreach ($data as $index => $nombres):   
    ?>
        <tr  style="background-color: #ccc">
            <td colspan="8"><strong>  <?= $nombres ?> </strong></td>
        </tr>  
               
       <?php 
       
           $fechaPermiso = array_filter($datos, array(new FilterData("nombres", $nombres), 'getFilter'));
                  
           $dataPermiso =  array_unique(array_map(array(new FilterColumn("codigo"), 'getValues'), $fechaPermiso));
                

           $tipo_permiso = "";
           

            foreach ($dataPermiso as $index2 => $codigo):
        
                
                $permisos = array_filter($fechaPermiso, array(new FilterData("codigo", $codigo), 'getFilter'));
                
                $tipo_permiso = obtenerPermiso($permisos[$index2]['id_sys_rrhh_permiso']);

                $entidad = "";

                foreach ($datos_medicos as $index3 => $dataMed):
                    
                    if(date('Y-m-d', strtotime($dataMed['inicio'])) == $permisos[$index2]['inicio']){
                        if ($dataMed['identificacion'] == $permisos[$index2]['identificacion']) {
                            $entidad = $dataMed['entidad'];
                        }
                    }
                    
                    

                endforeach;

            ?>

      
                
                <tr>
                    <td><?= date('Y-m-d', strtotime($permisos[$index2]['inicio'])). " / " .date('Y-m-d', strtotime($permisos[$index2]['fin']))?></td>
                    <td><?=$permisos[$index2]['area']?></td>
                    <td><?=$permisos[$index2]['departamento']?></td>
                    <td><?=$permisos[$index2]['cargo']?></td>
                    <td><?=$tipo_permiso?></td>
                    <td><?=$permisos[$index2]['jornada'] != "P" ? "COMPLETA" : "PARCIAL"?></td>
                    <td><?=$entidad  != "" ? $entidad : "NO APLICA"?></td>

                </tr>
                
            <?php endforeach;?>
            
        <?php endforeach; ?>
    </tbody>
  </table>

<?php

function obtenerPermiso($id){

    $permisos = SysRrhhPermisos::find()->where(['id_sys_rrhh_permiso'=> $id])->one();
    
    return $permisos['permiso'];

}

?>