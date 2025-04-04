<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<div class="wrap">
     <?php if(!Yii::$app->user->isGuest){?>
   
		<?php $this->beginContent('@app/views/layouts/nav.php')?>
		<?php $this->endContent()?>
	
	<?php }?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
	
</div>
	<?php if(!Yii::$app->user->isGuest){?>
	<footer class="footer">
		<div class="container">
			<p class="pull-left">&copy; Pespesca s.a <?= date('Y') ?></p>

			<p class="pull-right">Desarrollado por Dpto.Sistemas</p>
		</div>
	</footer>
<?php }?>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>

