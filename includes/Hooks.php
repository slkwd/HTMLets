<?php
namespace HTMLets;

use MediaWiki\Parser\Parser;

class Hooks {
	/**
	 * Register the <htmlet> tag with the parser.
	 *
	 * @param Parser $parser
	 * @return bool
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'htmlet', [ TagHandler::class, 'render' ] );
		return true;
	}
	/**
	 * Post-process the output to decode HTMLets bypass markers.
	 *
	 * @param Parser $parser
	 * @param string &$text
	 * @return bool
	 */
	public static function onParserAfterTidy( $parser, &$text ) {
		$text = preg_replace_callback(
			'/<!-- @HTMLetsHACK@ ([0-9a-zA-Z\\+\\/]+=*) @HTMLetsHACK@ -->/sm',
			function ( $m ) {
				return base64_decode( $m[1] );
			},
			$text
		);
		return true;
	}
}