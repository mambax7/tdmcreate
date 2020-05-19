<?php

namespace XoopsModules\Modulebuilder\Files\Templates\User\Defstyle;

use XoopsModules\Modulebuilder;
use XoopsModules\Modulebuilder\Files;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * modulebuilder module.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 *
 * @since           2.5.0
 *
 * @author          Txmod Xoops http://www.txmodxoops.org
 *
 */

/**
 * class PagesItem.
 */
class PagesItem extends Files\CreateFile
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
     * @return PagesItem
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
     * @param string $table
     * @param string $filename
     * @param        $tables
     */
    public function write($module, $table, $tables, $filename)
    {
        $this->setModule($module);
        $this->setTable($table);
        $this->setTables($tables);
        $this->setFileName($filename);
    }

    /**
     * @private function getTemplatesUserPagesItemPanel
     * @param string $moduleDirname
     * @param        $tableId
     * @param        $tableMid
     * @param        $tableName
     * @param        $tableSoleName
     * @param        $language
     * @return string
     */
    private function getTemplatesUserPagesItemPanel($moduleDirname, $tableId, $tableMid, $tableName, $tableSoleName, $language)
    {
        $hc      = Modulebuilder\Files\CreateHtmlCode::getInstance();
        $sc      = Modulebuilder\Files\CreateSmartyCode::getInstance();
        $fields  = $this->getTableFields($tableMid, $tableId);
        $ret     = '';
        $retNumb = '';
        $fieldId   = '';
        $ccFieldId = '';
        $keyDouble = '';
        foreach (array_keys($fields) as $f) {
            if (0 == $f) {
                $fieldId = $fields[$f]->getVar('field_name');
                $ccFieldId = $this->getCamelCase($fieldId, false, true);
                $keyDouble = $sc->getSmartyDoubleVar($tableSoleName, $fieldId);
            }
            $fieldElement = $fields[$f]->getVar('field_element');
            if (1 == $fields[$f]->getVar('field_user')) {
                if (1 == $fields[$f]->getVar('field_thead')) {
                    switch ($fieldElement) {
                        default:
                        //case 2:
                            $fieldName   = $fields[$f]->getVar('field_name');
                            $rpFieldName = $this->getRightString($fieldName);
                            $doubleVar   = $sc->getSmartyDoubleVar($tableSoleName, $rpFieldName);
                            $retNumb     .= $hc->getHtmlHNumb($doubleVar, '3', 'panel-title', "\t");
                            break;
                    }
                }
            }
        }
        $ret     .= $hc->getHtmlI('', '', $ccFieldId . '_' . $keyDouble);
        $ret     .= $hc->getHtmlDiv($retNumb, 'panel-heading');
        $retElem = '';
        foreach (array_keys($fields) as $f) {
            $fieldElement = $fields[$f]->getVar('field_element');
            if (1 == $fields[$f]->getVar('field_user')) {
                if (1 == $fields[$f]->getVar('field_tbody')) {
                    switch ($fieldElement) {
                        default:
                        //case 3:
                        //case 4:
                            $fieldName   = $fields[$f]->getVar('field_name');
                            $rpFieldName = $this->getRightString($fieldName);
                            $doubleVar   = $sc->getSmartyDoubleVar($tableSoleName, $rpFieldName);
                            $retElem     .= $hc->getHtmlSpan($doubleVar, 'col-sm-9 justify', "\t");
                            break;
                        case 10:
                            $fieldName   = $fields[$f]->getVar('field_name');
                            $rpFieldName = $this->getRightString($fieldName);
                            $singleVar   = $sc->getSmartySingleVar('xoops_icons32_url');
                            $doubleVar   = $sc->getSmartyDoubleVar($tableSoleName, $rpFieldName);
                            $img         = $hc->getHtmlImage($singleVar . '/' . $doubleVar, (string)$tableName);
                            $retElem     .= $hc->getHtmlSpan($img, 'col-sm-3', "\t");
                            unset($img);
                            break;
                        case 13:
                            $fieldName   = $fields[$f]->getVar('field_name');
                            $rpFieldName = $this->getRightString($fieldName);
                            $singleVar   = $sc->getSmartySingleVar($moduleDirname . '_upload_url');
                            $doubleVar   = $sc->getSmartyDoubleVar($tableSoleName, $rpFieldName);
                            $img         = $hc->getHtmlImage($singleVar . "/images/{$tableName}/" . $doubleVar, (string)$tableName);
                            $retElem     .= $hc->getHtmlSpan($img, 'col-sm-3',"\t");
                            unset($img);
                            break;
                    }
                }
            }
        }
        $ret .= $hc->getHtmlDiv($retElem, 'panel-body');

        $retFoot   = '';
        foreach (array_keys($fields) as $f) {
            if (1 == $fields[$f]->getVar('field_user')) {
                if (1 == $fields[$f]->getVar('field_tfoot')) {
                    $fieldName   = $fields[$f]->getVar('field_name');
                    $rpFieldName = $this->getRightString($fieldName);
                    $langConst   = mb_strtoupper($tableSoleName) . '_' . mb_strtoupper($rpFieldName);
                    $lang        = $sc->getSmartyConst($language, $langConst);
                    $doubleVar   = $sc->getSmartyDoubleVar($tableSoleName, $rpFieldName);
                    $retFoot     .= $hc->getHtmlSpan($lang . ': ' . $doubleVar, 'block-pie justify',"\t");
                }
            }
        }

        $anchors = '';
        $lang        = $sc->getSmartyConst($language, mb_strtoupper($tableName) . '_LIST');
        $contIf =  $hc->getHtmlAnchor($tableName . ".php?op=list&amp;#{$ccFieldId}_" . $keyDouble, $lang, $lang, '', 'btn btn-success right', '', "\t\t\t", "\n");
        $lang        = $sc->getSmartyConst($language, 'DETAILS');
        $contElse =  $hc->getHtmlAnchor($tableName . ".php?op=show&amp;{$fieldId}=" . $keyDouble, $lang, $lang, '', 'btn btn-success right', '', "\t\t\t", "\n");
        $anchors .= $sc->getSmartyConditions('showItem', '', '', $contIf, $contElse, '', '', "\t\t");
        $lang        = $sc->getSmartyConst('', '_EDIT');
        $contIf =  $hc->getHtmlAnchor($tableName . ".php?op=edit&amp;{$fieldId}=" . $keyDouble, $lang, $lang, '', 'btn btn-primary right', '', "\t\t\t", "\n");
        $lang        = $sc->getSmartyConst('', '_DELETE');
        $contIf .=  $hc->getHtmlAnchor($tableName . ".php?op=delete&amp;{$fieldId}=" . $keyDouble, $lang, $lang, '', 'btn btn-danger right', '', "\t\t\t", "\n");
        $anchors .= $sc->getSmartyConditions('permEdit', '', '', $contIf, false, '', '', "\t\t");
        $lang        = $sc->getSmartyConst($language, 'BROKEN');
        $anchors .=  $hc->getHtmlAnchor($tableName . ".php?op=broken&amp;{$fieldId}=" . $keyDouble, $lang, $lang, '', 'btn btn-warning right', '', "\t\t", "\n");
        $retFoot     .= $hc->getHtmlDiv($anchors, 'col-sm-12 right',"\t", "\n", );
        $ret .= $hc->getHtmlDiv($retFoot, 'panel-foot');

        return $ret;
    }

    /**
     * @public function render
     * @param null
     * @return bool|string
     */
    public function render()
    {
        $module = $this->getModule();
        $table  = $this->getTable();
        //$tables = $this->getTables();
        //$tables        = $this->getTableTables($module->getVar('mod_id'), 'table_order');
        $moduleDirname = $module->getVar('mod_dirname');
        $filename      = $this->getFileName();
        $language      = $this->getLanguage($moduleDirname, 'MA');
        $content       = '';
        $tableId         = $table->getVar('table_id');
        $tableMid        = $table->getVar('table_mid');
        $tableName       = $table->getVar('table_name');
        $tableSoleName   = $table->getVar('table_solename');
        $tableCategory[] = $table->getVar('table_category');
        //$tableIndex      = $table->getVar('table_index');
        if (in_array(0, $tableCategory)) {
            $content .= $this->getTemplatesUserPagesItemPanel($moduleDirname, $tableId, $tableMid, $tableName, $tableSoleName, $language);
        }

        $this->create($moduleDirname, 'templates', $filename, $content, _AM_MODULEBUILDER_FILE_CREATED, _AM_MODULEBUILDER_FILE_NOTCREATED);

        return $this->renderFile();
    }
}
