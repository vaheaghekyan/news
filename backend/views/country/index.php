<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\User;
use backend\models\Country;

$this->title=Yii::t('app', 'Countries') ;
?>

<div class="row">

    <div class="col-sm-6 col-lg-6">
        <div class="block block-themed">
            <div class="block-header bg-flat">
                <ul class="block-options">
                    <li>
                        <button type="button"><i class="fa fa-arrow-down"></i></button>
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'Countries') ?></h3>
            </div>
            <div class="block-content">

                <table id="countries" class="table table-striped">
                    <tr>
                        <td><strong>#</strong></td>
                        <td width="60%"><strong><?= Yii::t('app', 'Country') ?></strong></td>
                        <td width="20%" class="center"><strong><?= Yii::t('app', 'Stories') ?></strong> </td>
                    </tr>
                    <?php
                     $i=1;
                    foreach ($countries as $country) :
                    ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td width="60%" class="country-name"><?=$country["name"]?></td>
                        <td width="20%" class="center"><a href="javascript:;" class="number-stories"><?=$country["count_story"]?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-6">
        <div class="block block-themed">
            <div class="block-header bg-flat">
                <ul class="block-options">
                    <li>
                        <button type="button"><i class="fa fa-arrow-down"></i></button>
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'Add new country') ?></h3>
            </div>
            <div class="block-content">
                <p>
                <?= Html::beginForm(Url::to(['country/add']), 'post')?>
                <table class="table table-striped">
                    <tr>
                        <td><strong><?= Yii::t('app', 'Country') ?></strong></td>
                        <td><strong><?= Yii::t('app', 'Stories') ?></strong> </td>
                    </tr>
                    <?php

                    foreach ($addnewcountry as $country) :
                    ?>
                    <tr>
                        <td><input type="checkbox" value="<?=$country["name"]?>" name="country_name[]"></td>
                        <td><?=$country["name"]?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <button type="submit" class="btn btn-minw btn-success"><?= Yii::t('app', 'Submit')?></button>
                 <?= Html::endForm()?>
                </p>

            </div>
        </div>
    </div>

</div>