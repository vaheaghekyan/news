<?php
use  backend\models\User;
use backend\components\Helpers;



/*create label for menu items*/
function menuLabel($i_class, $label)
{
    return   '<i class="'.$i_class.'"></i><span class="sidebar-mini-hide">'.$label.'</span>';
}
//create seperator for categories in menu
function separator($label)
{
    return '<li class="nav-main-heading"><span class="sidebar-mini-hide">'.$label.'</span></li>';
}

//check if url consists specific word so you can activate parent menu
function activeParent($string)
{
    if(preg_match("~\b$string\b~",$_SERVER["REQUEST_URI"])) //match exact word
        return true;
    else
        return false;
}
$items=
[
    // Important: you need to specify url as 'controller/action',
    // not just as 'controller' even if default action is used.
    ['label' => separator(Yii::t('app', 'Dashboard')),],
    ['label' => menuLabel("si si-speedometer", Yii::t('app', 'Dashboard')), 'url' => ["/admin/index"]],
    //['label' => menuLabel("fa fa-envelope", Yii::t('app', 'Contact')), 'url' => ["/site/contact"]],

    ['label' => separator(Yii::t('app', 'Stories')),],
    ['label' => menuLabel("fa fa-newspaper-o", Yii::t('app', 'Stories')), 'url' => "javascript:;",
        'template'=>'<a href="{url}" class="nav-submenu" data-toggle="nav-submenu">{label}</a>',
        'items'=>
        [
            //['label' =>Yii::t('app', 'Stories'), 'url' => ["/story/index"]],
            ['label' =>Yii::t('app', 'Unpublished'), 'url' => ["/story/unpublished"]],
            ['label' =>Yii::t('app', 'Published'), 'url' => ["/story/published"]],
            ['label' =>Yii::t('app', 'Pending'), 'url' => ["/story/pending"]],
            [
                'label' =>Yii::t('app', 'Sponsored'), 'url' => ["/story/sponsored"],
                'visible'=>Helpers::columnVisible([User::ROLE_MARKETER, User::ROLE_SUPERADMIN])
            ],
            ['label' =>Yii::t('app', 'Create Story'), 'url' => ["/story/create"]],
        ],
        'active'=>activeParent('story'),
    ],
    ['label' => menuLabel("fa fa-tags", Yii::t('app', 'Categories')), 'url' => ["/category/index"]],

    ['label' => separator(Yii::t('app', 'User')),],
    ['label' => menuLabel("si si-user", Yii::t('app', 'Users')), 'url' => ["/user/index"]],
    ['label' => menuLabel("si si-settings", Yii::t('app', 'User settings')), 'url' => ["/admin/settings"]],

    ['label' => separator(Yii::t('app', 'Settings'))],
    ['label' => menuLabel("fa fa-gears", Yii::t('app', 'Settings')), 'url' => "javascript:;",
        'template'=>'<a href="{url}" class="nav-submenu" data-toggle="nav-submenu">{label}</a>',
        'items'=>
        [
            ['label' =>Yii::t('app', 'Translator'), 'url' => ["/settings/translation/index"]],
            ['label' =>Yii::t('app', 'Daily report'), 'url' => ["/statistics/daily-report"]],
            ['label' =>Yii::t('app', 'Stories per category'), 'url' => ["/statistics/stories-per-category"]],
        ],
       // 'active'=>activeParent('language'),
    ],
];

$roleItems=[];
if($role==User::ROLE_ADMIN)
{

}
else if($role==User::ROLE_SUPERADMIN || $role==User::ROLE_MARKETER)
{

    $roleItems=
    [
        ['label' => separator(Yii::t('app', 'Language')),],
        ['label' => menuLabel("fa fa-globe", Yii::t('app', 'Countries')), 'url' => ["/country/index"]],
        ['label' => menuLabel("fa fa-language", Yii::t('app', 'Language')), 'url' => "javascript:;",
            'template'=>'<a href="{url}" class="nav-submenu" data-toggle="nav-submenu">{label}</a>',
            'items'=>
            [
                ['label' =>Yii::t('app', 'Create'), 'url' => ["/language/create"]],
                ['label' =>Yii::t('app', 'List of current languages'), 'url' => ["/language/index"]],
            ],
            'active'=>activeParent('language/'),
        ],

        ['label' => separator(Yii::t('app', 'Admin Area'))],
        ['label' => menuLabel("fa fa-bank", Yii::t('app', 'Admin')), 'url' => "javascript:;",
            'template'=>'<a href="{url}" class="nav-submenu" data-toggle="nav-submenu">{label}</a>',
            'items'=>
            [
                ['label' =>Yii::t('app', 'Story Inject'), 'url' => ["/settings/settings-story-inject/index"]],
                ['label' => Yii::t('app', 'Contact everyone'), 'url' => ["/site/contact-everyone"]],
                ['label' => Yii::t('app', 'Prerolls'), 'url' => ["/preroll/preroll/index"]],
            ],
           // 'active'=>activeParent('language'),
        ],
    ];
}
else if($role==User::ROLE_SENIOREDITOR)
{

}

//other items
$roleItems[]=
[
    'label' => menuLabel("si si-logout", Yii::t('app', 'Logout')),
    'url' => ["/site/logout"],
    'template'=>'<a href="{url}" data-method="post">{label}</a>'

];

$mergeItems=array_merge($items, $roleItems);