<?php

$l = OC_L10N::get('files');

OCP\App::registerAdmin('files', 'admin');

OCP\App::addNavigationEntry(array("id" => "files_index",
	"order" => 0,
	"href" => OCP\Util::linkTo("files", "index.php"),
	"icon" => OCP\Util::imagePath("core", "places/files.svg"),
	"name" => $l->t("Files")));

\OC::$server->getSearch()->registerProvider('OC\Search\Provider\File');

$templateManager = OC_Helper::getFileTemplateManager();
$templateManager->registerTemplate('text/html', 'core/templates/filetemplates/template.html');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.presentation', 'core/templates/filetemplates/template.odp');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.text', 'core/templates/filetemplates/template.odt');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.spreadsheet', 'core/templates/filetemplates/template.ods');

\OCA\Files\App::getNavigationManager()->add(
	array(
		"id" => 'files',
		"appname" => 'files',
		"script" => 'list.php',
		"order" => 0,
		"name" => $l->t('All files')
	)
);

if (\OCP\App::isEnabled('files_encryption')) {
	// Use two hooks to allow encryption to initialize correctly before copying
	// 1. when the files folder is created remember to copy the skeleton
	\OCP\Util::connectHook('OC_Filesystem', 'createUserFiles', '\OC_Util', 'setCopySkeletonFlag');

	// 2. when setup is complete trigger the actual copying.
	\OCP\Util::connectHook('OCA\Encryption\Hooks', 'initialized', '\OC_Util', 'copySkeleton');
} else {
	\OCP\Util::connectHook('OC_Filesystem', 'createUserFiles', '\OC_Util', 'copySkeleton');
}