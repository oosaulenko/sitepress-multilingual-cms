<?php

use WPML\FP\Fns;
use WPML\FP\Json;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Str;
use function WPML\FP\pipe;

/**
 * This code is inspired by WPML Widgets (https://wordpress.org/plugins/wpml-widgets/),
 * created by Jeroen Sormani
 *
 * @author OnTheGo Systems
 */
class WPML_Widgets_Support_Frontend implements IWPML_Action {
	private $current_language;

    const TERM_REGEX = '#\{.*?\}#s';

	/**
	 * WPML_Widgets constructor.
	 *
	 * @param string $current_language
	 */
	public function __construct( $current_language ) {
		$this->current_language = $current_language;
	}

	public function add_hooks() {
		add_filter( 'widget_display_callback', array( $this, 'display' ), - PHP_INT_MAX, 1 );
	}

	/**
	 * Get display status of the widget.
	 *
	 * @param array|bool $instance
	 *
	 * @return array|bool
	 */
	public function display( $instance ) {
		if ( ! $instance || $this->it_must_display( $instance ) ) {
			return $instance;
		}

		return false;
	}

	/**
	 * Returns display status of the widget as boolean.
	 *
	 * @param array $instance
	 *
	 * @return bool
	 */
	private function it_must_display( $instance ) {
        if ( isset ( $instance['content'] ) ) {
            $matches  = Str::matchAll( self::TERM_REGEX, $instance['content'] );
            $matches  = Fns::map( pipe( Lst::nth( 0 ), Json::toArray() ), $matches );
            if ( count( $matches ) > 0 ) {
                $instance = array_merge(...$matches);
            }
        }

        return Lst::includes( Obj::propOr( null, 'wpml_language', $instance ), [ null, $this->current_language, 'all' ] );
	}
}
