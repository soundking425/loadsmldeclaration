<?php
use Bitrix\Main\HttpApplication;

/* @var CMain $APPLICATION */
/* @var CUser $USER */
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
$POST_RIGHT = CMain::GetUserRight($module_id);
if ($POST_RIGHT >= 'R') :
    IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
    IncludeModuleLangFile(__FILE__);

    $arAllOptions = [
            ['classifier_type_procedures', GetMessage('DOCUMENT_MODE_T_MODE_CODE'), ['text-list', 3, 20]],
            ['classifier_decisions_taken_customs_authorities', GetMessage('DOCUMENT_MODE_T_RES_CODE'), ['text-list', 3, 20]],
            ['classifier_type_document', GetMessage('DOCUMENT_MODE_CODE'), ['text', 35]],
    ];
    $aTabs = [
            ['DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => 'subscribe_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')],
    ];
    $tabControl = new CAdminTabControl('tabControl', $aTabs);

    /* @var $request \Bitrix\Main\HttpRequest */
    $request = \Bitrix\Main\Context::getCurrent()->getRequest();

    if (
            $request->isPost()
            && (
                    (string)$request['Update'] !== ''
            )
            && $POST_RIGHT === 'W'
            && check_bitrix_sessid()
    ) {
        foreach ($arAllOptions as $arOption) {
            $name = $arOption[0];
            if ($arOption[2][0] == 'text-list') {
                $val = '';
                foreach ($_POST[$name] as $postValue) {
                    $postValue = trim($postValue);
                    if ($postValue !== '') {
                        $val .= ($val !== '' ? ',' : '') . $postValue;
                    }
                }
            } else {
                $val = $_POST[$name];
            }

            if ($arOption[2][0] == 'checkbox' && $val !== 'Y') {
                $val = 'N';
            }

            COption::SetOptionString($module_id, $name, $val);
        }
    }

    ?>
    <form method="post"
          action="<?php echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= LANGUAGE_ID ?>">
        <?php
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        foreach ($arAllOptions as $Option) {
            $type = $Option[2];
            $val = COption::GetOptionString($module_id, $Option[0]);
            ?>
            <tr>
                <td width="40%" <?php echo ($type[0] == 'textarea' || $type[0] == 'text-list') ? 'class="adm-detail-valign-top"' : ''; ?>>
                    <label for="<?php echo htmlspecialcharsbx($Option[0]) ?>"><?php echo $Option[1] ?></label></td>
                <td width="60%">
                    <?php
                    if ($type[0] == 'checkbox') {
                        ?><input type="checkbox" name="<?php echo htmlspecialcharsbx($Option[0]) ?>"
                                 id="<?php echo htmlspecialcharsbx($Option[0]) ?>"
                                 value="Y" <?php echo ($val == 'Y') ? 'checked' : ''; ?>><?php
                    } elseif ($type[0] == 'text') {
                        ?><input type="text" size="<?php echo $type[1] ?>" maxlength="255"
                                 value="<?php echo htmlspecialcharsbx($val) ?>"
                                 name="<?php echo htmlspecialcharsbx($Option[0]) ?>"><?php
                    } elseif ($type[0] == 'textarea') {
                        ?><textarea rows="<?php echo $type[1] ?>" cols="<?php echo $type[2] ?>"
                                    name="<?php echo htmlspecialcharsbx($Option[0]) ?>"><?php echo htmlspecialcharsbx($val) ?></textarea><?php
                    } elseif ($type[0] == 'text-list') {
                        $aVal = explode(',', $val);
                        foreach ($aVal as $val) {
                            ?><input type="text" size="<?php echo $type[2] ?>"
                                     value="<?php echo htmlspecialcharsbx($val) ?>"
                                     name="<?php echo htmlspecialcharsbx($Option[0]) . '[]' ?>"><br><?php
                        }
                        for ($j = 0; $j < $type[1]; $j++) {
                            ?><input type="text" size="<?php echo $type[2] ?>" value=""
                                     name="<?php echo htmlspecialcharsbx($Option[0]) . '[]' ?>"><br><?php
                        }
                    } elseif ($type[0] == 'selectbox') {
                        ?><select name="<?php echo htmlspecialcharsbx($Option[0]) ?>"><?php
                        foreach ($type[1] as $optionValue => $optionDisplay) {
                            ?>
                            <option
                            value="<?php echo $optionValue ?>" <?php echo ($val == $optionValue) ? 'selected' : ''; ?>><?php echo htmlspecialcharsbx($optionDisplay) ?></option><?php
                        }
                        ?></select><?php
                    }
                    ?></td>
            </tr>
            <?php
        }
        ?>
        <?php $tabControl->Buttons(); ?>
        <input type="submit" name="Update" value="<?=GetMessage('MAIN_SAVE')?>" title="<?=GetMessage('MAIN_OPT_SAVE_TITLE')?>" class="adm-btn-save">
        <?= bitrix_sessid_post(); ?>
        <?php $tabControl->End(); ?>
    </form>
<?php endif;
