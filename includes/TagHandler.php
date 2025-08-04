<?php
namespace HTMLets;

use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOutput;
use Html;

class TagHandler {
	/**
	 * Render the <htmlet> tag.
	 *
	 * @param string $input
	 * @param array $args
	 * @param Parser $parser
	 * @return string
	 */
	public static function render( $input, $args, Parser $parser ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'main' );
		$dir = $config->get( 'HTMLetsDirectory' );
		if ( !$dir ) {
			$dir = MediaWikiServices::getInstance()->getMainConfig()->get( 'IP' ) . '/htmlets';
		}

		$name = trim( $input );
		// Sanitize file name: remove path separators and leading dots
		$name = preg_replace( '@[\\\\/!]|^\.+@', '', $name );
		$name .= '.html';

		$filePath = $dir . '/' . $name;

		// Handle nocache
		if ( isset( $args['nocache'] ) ) {
			$parser->getOutput()->updateCacheExpiry( 0 );
		}

		// Determine hack mode
		$hack = isset( $args['hack'] ) ? $args['hack'] : 'bypass';
		if ( $hack === 'strip' ) {
			$hack = 'strip';
		} elseif ( $hack === 'bypass' ) {
			$hack = 'bypass';
		} elseif ( $hack === 'none' || $hack === 'no' ) {
			$hack = 'none';
		} else {
			$hack = 'bypass';
		}

		// Try to load file
		if ( !preg_match( '/^\w+:\/\//', $dir ) && !file_exists( $filePath ) ) {
			return Html::rawElement(
				'div',
				[ 'class' => 'error' ],
				wfMessage( 'htmlets-filenotfound', $name )->inContentLanguage()->escaped()
			);
		}

		$output = @file_get_contents( $filePath );
		if ( $output === false ) {
			return Html::rawElement(
				'div',
				[ 'class' => 'error' ],
				wfMessage( 'htmlets-loadfailed', $name )->inContentLanguage()->escaped()
			);
		}

		// Apply hack modes
		if ( $hack === 'strip' ) {
			$output = trim( preg_replace( '![\r\n\t ]+!', ' ', $output ) ); // normalize whitespace
			$output = preg_replace( '!(.) *:!', '$1:', $output ); // strip blanks before colons

			if ( strlen( $output ) > 0 ) {
				$ch = substr( $output, 0, 1 );
				if ( $ch === '#' ) {
					$output = '&#35;' . substr( $output, 1 );
				} elseif ( $ch === '*' ) {
					$output = '&#42;' . substr( $output, 1 );
				} elseif ( $ch === ':' ) {
					$output = '&#58;' . substr( $output, 1 );
				} elseif ( $ch === ';' ) {
					$output = '&#59;' . substr( $output, 1 );
				}
			}
		} elseif ( $hack === 'bypass' ) {
			// Output marker for ParserAfterTidy hook to process
			$output = '<!-- @HTMLetsHACK@ ' . base64_encode( $output ) . ' @HTMLetsHACK@ -->';
		}

		return $output;
	}
}