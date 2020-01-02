<?php

namespace XoopsModules\Tdmcreate\Files\Admin;

use XoopsModules\Tdmcreate;
use XoopsModules\Tdmcreate\Files;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * tdmcreate module.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 *
 * @since           2.5.0
 *
 * @author          Txmod Xoops http://www.txmodxoops.org
 *
 * @version         $Id: admin_menu.php 12258 2014-01-02 09:33:29Z timgno $
 */

/**
 * Class AdminMenu.
 */
class AdminMenu extends Files\CreateFile
{
    /**
     * @public function constructor
     * @param null
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @static function getInstance
     * @param null
     * @return AdminMenu
     */
    public static function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @public function write
     * @param string $module
     * @param string $filename
     */
    public function write($module, $filename)
    {
        $this->setModule($module);
        $this->setFileName($filename);
    }

    /**
     * @private function getAdminMenuArray
     * @param $param
     * @param $adminObject
     * @param $ref
     *
     * @return string
     */
    private function getAdminMenuArray($param = [], $adminObject = false, $ref = false)
    {
        $xc  = Tdmcreate\Files\CreateXoopsCode::getInstance();
        $ret = '';
        foreach ($param as $key => $value) {
            if ($adminObject) {
                $ret .= $xc->getXcEqualsOperator("\$adminmenu[\$i]['{$key}']", (string)$value);
            } else {
                if ($ref) {
                    $ret .= $xc->getXcEqualsOperator((string)$key, (string)$value, null, true);
                } else {
                    $ret .= $xc->getXcEqualsOperator((string)$key, (string)$value);
                }
            }
        }

        return $ret;
    }

    /**
     * @private function getAdminMenuHeader
     * @param null
     * @return string
     */
    private function getAdminMenuHeader()
    {
        $dirname = ['$dirname' => 'basename(dirname(__DIR__))'];
        $ret     = $this->getAdminMenuArray($dirname);
        $mod     = [
            '$moduleHandler' => "xoops_getHandler('module')",
            '$xoopsModule'   => 'XoopsModule::getByDirname($dirname)',
            '$moduleInfo'    => "\$moduleHandler->get(\$xoopsModule->getVar('mid'))",
        ];
        $ret     .= $this->getAdminMenuArray($mod);
        $sys     = ['$sysPathIcon32' => "\$moduleInfo->getInfo('sysicons32')"];
        $ret     .= $this->getAdminMenuArray($sys);

        return $ret;
    }

    /**
     * @private function getAdminMenuDashboard
     * @param string $language
     * @param int    $menu
     *
     * @return string
     */
    private function getAdminMenuDashboard($language, $menu)
    {
        $xc    = Tdmcreate\Files\CreateXoopsCode::getInstance();
        $param = ['title' => "{$language}{$menu}", 'link' => "'admin/index.php'", 'icon' => "\$sysPathIcon32.'/dashboard.png'"];
        $ret   = $xc->getXcEqualsOperator('$i', '1');
        $ret   .= $this->getAdminMenuArray($param, true);
        $ret   .= $this->getSimpleString('++$i;');

        return $ret;
    }

    /**
     * @private function getAdminMenuImagesPath
     * @param array $tables
     * @param int   $t
     *
     * @return string
     */
    private function getAdminMenuImagesPath($tables, $t)
    {
        $xc     = Tdmcreate\Files\CreateXoopsCode::getInstance();
        $ret    = '';
        $fields = $this->getTableFields($tables[$t]->getVar('table_id'));
        foreach (array_keys($fields) as $f) {
            $fieldElement = $fields[$f]->getVar('field_element');
            switch ($fieldElement) {
                case 13:
                    $ret = $xc->getXcEqualsOperator("\$adminmenu[\$i]['icon']", "'assets/icons/32/{$tables[$t]->getVar('table_image')}'");
                    break;
                default:
                    $ret = $xc->getXcEqualsOperator("\$adminmenu[\$i]['icon']", "\$sysPathIcon32.'/{$tables[$t]->getVar('table_image')}'");
                    break;
            }
        }

        return $ret;
    }

    /**
     * @private function getAdminMenuList
     * @param string $module
     * @param string $language
     * @param string $langAbout
     * @param int    $menu
     *
     * @return string
     */
    private function getAdminMenuList($module, $language, $langAbout, $menu)
    {
        $ret    = '';
        $tables = $this->getTableTables($module->getVar('mod_id'), 'table_order');
        foreach (array_keys($tables) as $t) {
            $tablePermissions[] = $tables[$t]->getVar('table_permissions');
            if (1 == $tables[$t]->getVar('table_admin')) {
                ++$menu;
                $param1 = ['title' => "{$language}{$menu}", 'link' => "'admin/{$tables[$t]->getVar('table_name')}.php'", 'icon' => "'assets/icons/32/{$tables[$t]->getVar('table_image')}'"];
                $ret    .= $this->getAdminMenuArray($param1, true);
                $ret    .= $this->getSimpleString('++$i;');
            }
        }
        if (in_array(1, $tablePermissions)) {
            ++$menu;
            $param2 = ['title' => "{$language}{$menu}", 'link' => "'admin/permissions.php'", 'icon' => "\$sysPathIcon32.'/permissions.png'"];
            $ret    .= $this->getAdminMenuArray($param2, true);
            $ret    .= $this->getSimpleString('++$i;');
        }
        unset($menu);
        $param3 = ['title' => (string)$langAbout, 'link' => "'admin/about.php'", 'icon' => "\$sysPathIcon32.'/about.png'"];
        $ret    .= $this->getAdminMenuArray($param3, true);
        $ret    .= $this->getSimpleString('unset($i);');

        return $ret;
    }

    /**
     * @public function render
     * @param null
     * @return bool|string
     */
    public function render()
    {
        $module        = $this->getModule();
        $filename      = $this->getFileName();
        $moduleDirname = $module->getVar('mod_dirname');
        $language      = $this->getLanguage($moduleDirname, 'MI', 'ADMENU');
        $langAbout     = $this->getLanguage($moduleDirname, 'MI', 'ABOUT');
        $menu          = 1;
        $content       = $this->getHeaderFilesComments($module, $filename);
        $content       .= $this->getAdminMenuHeader();
        $content       .= $this->getAdminMenuDashboard($language, $menu);
        $content       .= $this->getAdminMenuList($module, $language, $langAbout, $menu);

        $this->create($moduleDirname, 'admin', $filename, $content, _AM_TDMCREATE_FILE_CREATED, _AM_TDMCREATE_FILE_NOTCREATED);

        return $this->renderFile();
    }
}
