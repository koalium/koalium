<?php

namespace cnb;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\RemoteTrace;
use cnb\admin\api\RemoteTracer;
use cnb\admin\settings\CnbSettingsController;
use cnb\utils\CnbUtils;

class CnbFooter {

    /**
     * @var CnbUtils
     */
    private $utils;

    public function __construct() {
        $this->utils = new CnbUtils();
    }

    public function render() {
	    if (apply_filters('cnb_show_footer', true)) {
		    $this->render_footer_content();
	    }
        echo '</div>'; // This is started in CnbHeader::renderHeader
    }

	private function render_footer_content() {
		if (apply_filters('cnb_show_feedback_collection', true)) {
			$this->cnb_show_feedback_collection();
		}

		if (apply_filters('cnb_show_api_traces', true)) {
			$this->cnb_show_api_traces();
		}

		if (apply_filters('cnb_show_usage_details', true)) {
			$this->add_usage_details();
		}

		if (apply_filters('cnb_show_user_info', true)) {
			$this->print_user_info();
		}
	}

    private function cnb_show_feedback_collection() {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils   = new CnbUtils();

        $url          = admin_url( 'admin.php' );
        $upgrade_link =
            add_query_arg(
                array( 'page' => 'call-now-button-upgrade' ),
                $url );

        ?>
        <div class="feedback-collection">
            <div class="cnb-clear"></div>
            <p class="cnb-url cnb-center">
                <a href="<?php echo esc_url( $cnb_utils->get_website_url( '', 'footer-links', 'branding' ) ) ?>"
                    target="_blank">
                    <?php if ( $cnb_utils->isCloudActive( $cnb_options ) ) {
                        echo esc_html(CNB_CLOUD_NAME);
                        echo '<span>(' . esc_html(CNB_NAME) . ' ⚡ NowButtons.com)</span>';
                    } else {
                        echo esc_html(CNB_NAME);
                    } ?>
                </a></p>
            <p class="cnb-center"><?php echo esc_html(CNB_NAME) ?> version <?php echo esc_attr( CNB_VERSION ) ?>
            <p class="cnb-center cnb-spacing">
                <a href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress/', 'footer-links', 'support' ) ) ?>"
                    target="_blank"
                    title="Support">Support</a> &middot;
                <a href="<?php echo esc_url( $cnb_utils->get_support_url( 'feature-request/', 'footer-links', 'suggestions' ) ) ?>"
                    target="_blank" title="Feature Requests">Suggestions</a>
                <?php if ( ! $cnb_utils->isCloudActive( $cnb_options ) ) { ?>
                    &middot; <strong><a href="<?php echo esc_url( $upgrade_link ) ?>"
                                        title="Unlock features"><?php echo esc_html(CNB_CLOUD_NAME); ?></a></strong>
                <?php } ?>
            </p>
        </div>
        <?php
    }

    /**
     * Error reporting is optional and disabled by default.
     *
     * It needs to be enabled via Settings in order to take effect.
     *
     * This adds some context data for the Error reporting integration to use to collect context
     * in case of an error.
     *
     * @return void
     */
    private function add_usage_details() {
        global $wp_version;
        if ($this->utils->is_reporting_enabled()) {
            printf('<template
                                    id="cnb-data"
                                    data-wordpress-version="%1$s"
                                    data-wordpress-environment="%2$s"
                                    data-plugin-version="%3$s"
                                    "></template>',
                esc_attr($wp_version),
                esc_attr(WP_DEBUG ? 'development' : 'production'),
                esc_attr(CNB_VERSION)
            );
        }
    }

    public function is_show_traces() {
        $cnb_options = get_option( 'cnb' );
        return ! wp_doing_ajax()
               // phpcs:ignore WordPress.Security
                && empty( $_POST )
                && isset( $cnb_options['footer_show_traces'] ) && $cnb_options['footer_show_traces'] == 1
                && CnbSettingsController::is_advanced_view()
            ;
    }

    private function cnb_show_api_traces() {
        if ( $this->is_show_traces() ) {
            $cnb_remoted_traces = RemoteTracer::getInstance();
            $traces = $cnb_remoted_traces->getTraces();
            if ( $traces ) {
                $this->print_traces($traces);
            }
        }
    }

    /**
     * @param $traces RemoteTrace[]
     *
     * @return void
     */
    public function print_traces($traces) {
        if (!$traces || count($traces) === 0) {
            return;
        }

        echo '<p>';
        echo '<strong>' . count( $traces ) . '</strong> remote call' . (count( $traces ) !== 1 ? 's' : '') . ' executed';
        $totaltime = 0.0;
        foreach ( $traces as $trace ) {
            $totaltime += (float) $trace->getTime();
        }
        echo ' in <strong>' . esc_html( $totaltime ) . '</strong>sec:<br />';

        echo '<ul>';
        foreach ( $traces as $trace ) {
            $this->print_trace($trace);
        }
        echo '</ul>';

        echo '</p>';
    }

    private function print_trace($trace) {
        echo '<li>';
        echo '<code>' . esc_html( $trace->getEndpoint() ) . '</code> in <strong>' . esc_html( $trace->getTime() ) . '</strong>sec';
        if ( $trace->isCacheHit() ) {
            echo ' (from cache)';
        }
        echo '.</li>';
    }

    private function print_user_info() {
        global $cnb_user, $cnb_domain;

        if ($this->is_show_traces()) {
            echo '<ul>';
            if ($cnb_user && !is_wp_error( $cnb_user )) {
                echo '<li>User ID: ' . esc_html($cnb_user->id) . '</li>';
                if ($cnb_user->stripeDetails && $cnb_user->stripeDetails->customerId) {
                    echo '<li>Stripe Customer ID: ' . esc_html($cnb_user->stripeDetails->customerId) . '</li>';
                }
            } else {
                echo '<li>User unknown</li>';
            }

            if ($cnb_domain && !is_wp_error( $cnb_domain )) {
                echo '<li>Domain ID: ' . esc_html($cnb_domain->id) . '</li>';
            } else {
                echo '<li>Domain unknown</li>';
            }
            echo '</ul>';
        }
    }
}
