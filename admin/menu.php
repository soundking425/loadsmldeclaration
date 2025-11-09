<?php

IncludeModuleLangFile(__FILE__);
/** @global CMain $APPLICATION */
global $APPLICATION;

if ($APPLICATION->GetGroupRight('search') != 'D') {
    $aMenu = [
        'parent_menu' => 'global_menu_content',
        'section' => 'search',
        'sort' => 200,
        'text' => GetMessage('mnu_test_work'),
        'title' => GetMessage('mnu_test_work_title'),
        'icon' => 'form_menu_icon',
        'page_icon' => 'form_page_icon',
        'items_id' => 'menu_search',
        'url' => 'load_dt.php?lang=' . LANGUAGE_ID,
        'more_url' => ['load_dt.php'],
    ];
    return $aMenu;
}
return false;
