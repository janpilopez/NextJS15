<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

//$url = Yii::$app->basePath.'\vendor\zklibrary\zklibrary';
//include ($url.'\zklibrary.php');
//$zk = new ZKLibrary('192.168.1.10', 4370);
//$zk->connect();
//print($zk->getDeviceName());

/*$zk->disableDevice();
sleep(1);
$attendance = $zk->getAttendance();
sleep(1);
$zk->enableDevice();
sleep(1);
$zk->disconnect();+*/

?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code>
</div>
