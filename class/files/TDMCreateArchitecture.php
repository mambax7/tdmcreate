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
 * @version         $Id: TDMCreateArchitecture.php 12258 2014-01-02 09:33:29Z timgno $
 */
include dirname(__DIR__).'/autoload.php';
/**
 * Class TDMCreateArchitecture.
 */
class TDMCreateArchitecture extends TDMCreateStructure
{
    /*
    * @var mixed
    */
    private $tdmcreate = null;

    /*
    * @var mixed
    */
    private $tdmcfile = null;

    /*
    *  @public function constructor class
    *  @param null
    */
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->tdmcreate = TDMCreateHelper::getInstance();
        $this->tdmcfile = TDMCreateFile::getInstance();
        $this->setUploadPath(TDMC_UPLOAD_REPOSITORY_PATH);
    }

    /*
    *  @static function &getInstance
    *  @param null
    */
    /**
     * @return TDMCreateArchitecture
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
    *  @public function setBaseFoldersFiles
    *  @param string $module
    */
    /**
     * @param $module
     */
    public function setBaseFoldersFiles($module)
    {
        // Module
        $modId = $module->getVar('mod_id');
        // Id of tables
        $tables = $this->tdmcfile->getTableTables($modId);
        //
        $table = null;
        foreach (array_keys($tables) as $t) {
            $tableId = $tables[$t]->getVar('table_id');
            $tableName = $tables[$t]->getVar('table_name');
            $table = $this->tdmcreate->getHandler('tables')->get($tableId);
        }
        //
        $indexFile = XOOPS_UPLOAD_PATH.'/index.html';
        $stlModuleAuthor = str_replace(' ', '', strtolower($module->getVar('mod_author')));
        $this->setModuleName($module->getVar('mod_dirname'));
        $uploadPath = $this->getUploadPath();
        // Creation of "module" folder in the Directory repository
        $this->makeDir($uploadPath.'/'.$this->getModuleName());
        if (1 != $module->getVar('mod_user')) {
            // Copied of index.html file in "root module" folder
            $this->copyFile('', $indexFile, 'index.html');
        }
        if (1 == $module->getVar('mod_admin')) {
            // Creation of "admin" folder and index.html file
            $this->makeDirAndCopyFile('admin', $indexFile, 'index.html');
        }
        if (1 == $module->getVar('mod_blocks')) {
            // Creation of "blocks" folder and index.html file
            $this->makeDirAndCopyFile('blocks', $indexFile, 'index.html');
        }
        $language = ($GLOBALS['xoopsConfig']['language'] != 'english') ? $GLOBALS['xoopsConfig']['language'] : 'english';
        $copyFiles = array('class' => $indexFile, 'include' => $indexFile, 'language' => $indexFile, 'assets' => $indexFile, 'assets/css' => $indexFile,
                        'assets/icons' => $indexFile, 'assets/icons/16' => $indexFile, 'assets/icons/32' => $indexFile, 'docs' => $indexFile,
                        'assets/images' => $indexFile, 'assets/js' => $indexFile, 'language/'.$language => $indexFile, 'language/'.$language.'/help' => $indexFile, 'preloads' => $indexFile, );
        foreach ($copyFiles as $k => $v) {
            // Creation of folders and index.html file
            $this->makeDirAndCopyFile($k, $v, 'index.html');
        }
        //Copy the logo of the module
        $modImage = str_replace(' ', '', strtolower($module->getVar('mod_image')));
        $this->copyFile('assets/images', TDMC_UPLOAD_IMGMOD_PATH.'/'.$modImage, $modImage);
        // Copy of 'module_author_logo.gif' file in uploads dir
        $logoGifFrom = TDMC_UPLOAD_IMGMOD_PATH.'/'.$stlModuleAuthor.'_logo.gif';
        // If file exists
        if (!file_exists($logoGifFrom)) {
            // Rename file
            $copyFile = TDMC_IMAGES_LOGOS_URL.'/xoopsdevelopmentteam_logo.gif';
            $copyNewFile = $logoGifFrom;
            copy($copyFile, $copyNewFile);
        } else {
            // Copy file
            $copyFile = TDMC_IMAGES_LOGOS_URL.'/'.$stlModuleAuthor.'_logo.gif';
            $copyNewFile = $logoGifFrom;
            copy($copyFile, $copyNewFile);
        }
        // Creation of 'module_author_logo.gif' file
        $this->copyFile('assets/images', $copyNewFile, $stlModuleAuthor.'_logo.gif');
        $docs = array('/credits.txt' => 'credits.txt', '/install.txt' => 'install.txt',
                    '/lang_diff.txt' => 'lang_diff.txt', '/license.txt' => 'license.txt', '/readme.txt' => 'readme.txt', );
        foreach ($docs as $k => $v) {
            // Creation of folder docs and .txt files
            $this->makeDirAndCopyFile('docs', TDMC_DOCS_PATH.$k, $v);
        }
        if (1 == $module->getVar('mod_admin')) {
            // Creation of "templates" folder and index.html file
            $this->makeDirAndCopyFile('templates', $indexFile, 'index.html');
        }
        if (1 == $module->getVar('mod_admin')) {
            // Creation of "templates/admin" folder and index.html file
            $this->makeDirAndCopyFile('templates/admin', $indexFile, 'index.html');
        }
        if (!empty($tableName)) {
            if ((1 == $module->getVar('mod_blocks')) && (1 == $table->getVar('table_blocks'))) {
                // Creation of "templates/blocks" folder and index.html file
                $this->makeDirAndCopyFile('templates/blocks', $indexFile, 'index.html');
            }
            // Creation of "sql" folder and index.html file
            $this->makeDirAndCopyFile('sql', $indexFile, 'index.html');
            if ((1 == $module->getVar('mod_notifications')) && (1 == $table->getVar('table_notifications'))) {
                // Creation of "language/local_language/mail_template" folder and index.html file
                $this->makeDirAndCopyFile('language/'.$language.'/mail_template', $indexFile, 'index.html');
            }
        }
    }

    /*
    *  @public function setFilesToBuilding
    *  @param string $module
    */
    /**
     * @param $module
     *
     * @return array
     */
    public function setFilesToBuilding($module)
    {
        // Module
        $modId = $module->getVar('mod_id');
        $moduleDirname = $module->getVar('mod_dirname');
        $icon32 = 'assets/icons/32';
        $tables = $this->tdmcfile->getTableTables($modId);
        $files = $this->tdmcfile->getTableMoreFiles($modId);
        $ret = array();
        //
        $table = array();
        $tableCategory = array();
        $tableName = array();
        $tableAdmin = array();
        $tableUser = array();
        $tableBlocks = array();
        $tableSearch = array();
        $tableComments = array();
        $tableNotifications = array();
        $tablePermissions = array();
        $tableBroken = array();
        $tablePdf = array();
        $tablePrint = array();
        $tableRate = array();
        $tableRss = array();
        $tableSingle = array();
        $tableSubmit = array();
        $tableVisit = array();
        $tableTag = array();
        foreach (array_keys($tables) as $t) {
            $tableId = $tables[$t]->getVar('table_id');
            $tableName = $tables[$t]->getVar('table_name');
            $tableCategory[] = $tables[$t]->getVar('table_category');
            $tableImage = $tables[$t]->getVar('table_image');
            $tableAdmin[] = $tables[$t]->getVar('table_admin');
            $tableUser[] = $tables[$t]->getVar('table_user');
            $tableBlocks[] = $tables[$t]->getVar('table_blocks');
            $tableSearch[] = $tables[$t]->getVar('table_search');
            $tableComments[] = $tables[$t]->getVar('table_comments');
            $tableNotifications[] = $tables[$t]->getVar('table_notifications');
            $tablePermissions[] = $tables[$t]->getVar('table_permissions');
            $tableBroken[] = $tables[$t]->getVar('table_broken');
            $tablePdf[] = $tables[$t]->getVar('table_pdf');
            $tablePrint[] = $tables[$t]->getVar('table_print');
            $tableRate[] = $tables[$t]->getVar('table_rate');
            $tableRss[] = $tables[$t]->getVar('table_rss');
            $tableSingle[] = $tables[$t]->getVar('table_single');
            $tableSubmit[] = $tables[$t]->getVar('table_submit');
            $tableVisit[] = $tables[$t]->getVar('table_visit');
            $tableTag[] = $tables[$t]->getVar('table_tag');
            // Get Table Object
            $table = $this->tdmcreate->getHandler('tables')->get($tableId);
            // Copy of tables images file
            if (file_exists($uploadTableImage = TDMC_UPLOAD_IMGTAB_PATH.'/'.$tableImage)) {
                $this->copyFile($icon32, $uploadTableImage, $tableImage);
            } elseif (file_exists($uploadTableImage = XOOPS_ICONS32_PATH.'/'.$tableImage)) {
                $this->copyFile($icon32, $uploadTableImage, $tableImage);
            }
            // Creation of admin files
            if (in_array(1, $tableAdmin)) {
                // Admin Pages File
                $adminPages = AdminPages::getInstance();
                $adminPages->write($module, $table, $tableName.'.php');
                $ret[] = $adminPages->render();
                // Admin Templates File
                $adminTemplatesPages = TemplatesAdminPages::getInstance();
                $adminTemplatesPages->write($module, $table);
                $ret[] = $adminTemplatesPages->renderFile($moduleDirname.'_admin_'.$tableName.'.tpl');
            }
            // Creation of blocks
            if (in_array(1, $tableBlocks)) {
                // Blocks Files
                $blocksFiles = BlocksFiles::getInstance();
                $blocksFiles->write($module, $table);
                $ret[] = $blocksFiles->renderFile($tableName.'.php');
                // Templates Blocks Files
                $templatesBlocks = TemplatesBlocks::getInstance();
                $templatesBlocks->write($module, $table);
                $ret[] = $templatesBlocks->renderFile($moduleDirname.'_block_'.$tableName.'.tpl');
            }
            // Creation of classes
            if (in_array(1, $tableAdmin) || in_array(1, $tableUser)) {
                // Class Files
                $classFiles = ClassFiles::getInstance();
                $classFiles->write($module, $table, $tables, $tableName.'.php');
                $ret[] = $classFiles->render();
            }
            // Creation of user files
            if (in_array(1, $tableUser)) {
                // User Pages File
                $userPages = UserPages::getInstance();
                $userPages->write($module, $table, $tableName.'.php');
                $ret[] = $userPages->renderFile();
                if (in_array(0, $tableCategory)) {
                    // User Templates File
                    $userTemplatesPages = TemplatesUserPages::getInstance();
                    $userTemplatesPages->write($module, $table);
                    $ret[] = $userTemplatesPages->renderFile($moduleDirname.'_'.$tableName.'.tpl');
                    // User List Templates File
                    $userTemplatesPagesList = TemplatesUserPagesList::getInstance();
                    $userTemplatesPagesList->write($module, $table, $tables, $moduleDirname.'_'.$tableName.'_list'.'.tpl');
                    $ret[] = $userTemplatesPagesList->renderFile();
                }
                if (in_array(1, $tableCategory)) {
                    // User List Templates File
                    $userTemplatesCategories = TemplatesUserCategories::getInstance();
                    $userTemplatesCategories->write($module, $table);
                    $ret[] = $userTemplatesCategories->renderFile($moduleDirname.'_'.$tableName.'.tpl');
                    // User List Templates File
                    $userTemplatesCategoriesList = TemplatesUserCategoriesList::getInstance();
                    $userTemplatesCategoriesList->write($module, $table);
                    $ret[] = $userTemplatesCategoriesList->renderFile($moduleDirname.'_'.$tableName.'_list'.'.tpl');
                }
            }
        }
        foreach (array_keys($files) as $t) {
            $fileName = $files[$t]->getVar('file_name');
            $fileExtension = $files[$t]->getVar('file_extension');
            $fileInfolder = $files[$t]->getVar('file_infolder');
            // More File
            $moreFiles = TDMCreateMoreFiles::getInstance();
            $moreFiles->write($module, $fileName, $fileInfolder, $fileExtension);
            $ret[] = $moreFiles->render();
        }
        // Language Modinfo File
        $languageModinfo = LanguageModinfo::getInstance();
        $languageModinfo->write($module, $table, $tables, 'modinfo.php');
        $ret[] = $languageModinfo->render();
        if (1 == $module->getVar('mod_admin')) {
            // Admin Header File
            $adminHeader = AdminHeader::getInstance();
            $adminHeader->write($module, $table, $tables, 'header.php');
            $ret[] = $adminHeader->render();
            // Admin Index File
            $adminIndex = AdminIndex::getInstance();
            $adminIndex->write($module, $tables, 'index.php');
            $ret[] = $adminIndex->render();
            // Admin Menu File
            $adminMenu = AdminMenu::getInstance();
            $adminMenu->write($module, $tables, 'menu.php');
            $ret[] = $adminMenu->render();
            // Admin About File
            $adminAbout = AdminAbout::getInstance();
            $adminAbout->write($module, 'about.php');
            $ret[] = $adminAbout->render();
            // Admin Footer File
            $adminFooter = AdminFooter::getInstance();
            $adminFooter->write($module, 'footer.php');
            $ret[] = $adminFooter->render();
            // Templates Admin About File
            $adminTemplatesAbout = TemplatesAdminAbout::getInstance();
            $adminTemplatesAbout->write($module, $moduleDirname.'_admin_about.tpl');
            $ret[] = $adminTemplatesAbout->render();
            // Templates Admin Index File
            $adminTemplatesIndex = TemplatesAdminIndex::getInstance();
            $adminTemplatesIndex->write($module, $moduleDirname.'_admin_index.tpl');
            $ret[] = $adminTemplatesIndex->render();
            // Templates Admin Footer File
            $adminTemplatesFooter = TemplatesAdminFooter::getInstance();
            $adminTemplatesFooter->write($module, $moduleDirname.'_admin_footer.tpl');
            $ret[] = $adminTemplatesFooter->render();
            // Templates Admin Header File
            $adminTemplatesHeader = TemplatesAdminHeader::getInstance();
            $adminTemplatesHeader->write($module, $moduleDirname.'_admin_header.tpl');
            $ret[] = $adminTemplatesHeader->render();
            // Language Admin File
            $languageAdmin = LanguageAdmin::getInstance();
            $languageAdmin->write($module, $table, $tables, 'admin.php');
            $ret[] = $languageAdmin->render();
        }
        // Class Helper File
        $classHelper = ClassHelper::getInstance();
        $classHelper->write($module, 'helper.php');
        $ret[] = $classHelper->render();
        // Include Functions File
        $includeFunctions = IncludeFunctions::getInstance();
        $includeFunctions->write($module, 'functions.php');
        $ret[] = $includeFunctions->render();
        // Creation of blocks language file
        if ($table->getVar('table_name') != null) {
            // Include Install File
            $includeInstall = IncludeInstall::getInstance();
            $includeInstall->write($module, $table, $tables, 'install.php');
            $ret[] = $includeInstall->render();
            if (in_array(1, $tableBlocks)) {
                // Language Blocks File
                $languageBlocks = LanguageBlocks::getInstance();
                $languageBlocks->write($module, $tables, 'blocks.php');
                $ret[] = $languageBlocks->render();
            }
            // Creation of admin permission files
            if (in_array(1, $tablePermissions)) {
                // Admin Permissions File
                $adminPermissions = AdminPermissions::getInstance();
                $adminPermissions->write($module, $tables, 'permissions.php');
                $ret[] = $adminPermissions->render();
                // Templates Admin Permissions File
                $adminTemplatesPermissions = TemplatesAdminPermissions::getInstance();
                $adminTemplatesPermissions->write($module, $moduleDirname.'_admin_permissions.tpl');
                $ret[] = $adminTemplatesPermissions->render();
            }
            // Creation of notifications files
            if (in_array(1, $tableNotifications)) {
                // Include Notifications File
                $includeNotifications = IncludeNotifications::getInstance();
                $includeNotifications->write($module, $table, 'notifications.inc.php');
                $ret[] = $includeNotifications->render();
                // Language Mail Template Category File
                $languageMailTpl = LanguageMailTpl::getInstance();
                $languageMailTpl->write($module, 'category_new_notify.tpl');
                $ret[] = $languageMailTpl->render();
            }
            // Creation of sql file
            if ($table->getVar('table_name') != null) {
                // Sql File
                $sqlFile = SqlFile::getInstance();
                $sqlFile->write($module, $tables, 'mysql.sql');
                $ret[] = $sqlFile->render();
                // Include Update File
                $includeUpdate = IncludeUpdate::getInstance();
                $includeUpdate->write($module, 'update.php');
                $ret[] = $includeUpdate->renderFile();
            }
            // Creation of search file
            if (in_array(1, $tableSearch)) {
                // Include Search File
                $includeSearch = IncludeSearch::getInstance();
                $includeSearch->write($module, $table, 'search.inc.php');
                $ret[] = $includeSearch->render();
            }
            // Creation of comments files
            if (in_array(1, $tableComments)) {
                // Include Comments File
                $includeComments = IncludeComments::getInstance();
                $includeComments->write($module, $table);
                $ret[] = $includeComments->renderCommentsIncludes($module, 'comment_edit');
                // Include Comments File
                $includeComments = IncludeComments::getInstance();
                $includeComments->write($module, $table);
                $ret[] = $includeComments->renderCommentsIncludes($module, 'comment_delete');
                // Include Comments File
                $includeComments = IncludeComments::getInstance();
                $includeComments->write($module, $table);
                $ret[] = $includeComments->renderCommentsIncludes($module, 'comment_post');
                // Include Comments File
                $includeComments = IncludeComments::getInstance();
                $includeComments->write($module, $table);
                $ret[] = $includeComments->renderCommentsIncludes($module, 'comment_reply');
                // Include Comments File
                $includeComments = IncludeComments::getInstance();
                $includeComments->write($module, $table);
                $ret[] = $includeComments->renderCommentsNew($module, 'comment_new');
                // Include Comment Functions File
                $includeCommentFunctions = IncludeCommentFunctions::getInstance();
                $includeCommentFunctions->write($module, $table, 'comment_functions.php');
                $ret[] = $includeCommentFunctions->renderFile();
            }
        }
        // Creation of admin files
        if (1 == $module->getVar('mod_admin')) {
            // Templates Index File
            $userTemplatesIndex = TemplatesUserIndex::getInstance();
            $userTemplatesIndex->write($module, $table, $tables, $moduleDirname.'_index.tpl');
            $ret[] = $userTemplatesIndex->render();
            // Templates Footer File
            $userTemplatesFooter = TemplatesUserFooter::getInstance();
            $userTemplatesFooter->write($module, $table, $moduleDirname.'_footer.tpl');
            $ret[] = $userTemplatesFooter->render();
            // Templates Header File
            $userTemplatesHeader = TemplatesUserHeader::getInstance();
            $userTemplatesHeader->write($module, $moduleDirname.'_header.tpl');
            $ret[] = $userTemplatesHeader->render();
        }
        // Creation of user files
        if ((1 == $module->getVar('mod_user')) && in_array(1, $tableUser)) {
            // User Footer File
            $userFooter = UserFooter::getInstance();
            $userFooter->write($module, 'footer.php');
            $ret[] = $userFooter->render();
            // User Header File
            $userHeader = UserHeader::getInstance();
            $userHeader->write($module, $table, $tables, 'header.php');
            $ret[] = $userHeader->render();
            // User Notification Update File
            if ((1 == $module->getVar('mod_notifications')) && in_array(1, $tableNotifications)) {
                $userNotificationUpdate = UserNotificationUpdate::getInstance();
                $userNotificationUpdate->write($module, 'notification_update.php');
                $ret[] = $userNotificationUpdate->render();
            }
            // User Broken File
            if (in_array(1, $tableBroken)) {
                $userBroken = UserBroken::getInstance();
                $userBroken->write($module, $table, 'broken.php');
                $ret[] = $userBroken->render();
                // User Templates Broken File
                $userTemplatesBroken = TemplatesUserBroken::getInstance();
                $userTemplatesBroken->write($module, $table);
                $ret[] = $userTemplatesBroken->renderFile($moduleDirname.'_broken.tpl');
            }
            // User Pdf File
            if (in_array(1, $tablePdf)) {
                $userPdf = UserPdf::getInstance();
                $userPdf->write($module, $table, 'pdf.php');
                $ret[] = $userPdf->render();
                // User Templates Pdf File
                $userTemplatesPdf = TemplatesUserPdf::getInstance();
                $userTemplatesPdf->write($module);
                $ret[] = $userTemplatesPdf->renderFile($moduleDirname.'_pdf.tpl');
            }
            // User Print File
            if (in_array(1, $tablePrint)) {
                $userPrint = UserPrint::getInstance();
                $userPrint->write($module, $table, 'print.php');
                $ret[] = $userPrint->render();
                // User Templates Print File
                $userTemplatesPrint = TemplatesUserPrint::getInstance();
                $userTemplatesPrint->write($module, $table);
                $ret[] = $userTemplatesPrint->renderFile($moduleDirname.'_print.tpl');
            }
            // User Rate File
            if (in_array(1, $tableRate)) {
                $userRate = UserRate::getInstance();
                $userRate->write($module, $table, 'rate.php');
                $ret[] = $userRate->render();
                // User Templates Rate File
                $userTemplatesRate = TemplatesUserRate::getInstance();
                $userTemplatesRate->write($module, $table);
                $ret[] = $userTemplatesRate->renderFile($moduleDirname.'_rate.tpl');
            }
            // User Rss File
            if (in_array(1, $tableRss)) {
                $userRss = UserRss::getInstance();
                $userRss->write($module, $table, 'rss.php');
                $ret[] = $userRss->render();
                // User Templates Rss File
                $userTemplatesRss = TemplatesUserRss::getInstance();
                $userTemplatesRss->write($module);
                $ret[] = $userTemplatesRss->renderFile($moduleDirname.'_rss.tpl');
            }
            // User Single File
            if (in_array(1, $tableSingle)) {
                $userSingle = UserSingle::getInstance();
                $userSingle->write($module, $table, 'single.php');
                $ret[] = $userSingle->render();
                // User Templates Single File
                $userTemplatesSingle = TemplatesUserSingle::getInstance();
                $userTemplatesSingle->write($module, $table);
                $ret[] = $userTemplatesSingle->renderFile($moduleDirname.'_single.tpl');
            }
            // User Submit File
            if (in_array(1, $tableSubmit)) {
                $userSubmit = UserSubmit::getInstance();
                $userSubmit->write($module, $table, 'submit.php');
                $ret[] = $userSubmit->render();
                // User Templates Submit File
                $userTemplatesSubmit = TemplatesUserSubmit::getInstance();
                $userTemplatesSubmit->write($module, $table);
                $ret[] = $userTemplatesSubmit->renderFile($moduleDirname.'_submit.tpl');
            }// User Visit File
            if (in_array(1, $tableVisit)) {
                $userVisit = UserVisit::getInstance();
                $userVisit->write($module, $table, 'visit.php');
                $ret[] = $userVisit->render();
            }
            // User Tag Files
            if (in_array(1, $tableTag)) {
                $userListTag = UserListTag::getInstance();
                $userListTag->write($module, 'list.tag.php');
                $ret[] = $userListTag->render();
                $userViewTag = UserViewTag::getInstance();
                $userViewTag->write($module, 'view.tag.php');
                $ret[] = $userViewTag->render();
            }
            // User Index File
            $userIndex = UserIndex::getInstance();
            $userIndex->write($module, $table, 'index.php');
            $ret[] = $userIndex->render();
            // Language Main File
            $languageMain = LanguageMain::getInstance();
            $languageMain->write($module, $tables, 'main.php');
            $ret[] = $languageMain->render();
            // User Templates Submit File
            $userTemplatesUserBreadcrumbs = TemplatesUserBreadcrumbs::getInstance();
            $userTemplatesUserBreadcrumbs->write($module, $moduleDirname.'_breadcrumbs.tpl');
            $ret[] = $userTemplatesUserBreadcrumbs->render();
        }
        // Css Styles File
        $cssStyles = CssStyles::getInstance();
        $cssStyles->write($module, 'style.css');
        $ret[] = $cssStyles->render();
        // Include Jquery File
        $JavascriptJQuery = JavascriptJQuery::getInstance();
        $JavascriptJQuery->write($module, 'functions.js');
        $ret[] = $JavascriptJQuery->render();
        // Include Common File
        $includeCommon = IncludeCommon::getInstance();
        $includeCommon->write($module, $table, 'common.php');
        $ret[] = $includeCommon->render();
        // Docs Changelog File
        $docsChangelog = DocsChangelog::getInstance();
        $docsChangelog->write($module, 'changelog.txt');
        $ret[] = $docsChangelog->render();
        // Language Help File
        $languageHelp = LanguageHelp::getInstance();
        $languageHelp->write($module, 'help.html');
        $ret[] = $languageHelp->render();
        // User Xoops Version File
        $userXoopsVersion = UserXoopsVersion::getInstance();
        $userXoopsVersion->write($module, $table, $tables, 'xoops_version.php');
        $ret[] = $userXoopsVersion->render();

        // Return Array
        return $ret;
    }
}
