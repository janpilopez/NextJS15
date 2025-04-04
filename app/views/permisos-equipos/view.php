<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisosEquiposDet;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */

$this->title = 'Permiso # '.str_pad($model->id, 5, "0", STR_PAD_LEFT);
$this->params['breadcrumbs'][] = ['label' => 'Permisos Equipos Informáticos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$detalle = SysRrhhEmpleadosPermisosEquiposDet::find()->where(['id_sys_rrhh_empleados_permisos_equipo'=> $model->id])->all();

?>
<div class="sys-rrhh-empleados-permisos-equipos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                
                'attribute'=>'id',
                'value'=> function($model){
               
                   return str_pad($model->id, 5, "0", STR_PAD_LEFT);
                }
             ],
            'id_sys_rrhh_cedula',
            [
                
                'attribute'=>'Nombres',
                'value'=> function($model){
                
                 $empleados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                  return $empleados->nombres;
                }
                ],
            'fecha_inicio',
            'fecha_fin',
            'motivo',
        ],
    ]) ?>
    
    <?php if($detalle):?>
    
    <table class= "table">
       <thead>
       		<tr>
       			<th>No</th>
       			<th>Tipo</th>
       			<th>Marca</th>
       			<th>Modelo</th>
       			<th>Serie</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($detalle as $index => $item):?>
           <tr>
           		<td><?= $index + 1?></td>
           		<td><?= getTipo($item['tipo'])?></td>
           		<td><?= $item['marca']?></td>
           		<td><?= $item['modelo']?></td>
           		<td><?= $item['serie']?></td>
           </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>
    
    <?php if($model->estado == 'P'):?>
   		<?= Html::a('Aprobar Permiso', ['aprobar',  'id'=>$model->id], ['class' => 'btn btn-success']) ?>
   	<?php endif;?>
</div>
<?php function getTipo($tipo){
    
    switch ($tipo) {
        case 'P':
            return  "PC";
            break;
        case 'L':
            return "Lapto";
            break;
        case 'I':
            return   "Impresora";
            break;
        case 'O':
            return   "Otros";
            break;
        default:
            echo "s/d";
    }
    
}?>



