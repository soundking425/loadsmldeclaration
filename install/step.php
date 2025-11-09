<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!check_bitrix_sessid()){
	return;
}

if($errorException = $APPLICATION->GetException()){
	echo CAdminMessage::ShowMessage($errorException->GetString());
}else{
	echo CAdminMessage::ShowNote("unstep");
}
?>

<form action="<?=$APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="RU" />
	<input type="submit" value="back">
</form>