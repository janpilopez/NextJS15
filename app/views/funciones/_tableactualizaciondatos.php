<?php
/* @var $this yii\web\View */

use app\models\SysAdmAreas;
use app\models\SysAdmRutas;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$con = 0;

 if($datos): ?>  
   <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
         <thead>
             <tr>
                <th>No</th>
                <th>Área</th>
                <th>Departamento</th>
                <th>Cédula</th>
                <th>Nombres y Apellidos</th>
                <th>Género</th>
                <th>Cargo</th>
                <th>¿Usa transporte?</th>
                <th>Medio de transporte</th>
                <th><input  type="checkbox" id="marca"> Marcar/Desmarcar</th>
             </tr>
         </thead>
         <tbody>
           <?php foreach ($datos as $data):
               $con++;
           ?>
             <tr>
                <td><?= $con?></td>
                <td><?= $data['area']?></td>
                <td><?= $data['departamento']?></td>
                <td><?= $data['id_sys_rrhh_cedula']?></td>
                <td><?= $data['nombres']?></td> 
                <td><?= $data['genero']== 'M'? 'Masculino': 'Femenino'?></td>
                <td><?= $data['cargo']?></td>
                <td><input  type="checkbox" id="<?php echo $data['transporte']?>" value="1" <?php echo ($data['transporte'] == true) ? 'checked' : ''; ?>/></td>
                <td>
                    <?php echo   Html::DropDownList('ruta', $data['id_sys_adm_ruta'], 
                       ArrayHelper::map(SysAdmRutas::find()->all(), 'id_sys_adm_ruta', 'ruta'), ['class'=>'form-control input-sm', 'id'=>'ruta'])
                    ?>
                </td>
                <td style = "text-align:center;"><input  type="checkbox" id="<?php echo $data['id_sys_rrhh_cedula']?>" value="<?= $anio ?>"/></td>
             </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <br>
    <div class="form-group text-center">
        <?= Html::button('Actualizar', ['class' => 'btn btn-success',  'id'=> 'btn-enviar']) ?>
     </div>
<?php endif;?>    


 
