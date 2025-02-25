<?php

namespace cnb\admin\profile;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\models\CnbUser;
use WP_Error;

class CnbProfileEdit {
    public function header() {
        echo 'Profile';
    }

    public function render() {
        wp_enqueue_script( CNB_SLUG . '-profile' );
        add_action( 'cnb_header_name', array( $this, 'header' ) );
        do_action( 'cnb_header' );
        $this->render_form();
        do_action( 'cnb_footer' );
    }

    /**
     * @param $modal boolean
     *
     * @return CnbUser|WP_Error
     */
    public function render_form( $modal = false ) {
        $controller = new CnbProfileController();
        $cnb_remote = new CnbAppRemote();
        $cnb_user   = $cnb_remote->get_user();
        if ( is_wp_error( $cnb_user ) ) {
            return $cnb_user;
        }

        $cnb_user_stripe_verified             = isset( $cnb_user->taxIds[0]->verification->status ) && $cnb_user->taxIds[0]->verification->status === 'verified';
        $cnb_user_stripe_verification_pending = isset( $cnb_user->taxIds[0]->verification->status ) && $cnb_user->taxIds[0]->verification->status === 'pending';
        ?>
        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post"
                class="cnb-container cnb-settings-profile">
            <input type="hidden" name="page" value="call-now-button"/>
            <input type="hidden" name="action" value="cnb_profile_edit"/>
            <?php wp_nonce_field( 'cnb-profile-edit' ) ?>
            <?php
            // Modal likely means "via domain-upgrade", so we need to send users back there
            if ( $modal ) { ?><input type="hidden" name="page_source" value="domain-upgrade"/><?php } ?>

            <table class="form-table nav-tab-only">
                <tbody>
                <?php if ( ! $modal ) { ?>
                    <tr>
                        <th colspan="2"><h2>Account owner</h2></th>
                    </tr>
                    <tr class="cnb_advanced_view">
                        <th scope="row"><label for="user_id">ID</label></th>
                        <td>
                            <code><?php echo esc_html( $cnb_user->id ) ?></code>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="user_email">Email</label></th>
                    <td>
                        <input type="text" id="user_email" name="user[email]"
                                value="<?php echo esc_attr( $cnb_user->email ) ?>"
                                disabled class="regular-text ltr">
                        <p class="description">Contact support to change your account email address.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="user[name]">Full name<span class="cnb_required">*</span></label></th>
                    <td>
                        <input type="text" id="user[name]" name="user[name]"
                                value="<?php echo esc_attr( $cnb_user->name ) ?>" required="required"
                                class="regular-text ltr">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="user[companyName]">Company name<span
                                    class="cnb_required cnb_vat_companies_show" style="display:none">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="user[companyName]" name="user[companyName]"
                                value="<?php echo esc_attr( $cnb_user->companyName ) ?>"
                                class="regular-text ltr cnb_vat_companies_required">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cnb_profile_country">Country<span class="cnb_required">*</span></label>
                    </th>
                    <td>
                        <label>
                            <select id="cnb_profile_country" class="select-menu" name="user[address][country]"
                                    required="required">
                                <option value=""></option>
                                <?php
                                foreach ( $controller->get_stripe_countries() as $country ) {
                                    $user_country = '';
                                    if ( isset( $cnb_user->address ) ) {
                                        $user_country = $cnb_user->address->country;
                                    }
                                    /** @noinspection HtmlUnknownAttribute */
                                    printf( '<option value="%1$s" %2$s>%3$s</option>',
                                        esc_attr( $country['code'] ),
                                        selected( $country['code'], $user_country ),
                                        esc_html( $country['country'] )
                                    );
                                }
                                ?>
                            </select>
                        </label>
                    </td>
                </tr>

                <tr class="cnb_show_vat_toggle" style="display:none">
                    <th scope="row"><label for="cnb-euvatbusiness">VAT registered business?</label></th>
                    <td>
                        <input type="hidden" name="user[euvatbusiness]" value="0">
                        <input id="cnb-euvatbusiness" type="checkbox" name="user[euvatbusiness]" value="1"
                            <?php checked( ! empty( $cnb_user->taxIds[0]->value ) ) ?>
                                class="ltr cnb_eu_values_only">
                        <label for="cnb-euvatbusiness">Yes</label>
                    </td>
                </tr>

                <tr class="cnb_vat_companies_show" style="display:none">
                    <th scope="row"><label for="user[address][line1]">Address<span class="cnb_required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="user[address][line1]" name="user[address][line1]"
                                value="<?php echo esc_attr( isset( $cnb_user->address ) ? $cnb_user->address->line1 : '' ) ?>"
                                class="regular-text ltr cnb_vat_companies_required cnb_eu_values_only">
                    </td>
                </tr>
                <tr class="cnb_vat_companies_show" style="display:none">
                    <th scope="row"><label for="user[address][line2]">Building, apartment, etc.</label></th>
                    <td>
                        <input type="text" id="user[address][line2]" name="user[address][line2]"
                                value="<?php echo esc_attr( isset( $cnb_user->address ) ? $cnb_user->address->line2 : '' ) ?>"
                                class="regular-text ltr cnb_eu_values_only">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="user[address][postalCode]"><span class="cnb_ie_only"
                                                                                style="display:none">Eircode/</span>Zip/Postal
                            code<span class="cnb_required">*</span></label></th>
                    <td>
                        <input type="text" id="user[address][postalCode]" name="user[address][postalCode]"
                                value="<?php echo esc_attr( isset( $cnb_user->address ) ? $cnb_user->address->postalCode : '' ) ?>"
                                class="regular-text ltr cnb_us_required cnb_vat_companies_required cnb_useu_values_only">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="user[address][city]">City<span class="cnb_required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="user[address][city]" name="user[address][city]"
                                value="<?php echo esc_attr( isset( $cnb_user->address ) ? $cnb_user->address->city : '' ) ?>"
                                required="required" class="regular-text ltr">
                    </td>
                </tr>

                <tr class="cnb_us_show" style="display:none">
                    <th scope="row"><label for="user[address][state]">State<span class="cnb_required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="user[address][state]" name="user[address][state]"
                                value="<?php echo esc_attr( isset( $cnb_user->address ) ? $cnb_user->address->state : '' ) ?>"
                                class="regular-text ltr cnb_us_required cnb_us_values_only">
                    </td>
                </tr>


                <tr class="cnb_vat_companies_show" style="display:none">
                    <th scope="row"><label for="cnb_profile_vat">VAT number<span class="cnb_required">*</span></label>
                    </th>
                    <td>
                        <input id="cnb_profile_vat" type="text" name="user[taxIds][0][value]"
                                value="<?php echo esc_attr( ( count( $cnb_user->taxIds ) > 0 ) ? $cnb_user->taxIds[0]->value : '' ) ?>"
                                class="regular-text ltr cnb_vat_companies_required cnb_eu_values_only">
                        <input id="cnb_user_taxids_type" type="hidden" name="user[taxIds][0][type]" value="eu_vat"
                                class="regular-text ltr cnb_vat_companies_required cnb_eu_values_only">

                        <?php
                        if ( $cnb_user_stripe_verified ) {
                            echo '<p class="description"><span class="dashicons dashicons-saved"></span><em>Your VAT number is verified.</em></p>';
                        } else if ( $cnb_user_stripe_verification_pending ) {
                            echo '<p class="description"><span class="dashicons dashicons-info"></span><em>Your VAT number is being verified.</em></p>';
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th></th>
                    <td><?php submit_button( 'Next', 'primary large' ) ?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
        return $cnb_user;
    }
}
