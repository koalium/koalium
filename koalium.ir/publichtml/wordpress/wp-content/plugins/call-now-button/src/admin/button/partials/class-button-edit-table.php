<?php
/** @noinspection SpellCheckingInspection */

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbAction;
use cnb\admin\action\CnbActionProperties;
use cnb\admin\action\CnbActionView;
use cnb\admin\action\CnbActionViewEdit;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\condition\CnbConditionView;
use cnb\admin\domain\CnbDomain;
use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;

class Button_Edit_Table {

    /**
     * @param CnbButton $button
     * @param boolean $hide_on_modal
     * @param CnbDomain $default_domain
     *
     * @return void
     */
    function render_tab_basic_options( $button, $hide_on_modal, $default_domain ) {
        global $cnb_domains, $wp_version;
        $adminFunctions = new CnbAdminFunctions();

	    $cnb_single_image_url       = plugins_url( 'resources/images/button-new-single.png', CNB_PLUGINS_URL_BASE );
	    $cnb_multi_image_url        = plugins_url( 'resources/images/button-new-multibutton.gif', CNB_PLUGINS_URL_BASE );
	    $cnb_multi_flower_image_url = plugins_url( 'resources/images/button-new-flower.gif', CNB_PLUGINS_URL_BASE );
	    $cnb_full_image_url         = plugins_url( 'resources/images/button-new-buttonbar.png', CNB_PLUGINS_URL_BASE );
	    $cnb_full_single_image_url  = plugins_url( 'resources/images/button-new-full.png', CNB_PLUGINS_URL_BASE );
	    $cnb_dots_image_url         = plugins_url( 'resources/images/button-new-dots.png', CNB_PLUGINS_URL_BASE );

	    // Only for WordPress 5.2 and higher (Gutenberg + React 16.8)
	    $has_gutenberg = version_compare( $wp_version, '5.2.0', '>=' );

        $url             = admin_url( 'admin.php' );
        $new_action_link =
            add_query_arg(
                array(
                    'page'   => 'call-now-button-actions',
                    'action' => 'new',
                    'id'     => 'new',
                    'tab'    => 'basic_options',
                    'bid'    => $button->id,
                ),
                $url );

        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $default_domain->id,
            ),
                $url );

        $action = $this->get_action( $button );

        ?>
    <table class="form-table <?php if ( ! $hide_on_modal ) {
        echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) );
    } else {
        echo 'nav-tab-only';
    } ?>" data-tab-name="basic_options">
        <tr class="cnb_hide_on_modal">
            <th></th>
            <td></td>
        </tr>
        <tr class="cnb_hide_on_modal">
            <th scope="row"><label for="cnb-enable">Button status</label></th>

            <td class="activated">
                <input type="hidden" name="button[active]" value="0"/>
                <input id="cnb-enable" class="cnb_toggle_checkbox" type="checkbox" name="button[active]"
                        value="1" <?php checked( true, $button->active ); ?> />
                <label for="cnb-enable" class="cnb_toggle_label">Toggle</label>
                <span data-cnb_toggle_state_label="cnb-enable"
                        class="cnb_toggle_state cnb_toggle_false">(Inactive)</span>
                <span data-cnb_toggle_state_label="cnb-enable"
                        class="cnb_toggle_state cnb_toggle_true">Active</span>
            </td>
        </tr>
	    <?php if ( $button->id === 'new' ) {
            $templates_link   =
	        add_query_arg(
		        array(
                    'page'    => 'call-now-button-templates',
		        ),
                admin_url( 'admin.php' ) );
            } ?>
        <tr class="cnb_button_name">
            <th scope="row"><label for="button_name">Button name</label></th>

            <td class="activated">
                <input type="text" name="button[name]" id="button_name" required="required"
                        value="<?php echo esc_attr( $button->name ); ?>" placeholder="My new button"/>
            </td>
        </tr>
        <tr class="cnb_hide_on_modal cnb_advanced_view">
            <th scope="row"><label for="button_domain">Domain</label></th>
            <td>
                <select name="button[domain]" id="button_domain">
                    <?php
                    // In case the domain list fails, fall back to just the current domain
                    if (! $cnb_domains || is_wp_error( $cnb_domains ) && $default_domain && is_wp_error( $default_domain ) ) { ?>
                        <option
                            value="<?php echo esc_attr( $default_domain->id ) ?>">
                            <?php echo esc_html( $default_domain->name ) ?>
                            (current WordPress domain)
                        </option>
                    <?php }
                    if ( is_array( $cnb_domains ) ) {
                    foreach ( $cnb_domains as $domain ) { ?>
                        <option
                            <?php selected( $domain->id, $button->domain->id ) ?>
                                value="<?php echo esc_attr( $domain->id ) ?>">
                            <?php echo esc_html( $domain->name ) ?>
                            <?php if ( $domain->id == $default_domain->id ) {
                                echo ' (current WordPress domain)';
                            } ?>
                        </option>
                    <?php } } ?>
                </select>
            </td>
        </tr>
        <?php if ( $button->type !== 'SINGLE' ) { ?>
            <tr class="cnb_hide_on_modal">
                <th colspan="2" class="cnb_padding_0">
                    <h2>
                        Actions
                        <?php
                        if ($default_domain->type === 'STARTER' && $button->type === 'FULL' && count($button->actions)) {
                            echo '<a href="#" class="page-title-action button-disabled" title="Upgrade to PRO to add more actions">Add Action</a>';
                        } else {
                            echo '<a href="' . esc_url( $new_action_link ) . '" class="page-title-action">Add Action</a>';
                        }
                        ?>
                    </h2>
                    <?php
                    if ($default_domain->type === 'STARTER' && $button->type === 'FULL' && count($button->actions)) {
                        echo '<p class="description" style="font-weight:400;">
                            Add up to 5 actions to a single Buttonbar with <span class="cnb-pro-badge">Pro</span>. <a href="' . esc_url( $upgrade_link ) . '">Upgrade</a>
                        </p>';
                    }
                    ?>
                </th>
            </tr>
        <?php }

        if ( $button->type === 'SINGLE' ) {
            // Start workaround: This table below (<tr>...</tr>) needs to be there for the modal to work!
            if ( $hide_on_modal ) { ?>
                <tr class="cnb_hide_on_modal">
                <th></th>
                <td>
                <input type="hidden" name="actions[<?php echo esc_attr( $action->id ) ?>][id]"
                        value="<?php echo esc_attr( $action->id ) ?>"/>
            <?php }

	        /**
	         * We don't actually need any action details in the modal - the only important piece is
             * the Button details (and really, only the domain id and button type).
             *
             * This (hidden) form conflicts with the Button Controller and would create Actions
             * when none were needed/requested, so we're hiding/skipping this for the modal window
	         */
	        if ( !$hide_on_modal ) {
		        ( new CnbActionViewEdit() )->render_main( $action, $button, $default_domain );
	        }
            if ( $hide_on_modal ) { ?>
                </td>
                </tr>
            <?php } // End workaround
        }

        if ( $button->type !== 'SINGLE' ) { ?>
            </table>

            <!-- This div exists to allow rendering the Action table outside the existing table -->
            <div data-tab-name="basic_options" class="cnb-button-edit-action-table <?php if ( $hide_on_modal ) {
                echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) );
            } else {
                echo 'nav-tab-only';
            } ?>" <?php if ( ! $adminFunctions->is_active_tab( 'basic_options' ) ) {
                echo 'style="display:none"';
            } ?>>
                <?php ( new CnbActionView() )->renderTable( $button ); ?>
            </div>

            <table class="form-table <?php if ( ! $hide_on_modal ) {
                echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) );
            } else {
                echo 'nav-tab-only';
            } ?>"><?php
        } ?>
        <script>
            let cnb_actions = <?php echo wp_json_encode( $button->actions ) ?>;
            let cnb_domain = <?php echo wp_json_encode( $button->domain ) ?>;
        </script>

        <?php if ( $button->id === 'new' ) { ?>
            <tr class="cnb_button_type">
                <th scope="row" colspan="2">Select button type 
                    <?php if ($has_gutenberg) { ?><span class="cnb-smaller-text-in-heading">(or start with a <a class="cnb-green cnb_font_bold" href="<?php echo esc_url( $templates_link ); ?>">template</a>)<?php } ?></span></th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cnb_type_selector cnb_type_selector_container">
                        <div class="cnb_type_selector cnb_type_selector_item cnb_type_selector_single cnb_type_selector_active"
                            data-cnb-selection="single">
                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                <img style="max-width:100%;" alt="Select the Single button"
                                src="<?php echo esc_url( $cnb_single_image_url ) ?>">          
                                </div>
                            </div>
                            
                            <div style="text-align:center">Single<span class="cnb-hide-on-mobile"> button</span></div>
                        </div>

                        <?php
                        // Only show this tot non-PRO domains, since the button bar is available for PRO domains
                        // And FULL is basically (and technically) the same as the FULL WIDTH
                        if ( $default_domain->type !== 'PRO' ) { ?>
                        <div class="cnb_type_selector cnb_type_selector_item cnb_type_selector_full"
                            data-cnb-selection="full">
                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                <img style="max-width:100%;" alt="Select the Full width button"
                                src="<?php echo esc_url( $cnb_full_single_image_url ) ?>"> 
                                </div>
                            </div>
                            
                            <div style="text-align:center">
                                Full width<span class="cnb-hide-on-mobile"> button</span>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="cnb_type_selector <?php if ( $default_domain->type !== 'STARTER' ) { ?>cnb_type_selector_item<?php } else { ?>cnb_type_only_pro<?php } ?> cnb_type_selector_multi"
                            data-cnb-selection="multi">
                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                <img style="max-width:100%;" alt="Select the Multibutton"
                                src="<?php echo esc_url( $cnb_multi_image_url ) ?>">         
                                </div>
                            </div>
                            
                            <div style="text-align:center">
                                Multibutton
                                <?php if ( $default_domain->type === 'STARTER' ) { ?><span
                                        class="cnb-pro-badge">Pro</span><?php } ?>
                            </div>
                            <?php if ( $default_domain->type === 'STARTER' ) { ?>
                                <div class="cnb-pro-overlay">
                                <p class="description">
                                    Multibutton is a <span class="cnb-pro-badge">Pro</span> feature.
                                    <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a> here.
                                </p>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="cnb_type_selector <?php if ( $default_domain->type === 'PRO' ) { ?>cnb_type_selector_item<?php } else { ?>cnb_type_only_pro<?php } ?> cnb_type_selector_multi_flower"
                            data-cnb-selection="multi">

                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                <img style="max-width:100%;" alt="Select Flower"
                                src="<?php echo esc_url( $cnb_multi_flower_image_url ) ?>">
                                </div>
                            </div>
                            
                            <div style="text-align:center">
                                Flower<span class="cnb-hide-on-mobile"> button</span>
			                    <?php if ( $default_domain->type !== 'PRO' ) { ?><span
                                        class="cnb-pro-badge">Pro</span><?php } ?>
                            </div>
		                    <?php if ( $default_domain->type !== 'PRO' ) { ?>
                                <div class="cnb-pro-overlay">
                                    <p class="description">
                                        Flower is a <span class="cnb-pro-badge">Pro</span> feature.
                                        <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a> here.
                                    </p>
                                </div>
		                    <?php } ?>
                        </div>

                        <div class="cnb_type_selector <?php if ( $default_domain->type === 'PRO' ) { ?>cnb_type_selector_item<?php } else { ?>cnb_type_only_pro<?php } ?> cnb_type_selector_full"
                            data-cnb-selection="full">
                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                    <img style="max-width:100%;" alt="Select the Buttonbar"
                                src="<?php echo esc_url( $cnb_full_image_url ) ?>">       
                                </div>
                            </div>
                            
                            <div style="text-align:center">
                                Buttonbar
	                            <?php if ( $default_domain->type !== 'PRO' ) { ?><span
                                        class="cnb-pro-badge">Pro</span><?php } ?>
                            </div>
	                        <?php if ( $default_domain->type !== 'PRO' ) { ?>
                                <div class="cnb-pro-overlay">
                                    <p class="description">
                                        Buttonbar is a <span class="cnb-pro-badge">Pro</span> feature.
                                        <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a> here.
                                    </p>
                                </div>
	                        <?php } ?>
                        </div>

                        <div class="cnb_type_selector <?php if ( $default_domain->type === 'PRO' ) { ?>cnb_type_selector_item<?php } else { ?>cnb_type_only_pro<?php } ?> cnb_type_selector_dots"
                            data-cnb-selection="dots">

                            <div class="cnb-phone-outside">
                                <div class="cnb-phone-inside">
                                    <img style="max-width:100%;" alt="Select Dots"
                                src="<?php echo esc_url( $cnb_dots_image_url ) ?>">
                                </div>
                            </div>
                            
                            <div style="text-align:center">
                                Dots
			                    <?php if ( $default_domain->type !== 'PRO' ) { ?><span
                                        class="cnb-pro-badge">Pro</span><?php } ?>
                            </div>
		                    <?php if ( $default_domain->type !== 'PRO' ) { ?>
                                <div class="cnb-pro-overlay">
                                    <p class="description">
                                        Dots is a <span class="cnb-pro-badge">Pro</span> feature.
                                        <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a> here.
                                    </p>
                                </div>
		                    <?php } ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </table>
        <?php
    }

    /**
     * "extra_options" == "Presentation"
     *
     * @param $button CnbButton
     *
     * @return void
     */
    function render_tab_extra_options( $button ) {
        $adminFunctions = new CnbAdminFunctions();
        $cnb_utils      = new CnbUtils();
        $action         = $this->get_action( $button );

        // For the image selector
        wp_enqueue_media();

        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $button->domain->id,
            ),
                admin_url( 'admin.php' ) );

	    // Migration note:
	    //- we move from button.options.iconBackgroundColor to action.backgroundColor
	    //- we move from button.options.iconColor to action.iconColor
	    // So for now, "button" take priority, but once the new value is saved, we blank the button options
	    $backgroundColor = $this->get_background_color( $button, $action );
	    $iconColor       = $this->get_icon_color( $button, $action );

	    ?>
        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'extra_options' ) ) ?>"
                data-tab-name="extra_options">
            <?php if ( $button->type === 'FULL' ) { ?>
                <tr class="cnb_advanced_view">
                    <th colspan="2">
                        <h2>Colors for the Buttonbar are defined via the individual Action(s).</h2>
                        <input name="button[options][iconBackgroundColor]" type="hidden"
                                value="<?php echo esc_attr( $button->options->iconBackgroundColor ); ?>"/>
                        <input name="button[options][iconColor]" type="hidden"
                                value="<?php echo esc_attr( $button->options->iconColor ); ?>"/>
                    </th>
                </tr>
            <?php } else if ( $button->type === 'SINGLE' ) { ?>
                <tr class="cnb_hide_on_modal">
                    <th></th>
                    <td>
                        <input name="button[options][iconBackgroundColor]" type="hidden" value=""/>
                        <input name="button[options][iconColor]" type="hidden" value=""/>
                        <!-- We always enable the icon when the type if SINGLE, original value is "<?php echo esc_attr( $action->iconEnabled ) ?>" -->
                        <input name="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]" type="hidden"
                                value="1"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="actions-options-iconBackgroundColor">Button color</label></th>
                    <td>
                        <input name="actions[<?php echo esc_attr( $action->id ) ?>][backgroundColor]"
                                id="actions-options-iconBackgroundColor" type="text"
                                value="<?php echo esc_attr( $backgroundColor ); ?>" class="cnb-color-field"
                                data-default-color="#009900"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="actions-options-iconColor">Icon color</label></th>
                    <td>
                        <input name="actions[<?php echo esc_attr( $action->id ) ?>][iconColor]"
                                id="actions-options-iconColor" type="text"
                                value="<?php echo esc_attr( $iconColor ); ?>" class="cnb-color-field"
                                data-default-color="#FFFFFF"/>
                    </td>
                </tr>

            <?php } else if ( $button->type === 'MULTI' ) {
                ?>
                <tr>
                    <td colspan="2"></td>
                </tr>
                <?php
                $icon_picker = new Button_Icon_Picker();
                $icon_picker->render($button);
                $label_editor = new Button_Label();
                $label_editor->render($button);
            } ?>
            <tr>
                <th scope="row">Position <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/presentation/button-position/', 'question-mark', 'button-position' ) ) ?>"
                            target="_blank" class="cnb-nounderscore">
                        <span class="dashicons dashicons-editor-help"></span>
                    </a></th>
                <td class="appearance">
                    <div class="appearance-options">
                        <?php if ( $button->type === 'FULL' ) { ?>
                            <div class="cnb-radio-item">
                                <input type="radio" id="appearance1" name="button[options][placement]"
                                        value="TOP_CENTER" <?php checked( 'TOP_CENTER', $button->options->placement ); ?>>
                                <label title="top-center" for="appearance1">Top</label>
                            </div>
                            <div class="cnb-radio-item">
                                <input type="radio" id="appearance2" name="button[options][placement]"
                                        value="BOTTOM_CENTER" <?php checked( 'BOTTOM_CENTER', $button->options->placement ); ?>>
                                <label title="bottom-center" for="appearance2">Bottom</label>
                            </div>
                        <?php } else { ?>
                            <div class="cnb-radio-item">
                                <input type="radio" id="appearance1" name="button[options][placement]"
                                        value="BOTTOM_RIGHT" <?php checked( 'BOTTOM_RIGHT', $button->options->placement ); ?>>
                                <label title="bottom-right" for="appearance1">Right corner</label>
                            </div>
                            <div class="cnb-radio-item">
                                <input type="radio" id="appearance2" name="button[options][placement]"
                                        value="BOTTOM_LEFT" <?php checked( 'BOTTOM_LEFT', $button->options->placement ); ?>>
                                <label title="bottom-left" for="appearance2">Left corner</label>
                            </div>
                            <div class="cnb-radio-item">
                                <input type="radio" id="appearance3" name="button[options][placement]"
                                        value="BOTTOM_CENTER" <?php checked( 'BOTTOM_CENTER', $button->options->placement ); ?>>
                                <label title="bottom-center" for="appearance3">Center</label>
                            </div>

                            <!-- Extra placement options -->
                            <br class="cnb-extra-placement">
                            <div class="cnb-radio-item cnb-extra-placement <?php echo $button->options->placement == 'MIDDLE_RIGHT' ? 'cnb-extra-active' : ''; ?>">
                                <input type="radio" id="appearance5" name="button[options][placement]"
                                        value="MIDDLE_RIGHT" <?php checked( 'MIDDLE_RIGHT', $button->options->placement ); ?>>
                                <label title="middle-right" for="appearance5">Middle right</label>
                            </div>
                            <div class="cnb-radio-item cnb-extra-placement <?php echo $button->options->placement == 'MIDDLE_LEFT' ? 'cnb-extra-active' : ''; ?>">
                                <input type="radio" id="appearance6" name="button[options][placement]"
                                        value="MIDDLE_LEFT" <?php checked( 'MIDDLE_LEFT', $button->options->placement ); ?>>
                                <label title="middle-left" for="appearance6">Middle left </label>
                            </div>
                            <br class="cnb-extra-placement">
                            <div class="cnb-radio-item cnb-extra-placement <?php echo $button->options->placement == 'TOP_RIGHT' ? 'cnb-extra-active' : ''; ?>">
                                <input type="radio" id="appearance7" name="button[options][placement]"
                                        value="TOP_RIGHT" <?php checked( 'TOP_RIGHT', $button->options->placement ); ?>>
                                <label title="top-right" for="appearance7">Top right corner</label>
                            </div>
                            <div class="cnb-radio-item cnb-extra-placement <?php echo $button->options->placement == 'TOP_LEFT' ? 'cnb-extra-active' : ''; ?>">
                                <input type="radio" id="appearance8" name="button[options][placement]"
                                        value="TOP_LEFT" <?php checked( 'TOP_LEFT', $button->options->placement ); ?>>
                                <label title="top-left" for="appearance8">Top left corner</label>
                            </div>
                            <div class="cnb-radio-item cnb-extra-placement <?php echo $button->options->placement == 'TOP_CENTER' ? 'cnb-extra-active' : ''; ?>">
                                <input type="radio" id="appearance9" name="button[options][placement]"
                                        value="TOP_CENTER" <?php checked( 'TOP_CENTER', $button->options->placement ); ?>>
                                <label title="top-center" for="appearance9">Center top</label>
                            </div>
                            <a href="#" id="button-more-placements">More placement options...</a>
                            <!-- END extra placement options -->
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php if ( $button->type !== 'FULL' ) { ?>
                <tr>
                    <th scope="row"><label for="button_options_animation">Button animation <?php if ( $button->domain->type !== 'STARTER' ) { ?><a
                                    href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress/buttons/button-animations/', 'question-mark', 'button-animation' ) ) ?>"
                                    target="_blank" class="cnb-nounderscore">
                                <span class="dashicons dashicons-editor-help"></span>
                            </a><?php } ?></label>
                            <?php if ( $button->domain->type === 'STARTER' ) { ?>
                                <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a>
                            <?php } ?>
                            </th>
                    <td>
                        <select
                                name="button[options][animation]"
                                id="button_options_animation"
                                <?php if ( $button->domain->type === 'STARTER' ) { ?>disabled="disabled"<?php } ?>
                        >
                            <?php foreach ( CnbButtonOptions::getAnimationTypes() as $animation_type_key => $animation_type_value ) {?>
                                <option value="<?php echo esc_attr( $animation_type_key ) ?>"<?php selected( $animation_type_key, $button->options->animation ) ?>><?php echo esc_html( $animation_type_value ) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            <?php } ?>
	        <?php if ( $button->type === 'MULTI' ) { ?>
                <tr>
                    <th scope="row"><label for="multi_button_options_default_state">Default state</label>
				        <?php if ( $button->domain->type !== 'PRO' ) { ?>
                            <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a>
				        <?php } ?>
                    </th>
                    <td>
                        <select
                                name="button[multiButtonOptions][defaultState]"
                                id="multi_button_options_default_state"
						        <?php if ( $button->domain->type !== 'PRO' ) { ?>disabled="disabled"<?php } ?>
                        >
					        <?php foreach ( CnbMultiButtonOptions::getDefaultStates() as $default_state_key => $default_state_value ) {?>
                                <option value="<?php echo esc_attr( $default_state_key ) ?>"<?php selected( $default_state_key, $button->multiButtonOptions->defaultState ) ?>><?php echo esc_html( $default_state_value ) ?></option>
					        <?php } ?>
                        </select>
                    </td>
                </tr>
	        <?php } ?>
            <tr class="cnb_advanced_view">
                <th scope="row"><label for="button_options_css_classes">CSS Classes</label>
			        <?php if ( $button->domain->type !== 'PRO' ) { ?>
                        <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a>
			        <?php } ?>
                </th>
                <td>
                    <input
                            name="button[options][cssClasses]"
                            id="button_options_css_classes"
					        type="text" <?php if ( $button->domain->type !== 'PRO' ) { ?>disabled="disabled"<?php } ?>
                            value="<?php echo esc_attr($button->options->cssClasses) ?>" />
                </td>
            </tr>
        </table>
        <?php
    }

    private function get_background_color( $button, $action) {
        if ( $button && $button->options && $button->options->iconBackgroundColor ){
            return $button->options->iconBackgroundColor;
        }
        if ( $action->backgroundColor ) {
            return $action->backgroundColor;
        }
        return '#009900';
    }

    private function get_icon_color( $button, $action ) {
        if ( $button && $button->options && $button->options->iconColor ) {
            return $button->options->iconColor;
        }
        if ( $action->iconColor ) {
            return $action->iconColor;
        }
        return '#FFFFFF';
    }

    function render_tab_visibility( $button ) {
        $adminFunctions = new CnbAdminFunctions();

        $url                = admin_url( 'admin.php' );
        $new_condition_link =
            add_query_arg(
                array(
                    'page'   => 'call-now-button-conditions',
                    'action' => 'new',
                    'id'     => 'new',
                    'bid'    => $button->id,
                ),
                $url );

        ?>
        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'visibility' ) ) ?>"
                data-tab-name="visibility">
            <tbody id="cnb_form_table_visibility">
            <tr>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th scope="row"><label for="button_options_displaymode">Display on </label></th>
                <td class="appearance">
                    <select name="button[options][displayMode]" id="button_options_displaymode">
                        <option value="MOBILE_ONLY"<?php selected( 'MOBILE_ONLY', $button->options->displayMode ) ?>>
                            Mobile only
                        </option>
                        <option value="DESKTOP_ONLY"<?php selected( 'DESKTOP_ONLY', $button->options->displayMode ) ?>>
                            Desktop only
                        </option>
                        <option value="ALWAYS"<?php selected( 'ALWAYS', $button->options->displayMode ) ?>>All
                            screens
                        </option>
                    </select>
                </td>
            </tr>
            <?php $this->render_scroll_options( $button ); ?>
            <tr class="cnb_hide_on_modal">
                <th class="cnb_padding_0">
                    <h2>Display rules</h2>
                </th>
                <td>
                    <?php echo '<a href="' . esc_url( $new_condition_link ) . '" class="button">Add display rule</a>'; ?>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    function render_tab_visibility_condition_table( $button, $hide_on_modal ) {
        $adminFunctions = new CnbAdminFunctions();
        ?>
        <!-- This div exists to allow rendering the Conditions' table outside the existing table -->
        <div data-tab-name="visibility" class="cnb-button-edit-conditions-table <?php if ( $hide_on_modal ) {
            echo esc_attr( $adminFunctions->is_active_tab( 'visibility' ) );
        } else {
            echo 'nav-tab-only';
        } ?>" <?php if ( ! $adminFunctions->is_active_tab( 'visibility' ) ) {
            echo 'style="display:none"';
        } ?>>
            <?php
            $view = new CnbConditionView();
            $view->renderTable( $button );
            ?>
        </div>
        <?php
    }

    /**
     * @param $button CnbButton
     *
     * @return void
     */
    private function render_scroll_options( $button ) {
        global $cnb_domain;
        $isPro = $cnb_domain != null && ! is_wp_error( $cnb_domain ) && $cnb_domain->type === 'PRO';
        ?>
        <tr class="cnb_hide_on_modal">
            <?php $reveal_at_height = $button->options->scroll ? $button->options->scroll->revealAtHeight : 0 ?>
            <th><label for="cnb-button-options-scroll-revealatheight">Reveal after scrolling</label>
                <?php if ( ! $isPro ) {
                    $upgrade_link =
                        add_query_arg( array(
                            'page'   => 'call-now-button-domains',
                            'action' => 'upgrade',
                            'id'     => $cnb_domain->id,
                        ),
                            admin_url( 'admin.php' ) );
                    ?>
                    <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a></th>
                <?php } ?>
            <td>
                <input
                        name="button[options][scroll][revealAtHeight]"
                        id="cnb-button-options-scroll-revealatheight"
                        type="number"
                        min="0"
                        <?php if ( ! $isPro ) { ?>disabled="disabled"<?php } ?>
                        style="width: 80px"
                        value="<?php echo esc_attr( $reveal_at_height ) ?>"> pixels from the top
            </td>
        </tr>
        <tr class="cnb_hide_on_modal cnb_advanced_view">
            <?php $hide_at_height = $button->options->scroll ? $button->options->scroll->hideAtHeight : 0 ?>
            <th><label for="cnb-button-options-scroll-hideAtHeight">Hide after scrolling</label></th>
            <td>
                <input name="button[options][scroll][hideAtHeight]" id="cnb-button-options-scroll-hideAtHeight"
                        type="number" min="0" style="width: 80px" value="<?php echo esc_attr( $hide_at_height ) ?>">
                pixels from the top
                <p class="description">hideAtHeight</p>
            </td>
        </tr>
        <tr class="cnb_hide_on_modal cnb_advanced_view">
            <?php $never_hide = $button->options->scroll ? $button->options->scroll->neverHide : false ?>
            <th><label for="cnb-button-options-scroll-neverhide">Never hide</label></th>
            <td>
                <input type="hidden" name="button[options][scroll][neverHide]" value="0"/>
                <input id="cnb-button-options-scroll-neverhide" class="cnb_toggle_checkbox" type="checkbox"
                        name="button[options][scroll][neverHide]"
                        value="1" <?php checked( true, $never_hide ); ?> />
                <label for="cnb-button-options-scroll-neverhide" class="cnb_toggle_label">Toggle</label>
                <span data-cnb_toggle_state_label="cnb-button-options-scroll-neverhide"
                        class="cnb_toggle_state cnb_toggle_false">(Inactive)</span>
                <span data-cnb_toggle_state_label="cnb-button-options-scroll-neverhide"
                        class="cnb_toggle_state cnb_toggle_true">Active</span>
                <p class="description">Once this Button is revealed, it will not be hidden again.</p>
            </td>
        </tr>
        <?php
    }

    /**
     * @param $button CnbButton
     *
     * @return CnbAction
     */
    private function get_action( $button ) {
        $action = new CnbAction();
        // Create a dummy Action
        $action->id          = 'new';
        $action->actionType  = '';
        $action->actionValue = '';
        $action->labelText   = '';
        $action->properties  = new CnbActionProperties();
        // If there is a real one, use that one
        if ( sizeof( $button->actions ) > 0 ) {
            $action = $button->actions[0];
        }

        return $action;
    }
}
