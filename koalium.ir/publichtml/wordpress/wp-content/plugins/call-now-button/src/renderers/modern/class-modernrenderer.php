<?php

namespace cnb\renderer;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ModernRenderer extends Renderer {

    /**
     * Enqueues the style required for the modern renderer
     *
     * @return void
     */
    private function enqueue_frontend_style() {
        wp_register_style(
            CNB_SLUG . '-modern-style',
            plugins_url('resources/style/modern.css', CNB_PLUGINS_URL_BASE ),
            array(),
            CNB_VERSION
        );
    }

    /**
     * Note this function escapes both inputs
     *
     * @param $icon
     *
     * @return string
     */
    private function svg( $icon ) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M27.01355,23.48859l-1.753,1.75305a5.001,5.001,0,0,1-5.19928,1.18243c-1.97193-.69372-4.87335-2.36438-8.43848-5.9295S6.387,14.028,5.6933,12.05615A5.00078,5.00078,0,0,1,6.87573,6.85687L8.62878,5.10376a1,1,0,0,1,1.41431.00012l2.828,2.8288A1,1,0,0,1,12.871,9.3468L11.0647,11.153a1.0038,1.0038,0,0,0-.0821,1.32171,40.74278,40.74278,0,0,0,4.07624,4.58374,40.74143,40.74143,0,0,0,4.58374,4.07623,1.00379,1.00379,0,0,0,1.32171-.08209l1.80622-1.80627a1,1,0,0,1,1.41412-.00012l2.8288,2.828A1.00007,1.00007,0,0,1,27.01355,23.48859Z" fill="' . esc_attr( $icon ) . '"/></svg>';

