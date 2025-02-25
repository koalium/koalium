<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\models\CnbPlan;
use cnb\CnbHeaderNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;

class CnbDomainController {

    /**
     * This is called to create the Domain
     */
    public function create() {
        do_action( 'cnb_init', __METHOD__ );
        $nonce          = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
        $action         = 'cnb_create_domain';
        $nonce_verified = wp_verify_nonce( $nonce, $action );
        if ( $nonce_verified ) {
            // sanitize the input
            $domain                  = filter_input(
                INPUT_POST,
                'domain',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );
            $cnb_cloud_notifications = array();

            $processed_domain = CnbDomain::fromObject( $domain );
            // Alligator alert - this is different from other update functions!!
            $processed_domain->properties->zindex = $this->order_to_zindex( $processed_domain->properties->zindex );
            // do the processing
            $result = CnbAdminCloud::cnb_create_domain( $cnb_cloud_notifications, $processed_domain );

	        // Create link
	        $url = admin_url( 'admin.php' );
	        // redirect the user to the appropriate page
			if ( is_wp_error( $result ) ) {
				$transient_id = (new CnbHeaderNotices())->generate_notice_id();
				$notice = CnbAdminCloud::cnb_admin_get_error_message('create', 'domain', $result);
				set_transient( $transient_id, array( $notice ), HOUR_IN_SECONDS );

				$redirect_link = add_query_arg(
					array(
						'page'     => CNB_SLUG . '-domains',
						'tid'      => $transient_id,
						'_wpnonce' => wp_create_nonce( $transient_id ),
					),
					$url );
				$redirect_url = esc_url_raw( $redirect_link );
				do_action( 'cnb_finish' );
				wp_safe_redirect( $redirect_url );
				exit;
			} else {
				$redirect_link =
					add_query_arg(
						array(
							'page'   => CNB_SLUG . '-domains',
							'action' => 'edit',
							'id'     => $result->id,
						),
						$url );
				$redirect_url  = esc_url_raw( $redirect_link );
				do_action( 'cnb_finish' );
				wp_safe_redirect( $redirect_url );
				exit;
			}
        } else {
            do_action( 'cnb_finish' );
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

    public function update() {
        do_action( 'cnb_init', __METHOD__ );
        $nonce          = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
        $action         = 'cnb_update_domain';
        $nonce_verified = wp_verify_nonce( $nonce, $action );
        if ( $nonce_verified ) {
            $domain = $this->getDomainFromRequest();
            // do the processing
            $cnb_cloud_notifications = array();
            $result                  = CnbAdminCloud::cnb_update_domain( $cnb_cloud_notifications, $domain );

            // redirect the user to the appropriate page
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

            // Create link
            $url = admin_url( 'admin.php' );

            $redirect_link =
                add_query_arg(
                    array(
                        'page'     => CNB_SLUG . '-domains',
                        'action'   => 'edit',
                        'id'       => $result->id,
                        'tid'      => $transient_id,
                        '_wpnonce' => wp_create_nonce( $transient_id ),
                    ),
                    $url );
            $redirect_url  = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        } else {
            do_action( 'cnb_finish' );
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

    /**
     * This is very similar to the <code>delete()</code> function above.
     *
     * This always has to come via a $_POST request (specifically, via admin-post.php),
     * so this should end in a redirect (or an error via wp_die)
     *
     * Big differences are:
     * - This handles multiple IDs, versus 1
     * - Instead of rendering the Notice, is it stored and the user redirected
     *
     * nonce name via WP_List_Table = bulk-{plural}
     * so in this case: bulk-cnb_list_domains
     *
     * @return void
     */
    public function handle_bulk_actions() {
        do_action( 'cnb_init', __METHOD__ );
        $cnb_utils      = new CnbUtils();
        $nonce          = $cnb_utils->get_post_val( '_wpnonce' );
        $action         = 'bulk-cnb_list_domains';
        $nonce_verified = wp_verify_nonce( $nonce, $action );

        if ( $nonce_verified ) {
            $domainIds = filter_input( INPUT_POST, 'cnb_list_domain', @FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
            if ( $cnb_utils->get_post_val( 'bulk-action' ) === 'delete' ) {
                $cnb_cloud_notifications = array();
                foreach ( $domainIds as $domainId ) {
                    $domain     = new CnbDomain();
                    $domain->id = $domainId;
                    CnbAdminCloud::cnb_delete_domain( $cnb_cloud_notifications, $domain );
                }

                // Create notice for link (and yes - we ignore the content of $cnb_cloud_notifications here, we just use it to count)
                $notice       = new CnbNotice( 'success', '<p>' . count( $cnb_cloud_notifications ) . ' Domain(s) deleted.</p>' );
                $transient_id = (new CnbHeaderNotices())->generate_notice_id();
                set_transient( $transient_id, array( $notice ), HOUR_IN_SECONDS );

                // Create link
                $url           = admin_url( 'admin.php' );
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'     => 'call-now-button-domains',
                            'tid'      => $transient_id,
                            '_wpnonce' => wp_create_nonce( $transient_id ),
                        ),
                        $url );
                $redirect_url  = esc_url_raw( $redirect_link );
                do_action( 'cnb_finish' );
                wp_safe_redirect( $redirect_url );
                exit;
            } else {
                do_action( 'cnb_finish' );
                wp_die(
                    esc_html__( 'Unknown Bulk action specified' ),
                    esc_html__( 'Cannot process Bulk action' ),
                    array(
                        'response'  => 403,
                        'link_text' => esc_html( 'Go back to the Domains overview' ),
                        'link_url'  => esc_url_raw( admin_url( 'admin.php' ) . '?page=' . CNB_SLUG . '-domains' ),
                    )
                );
            }
        } else {
            wp_die(
                esc_html__( 'Invalid nonce specified' ),
                esc_html__( 'Error' ),
                array(
                    'response'  => 403,
                    'back_link' => true,
                )
            );
        }
    }

    public function update_timezone() {
        global $cnb_domain;
        do_action( 'cnb_init', __METHOD__ );
        if ( isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
            $action = 'cnb_update_domain_timezone';
            if ( wp_verify_nonce( $nonce, $action ) ) {
                $timezone         = filter_input( INPUT_POST, 'timezone', @FILTER_SANITIZE_STRING );
                $cnb_domain->timezone = $timezone;
                $notifications    = array();
                CnbAdminCloud::cnb_update_domain( $notifications, $cnb_domain );
                wp_send_json( array(
'success'      => true,
                                    'domain'       => $cnb_domain,
                                    'notification' => $notifications,
                                    'timezone'     => esc_html( $timezone ),
                ) );
                do_action( 'cnb_finish' );
                return;
            }
            wp_send_json( array( 'success' => false, 'reason' => 'nonce fail' ) );
            do_action( 'cnb_finish' );
            return;
        }
        wp_send_json( array( 'success' => false, 'reason' => 'no nonce' ) );
        do_action( 'cnb_finish' );
    }

    private function getDomainFromRequest() {
        $domain_controller = new CnbDomainController();
        // sanitize the input
        $domain = filter_input(
            INPUT_POST,
            'domain',
            @FILTER_SANITIZE_STRING,
            FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );

        $processed_domain = CnbDomain::fromObject( $domain );
        // Alligator alert - this is different from other update functions!!
        $processed_domain->properties->zindex = $domain_controller->order_to_zindex( $processed_domain->properties->zindex );

        return $processed_domain;
    }

    public function updateWithoutRedirect() {
        $domain = $this->getDomainFromRequest();
        // do the processing
        $cnb_cloud_notifications = array();
        CnbAdminCloud::cnb_update_domain( $cnb_cloud_notifications, $domain );

        return $cnb_cloud_notifications;
    }

	/**
	 * Via the quick action "Delete" (called admin_post_cnb_delete_domain), to be able to delete a Domain.
	 *
	 * Since "admin-post.php" is used, that means there is no output (and we can/should safely redirect to the Button overview after deleting).
	 *
	 * @return void
	 */
	public function delete() {
		$cnb_utils = new CnbUtils();
		$id        = $cnb_utils->get_query_val( 'id', null );
		$nonce     = $cnb_utils->get_query_val( '_wpnonce', null );
		$action    = 'cnb_delete_domain';

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			do_action( 'cnb_finish' );
			wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
				'response'  => 403,
				'back_link' => true,
			) );
		}

		$cnb_cloud_notifications = array();
		$domain                  = new CnbDomain();
		$domain->id              = $id;
		CnbAdminCloud::cnb_delete_domain( $cnb_cloud_notifications, $domain );

		// Save notices
		$transient_id = (new CnbHeaderNotices())->generate_notice_id();
		set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

		// Create link
		$redirect_link =
			add_query_arg(
				array(
					'page'     => 'call-now-button-domains',
					'tid'      => $transient_id,
					'_wpnonce' => wp_create_nonce( $transient_id ),
				),
				admin_url( 'admin.php' ) );
		$redirect_url  = esc_url_raw( $redirect_link );
		do_action( 'cnb_finish' );
		wp_safe_redirect( $redirect_url );
	}

