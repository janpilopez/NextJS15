<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisosIngresosDet;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosIngresos */

$this->title = 'Permiso # '.str_pad($model->id, 5, "0", STR_PAD_LEFT);
$this->params['breadcrumbs'][] = ['label' => 'Permisos Visitas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$detalle = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $model->id])->all();

?>
<div class="sys-rrhh-empleados-permisos-ingresos-view">

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
            'empresa',
            'observacion',
            'fecha_ingreso',
            [
                
                'attribute'=>'hora_ingreso',
                'value'=> function($model){
                    return date('H:i', strtotime($model->hora_ingreso));
                }
            ],
    
        ],
    ]) ?>
    
    <?php if($detalle):?>
    
    <table class= "table">
       <thead>
       		<tr>
       			<th>No.</th>
       			<th>Cédula</th>
       			<th>Nombres</th>
                <th width="15%">Fecha/Hora Ingreso</th>
                <th width="15%">Fecha/Hora Salida</th>
                <th>Telefono</th>
                <th>Laptop</th>
                <th>Auto</th>
                <th>Placa Auto</th>
                <th>Otros</th>
                <th>Foto Documento</th>
                <th>Foto Firma</th>
                <th>Estado</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($detalle as $index => $item):
            $db =  $_SESSION['db'];

            $fotoFirma =   Yii::$app->$db->createCommand("select foto_firma, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_firma as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$model->id}")->queryOne();
            $fotoDocumento =   Yii::$app->$db->createCommand("select foto_documento, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_documento as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$model->id}")->queryOne();
            ?>
           <tr>
           		<td><?= $index + 1?></td>
           		<td><?= $item['id_sys_rrhh_cedula']?></td>
           		<td><?= $item['nombres']?></td>
                <td><?= $item['fecha_ingreso'] != NULL ? $item['fecha_ingreso'].'/'.date('H:i:s', strtotime($item['hora_ingreso'])): 'NA'?></td>
                <td><?= $item['fecha_salida'] != NULL ? $item['fecha_salida'].'/'.date('H:i:s', strtotime($item['hora_salida'])): 'NA'?></td>
                <td><?= $item['telefono'] == 1 ? 'Autorizado' : 'No Autorizado'?></td>
                <td><?= $item['laptop'] == 1 ? 'Autorizado' : 'No Autorizado'?></td>
                <td><?= $item['auto'] == 1 ? 'Autorizado' : 'No Autorizado'?></td>
                <td><?= $item['marca_auto'] =! "" ? $item['marca_auto'] : ''?></td>
                <td><?= $item['otros'] == 1 ? 'Autorizado' : 'No Autorizado'?></td>
                <td><img width="40%" height ='10%' src="data:image/png;base64, <?= $fotoDocumento['baze64'] ?? null?>" alt="" /></td>
                <td><img width="90%" height ='10%' src="data:image/png;base64, <?= $fotoFirma['baze64'] ?? null?>" alt="" /></td>
                <td><?= $item['estado'] == 1 ? 'Ingreso' : 'No Ingreso'?></td>
            </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>
    
</div>



