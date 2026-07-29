<?php $ThemeTree = new ThemeTree;
$ThemeTree->folderpath = str_replace('/*','',$ThemeTree->folderpath);
$PathSaved = $ThemeTree->folderpath."shortcodes/codes/*.*";
$packsDB = glob($PathSaved);
foreach ($packsDB as $pack) {
	require($pack);
}