<?php

namespace cnb\admin\condition;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\utils\CnbAdminFunctions;
use cnb\notices\CnbAdminNotices;
use cnb\utils\CnbUtils;

class CnbConditionViewEdit {

    /**
     * @param CnbCondition $condition
     *
     * @return void
     */
    function add_header( $condition ) {
        if ( $condition->id !== 'new' ) {
            $name = $condition->filterType;
            if ( $condition->matchValue ) {
                $name = $condition->matchValue;
            }
            echo esc_html__( 'Editing display rule' ) . ' <span class="cnb_button_name">' . esc_html( $name ) . '</span>';
        } else {
            echo esc_html__( 'Add display rule' );
        }
    }

    /**
     * Create the over table for Conditions
     *
     * @param CnbCondition $condition
     */
    function render_table( $condition ) {
        global $cnb_domain;
        $isPro        = $cnb_domain != null && ! is_wp_error( $cnb_domain ) && $cnb_domain->type === 'PRO';
        $cnb_utils    = new CnbUtils();
        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $cnb_domain->id,
            ),
                admin_url( 'admin.php' ) );

        $bid = $cnb_utils->get_query_val( 'bid' );
        if ( $bid !== null ) {
            // Create back link
            $url           = admin_url( 'admin.php' );
            $redirect_link = esc_url(
                add_query_arg(
                    array(
                        'page'   => 'call-now-button',
                        'action' => 'edit',
                        'tab'    => 'visibility',
                        'id'     => $bid,
                    ),
                    $url ) );
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_url( $redirect_link ); ?>" class="cnb-nav-tab"><span
                            class="dashicons dashicons-arrow-left-alt"></span></a>
            </h2>
            <?php

        }
        ?>

        <table class="form-table nav-tab-active">
            <tbody>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="conditions[<?php echo esc_attr( $condition->id ) ?>][id]"
                            value="<?php if ( $condition->id !== null && $condition->id !== 'new' ) {
                                echo esc_attr( $condition->id );
                            } ?>"/>
                    <input type="hidden" name="conditions[<?php echo esc_attr( $condition->id ) ?>][delete]"
                            id="cnb_condition_<?php echo esc_attr( $condition->id ) ?>_delete" value=""/>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="cnb_condition_filter_type">I want to</label></th>
                <td>
                    <select id="cnb_condition_filter_type"
                            name="conditions[<?php echo esc_attr( $condition->id ) ?>][filterType]">
                        <?php foreach ( ( new CnbAdminFunctions() )->cnb_get_condition_filter_types() as $condition_filter_type_key => $condition_filter_type_value ) { ?>
                            <option value="<?php echo esc_attr( $condition_filter_type_key ) ?>"<?php selected( $condition_filter_type_key, $condition->filterType ) ?>>
                                <?php echo esc_html( $condition_filter_type_value ) ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="cnb_condition_condition_type">Based on</label></th>
                <td>
                    <select id="cnb_condition_condition_type"
                            name="conditions[<?php echo esc_attr( $condition->id ) ?>][conditionType]">
                        <?php foreach ( ( new CnbAdminFunctions() )->cnb_get_condition_types() as $type_key => $type_key_value ) { ?>
                            <option
                                    value="<?php echo esc_attr( $type_key ) ?>"
                                <?php selected( $type_key, $condition->conditionType ) ?>
                                    <?php if ( $type_key_value['proOnly'] && ! $isPro ) { ?>disabled="disabled" <?php } ?>
                            >
                                <?php echo esc_html( $type_key_value['name'] ) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php if ( ! $isPro ) { ?>
                        <p class="description">
                            Location based rules are a <span class="cnb-pro-badge">Pro</span> feature.
                            <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                        </p>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="cnb_condition_match_type">Where</label></th>
                <td>
                    <select id="cnb_condition_match_type"
                            name="conditions[<?php echo esc_attr( $condition->id ) ?>][matchType]">
                        <?php
                        foreach ( ( new CnbAdminFunctions() )->cnb_get_condition_match_types_url() as $condition_match_type_key => $condition_match_type_value ) { ?>
                            <option
                                    class="conditionType conditionType_URL"
                                    value="<?php echo esc_attr( $condition_match_type_key ) ?>"
                                    <?php if ( ! in_array( $cnb_domain->type, $condition_match_type_value['plans'] ) ) { ?>disabled="disabled"<?php } ?>
                                <?php selected( $condition_match_type_key, $condition->matchType ) ?>>
                                <?php echo esc_html( $condition_match_type_value['name'] ) ?>
                            </option>
                        <?php } ?>

                        <?php
                        foreach ( ( new CnbAdminFunctions() )->cnb_get_condition_match_types_geo() as $condition_match_type_key => $condition_match_type_value ) { ?>
                            <option class="conditionType conditionType_GEO"
                                    value="<?php echo esc_attr( $condition_match_type_key ) ?>"<?php selected( $condition_match_type_key, $condition->matchType ) ?>>
                                <?php echo esc_html( $condition_match_type_value ) ?>
                            </option>
                        <?php } ?>

	                    <?php
	                    foreach ( ( new CnbAdminFunctions() )->cnb_get_condition_match_types_chat() as $condition_match_type_key => $condition_match_type_value ) { ?>
                            <option class="conditionType conditionType_CHAT"
                                    value="<?php echo esc_attr( $condition_match_type_key ) ?>"<?php selected( $condition_match_type_key, $condition->matchType ) ?>>
			                    <?php echo esc_html( $condition_match_type_value ) ?>
                            </option>
	                    <?php } ?>
                    </select>
                    <?php if ( $cnb_domain->type === 'STARTER' ) { ?>
                        <p class="description">
                            RegEx filtering rules are a <span class="cnb-pro-badge">Pro</span> feature.
                            <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                        </p>
                    <?php } ?>

                </td>
            </tr>
            <tr class="conditionType conditionType_URL conditionType_GEO">
                <th scope="row"><label for="cnb_condition_match_value">Match value</label></th>
                <td>
                    <input type="text" id="cnb_condition_match_value" class="regular-text"
                            name="conditions[<?php echo esc_attr( $condition->id ) ?>][matchValue]"
                            value="<?php echo esc_attr( $condition->matchValue ) ?>"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    function render() {
        $cnb_utils    = new CnbUtils();
        $cnb_remote   = new CnbAppRemote();
        $condition_id = filter_input( INPUT_GET, 'id', @FILTER_SANITIZE_STRING );
        $condition    = new CnbCondition();
        if ( strlen( $condition_id ) > 0 && $condition_id !== 'new' ) {
            $condition = $cnb_remote->get_condition( $condition_id );
        } elseif ( $condition_id === 'new' ) {
            $condition->id = 'new';
        }

        wp_enqueue_script( CNB_SLUG . '-condition-edit' );
        add_action( 'cnb_header_name', function () use ( $condition ) {
            $this->add_header( $condition );
        } );

        $bid = $cnb_utils->get_query_val( 'bid' );
        if ( $bid !== null ) {
            // Create back link
            $url           = admin_url( 'admin.php' );
            $redirect_link = esc_url(
                add_query_arg(
                    array(
                        'page'   => 'call-now-button',
                        'action' => 'edit',
                        'tab'    => 'visibility',
                        'id'     => $bid,
                    ),
                    $url ) );

            $action_verb = $condition->id === 'new' ? 'adding' : 'editing';
            $message     = '<p><strong>You are ' . $action_verb . ' a display rule</strong>.
                    Click <a href="' . $redirect_link . '">here</a> to go back.</p>';
            CnbAdminNotices::get_instance()->renderInfo( $message );
        }

        do_action( 'cnb_header' );
        ?>

        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
            <input type="hidden" name="page" value="call-now-button-conditions"/>
            <input type="hidden" name="bid" value="<?php echo esc_attr( $bid ) ?>"/>
            <input type="hidden" name="condition_id" value="<?php echo esc_attr( $condition->id ) ?>"/>
            <input type="hidden" name="action"
                    value="<?php echo $condition_id === 'new' ? 'cnb_create_condition' : 'cnb_update_condition' ?>"/>
            <input type="hidden" name="_wpnonce"
                    value="<?php echo esc_attr( wp_create_nonce( $condition->id === 'new' ? 'cnb_create_condition' : 'cnb_update_condition' ) ) ?>"/>
            <?php
            $this->render_table( $condition );
            submit_button();
            ?>
        </form>
        <?php do_action( 'cnb_footer' );
    }
}