        return base64_encode( $svg );
    }

    private function render_comment() {
        printf( '<!-- Call Now Button %1$s (https://callnowbutton.com) [renderer:modern]-->%2$s',
            esc_attr( CNB_VERSION ),
            "\n" );
    }

    public function cnb_footer() {
        $this->render_comment();

        if ($this->should_render()) {
            // phpcs:ignore WordPress.Security
            echo $this->get_button_link();
        }
    }

    /**
     * @return bool return true if the button should be rendered, false otherwise
     */
    function should_render() {
        $cnb_options = get_option( 'cnb' );

        // if we cannot even find this option, we skip the header completely
        if (!is_array($cnb_options)) {
            return false;
        }

        $cnb_hide_frontpage      = isset( $cnb_options['frontpage'] ) && $cnb_options['frontpage'] == 1;
        $cnb_show_limited        = isset( $cnb_options['show'] ) && $cnb_options['show'] != '';
        /** @noinspection PhpTernaryExpressionCanBeReplacedWithConditionInspection */
        $cnb_show_included = isset($cnb_options['limit']) ? $cnb_options['limit'] === 'include' : true;

        $is_page_for_posts = get_option( 'page_for_posts' ) == get_queried_object_id() && get_option( 'page_for_posts' ) > 0 && get_queried_object_id() > 0;

        if ( $cnb_show_limited ) {
            $cnb_show_ids = explode( ',', str_replace( ' ', '', $cnb_options['show'] ) );
        }

        if ( is_front_page() ) {
            if ( ! $cnb_hide_frontpage ) {
                return 1;
            }
        } elseif ( $cnb_show_limited ) {
            // Is the current page the "posts overview" page
            if ( $cnb_show_included ) {
                if ( is_single( $cnb_show_ids ) || is_page( $cnb_show_ids ) || $is_page_for_posts ) {
                    return 2;
                }
            } elseif ( ! is_single( $cnb_show_ids ) && ! is_page( $cnb_show_ids ) && ! $is_page_for_posts ) {
                    return 3;
            }
        } else {
            return 4;
        }
        return false;
    }
    /**
     * Returns the <a.. > element that makes up the Modern Call Now Button.
     *
     * Note that all values are pre-escaped, so it is safe to echo without escaping.
     *
     * @return string|null null in case there is no Button to output
     */
    function get_button_link() {
        $cnb_options = get_option( 'cnb' );

        // if we cannot even find this option, we skip the header completely
        if (!is_array($cnb_options)) {
            return null;
        }

        $cnb_is_full_width = $cnb_options['appearance'] == 'full' || $cnb_options['appearance'] == 'tfull';
        $cnb_has_text = isset( $cnb_options['text'] ) && $cnb_options['text'] !== '';

        $cnb_tracking_id         = isset($cnb_options['tracking']) ? (int) $cnb_options['tracking'] : 0;
        $cnb_conversion_id       = isset($cnb_options['conversions']) ? (int) $cnb_options['conversions'] : 0;
        $cnb_tracking_id         = ( $cnb_tracking_id >= 0 && $cnb_tracking_id <= 3 ) ? $cnb_tracking_id : 0;
        $cnb_conversion_id       = ( $cnb_conversion_id >= 0 && $cnb_conversion_id <= 2 ) ? $cnb_conversion_id : 0;
        $cnb_click_tracking      = $cnb_tracking_id > 0;
        $cnb_conversion_tracking = $cnb_conversion_id > 0;
        $cnb_number              = isset( $cnb_options['number'] ) ? $cnb_options['number'] : null;

        $cnb_classnames        = $this->get_class_names();

        // These all return pre-escaped results
        $cnb_button_background = $this->get_background_css();
        $tracking_code         = $this->get_google_tracking_code( $cnb_click_tracking, $cnb_tracking_id );
        $conversion_code       = $this->get_google_conversion_code( $cnb_conversion_tracking, $cnb_conversion_id );
        $cnb_button_text       = $this->create_button_text();

        $cnb_aria_label = $cnb_has_text ? '' : 'aria-label="Call Now Button"';

	    $cnb_call_link = '<a ' . $cnb_aria_label;
        $cnb_call_link .= ' href="tel:' . esc_attr( $cnb_number ) . '"';
        $cnb_call_link .= ' id="callnowbutton"';
        $cnb_call_link .= ' class="' . esc_attr( implode(' ', $cnb_classnames ) ) . '"';

        $cnb_call_link .= ' style="';
        $cnb_call_link .= $cnb_button_background;
        $cnb_call_link .= '"';

        $cnb_call_link .= $cnb_click_tracking || $cnb_conversion_tracking ? " onclick='" . $tracking_code . $conversion_code . "'" : '';
        $cnb_call_link .= '>';
        $cnb_call_link .= $cnb_button_text;
        $cnb_call_link .= '</a>';

        return $cnb_call_link;
    }

    public function cnb_head() {
        $cnb_options = get_option( 'cnb' );

        // if we cannot even find this option, we skip the header completely
        if (!is_array($cnb_options) || !key_exists('appearance', $cnb_options)) {
            return;
        }

        $appearance = $cnb_options['appearance'];
        if ( 'full' === $appearance || 'tfull' === $appearance ) {
            switch ( $appearance ) {
                case 'tfull':
                    echo '<style>@media screen and (max-width: 650px) {body {padding-top:60px;}}</style>';
                    break;
                case 'full':
                default:
                    echo '<style>@media screen and (max-width: 650px) {body {padding-bottom:60px;}}</style>';
                    break;
            }
        }

        wp_enqueue_style( CNB_SLUG . '-modern-style' );
    }

    function register() {
        $this->enqueue_frontend_style();

        // We add priority 7 to ensure that we are loaded before wp_head renders the CSS (which is at priority >= 8)
        add_action( 'wp_head', array( $this, 'cnb_head' ), 7 );

        add_action( 'wp_footer', array( $this, 'cnb_footer' ) );
    }

    /**
     * Decide on the background settings
     *
     * Get (pre escaped) CSS for the background
     *
     * @return string
     */
    private function get_background_css() {
        $cnb_options = get_option( 'cnb' );
        if ( 'full' === $cnb_options['appearance'] || 'tfull' === $cnb_options['appearance'] ) {
            return sprintf( 'background-color:%1$s;',
                esc_attr( $cnb_options['color'] )
            );
        } else {
            $svg = $this->svg(
                $cnb_options['iconcolor'] );

            return
                sprintf( 'background-image:url(data:image/svg+xml;base64,%1$s); background-color:%2$s;',
                    esc_attr( $svg ),
                    esc_attr( $cnb_options['color'] )
                );
        }
    }

    /**
     * @param $cnb_click_tracking
     * @param $cnb_tracking_id
     *
     * @return string
     */
    private function get_google_tracking_code( $cnb_click_tracking, $cnb_tracking_id ) {
        $cnb_options = get_option( 'cnb' );

        if ( $cnb_click_tracking ) {
            switch ( $cnb_tracking_id ) {
                case 1:
                    return '_gaq.push(["_trackEvent", "Contact", "Call Now Button", "Phone"]);';
                case 2:
                    return 'ga("send", "event", "Contact", "Call Now Button", "Phone");';
                case 3:
                    $action_value = isset($cnb_options['number']) ? $cnb_options['number'] : null;
                    $action_label = isset($cnb_options['text']) ? $cnb_options['text'] : null;
                    $gtag_props = wp_json_encode( array(
                        'event_category' => 'contact',
                        'event_label'    => 'phone',
                        'category'       => 'Call Now Button',
                        'action_type'    => 'PHONE',
                        'button_type'    => 'Single',
                        'action_value'   => $action_value,
                        'action_label'   => $action_label,
                        'cnb_version'    => CNB_VERSION,
                    ) );

                    return 'gtag("event", "Call Now Button", ' . $gtag_props . ');';
            }
        }

        return '';
    }

    private function get_google_conversion_code( $cnb_conversion_tracking, $cnb_conversion_id ) {
        $cnb_options = get_option( 'cnb' );

        if ( $cnb_conversion_tracking ) {
            switch ( $cnb_conversion_id ) {
                case 1:
                    return 'return gtag_report_conversion("tel:' . esc_js( $cnb_options['number'] ) . '");';
                case 2:
                    return 'goog_report_conversion("tel:' . esc_js( $cnb_options['number'] ) . '");';
            }
        }

        return '';
    }

    private function create_button_text() {
        $cnb_options = get_option( 'cnb' );

	    $cnb_has_text = isset( $cnb_options['text'] ) && $cnb_options['text'] !== '';
        $cnb_is_full_width = $cnb_options['appearance'] == 'full' || $cnb_options['appearance'] == 'tfull';
        $cnb_hide_icon     = isset( $cnb_options['hideIcon'] ) && $cnb_options['hideIcon'] == 1;

        // Full Width options
        if ( $cnb_is_full_width ) {
            $svg = $this->svg( $cnb_options['iconcolor'] );

            $result = '';

            if ( ! $cnb_hide_icon ) {
                $altAttr = $cnb_has_text ? 'alt=""' : 'alt="Call Now Button"';
                $result .= sprintf( '<img ' . $altAttr . ' src="data:image/svg+xml;base64,%1$s" width="40">',
                    esc_attr( $svg ) );
            }
            
            if ( $cnb_has_text ) {
                $result .= sprintf( '<span style="color:%1$s">%2$s</span>',
                    esc_attr( $cnb_options['iconcolor'] ),
                    esc_html( $cnb_options['text'] )
                );
            }

            return $result;
        }

        // Single options
        if ( ! $cnb_has_text ) {
            return '<span>Call Now Button</span>';
        }

        return sprintf( '<span>%1$s</span>',
            esc_html( $cnb_options['text'] )
        );
    }

    /**
     * Get the zoom level for the Modern button.
     *
     * The internal values are 0.7 - 1.3 (it defaults to 1.0)
     *
     * @return int 70 - 130, defaults to 100
     */
    private function get_zoom() {
        $cnb_options    = get_option( 'cnb' );
        $cnb_zoom                = isset( $cnb_options['zoom'] ) ? (float) $cnb_options['zoom'] : 1.0;
        return (int) ($cnb_zoom * 100);
    }

    /**
     * Get the z-index meta number for the Modern button.
     *
     * @return int 1-10, defaults to 10
     */
    private function get_zindex() {
        $cnb_options    = get_option( 'cnb' );
        return isset( $cnb_options['z-index'] ) ? (int) $cnb_options['z-index'] : 10;
    }

    /**
     * Return all the various classes needed to style the legacy Call Now Button.
     *
     * @return string[]
     */
    private function get_class_names() {
        $cnb_options    = get_option( 'cnb' );
        $cnb_classnames = array( 'call-now-button' );

        // Button zoom
        $cnb_classnames[] = ' cnb-zoom-' . esc_attr( $this->get_zoom() );

        // Button z-index
        $cnb_classnames[] = ' cnb-zindex-' . esc_attr( $this->get_zindex() );

        // Button has text?
	    if ( isset( $cnb_options['text'] ) && $cnb_options['text'] !== '' ) {
            $cnb_classnames[] = ' cnb-text';
        }

        // Button type & position
        if ( $cnb_options['appearance'] === 'full' ) {
            $cnb_classnames[] = ' cnb-full cnb-full-bottom';
        } elseif ( $cnb_options['appearance'] === 'tfull' ) {
            $cnb_classnames[] = ' cnb-full cnb-full-top';
        } else {
            $cnb_classnames[] = ' cnb-single cnb-' . $cnb_options['appearance'];
        }

        $displayMode         = isset( $cnb_options['displaymode'] ) ? $cnb_options['displaymode'] : 'MOBILE_ONLY';
        switch ($displayMode) {
            case 'MOBILE_ONLY':
                array_push($cnb_classnames, 'cnb-displaymode', 'cnb-displaymode-mobile-only');
                break;
            case 'DESKTOP_ONLY':
                array_push($cnb_classnames, 'cnb-displaymode', 'cnb-displaymode-desktop-only');
                break;
            case 'ALWAYS':
                array_push($cnb_classnames, 'cnb-displaymode', 'cnb-displaymode-always');
                break;
        }

        return $cnb_classnames;
    }
}
