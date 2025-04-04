<?php

use app\models\SysAdmDepartamentos;
use app\models\SysIndicadores;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Indicadores Sistemas';
$this->params['breadcrumbs'][] = ['label' => 'Indicadores Sistema', 'url' => ['index','id_encabezado_indicador'=>$model->id_encabezado_indicador]];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-encabezado-indicador-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          [
            'attribute'=>'departamental',
            'value'=> function($model){
              
              $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->departamental])->one();
              if($departamento):
                return $departamento->departamento;
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          [
            'attribute'=>'tipo_indicador',
            'value'=> function($model){
              
              $tipo_indicador = SysIndicadores::find()->where(['id_indicador'=> $model->tipo_indicador])->one();
              if($tipo_indicador):
                return $tipo_indicador->nombre_indicador;
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          [
            'attribute'=>'imp_departamento',
            'value'=> function($model){
              
              $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->imp_departamento])->one();
              if($departamento):
                return $departamento->departamento;
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          'fecha',
        ],
      ])
    ?>

    <?php if($modeldet):?>
    
    <table class= "table">
       <thead>
       		<tr>
       			<th>Usuario</th>
       			<th>Cantidad B/N</th>
            <th>Cantidad Color</th>
            <th>Resmas Solicitadas</th>
       		</tr>
       </thead>
       <tbody>
          <?php foreach ($modeldet as $index => $item):
            ?>
            <tr>
           		<td><?= $item['usuario']?></td>
           		<td><?= $item['can_negro']?></td>
              <td><?= $item['can_color']?></td>
              <td><?= $item['rem_sol']?></td>
            </tr>
          <?php endforeach;?>
       </tbody>
    
    </table>
    	
    <?php endif;?>
    </div>
  </div>

</div>
