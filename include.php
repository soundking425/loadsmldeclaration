<?php

use \Bitrix\Main\EventManager;

IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'loadxmldeclaration',
    [
        'AbstractXmlParser' => 'classes/general/AbstractXmlParser.php',
        'DeclarationParser' => 'classes/general/DeclarationParser.php',
        'FreeDocParser' => 'classes/general/FreeDocParser.php',
    ]
);
