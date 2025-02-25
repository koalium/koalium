<?php

namespace cnb\admin\apikey;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbApiKeyView {
    function header() {
        echo 'API keys ';
    }

    function add_modal() {
        $url      = admin_url( 'admin.php' );
        $new_link =
            add_query_arg(
                array(
                    'TB_inline' => 'true',
                    'inlineId'  => 'cnb-add-new-apikey-modal',
                    'height'    => '150',
                ),
                $url );
        printf(
            '<a href="%s" title="%s" class="thickbox open-plugin-details-modal page-title-action" data-title="%s">%s</a>',
            esc_url( $new_link ),
            esc_html__( 'Create new API key' ),
            esc_html__( 'Create new API key' ),
            esc_html__( 'Add New' )
        );
    }

    public function render() {
        $wp_list_table = new Cnb_Apikey_List_Table();
        $data          = $wp_list_table->prepare_items();

        add_action( 'cnb_header_name', array( $this, 'header' ) );

        if ( ! is_wp_error( $data ) ) {
            add_action( 'cnb_after_header', array( $this, 'add_modal' ) );
        }

        wp_enqueue_script( CNB_SLUG . '-form-bulk-rewrite' );

        do_action( 'cnb_header' );
        printf( '<form class="cnb_list_event" action="%s" method="post">', esc_url( admin_url( 'admin-post.php' ) ) );
        echo '<input type="hidden" name="page" value="call-now-button-apikeys" />';
        echo '<input type="hidden" name="action" value="cnb_apikey_bulk" />';
        $wp_list_table->display();
        echo '</form>';

        $this->render_thickbox();

        do_action( 'cnb_footer' );
    }

    private function render_thickbox() {
        add_thickbox();
        ?>
        <div id="cnb-add-new-apikey-modal" style="display:none;">
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
                <input type="hidden" name="page" value="call-now-button-apikeys"/>
                <input type="hidden" name="action" value="cnb_apikey_create"/>
                <input type="hidden" name="_wpnonce"
                        value="<?php echo esc_attr( wp_create_nonce( 'cnb_apikey_create' ) ) ?>"/>

                <table>
                    <tbody>
                    <tr>
                        <th><label for="apikey-name">Name</label></th>
                        <td><input type="text" id="apikey-name" name="apikey[name]" required="required"/></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><?php submit_button(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }
}
