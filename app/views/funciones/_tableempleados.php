<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
use yii\bootstrap\Html;
AppAsset::register($this);
$con = 0;
 if($datos): ?>  
    <table id="tableempleados" class="table table-bordered table-condensed" style="background-color: white; font-size: 11px; width: 100%;">
         <thead>
             <tr>
                <th width = "5%">No</th>
                <th>Area</th>
                <th>Departamento</th>
                <th>Cedula</th>
                <th>Nombres</th>
                <th>Email</th>
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
                <td><?= utf8_encode( $data['nombres'])?></td>
                <td><?= $data['email'];?></td>
                <td style = "text-align:center;"><input  type="checkbox" id="<?php echo $data['id_sys_rrhh_cedula'];?>"/></td>
             </tr>
            <?php endforeach;?>
         </tbody>    
    </table>
      <div class="form-group text-center">
        <?= Html::button('Enviar Correo', ['class' => 'btn btn-success',  'id'=> 'btn-enviar']) ?>
     </div>
<?php endif;?>    
