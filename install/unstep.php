<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!check_bitrix_sessid()){
	return;
}

echo(CAdminMessage::ShowNote(" ".Loc::getMessage("FALBAR_TOTOP_UNSTEP_AFTER")));
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="RU" />
	<input type="submit" value="step">
</form>