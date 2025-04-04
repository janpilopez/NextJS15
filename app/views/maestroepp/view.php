<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Equipo de proteccición personal';
$this->params['breadcrumbs'][] = ['label' => 'Lista Epp', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-acceso-maestroepp-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // [
                
            //     'attribute'=>'id_sys_ssoo_epp',
            //     'value'=> function($model){
              
            //       return str_pad($model->id_sys_ssoo_epp, 5, "0", STR_PAD_LEFT);
            //     }
            // ],
            'nombre',
            'estado',
            'vida_util',
            'um',

        ],
    ]) ?>
</div>
