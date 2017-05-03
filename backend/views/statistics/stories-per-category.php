<?php
use backend\models\User;
use backend\models\Language;
use backend\models\Statistics;
use backend\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title=Yii::t('app', 'Stories per category') ;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <h3><?=Yii::t("app", "Stories per category")?>: <?= $date?>/<?=$user->name?></h3>
            </div>
            <div class="block-content">
                <?= Html::beginForm(Url::to(["/statistics/stories-per-category"]), 'get', []); ?>
                <?=Yii::t("app", "Choose different date")?>
               <b> (Date published) </b>
                <?php $select=(isset($_GET["date"])) ? $_GET["date"] : date("Y-m-d"); ?>
                <?= Html::textInput ('date', $select, ['class'=>'datepicker form-control']); ?>
                <br>


                <?php if(Helpers::columnVisible([User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_MARKETER]) ): ?>
                <?=Yii::t("app", "Author")?>
                <?php $select=(isset($_GET["user"])) ? $_GET["user"] : NULL; ?>
                <?= Html::dropDownList("user", $select, User::usersDropDownList(), ['prompt'=>'-', 'class'=>'form-control'] ); ?>
                <br>
                <?php endif; ?>

                <?= Html::submitButton(Yii::t('app', 'Submit'),  ['class'=>'btn btn-minw btn-success'] ); ?>
                <?= Html::endForm(); ?>

                <p>
                <h3><?=Yii::t("app", "Total")?>: <?=$total?></h3>
                </p>

                <p>
                <table class="table table-striped">
                    <tr>
                        <td>Count</td>
                        <td>Category</td>
                    </tr>

                <?php
                foreach($stories as $story)
                {
                    echo
                    '<tr>
                        <td>'.$story["count"].'</td>
                        <td>'.$story["category_name"].'</td>
                    </tr>';
                }
                ?>
                </table>
                </p>
                <br>
            </div>
        </div>
    </div>
</div>