<?php

namespace cnb\admin\gettingstarted;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\CnbHeaderNotices;

class GettingStartedView {
    public function render() {

        wp_enqueue_style( CNB_SLUG . '-styling' );
        wp_enqueue_script( CNB_SLUG . '-premium-activation' );

        // Create link to the regular legacy page
        $url      = admin_url( 'admin.php' );
        $link =
            add_query_arg(
                array(
                    'page'   => 'call-now-button',
                ),
                $url );
        ?>
        <style>
            @font-face {
            font-family: 'CNB Icons';
            font-style: normal;
            font-weight: 400;
            src: url("<?php echo esc_url(plugins_url('resources/font/cnb.woff', CNB_PLUGINS_URL_BASE)) ?>") format('woff2');
            }
            .cnb-font-icon i {
            font-family: 'CNB Icons';
            outline: none;
            font-style: normal;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            -webkit-font-feature-settings: 'liga';
            font-size:250%;
            }
            .action-item {
            display: inline-block;
            margin: 10px;
            }
        </style>
        <div class="cnb-welcome-page">
            <div class="cnb-welcome-blocks cnb-extra-top">

            <img class="cnb-logo" src="<?php echo esc_url(plugins_url('resources/images/icon-256x256.png', CNB_PLUGINS_URL_BASE))?>" width="128" height="128" alt="Call Now Button icon" />
            <h1>Welcome to Call Now Button</h1>
            <h3>Thank you for choosing Call Now Button - The web's most popular click-to-call button</h3>
            <div class="cnb-block cnb-signup-box">
                <br>
            <?php echo CnbHeaderNotices::cnb_settings_email_activation_input(); // phpcs:ignore WordPress.Security ?>
            </div>
            <div class="cnb-divider"></div>
            <p>Or click <a href="<?php echo esc_url( $link ) ?>">here</a> to continue without an account.</p>
            <div class="cnb-divider"></div>
            <br>
            <h2>✨ Connect with NowButtons.com to enable more actions: ✨</h2>
                <div class="cnb-block">

                    <div style="line-height:1.9">
                    <div class="action-item cnb-font-icon"><i style="color:#25d366">whatsapp</i><br>WhatsApp</div>
                    <div class="action-item cnb-font-icon"><i style="color:#1778f2">facebook_messenger</i><br>Messenger</div>
                    <div class="action-item cnb-font-icon"><i style="color:#0088cc">telegram</i><br>Telegram</div>
                    <div class="action-item cnb-font-icon"><i style="color:#3A76F0">signal</i><br>Signal</div>

                    <div class="action-item cnb-font-icon"><i style="color:#0078d7">skype</i><br>Skype</div>
                    <div class="action-item cnb-font-icon"><i style="color:#59267c">viber</i><br>Viber</div>
                    <div class="action-item cnb-font-icon"><i style="color:#06c755">line</i><br>Line</div>
                    <div class="action-item cnb-font-icon"><i style="color:#0573ff">zalo</i><br>Zalo</div>
                    <div class="action-item cnb-font-icon"><i style="color:#7bb32e">wechat</i><br>WeChat</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">call</i><br>Phone</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">chat</i><br>SMS/Text</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">email</i><br>Email</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">directions</i><br>Location</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">link</i><br>URLs</div>
                    <div class="action-item cnb-font-icon"><i style="color:#090">anchor</i><br>Scroll to Point</div><br>
                    </div>

                    <br>
                    <h2>...and enable more features!</h2>
                    <br>


                    <h3>🆕 4 extra buttons</h3>
                    <p>Get 5 buttons instead of 1</p>
                    <h3>🖥️ All devices</h3>
                    <p>Desktop/laptop and mobile support</p>
                    <h3>🎯 Display rules</h3>
                    <p>Create smarter rules for your buttons to appear</p>

                </div>
                <div class="cnb-block cnb-signup-box">
                <br>
                <h2>Sign up now to enable all of this for free</h2>
                <?php echo CnbHeaderNotices::cnb_settings_email_activation_input(); // phpcs:ignore WordPress.Security ?>
                </div>
            </div>
            <div class="cnb-welcome-blocks cnb-welcome-blocks-plain">
                <div class="cnb-block">
                <p><i>Only need a Call button? <a href="<?php echo esc_url( $link ) ?>">Continue without an account</a>.</i></p>
                </div>
            </div>
            <div class="cnb-welcome-blocks">
            <div class="cnb-block">
                <h1>Why do I need an account?</h1>
                <h3>With an account you enable the cloud features from nowbuttons.com.</h3>
                <p>Once you've signed up you directly have access to the features described above. <strong>Completely FREE!</strong></p>
                <div class="cnb-block cnb-signup-box">
                <?php echo CnbHeaderNotices::cnb_settings_email_activation_input(); // phpcs:ignore WordPress.Security ?>
                </div>
            </div>
            </div>
            <div class="cnb-welcome-blocks">
            <div class="cnb-block">
                <h1>Upgrade to PRO to get even more!</h1>


                <br>
                <h2>🎁 Icon selection with each action 🎁</h2>
                    <img class="cnb-width-80 cnb-extra-space" src="<?php echo esc_url(plugins_url('resources/images/cnb-icons-actions.png', CNB_PLUGINS_URL_BASE)) ?>" alt="WhatsApp modal">

                <div class="cnb-divider"></div>

                <h2>💬 Add WhatsApp Chat to your website 💬</h2>
                <img src="<?php echo esc_url(plugins_url('resources/images/whatsapp-modal.png', CNB_PLUGINS_URL_BASE))?>" alt="WhatsApp modal">
                <p>Start the WhatsApp conversation on your website.</p>

                <div class="cnb-divider"></div>

                <h2>💎 Multibutton 💎</h2>
                <img class="cnb-width-80" src="<?php echo esc_url(plugins_url('resources/images/multibutton.png', CNB_PLUGINS_URL_BASE))?>" alt="Multibutton">
                <p>Takes up little space but reveals a treasure of options.</p>

                <div class="cnb-divider"></div>

                <h2>✨ Buttonbar ✨</h2>
                <img class="cnb-width-80" src="<?php echo esc_url(plugins_url('rresources/images/buttonbar.png', CNB_PLUGINS_URL_BASE))?>" alt="Buttonbar">
                <p>Create a web app experience on your website.</p>

                <div class="cnb-divider"></div>

                <h2>🕘 The scheduler 🕔</h2>
                <img src="<?php echo esc_url(plugins_url('resources/images/button-scheduler.png', CNB_PLUGINS_URL_BASE))?>" alt="The scheduler">
                <p>Control exactly when your buttons are displayed. Maybe a call button during business hours and a mail buttons when you're closed.</p>

                <br>
                <h2>Plus...</h2>
                <div class="cnb-center">
                    <h3>🌼 More button types</h3>
                    <h3>📄 Slide-in content windows</h3>
                    <h3>📷 Use custom images on buttons</h3>
                    <h3>🌍 Include and exclude countries</h3>
                    <h3>↕️ Set scroll height for buttons to appear</h3>
                    <h3>🔌 Intercom Chat integration</h3>
                </div>
                <h2>...and much more!</h2>
                </div>
            </div>

        </div>
        <div class="cnb-welcome-blocks">
            <div class="cnb-block cnb-signup-box">
            <h2>Create your free account and supercharge your Call Now Button.</h2>
            <?php echo CnbHeaderNotices::cnb_settings_email_activation_input(); // phpcs:ignore WordPress.Security ?>
            </div>
        </div>

        <div class="cnb-welcome-blocks cnb-welcome-blocks-plain">
            <div class="cnb-block cnb-signup-box">
            <p><i>Only need a Call button? <a href="<?php echo esc_url( $link ) ?>">Continue without an account</a>.</i></p>
        </div>
        </div>

    <?php  }
}
