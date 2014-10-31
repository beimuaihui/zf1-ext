<?php
/**
 * beimuaihui System
 * Copyright(c) 2011-2020 beimuaihui.
 * @license    http://www.gnu.org/licenses/gpl.html     This software Under GPL V3 License 
 * beimuaihui@gmail.com
 * http://code.google.com/p/beimuaihui/
 * $Id: Html.php 487 2012-04-09 03:42:36Z beimuaihui@gmail.com $
 */
class Baogg_View_Html {
	public static function MakeActionLink($text, $href, $aAttribute = array(), $img = '', $aImgAttribute = array()) {
		if (! $text) {
			$img = addslashes ( $v );
			$sImgAttribute = '';
			foreach ( ( array ) $aImgAttribute as $k => $v ) {
				$v = addcslashes ( $v, "'\\" );
				$sImgAttribute .= " $k='{$v}' ";
			}
			$text = "<img src='{BAOGG_THEME}/images/{$img}'  {$sImgAttribute}>";
		}
		$sAttribute = '';
		foreach ( ( array ) $aAttribute as $k => $v ) {
			$v = addcslashes ( $v, "'\\" );
			$sAttribute .= " $k='{$v}' ";
		}
		return "<a href='{$href}' {$sAttribute}>$text</a>";
	}
	public static function MakeEditLink($href = '#') {
		global $multi;
		return $this->MakeActionLink ( $multi->g_edit, $href, '', 'edit.gif' );
	}
	
	public static function Tidy($html, $tidy_config='' ) {
		$config = array('clean' => false,
			'output-html'=>false,
			'show-body-only'	=> true
		);
		/*$config = array(
			'show-body-only' => false,
			'clean' => true,
			'char-encoding' => 'utf8',
			'add-xml-decl' => true,
			'add-xml-space' => true,
			'output-html' => false,
			'output-xml' => false,
			'output-xhtml' => true,
			'numeric-entities' => false,
			'ascii-chars' => false,
			'doctype' => 'strict',
			'bare' => true,
			'fix-uri' => true,
			'indent' => true,
			'indent-spaces' => 4,
			'tab-size' => 4,
			'wrap-attributes' => true,
			'wrap' => 0,
			'indent-attributes' => true,
			'join-classes' => false,
			'join-styles' => false,
			'enclose-block-text' => true,
			'fix-bad-comments' => true,
			'fix-backslash' => true,
			'replace-color' => false,
			'wrap-asp' => false,
			'wrap-jste' => false,
			'wrap-php' => false,
			'write-back' => true,
			'drop-proprietary-attributes' => false,
			'hide-comments' => false,
			'hide-endtags' => false,
			'literal-attributes' => false,
			'drop-empty-paras' => true,
			'enclose-text' => true,
			'quote-ampersand' => true,
			'quote-marks' => false,
			'quote-nbsp' => true,
			'vertical-space' => true,
			'wrap-script-literals' => false,
			'tidy-mark' => true,
			'merge-divs' => false,
			'repeated-attributes' => 'keep-last',
			'break-before-br' => true,
		);*/
	   
		if( $tidy_config == '' ) {
			$tidy_config = $config;
		}
	   
		$tidy = new tidy;
		$tidy->parseString($html, $tidy_config, 'utf8');
		$tidy->cleanRepair();
		//remove body tag
		return trim($tidy->value);
	}
}
?>