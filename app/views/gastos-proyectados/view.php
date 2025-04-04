<?php

use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosGastosProyectadosDet;
use app\models\SysRrhhRubrosGastos;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillas */

$this->title = 'Gasto Proyectado #'.$model->id_gasto_proyectado;
$this->params['breadcrumbs'][] = ['label' => 'Gastos Proyectados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$detalle = SysRrhhEmpleadosGastosProyectadosDet::find()->where(['id_gasto_proyectado'=> $model->id_gasto_proyectado])->all();

?>
<div class="sys-rrhh-soextras-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_gasto_proyectado], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Download file', ['download', 'id' => $model->id_gasto_proyectado], ['class' => 'btn btn-primary',"target" => "_blank"]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_gasto_proyectado',
            'id_sys_rrhh_cedula',
            'id_sys_empresa',
            'anio', 
            //'id_sys_empresa',
            //'estado',
            //'transaccion_usuario',
        ],
    ]) ?>

    <?php if($detalle):?>
    
    <table class= "table">
       <thead>
       		<tr>
       			<th>Rubro</th>
       			<th>Cantidad</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($detalle as $index => $item):
            $rubro = SysRrhhRubrosGastos::find()->where(['id_sys_rrhh_rubros_gastos'=> $item['id_sys_rrhh_rubros_gastos']])->one()
            
            ?>
           <tr>
           		<td><?= $rubro['rubro']?></td>
           		<td><?= $item['cantidad']?></td>
           </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>
</div>
