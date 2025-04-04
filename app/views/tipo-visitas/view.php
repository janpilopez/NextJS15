<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAccesoTipoVisitas */

$this->title = 'Ver Tipo';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Visita', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-acceso-tipos-visitas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'tipo_visita',
        ],
    ]) ?>

</div>
