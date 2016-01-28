<?php

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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 *
 * @since           2.5.0
 *
 * @author          Txmod Xoops http://www.txmodxoops.org
 *
 * @version         $Id: UserXoopsCode.php 12258 2014-01-02 09:33:29Z timgno $
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class UserXoopsCode.
 */
class UserXoopsCode
{
    /*
    * @var mixed
    */
    private $phpcode = null;

    /*
    * @var mixed
    */
    private $xoopscode = null;

    /*
    *  @public function constructor
    *  @param null
    */
    /**
     *
     */
    public function __construct()
    {
        $this->phpcode = TDMCreatePhpCode::getInstance();
        $this->xoopscode = TDMCreateXoopsCode::getInstance();
    }

    /*
    *  @static function &getInstance
    *  @param null
    */
    /**
     * @return UserXoopsCode
     */
    public static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /*
    *  @public function getUserTplMain
    *  @param string $moduleDirname
    *  @param string $tableName
    *  @return string
    */
    public function getUserTplMain($moduleDirname, $tableName = 'index')
    {
        return "\$GLOBALS['xoopsOption']['template_main'] = '{$moduleDirname}_{$tableName}.tpl';\n";
    }

    /*
     * @public function getUserAddMeta
     * @param $type
     * @param $language
     * @param $tableName
     *  
     * @return string
     */
    public function getUserAddMeta($type = '', $language, $tableName = '')
    {
        $stuTableName = strtoupper($tableName);

        return "\$GLOBALS['xoTheme']->addMeta( 'meta', '{$type}', strip_tags({$language}{$stuTableName}));\n";
    }

    /*
    *  @public function getUserMetaKeywords
    *  @param string $moduleDirname
    *  
    *  @return string
    */
    public function getUserMetaKeywords($moduleDirname)
    {
        return "{$moduleDirname}MetaKeywords(\${$moduleDirname}->getConfig('keywords').', '. implode(', ', \$keywords));\n";
    }

    /*
    *  @public function getUserMetaDesc
    *  @param string $moduleDirname
    *  @param string $stuTableSoleName
    *  @param string $language
    *  
    *  @return string
    */
    public function getUserMetaDesc($moduleDirname, $stuTableSoleName, $language)
    {
        $stuModuleDirname = strtoupper($moduleDirname);

        return "{$moduleDirname}MetaDescription({$language}{$stuTableSoleName}_DESC);\n";
    }

    /*
    *  @public function getUserBreadcrumbs
    *  @param string $moduleDirname
    *  @param string $language
    *  
    *  @return string
    */
    public function getUserBreadcrumbs($tableName, $language)
    {
        $stuTableName = strtoupper($tableName);

        return "\$xoBreadcrumbs[] = array('title' => {$language}{$stuTableName});\n";
    }

    /*
    *  @public function getUserBreadcrumbs
    *  @param string $moduleDirname
    *  
    *  @return string
    */
    public function getUserBreadcrumbsHeaderFile($moduleDirname)
    {
        $stuModuleDirname = strtoupper($moduleDirname);
        $ret = $this->phpcode->getPhpCodeCommentLine('Breadcrumbs');
        $ret .= "\$xoBreadcrumbs = array();\n";
        $ret .= "\$xoBreadcrumbs[] = array('title' => \$GLOBALS['xoopsModule']->getVar('name'), 'link' => {$stuModuleDirname}_URL . '/');\n";

        return $ret;
    }

    /*
    *  @public function getUserBreadcrumbs
    *  @param null
    *  
    *  @return string
    */
    public function getUserBreadcrumbsFooterFile()
    {
        $cond = $this->xoopscode->getXoopsCodeTplAssign('xoBreadcrumbs', '$xoBreadcrumbs');
        $ret .= $this->phpcode->getPhpCodeConditions('count($xoBreadcrumbs)', ' > ', '1', $cond, false, "\t\t");

        return $ret;
    }

    /*
    *  @public function getUserModVersion
    *  @param $array
    *  
    *  @return string
    */
    public function getUserModVersion($array = array(), $element = 1, $left = '', $index = '', $right = '', $desc = '', $arrayOptions = '')
    {
        $index = (is_string($index) && !empty($index)) ? "'{$index}'" : $index;
        $right = (is_string($right) && !empty($right)) ? "'{$right}'" : $right;
        $desc = is_string($desc) ? "'{$desc}'" : $desc;
        foreach ($array as $key => $value) {
            if ($element == 1) {
                $ret = "\$modversion['{$left}'] = {$desc};\n";
            }
            if ($element == 2) {
                $ret = "\$modversion['{$left}'][{$index}] = {$desc};\n";
            }
            if ($element == 3) {
                $ret = "\$modversion['{$left}'][{$index}][{$right}] = {$desc};\n";
            }
            if (($element == 3) && !empty($arrayOptions)) {
                $ret = "\$modversion['{$left}'][{$index}][{$right}] = {$desc};";
                $ret .= "\$modversion['{$left}'][{$index}][{$right}] = {$arrayOptions};\n";
            }
        }

        return $ret;
    }
}