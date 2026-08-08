<?php
/**
 * Fallback template — عادةً لا يُستدعى لأن ThemeStatic::Locate()
 * يعترض الطلب على template_redirect ثم die().
 * إن وصل التنفيذ هنا، نعرض قالب الرئيسية عبر Blade.
 */
global $ThemeStatic;
if ( $ThemeStatic instanceof ThemeStatic ) {
	$ThemeStatic->Blade( 'index' );
} else {
	( new ThemeStatic() )->Blade( 'index' );
}