    /**
     * Convert an "order" integer into a proper zIndex (10 to 2147483647, for example)
     *
     * This is the reverse operation of zindex_to_order
     *
     * @param $value int 1-10
     *
     * @return int 2-2147483647
     */
    public function order_to_zindex( $value ) {
        $zindexMap = $this->get_zindex_map();
        $default = 10;
        if (array_key_exists($value, $zindexMap)) {
            return $zindexMap[ $value ];
        }
        return $zindexMap[$default];
    }


    /**
     * Convert zIndex into an "order" int (2147483647 to 10, for example)
     *
     * This is the reverse operation of order_to_zindex
     *
     * @param $zindex int 2-2147483647
     *
     * @return int 1-10
     */
    public function zindex_to_order( $zindex ) {
        // This starts at the higher number
        foreach ( $this->get_zindex_map() as $order => $value ) {
            if ( $zindex >= $value ) {
                return $order;
            }
        }

        return 1;
    }

    /**
     * Returns the conversion map from the internal zindex number (1-10)
     * to the actual z-index (2 - 2147483647).
     *
     * @return int[]
     */
    private function get_zindex_map() {
        return array(
            10 => 2147483647,
            9  => 214748365,
            8  => 21474836,
            7  => 2147484,
            6  => 214748,
            5  => 21475,
            4  => 2147,
            3  => 215,
            2  => 21,
            1  => 2,
        );
    }

    /**
     * Get the discount percentage (difference for a year between the 2 Plans).
     *
     * @param $plan_year CnbPlan
     * @param $plan_month CnbPlan
     *
     * @return float integer rounded up to "12", "16", etc.
     */
    function get_discount_percentage($plan_year, $plan_month) {
        return ceil(100 - ($plan_year->price/(12*$plan_month->price)*100));
    }
}
