<?php

use app\models\SysRrhhEmpleados;
use app\models\SysRrhhSoextrasEmpleados;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhSolicitudTaxisEmpleados;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillas */

$this->title = 'Solicitud #'.$model->id_sys_rrhh_sotaxis;
$this->params['breadcrumbs'][] = ['label' => 'Solicitud Taxis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

function getTipoUsuario($id_usuario){
        
    $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->one();
    
    if($usertipo):
    
    return $usertipo->usuario_tipo;
    
    endif;
    
    return 'N';
}

$tipousuario =  getTipoUsuario(Yii::$app->user->id);

$detalle = SysRrhhSolicitudTaxisEmpleados::find()->where(['id_sys_rrhh_sotaxis'=> $model->id_sys_rrhh_sotaxis])->all();

?>
<div class="sys-rrhh-soextras-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_sys_rrhh_sotaxis], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_sotaxis',
            [
                'attribute'=>'id_sys_adm_area',
                'value'=> function($model){
                    if(empty($model->sysAdmCargo->id_sys_adm_cargo)){
                        $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $model->sysAdmArea->id_sys_adm_area])->one();
                    }else{
                        $cargo        = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->sysAdmCargo->id_sys_adm_cargo])->one();
                        $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                    }
                    
                        
                    if($area):
                        return $area->area;
                    else:
                        return "s/n";
                    endif;
                }
            ],
            [
                'attribute'=>'id_sys_adm_departamento',
                'value'=> function($model){
                    
                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $model->id_sys_adm_departamento])->one();
                        
                    if($departamento):
                        return $departamento->departamento;
                    else:
                        return "S/D";
                    endif;
                }
                
            ],
            'fecha_solicitada',
            'comentario',
            //'id_sys_empresa',
            //'estado',
            //'transaccion_usuario',
        ],
    ]) ?>

    <?php if($detalle):?>
    
    <table class= "table">
       <thead>
       		<tr>
       			<th>Cédula</th>
       			<th>Nombres</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($detalle as $index => $item):
            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $item['id_sys_rrhh_cedula']])->one()
            
            ?>
           <tr>
           		<td><?= $item['id_sys_rrhh_cedula']?></td>
           		<td><?= $empleado['nombres']?></td>
           </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>

    <div class="form-group text-center">
   		<?= Html::a('Aprobar Solicitud', ['aprobar',  'id'=>$model->id_sys_rrhh_sotaxis], ['class' => 'btn btn-success']) ?>
    </div>

</div>
