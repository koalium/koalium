<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\models\CnbPlan;
use cnb\admin\models\CnbUser;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbDomainViewUpgradeOverview {

    /**
     * @param $user CnbUser|WP_Error
     *
     * @return string|null
     */
    private function get_active_currency( $user ) {
        $active_currency = null;
        if ( $user && ! is_wp_error( $user ) && isset( $user->stripeDetails ) && ! empty( $user->stripeDetails->currency ) ) {
            $active_currency = $user->stripeDetails->currency;
        }

        return $active_currency;
    }

    /**
     * Render upgrade form
     *
     * @param $domain CnbDomain
     *
     * @return void
     */
    function render( $domain ) {
	    wp_enqueue_script( CNB_SLUG . '-tally' );
	    wp_enqueue_script( CNB_SLUG . '-settings' );

        global $cnb_plans, $cnb_user;

        $cnb_utils = new CnbUtils();
        if ( $domain->type === 'PRO' ) {
            ?><p>Your domain is currently on the <code><?php echo esc_html( $domain->type ) ?></code> plan.</p><?php
        }

        $this->render_payment_cancelled_message();

        $this->render_coupon();
        $upgrade_msg = $cnb_utils->get_query_val( 'upgrade' );
        ?>

        <div class="cnb-welcome-blocks">
            <?php if ( $upgrade_msg === 'success?payment=cancelled' ) { ?>
                <h1>Yearly billing gives you <b>3 months free</b> each year!</h1>
            <?php } else { ?>
                <h1>Ready to turn your website into a conversion engine?</h1>
            <?php } ?>
            <h2>Upgrade to PRO and unlock everything NowButtons has to offer</h2>
            <br>
            <p id="domain_status" class="cnb_advanced_view"></p>
            <?php
            $this->render_upgrade_form( $domain, '-comparison-top' );
            ?>
            <p>Looking for a domain bundle deal? <a href="#domain_bundle">Click here</a>.</p>
        </div>
        <div class="cnb-welcome-blocks">
            <?php
            $this->render_pro_features_nice_view();
            ?>
            <br><br>
            <?php
            $this->render_pro_features_extras();
            ?>
            <br>
        <?php
            $planTrial = $this->get_plan( $cnb_plans, 'powered-by-eur-yearly' );            
            if ( $planTrial->trialPeriodDays && $planTrial->trialPeriodDays > 0 ) { ?>
            <h1>Select a plan and try it <b>14 days for free</b>!</h1>
        <?php } else { ?>
            <h1>Select your preferred payment interval</h1>
        <?php } ?>

            <br>
            <?php
            $this->render_upgrade_form( $domain );
            ?>
            <p>Need a PRO license for more than 10 websites? Get a <a href="#domain_bundle">PRO Account</a>!</p>
        </div>
        <div class="cnb-welcome-blocks">
            <h1>All PRO features</h1>
            <h3>An overview of the features that are included in the PRO plan.</h3>
            <?php
            $this->render_pro_feature_comparison();
            ?>
            <br>
        <?php if ( $planTrial->trialPeriodDays && $planTrial->trialPeriodDays > 0 ) { ?>
            <h1>Ready to give PRO a try?</h1>            
        <?php } else { ?>
            <h1>Yearly billing gives you <b>3 months free</b> each year!</h1>            
        <?php } ?>
            <br>
            <?php
            $this->render_upgrade_form( $domain, '-comparison-bottom' );
            ?>
        </div>
        <?php
        $active_currency = $this->get_active_currency( $cnb_user );
        $pro_account_blocks = new CnbProAccountBlocks( $active_currency );
        $pro_account_blocks->render_pro_account_block();
        ?>
        <br><br>
        <div class="cnb-message notice"><p class="cnb-error-message"></p></div>
        <?php
    }

    private function render_coupon() {
        global $cnb_coupon;
        $cnb_utils        = new CnbUtils();
        $notshowingcoupon = $cnb_utils->get_query_val( 'notshowingcoupon' );
        if ( $notshowingcoupon ) { // hiding this block for now
            if ( $cnb_coupon != null && ! is_wp_error( $cnb_coupon ) ) { ?>
                <div class="cnb-welcome-blocks ">
                    <div class="cnb-coupon-details">
                        <h5>USE COUPON <code class="cnb-coupon-code"><?php echo esc_html( $cnb_coupon->code ); ?></code> FOR
                            EXTRA <?php echo esc_html( $cnb_coupon->get_discount() ); ?> DISCOUNT</h5>
                        <p>Add coupon code <code class="cnb-coupon-code"><?php echo esc_html( $cnb_coupon->code ); ?></code>
                            during checkout for an extra
                            <strong><?php echo esc_html( $cnb_coupon->get_discount() ); ?></strong>
                            off <?php echo esc_html( $cnb_coupon->get_period() ); ?> <?php echo esc_html( $cnb_coupon->get_plan() ); ?>
                            .</div>
                    <?php if ( $cnb_coupon->redeemByDate ) { ?>
                        <div class="cnb_align_right cnb-coupon-timer" id="cnb-coupon-expiration-countdown"
                            data-coupon-expiration-time="<?php echo esc_attr( $cnb_coupon->redeemBy ); ?>">
                            Coupon expires in <?php echo esc_html( $cnb_coupon->get_redeem_by() ) ?>
                        </div>
                    <?php } else { ?>
                        <div class="cnb_align_right cnb-coupon-timer">&nbsp;</div>
                    <?php } ?>
                </div>
                <?php
            }
        }
    }

    private function render_payment_cancelled_message() {
        $cnb_utils   = new CnbUtils();
        $upgrade_msg = $cnb_utils->get_query_val( 'upgrade' );
        if ( $upgrade_msg === 'success?payment=cancelled' ) { ?>
            <div class="cnb-welcome-blocks ">
                <h2 class="cnb-left">Payment cancelled</h2>
                <p class="cnb-left">Please let us know if you have any questions or if there's anything we should add or improve.</p>
                <iframe data-tally-src="https://tally.so/embed/mBEj51?alignLeft=1&hideTitle=1&transparentBackground=1&dynamicHeight=1" loading="lazy" width="100%" height="233" frameborder="0" marginheight="0" marginwidth="0" title="Abandoned checkout"></iframe>
                <script>jQuery(function () {cnb_show_tally_abandoned_checkout()})</script>
            </div>
        <?php }
    }

    private function render_js_to_hide_currency( $user ) {
        $active_currency = $this->get_active_currency( $user );
        if ( $active_currency ) {
            // We already know the currency, so a "select currency" tab menu makes no sense
            echo '<script>';
            echo "jQuery(() => { jQuery('.nav-tab-wrapper').hide(); })";
            echo '</script>';
        }
    }

    /**
     * @param $domain CnbDomain
     * @param $additional_id_value string. Default is 0 but required for the toggle if more occurrences on the same page (ID should be unique)
     *
     * @return void
     */
    public function render_upgrade_form( $domain, $additional_id_value = '' ) {
        global $cnb_user, $cnb_plans;

        $this->render_js_to_hide_currency( $cnb_user );
        $active_currency = $this->get_active_currency( $cnb_user );
        ?>

        <?php if ( ! $active_currency ) { ?>
            <div class="cnb-currency-toggle">
                <span class="cnb_currency_active cnb_currency_active_eur" style="font-weight:bold">EUR</span>
                <input id="cnb-currency-toggle<?php echo esc_attr( $additional_id_value ) ?>"
                        class="cnb-currency-toggle-cb cnb_toggle_checkbox" name="currency" type="checkbox"
                        value="usd"/>
                <label for="cnb-currency-toggle<?php echo esc_attr( $additional_id_value ) ?>" class="cnb_toggle_label">Toggle</label>
                <span style="display: inline-block; margin-left: 4px;"
                        class="cnb_currency_active cnb_currency_active_usd">USD</span>
            </div>
        <?php } ?>
        <form class="wp_domain_upgrade" method="post">
            <input type="hidden" name="cnb_domain_id" id="cnb_domain_id" value="<?php echo esc_attr( $domain->id ) ?>">

            <div class="cnb-price-plans">
                <div class="currency-box currency-box-eur cnb-flexbox" style="<?php if ( $active_currency === 'usd' ) {
                    echo 'display:none';
                } ?>">
                    <?php
                    $plan_year         = $this->get_plan( $cnb_plans, 'powered-by-eur-yearly' );
                    $plan_year_monthly = $plan_year->price / 12;
                    $plan_x            = floor( $plan_year_monthly );
                    $plan_y            = round( ( $plan_year_monthly ) - floor( $plan_year_monthly ), 2 ) * 100;
                    ?>

                    <div class="cnb-pricebox cnb-currency-box currency-box-active">

                        <h3 class="cnb-price cnb-price-eur">Yearly billing</h3>

                        <div class="plan-amount"><span class="currency">€</span><span
                                    class="euros"><?php echo esc_html( $plan_x ) ?></span><span
                                    class="cents">.<?php echo esc_html( $plan_y ) ?></span><span class="timeframe">/month</span>
                        </div>
                        <div class="billingprice">
                            <span class=""><b>Billed &euro;<?php echo esc_html( number_format( $plan_year->price, 2, '.', '' ) ); ?> every 12 months</b><br>Subscription applies to current website.<br>VAT may apply</span>
                        </div>

                        <?php if ( $plan_year->trialPeriodDays && $plan_year->trialPeriodDays > 0 ) { ?>
                            <a class="button button-primary button-upgrade powered-by-eur-yearly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan_year->id ) ?>')">Try <?php echo esc_html( $plan_year->trialPeriodDays ) ?>
                                days free</a>
                        <?php } else { ?>
                            <a class="button button-primary button-upgrade powered-by-eur-yearly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan_year->id ) ?>')">Upgrade now</a>
                        <?php } ?>
                    </div>
                    <?php
                    $plan   = $this->get_plan( $cnb_plans, 'powered-by-eur-monthly' );
                    $plan_x = floor( $plan->price );
                    $plan_y = round( ( $plan->price ) - floor( $plan->price ), 2 ) * 100;
                    ?>
                    <div class="cnb-pricebox cnb-currency-box currency-box-active">
                        <h3 class="cnb-price cnb-price-usd">Monthly billing</h3>

                        <div class="plan-amount"><span class="currency">&euro;</span><span
                                    class="euros"><?php echo esc_html( $plan_x ) ?></span><span
                                    class="cents">.<?php echo esc_html( $plan_y ) ?></span><span class="timeframe">/month</span>
                        </div>
                        <div class="billingprice">
                            <span class="">Billed monthly.<br>Subscription applies to current website.<br>VAT may apply</span>
                        </div>

                        <?php if ( $plan_year->trialPeriodDays && $plan_year->trialPeriodDays > 0 ) { ?>
                            <a class="button button-primary button-upgrade powered-by-eur-monthly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan->id ) ?>')">Try <?php echo esc_html( $plan_year->trialPeriodDays ) ?>
                                days free</a>
                        <?php } else { ?>
                            <a class="button button-primary button-upgrade powered-by-eur-monthly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan->id ) ?>')">Upgrade now</a>
                        <?php } ?>
                    </div>

                </div>
                <div class="currency-box currency-box-usd cnb-flexbox"
                    style="<?php if ( $active_currency !== 'usd' ) { ?>display:none<?php } ?>">
                    <?php
                    $plan_year         = $this->get_plan( $cnb_plans, 'powered-by-usd-yearly' );
                    $plan_year_monthly = $plan_year->price / 12;
                    $plan_x            = floor( $plan_year_monthly );
                    $plan_y            = round( ( $plan_year_monthly ) - floor( $plan_year_monthly ), 2 ) * 100;
                    ?>

                    <div class="cnb-pricebox cnb-currency-box currency-box-active">
                        <h3 class="cnb-price cnb-price-eur">Yearly billing</h3>

                        <div class="plan-amount"><span class="currency">$</span><span
                                    class="euros"><?php echo esc_html( $plan_x ) ?></span><span
                                    class="cents">.<?php echo esc_html( $plan_y ) ?></span><span class="timeframe">/month</span>
                        </div>
                        <div class="billingprice">
                            <span>Billed $<?php echo esc_html( number_format( $plan_year->price, 2, '.', '' ) ); ?> every 12 months.<br>Subscription applies to current website.<br>VAT may apply</span>
                        </div>

                        <?php if ( $plan_year->trialPeriodDays && $plan_year->trialPeriodDays > 0 ) { ?>
                            <a class="button button-primary button-upgrade powered-by-usd-yearly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan_year->id ) ?>')">Try <?php echo esc_html( $plan_year->trialPeriodDays ) ?>
                                days free</a>
                        <?php } else { ?>
                            <a class="button button-primary button-upgrade powered-by-usd-yearly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan_year->id ) ?>')">Upgrade now</a>
                        <?php } ?>
                    </div>

                    <?php
                    $plan   = $this->get_plan( $cnb_plans, 'powered-by-usd-monthly' );
                    $plan_x = floor( $plan->price );
                    $plan_y = round( ( $plan->price ) - floor( $plan->price ), 2 ) * 100;
                    ?>
                    <div class="cnb-pricebox cnb-currency-box currency-box-active">
                        <h3 class="cnb-price cnb-price-usd">Monthly billing</h3>

                        <div class="plan-amount"><span class="currency">$</span><span
                                    class="euros"><?php echo esc_html( $plan_x ) ?></span><span
                                    class="cents">.<?php echo esc_html( $plan_y ) ?></span><span class="timeframe">/month</span>
                        </div>
                        <div class="billingprice">
                            <span class="">Billed monthly.<br>Subscription applies to current website.<br>VAT may apply</span>
                        </div>

                        <?php if ( $plan_year->trialPeriodDays && $plan_year->trialPeriodDays > 0 ) { ?>
                            <a class="button button-primary button-upgrade powered-by-usd-monthly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan->id ) ?>')">Try <?php echo esc_html( $plan_year->trialPeriodDays ) ?>
                                days free</a>
                        <?php } else { ?>
                            <a class="button button-primary button-upgrade powered-by-usd-monthly" href="#"
                                onclick="cnb_get_checkout('<?php echo esc_js( $plan->id ) ?>')">Upgrade now</a>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </form>
        <?php
    }

    /**
     * @return void
     */
    public function render_pro_features_nice_view() {
        global $cnb_plans;

        $plan_year = $this->get_plan( $cnb_plans, 'powered-by-usd-yearly' ); ?>

        <div class="cnb-block">

            <?php if ( $plan_year->trialPeriodDays && $plan_year->trialPeriodDays > 0 ) { ?>
                <h1>Try <b>PRO</b> <?php echo esc_html( $plan_year->trialPeriodDays ) ?> days for FREE!</h1>
                <h3>Upgrade today and try out all professional
                    features <?php echo esc_html( $plan_year->trialPeriodDays ) ?> days for free!</h3>
            <?php } else { ?>
                <h1><b>Upgrade to PRO</b> and enjoy everything NowButtons has to offer!</h1>
            <?php } ?>


            <br>
            <h2>The scheduler</h2>
            <img src="<?php echo esc_url( plugins_url( 'resources/images/button-scheduler.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="The scheduler">
            <p>Control exactly when your buttons are displayed. Maybe a call button during business hours and a mail
                buttons when you're closed.</p>
            <div class="cnb-divider"></div>
            <h2>Icon selection with each action</h2>
            <img class="cnb-width-80 cnb-extra-space"
                src="<?php echo esc_url( plugins_url( 'resources/images/cnb-icons-actions.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="WhatsApp modal">
            <p>Pick the icon that works best for your situation. And if you want to be completely original, you can use your own image as well.</p>

            <div class="cnb-divider"></div>

            <h2>Add WhatsApp Chat to your website</h2>
            <img src="<?php echo esc_url( plugins_url( 'resources/images/whatsapp-modal.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="WhatsApp modal">
            <p>Start the WhatsApp conversation on your website.</p>

            <div class="cnb-divider"></div>

            <h2>Multibutton</h2>
            <img class="cnb-width-80"
                src="<?php echo esc_url( plugins_url( 'resources/images/multibutton.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="Multibutton">
            <p>Takes up little space but reveals a treasure of options. Add an infinite number of actions to the
                Multibutton.</p>

            <div class="cnb-divider"></div>

            <h2>Buttonbar</h2>
            <img class="cnb-width-80"
                src="<?php echo esc_url( plugins_url( 'resources/images/buttonbar.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="Buttonbar">
            <p>Create a web app experience on your website with the Buttonbar. Add up to 5 actions to the Buttonbar that
                sits fixed at the bottom or top of your page.</p>

            <div class="cnb-divider"></div>

            <h2>Button Flower</h2>
            <img class="cnb-width-80"
                src="<?php echo esc_url( plugins_url( 'resources/images/button-flower.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="Buttonbar">
            <p>The Button Flower is a circular spread of buttons around a center button. It unfolds when it's pressed.<br>Delicate and unintrusive while making your website so much more fun and easy to navigate.</p>

            <div class="cnb-divider"></div>

            <h2>Button Dots</h2>
            <img class="cnb-width-80"
                src="<?php echo esc_url( plugins_url( 'resources/images/dots.png', CNB_PLUGINS_URL_BASE ) ); ?>"
                alt="Buttonbar">
            <p>No words, just clear icons that guide your visitors exactly where they need to go. Increase conversions while boosting your site's UX.</p>

        </div>
    <?php }

    public function render_pro_features_extras() {
        ?>
        <div class="cnb-block">
            <h2>Plus...</h2>
            <div class="cnb-center">
                <div class="cnb-pro-tile">
                    <h3>👋 Button animations</h3>
                    <p>Add an extra animation effect to your button to draw more attention to it.</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>📄 Slide-in content windows</h3>
                    <p>Add any content to your slide-in window. E.g. a form, quick links, a YouTube video, etc.</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>📷 Use custom images on buttons</h3>
                    <p>Freedom to use your own image on a button. E.g. add a headshot to your contact button to make it
                        more personal.</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>👮 More advanced Display rules</h3>
                    <p>Create more sophisticated Display rules with RegEx rules and parameter filtering (e.g. for ad
                        campaigns).</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>🌍 Include and exclude countries</h3>
                    <p>Show different contact details depending on the visitor's location.</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>↕️ Set scroll height for buttons to appear</h3>
                    <p>Don't want to distract people from your hero section? Set a scroll height so your buttons appear
                        when a user has scrolled past it.</p>
                </div>
                <div class="cnb-pro-tile">
                    <h3>🔌 Intercom Chat integration</h3>
                    <p>Intercom customers can use our buttons, animations and scheduler to fire the Intercom chat
                        window.</p>
                </div>
            </div>
            <h2>...and much more!</h2>
        </div>
        <?php
    }

    public function render_pro_feature_comparison() {
        ?>
        <style>
            tr.cnb-starter {
                display: none;
            }

            td.cnb-starter, th.cnb-starter {
                display: none;
            }

            table.cnb-nb-plans tbody th {
                font-size: 15px;
                padding: 3px 0;
            }

            table.cnb-nb-plans tbody th .cnb-tooltip-icon {
                font-size: 13px;
            }

            .cnbShowStarterFeatures {
                cursor: pointer;
            }
        </style>
        <div class="cnb-block">

            <table class="cnb-nb-plans">
                <thead>
                <tr>
                    <td></td>
                    <th class="cnb-starter"><h3>Starter</h3></th>
                    <th><h3>Pro</h3></th>
                </tr>
                </thead>
                <tbody>
                <tr class="line">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>No. of buttons</th>
                    <td class="value cnb-starter">5</td>
                    <td class="value">100</td>
                </tr>
                <tr class="line cnb-starter">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr class="cnb-starter">
                    <th>Phone</th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>SMS/Text</th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Email</th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Maps
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            The button will open the Google Maps or Apple Maps app to show the location or prompt for
                            the input of an origin to give travel directions.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>URLs
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Create buttons for your most important CTAs.<br><br>E.g. a link to your signup or contact form.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Scroll to point
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Scroll to point enables you to create a button that triggers a smooth scroll through the
                            page to a selected point.<br><br>E.g. a Back-to-top button
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>WhatsApp
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A WhatsApp button that starts a conversation with you directly in the WhatsApp app.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Messenger
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Messenger app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Telegram
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Telegram app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Signal
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Signal app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Skype
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Skype app and enables a chat or text conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Viber
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Viber app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>WeChat
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the WeChat app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Line
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that opens the Line app and starts a conversation with you.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="line">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr class="cnb-starter">
                    <th>Mobile + Desktop
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Buttons can be displayed on all screen sizes.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Click tracking in GA
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            With a single click you can enable event tracking in Google Analytics to get insights into
                            button engagement on your website.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Google Ads conversion tracking
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Enable conversion tracking on your Google Ads landing pages so when a paid visitor clicks on
                            the button it's measured as a conversion.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Live preview
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Displays a real-time preview of the buttons your building. PRO can simulate the day and time
                            to test scheduled buttons.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="line cnb-starter">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr class="cnb-starter">
                    <th>Single button</th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Buttonbar (single action)
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            The Buttonbar is a bar of one or more buttons that sits at the top or bottom of your screen.
                            In the Starter plan the Buttonbar only offers a single action.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Buttonbar (multi-action)
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            The multi-action Buttonbar can hold up to 5 actions.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Multibutton
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            The Multibutton shows a single circular button but expands into multiple buttons when
                            clicked.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Button Dots
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Button dots is a row of circular buttons that can be placed in any of the 8 screen positions.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Button Flower
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            The Button Flower shows a single circular button but expands playfully into multiple buttons (resembling a flower) when clicked.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Multiple buttons per page
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            PRO users can add up to 8 buttons on a single page.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Change button icons</th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Custom button images
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Instead of an icon you can add your own image to your button.<br><br>E.g. add a headshot of
                            the person answering the phone to make it more personal.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Button animations
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Draw more attention to your buttons by adding delicate animations.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>

                <tr class="line">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>WhatsApp Chat window
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            A button that fires a WhatsApp chat window right on your website where you can greet the
                            visitor. You can automate multiple speech bubbles which appear in sequence.<br>The WhatsApp
                            app is opened once the visitor engages in the conversation.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Intercom chat (integration)
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Create a button to open the Intercom chat window. This way the Intercom button will match
                            nicely with your other buttons and allows you to combine it with the scheduler. You can also
                            place it inside a Multibutton or Buttonbar.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Content Windows
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Opened by a button, the Content Window enables you to add any content to a small window that
                            slides into the screen.<br><br>A great use-case is showing a booking form without sending
                            visitors off the page.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Tally Form window
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            With this integration you only need a form ID to place a Tally contact form inside a
                            slide-in Content Window.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="line">
                    <td>&nbsp;</td>
                    <td class="cnb-starter"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Scheduler
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Schedule your buttons to appear on the right days at the right time.<br>The scheduler can
                            be used on each individual action, so you can change the contents of multi-action buttons
                            throughout the
                            day.<br><br>E.g. a phone button during working hours and a contact form when you're closed.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Appear after scrolling
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Set the number of pixels your visitors have to scroll down before the button
                            appears.<br><br>E.g. combine it with the Scroll-to-point action to create a back-to-top
                            button.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr class="cnb-starter">
                    <th>Display rules (Basic)
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Create simple rules for where your buttons should appear. They can match an exact url, a
                            path or if the url contains a string.
                        </div>
                    </th>
                    <td class="yes cnb-starter">✓</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Display rules (Advanced)
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            With the advanced display rules you can set rules on parameters (great in combination with
                            PPC campaigns) and use RegEx to create even more powerful rules.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                <tr>
                    <th>Geo targeting
                        <span class="cnb-tooltip-icon">?</span>
                        <div class="cnb-tooltip-text">
                            Show the right contact options to the right people. With Geo display rules you tailor the
                            buttons to the visitor's country.
                        </div>
                    </th>
                    <td class="cnb-starter">𐄂</td>
                    <td class="yes">✓</td>
                </tr>
                </tbody>
            </table>
            <p><a class=" button cnbShowStarterFeatures" onclick="cnbShowStarterFeatures()">Compare against features
                    included in starter</a></p>
        </div>
        <?php
    }

    /**
     * @param $plans CnbPlan[]
     * @param $name string
     *
     * @return CnbPlan|null
     */
    private function get_plan( $plans, $name ) {
        foreach ( $plans as $plan ) {
            if ( $plan->nickname === $name ) {
                return $plan;
            }
        }

        return null;
    }
}
