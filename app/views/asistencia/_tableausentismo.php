<?php use yii\helpers\Html;
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

function getColor($hora) {
    $segundos = horasToSegundos($hora);

    if ($segundos >= horasToSegundos("52:00:00")) {
        return '#e06666'; // Rojo
    } elseif ($segundos > horasToSegundos("40:00:00") && $segundos < horasToSegundos("52:00:00")) {
        return '#ffe599'; // Amarillo
    } else {
        return '#6aa84f'; // Verde
    }
}

function horasToSegundos($hora) {
    list($h, $m, $s) = explode(":", $hora);
    return ($h * 3600) + ($m * 60) + $s;
}


?>
<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Area</th>
        <th>Departamento</th>
        <th>C.I</th>
        <th>Nombres</th>
        <th>Género</th>
        <th>Horas Laboradas</th>
   </thead>
   <body>
   <?php if ($datos):
             
    $dataFilterIdSysRrhhCedula =  array_unique(array_map(array(new FilterColumn("id_sys_rrhh_cedula"), 'getValues'), $datos));  
   
    $con = 0;
    foreach ($dataFilterIdSysRrhhCedula as $index => $id_sys_rrhh_cedula):  
        $con+=1;
        $area = '';
        $deparamento = '';
        $nombres = '';
        $genero = '';
        $arrayData   = array_filter($datos, array(new FilterData("id_sys_rrhh_cedula", $id_sys_rrhh_cedula), 'getFilter'));
        $horas = '';
        $desayuno = 0;
        $almuerzo = 0;
        $merienda = 0;
        $horasDecimal  = 0;
        $horasComida = 0;
        foreach ($arrayData as $index => $row):
        
            $area = $row['area'];
            $deparamento = $row['departamento'];
            $nombres = $row['nombres'];
            $genero = $row['genero'];

            //$data_marcacion = ObtenerDatosMarcacion($row['id_sys_rrhh_cedula'], $row['fecha']);

            if ($row['entrada'] != null && $row['salida'] != null):
            
                $horas = getTotalhoras($row['entrada'], $row['salida']);
                /*if(date("H:i:s",strtotime($data_marcacion['entrada'])) > date("H:i:s",strtotime($data_marcacion['hora_desayuno']))){
                    $tiempo = $data_marcacion['almuerzo'] + $data_marcacion['merienda'];
                  }else{
                    $tiempo = $data_marcacion['desayuno'] + $data_marcacion['almuerzo'] + $data_marcacion['merienda'];
                }
                $thorasefectivas = getRendonminutos(restarMinutosLunch(DecimaltoHoras(number_format($tiempo, 2, '.', '')),getTotalhoras(date("Y-m-d H:i:s",strtotime($data_marcacion['entrada'])),date("Y-m-d H:i:s",strtotime($data_marcacion['salida'])))));
                */
                if ($horas != '00:00:00'):
                    $horasDecimal += floatval(number_format(HorasToDecimal($horas),2, '.', ''));
                endif;
            
                if($row['desayuno'] != '00:00:00'):
                    $horasComida += floatval(number_format(HorasToDecimal($row['desayuno']),2, '.', ''));
                endif;

                if($row['almuerzo'] != '00:00:00'):
                    $horasComida += floatval(number_format(HorasToDecimal($row['almuerzo']),2, '.', ''));
                endif;

                if($row['merienda'] != '00:00:00'):
                    $horasComida += floatval(number_format(HorasToDecimal($row['merienda']),2, '.', ''));
                endif;
            endif;
        
        endforeach;

        $horasDecimal = $horasDecimal - $horasComida;

    ?> 
    <?php if ($horasDecimal > 0):?>
     <tr>
       <td><?= $con?></td>
       <td><?= $area?></td>
       <td><?= $deparamento?></td>
       <td><?= $id_sys_rrhh_cedula?></td>
       <td><?= $nombres?></td>
       <td><?= $genero  == "M" ? 'Masculino' : 'Femenino' ?></td>
        <td bgcolor="<?= getColor(DecimaltoHoras(number_format($horasDecimal, 2, '.', ''))) ?>">
            <?= DecimaltoHoras(number_format($horasDecimal, 2, '.', '')) ?>
        </td>
     </tr>        
    <?php  
        endif;
    endforeach;
    endif;?>
   </body>
</table>