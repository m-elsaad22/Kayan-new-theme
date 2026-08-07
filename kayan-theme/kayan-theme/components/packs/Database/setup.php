<?php $ThemeTree = new ThemeTree;
$ThemeTree->folderpath = str_replace('/*','',$ThemeTree->folderpath);
$PathSaved = $ThemeTree->folderpath."Database/DB/*.*";
$packsDB = glob($PathSaved);
foreach ($packsDB as $pack) {
	require($pack);
}