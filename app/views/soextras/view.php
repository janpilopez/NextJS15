<?php

use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhSoextrasEmpleados;
use app\models\SysAdmUsuariosDep;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillas */
echo $this->render('funciones');

$this->title = 'Solicitud #'.$model->id_sys_rrhh_soextras;
$this->params['breadcrumbs'][] = ['label' => 'Solicitud Horas Extras', 'url' => ['index']];
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

$detalle = SysRrhhSoextrasEmpleados::find()->where(['id_sys_rrhh_soextras'=> $model->id_sys_rrhh_soextras])->all();

?>
<div class="sys-rrhh-soextras-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_sys_rrhh_soextras], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_soextras',
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
            'fecha_registro',
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
       			<th>H50</th>
       			<th>H100</th>
                <th>Comentario Individual</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($detalle as $index => $item):
            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $item['id_sys_rrhh_cedula']])->one()
            
            ?>
           <tr>
           		<td><?= $item['id_sys_rrhh_cedula']?></td>
           		<td><?= $empleado['nombres']?></td>
           		<td><?= DecimaltoHoras($item['horas50'])?></td>
           		<td><?= DecimaltoHoras($item['horas100'])?></td>
                <td><?= $item['comentario'] ?></td>
           </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>

    <?php if($model->estado == 'P' && $tipousuario == 'A'):?>
        <div class="form-group text-center">
   		    <?= Html::a('Revisar Solicitud', ['revisar',  'id'=>$model->id_sys_rrhh_soextras], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Anular Solicitud', ['anularsolicitud',  'id'=>$model->id_sys_rrhh_soextras], ['class' => 'btn btn-danger']) ?>
        </div>
    <?php endif;?>

</div>
