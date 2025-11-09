<?php

use Bitrix\Main\Localization\Loc;
use	Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$aTabs = array(
	array(
		"DIV" 	  => "edit",
		"TAB" 	  => Loc::getMessage("SETTING"),
		"TITLE"   => Loc::getMessage("SETTING"),
		"OPTIONS" => array(
            array(
                "classifier_type_procedures",
                Loc::getMessage("DOCUMENT_MODE_T_MODE_CODE"),
                "50",
                array("text", 5)
            ),
            array(
                "classifier_decisions_taken_customs_authorities",
                Loc::getMessage("DOCUMENT_MODE_T_RES_CODE"),
                "50",
                array("text", 5)
            ),
            array(
                "classifier_type_document",
                Loc::getMessage("DOCUMENT_MODE_CODE"),
                "01",
                array("text", 5)
            ),
		)
	)
);

if($request->isPost() && check_bitrix_sessid()){
	foreach($aTabs as $aTab){
		foreach($aTab["OPTIONS"] as $arOption){
			if(!is_array($arOption)){
				continue;
			}

			if($arOption["note"]){
				continue;
			}

			if($request["apply"]){
				$optionValue = $request->getPost($arOption[0]);
				if($arOption[0] == "switch_on"){
					if($optionValue == ""){
						$optionValue = "N";
					}
				}
				Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
			}elseif($request["default"]){
				Option::set($module_id, $arOption[0], $arOption[2]);
			}
		}
	}

	LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}

$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);

$tabControl->Begin();
?>

<form action="<?php echo($APPLICATION->GetCurPage()); ?>?mid=<?php echo($module_id); ?>&lang=<?php echo(LANG); ?>" method="post">

	<?php
	foreach($aTabs as $aTab){
		if($aTab["OPTIONS"]){
			$tabControl->BeginNextTab();
			__AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
		}
	}

	$tabControl->Buttons();
	?>

    <input type="submit" name="Update" value="<?=GetMessage('MAIN_SAVE')?>" title="<?=GetMessage('MAIN_OPT_SAVE_TITLE')?>" class="adm-btn-save">

	<?php
	echo(bitrix_sessid_post());
	?>

</form>

<?
$tabControl->End();
?>
