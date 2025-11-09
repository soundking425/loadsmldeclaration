<?php
// WOW replace def code
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class LoadXmlDeclaration extends CModule
{
    public function __construct()
    {

        if (file_exists(__DIR__ . "/version.php")) {

            $arModuleVersion = array();

            include_once(__DIR__ . "/version.php");

            $this->MODULE_ID = strtolower(get_class($this));
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESC");
            $this->PARTNER_NAME = 'TEST';
            $this->PARTNER_URI = '://';
        }

        return false;
    }

    public function DoInstall()
    {
        $this->putPathAdminFile();
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);

        return false;
    }

    private function putPathAdminFile()
    {
        $parent_dir = __DIR__ . '/..';
        $current = "<?php\nrequire  '$parent_dir/admin/load_dt.php';";
        file_put_contents(__DIR__ . '/admin/load_dt.php', $current);
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');

        return true;
    }

    public function InstallDB()
    {
        return true;
    }

    public function InstallEvents()
    {
        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);

        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');

        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }
}
