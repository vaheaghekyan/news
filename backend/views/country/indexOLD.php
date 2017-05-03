<?php
use yii\web\View;
use yii\helpers\Url;
use backend\models\User;
use backend\models\Country;
$this->title="Countries"; 
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/bootstrap.min.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/modal.bootstrap.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery-ui.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/country.js', array('position'  => View::POS_END));
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/jquery-ui.min.css');

?>
<script>
$(document).ready(function()
{
    //when you click on "down arrow" next to continent it will expand that section of countries
    $(".fa-arrow-down").click(function()
    {
        //get thata so you can find proper div to expand. Div are tagged as for example: toggle-countries-2
        var data_x=$(this).data("continentid");
        $(".toggle-countries-"+data_x).toggle();
    });
});
</script>
<div class="row">
    <?php foreach ( $continents as $key => $continent ) : ?>
    <div class="col-sm-6 col-lg-4">
        <div class="block block-themed">
            <div class="block-header bg-flat">
                <ul class="block-options">
                    <li>
                        <button type="button"><i class="fa fa-arrow-down" data-continentid="<?=$continent->id?>"></i></button>
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', $continent->name) ?></h3>
            </div>
            <div class="block-content toggle-countries-<?=$continent->id?>" style="display:none;">

                <table id="countries" class="table table-striped">
                    <tr>
                        <td width="60%"><strong><?= Yii::t('app', 'Country') ?></strong></td>
                        <td width="20%" class="center"><strong><?= Yii::t('app', 'Stories') ?></strong> </td>
                        <td width="20%" class="center"><strong><?= Yii::t('app', 'Actions') ?></strong></td>
                    </tr>
                    <?php
                        $list = $continent->getCountriesByContinent();
                        if ( Country::isWw($continent) &&  count($list) == 0) {

                            $list[] = Country::getWorldwide();

                        }
                        foreach ( $list as $country ) :
                    ?>
                    <tr _id="<?=$country->id?>">
                        <td width="60%" class="country-name"><?=$country->name?></td>
                        <td width="20%" class="center"><a href="<?=Url::to(['story/index', 'country' => $country->id])?>" class="number-stories"><?=$country->numberOfStories()?></a></td>
                        <td width="20%" class="center">
                            <?php if ( !Country::isWw($continent) && ( Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ) ) : ?>

                                <img class="delete-action" src="/img/icons/deleteicon.png">
                            <?php endif;?>

                        </td>

                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php if (  !Country::isWw($continent) && (Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN) ) : ?>

                <a class="add-action btn btn-xs btn-primary"><?= Yii::t('app', 'Add new country') ?></a>
                <?php endif;?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>


<ul id="sortable">
<?php $i=0; $j=1; foreach ( $continents as $key => $continent ) : ?>

    <li class="col span_1_of_3 whitebox-cell dragable" _id="<?=$continent->id?>">
        <div>
            <div id="category" class="gpad">
                <div class="parent-category">
                    <h2><a class="expander collapsed" data-target="cont-<?=$continent->id?>"><span><?=$continent->name?></span></a></h2>
                </div>
                <div class="clear"></div>
                <div class="cont-<?=$continent->id?>">
                    <table id="tcountries" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%"><strong>Country</strong></td>
                            <td width="20%" class="center"><strong>Stories</strong> </td>
                            <td width="20%" class="center"><strong>Actions</strong></td>
                        </tr>
                    </table>
                    <table id="countries" width="100%" cellpadding="0" cellspacing="0">
                        <?php
                            $list = $continent->getCountriesByContinent();
                            if ( Country::isWw($continent) &&  count($list) == 0) {

                                $list[] = Country::getWorldwide();

                            }
                            foreach ( $list as $country ) :
                        ?>
                        <tr _id="<?=$country->id?>">
                            <td width="60%" class="country-name"><?=$country->name?></td>
                            <td width="20%" class="center"><a href="<?=Url::to(['story/index', 'country' => $country->id])?>" class="number-stories"><?=$country->numberOfStories()?></a></td>
                            <td width="20%" class="center">
                                <?php if ( !Country::isWw($continent) && ( Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ) ) : ?>

                                    <img class="delete-action" src="/img/icons/deleteicon.png">
                                <?php endif;?>

                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php if (  !Country::isWw($continent) && (Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN) ) : ?>

                    <a class="add-action">Add new country</a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </li>
<?php
    if ($j == 3) {

        $j = 0;
?>
        <div class="clear"></div>
    <?php


    }
    $j++;

    ?>
<?php endforeach; ?>
</ul>
<?php
$this->registerJsFile('/js/expander.js');
?>
<script>

    $(function(){

        countryModule.init({
            isOrdering  : <?=( ( Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ) ? 'true' : 'false' )?>,
            countriesUrl: '<?=Url::to(['country/countries'])?>',
            deleteUrl   : '<?=Url::to(['country/delete'])?>',
            orderUrl    : '<?=Url::to(['country/order'])?>',
            addUrl      : '<?=Url::to(['country/add'])?>',
            orderContinentUrl : '<?=Url::to(['country/order-continent'])?>'
        });
        $(".expander").expander();


    });

</script>