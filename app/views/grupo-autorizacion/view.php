<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysGrupoAutorizacion */

$this->title = 'Ver Grupo';
$this->params['breadcrumbs'][] = ['label' => 'Grupo Autorización', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-grupo-autorizacion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'nombre',
        ],
    ]) ?>

</div>
