<?php

use local\LoadXmlVed;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
/** @global CMain $APPLICATION */
/** @global CUser $USER */
global $APPLICATION;

if (!$USER->CanDoOperation('edit_other_settings'))
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

/** @var CAdminMessage $message */


CModule::IncludeModule("loadxmldeclaration");
$arError = [];

$bLoadComplete = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['LoadDt'] == 'Y') {
    $fileNameDt = 'data_dt' . $USER->GetID() . time() . '.xml';
    $fileNameFreeDoc = 'data_free_doc' . $USER->GetID() . time() . '.xml';
    $sUploadDir = CTempFile::GetDirectoryName(1);
    $filePathDt = $sUploadDir . $fileNameDt;
    $filePathFreeDoc = $sUploadDir . $fileNameFreeDoc;


    if (!check_bitrix_sessid()) {
        $arError[] = [
            "id" => "bad_sessid",
            "text" => GetMessage("ERROR_BAD_SESSID")
        ];
    } else {
        if (!empty($_FILES["file_dt"]["tmp_name"])) {
            CheckDirPath($sUploadDir);
            $res = CFile::CheckFile($_FILES["file_dt"], 0, false, 'xml');

            if ($res <> '') {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => $res
                ];
            } elseif (file_exists($filePathDt)) {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => GetMessage("ERROR_EXISTS_FILE")
                ];
            } elseif (!@copy($_FILES["file_dt"]["tmp_name"], $filePathDt)) {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => GetMessage("ERROR_COPY_FILE")
                ];
            } else {
                @chmod($filePathDt, BX_FILE_PERMISSIONS);
            }

        } elseif (empty($_FILES["file_dt"]["tmp_name"])) {
            $arError[] = [
                "id" => "IMPORT",
                "text" => GetMessage("ERROR_EXISTS_FILE")
            ];
        }

        if (!empty($_FILES["file_free_doc"]["tmp_name"])) {
            CheckDirPath($sUploadDir);
            $res = CFile::CheckFile($_FILES["file_free_doc"], 0, false, 'xml');

            if ($res <> '') {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => $res
                ];
            } elseif (file_exists($filePathFreeDoc)) {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => GetMessage("ERROR_EXISTS_FILE")
                ];
            } elseif (!@copy($_FILES["file_free_doc"]["tmp_name"], $filePathFreeDoc)) {
                $arError[] = [
                    "id" => "IMPORT",
                    "text" => GetMessage("ERROR_COPY_FILE")
                ];
            } else {
                @chmod($filePathFreeDoc, BX_FILE_PERMISSIONS);
            }

        } elseif (empty($_FILES["file_free_doc"]["tmp_name"])) {
            $arError[] = [
                "id" => "IMPORT",
                "text" => GetMessage("ERROR_EXISTS_FILE")
            ];
        }
    }

    if (empty($arError)) {
        try {
            $LoadXmlDt = new DeclarationParser($filePathDt, $filePathFreeDoc);
            $data = $LoadXmlDt->parse();
            $bLoadComplete = true;
            unlink($filePathDt);
            unlink($filePathFreeDoc);
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            die;
        } catch (Exception $e) {
            $arError[] = [
                "id" => "IMPORT",
                "text" => GetMessage("ERROR_XLM_READ")
            ];
        }


    }

    $e = new CAdminException($arError);
    $message = new CAdminMessage(GetMessage("ERROR_LOAD_DT"), $e);
}


require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

if ($bLoadComplete) {
//    CAdminMessage::ShowMessage(array(
//            "MESSAGE"=>GetMessage("IM_IMPORT_COMPLETE"),
//            "DETAILS"=>GetMessage("IM_IMPORT_TOTAL", Array('#COUNT#' => $importCount)),
//            "HTML"=>true,
//            "TYPE"=>"OK",
//    ));

} else if (isset($message) && $message)
    echo $message->Show();

$APPLICATION->SetTitle(GetMessage('LOAD_DT_TITLE'));

$aTabs = [
        ['DIV' => 'edit1', 'TAB' => GetMessage('LOAD_DT_TAB'), 'ICON' => 'main_user_edit', 'TITLE' => GetMessage('LOAD_DT_TAB_TITLE')],
];
$tabControl = new CAdminTabControl('tabControl', $aTabs, true, true);

?>
    <form method="POST" action="<?= $APPLICATION->GetCurPageParam() ?>" name="load_dt" enctype="multipart/form-data">
        <input type="hidden" name="Update" value="Y"/>
        <input type="hidden" name="lang" value="<?= LANG ?>"/>
        <?= bitrix_sessid_post() ?>
        <?php
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td><?php echo GetMessage('LOAD_DT_file_path') ?></td>
            <td>
                <input type="file" name="file_dt" size="30"/>
            </td>
        </tr>

        <tr>
            <td><?php echo GetMessage('LOAD_FREE_DOC_file_path') ?></td>
            <td>
                <input type="file" name="file_free_doc" size="30"/>
            </td>
        </tr>
        <input type="hidden" name="LoadDt" value="Y">
        <?php
        $tabControl->EndTab();
        $tabControl->Buttons();
        ?>
        <input type="submit" id="start_button" value="<?php echo GetMessage('LOAD_DT_BUTTON') ?>" class="adm-btn-save">

        <?php
        $tabControl->End();
        $tabControl->ShowWarnings("load_dt", $message);
        ?>
    </form>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
