<?php

namespace cnb\admin\legacy;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\CnbHeaderNotices;

class CnbLegacyUpgrade {
    function header() {
        echo 'Unlock more features';
    }

    private function feature_comparison_free_promobox() {
        ?>
        <style>
            @font-face {
            font-family: 'CNB Icons';
            font-style: normal;
            font-weight: 400;
            src: url("<?php echo esc_url(plugins_url( 'resources/font/cnb.woff', CNB_PLUGINS_URL_BASE)) ?>") format('woff2');
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
            }
            .cnb-compare-features,
            .cnb-signup-block-fixed .cnb-signup-block {
            max-width: 800px;
            }
            .benefit-number {
            /* padding-bottom: 10px; */
            box-sizing: border-box;
            min-height: 32px;
            font-weight: 400
            }
            .benefit-number,
            .cnb-font-icon i,
            .cnb-font-icon span {
            font-size: 32px;
            }
            .cnb-font-icon .dashicons {
            width: auto;
            height: auto;
            font-size: 26px;
            }
            .benefit-section {
            display: flex;
            justify-content: left;
            flex-wrap: wrap;

            }
            .benefit-box {
            width: 70px;
            min-width:70px;
            height: 80px;
            margin: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5px;
            border-radius: 5px;
            border:3px solid rgba(0,131,0,1);
            text-align: center;
            background:  
                rgb(0,170,0);
            background: linear-gradient(120deg, rgba(0,170,0,0.8533788515406162) 0%, rgba(0,131,0,1) 100%);
            color:#fff;
            font-size: 14px;
            font-weight: 400;
            text-shadow: -1px -1px 1px rgba(0,0,0,0.3);
            box-shadow: 
                rgba(50, 50, 50, 0.25) 0px 13px 27px -5px, 
                rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;

            }
            .wrap.call-now-button-plugin {
            font-family: BlinkMacSystemFont,-apple-system,Segoe UI,Roboto, Helvetica, Arial,sans-serif;
            }
            @media screen and (max-width:499px) {
            .benefit-box {
                margin: 2px;
            }
            .cnb-not-on-mobile {
                display: none;
            }
            .cnb-signup-block-fixed h3 {
                margin-top:0;
            }
            .cnb-signup-block-fixed p.nonessential {
                margin:0;
            }
            .cnb-compare-features .cnb-nb-plans th h3 {
                font-size: 14px;
            }
            input.cnb_activation_input_field ~ input[type=submit] {
                width: 100%;
            }
            }
            @media screen and (min-width:500px) {
            .call-now-button-plugin h1 {
                font-size: 60px;
                line-height: 1.2;
                font-weight: 600;
                margin: 0;
            }
            .cnb-compare-features h2 {
                font-size: 40px;
                line-height: 1.25;
                font-weight: 600;
                margin: 0;
            }
            .cnb-compare-features h3 {
                font-size: 30px;
                font-weight: 600;
                margin: 0.5em 0;
            }   
            
            .cnb-compare-features p,
            .cnb-compare-features table tbody th {
                font-size:16.5px;
                font-weight: 300;
                line-height:1.4;
            }

            .cnb-body-content .benefits-section-signup form.cnb-container input[type=text] {
                max-width: calc(100% - 190px);
                font-size: 26px;
                box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
            } 

            .benefits-section-signup p.nonessential {
                font-size: 14px;
                padding-left: 0
            }   
            }
            .cnb-compare-features th h3 {
            font-size:18px;
            margin:1em 0;
            }
            .cnb-faq-section {
            max-width: 800px;
            padding-bottom:100px;
            }
            .cnb-faq-section h3 {
            font-size:18px;
            }
            .cnb-faq-section p {
            font-size:14px;
            }
            .cnb-signup-block-fixed {
            box-sizing: border-box;
            position: fixed;
            padding: 10px;
            background-color: #f0f0f1;
            bottom:0;
            z-index: 99;
            margin-left:-22px;
            box-shadow: 0 0 5px rgb(0 0 0 / 50%);
            border-radius: 5px 5px 0 0;
            }

            .cnb-signup-block-fixed {
            width:calc(100% - 160px);
            }
            .folded .cnb-signup-block-fixed {
            width:calc(100% - 36px);
            }
            @media only screen and (max-width: 960px) {
            .auto-fold .cnb-signup-block-fixed {
                width:calc(100% - 36px);
            }
            }
            @media screen and (max-width: 782px) {
            .cnb-signup-block-fixed,
            .auto-fold .cnb-signup-block-fixed {
                width:100%;
                margin-left:0px;
                left:0;
            }
            }

            .cnb-signup-block > p {
            margin-top: 0;
            }

            .cnb-compare-features .benefits-section-signup .button-primary {
            font-size: 20px;
            padding: 4px 10px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
            }
            @media screen and (max-width: 782px) {
            .cnb-compare-features .benefits-section-signup .button-primary {
                vertical-align:top;
                padding: 2px 10px;
            }
            }
            @media screen and (max-width: 499px) {
            .cnb-body-content .benefits-section-signup form.cnb-container input[type=text] {
                max-width: 100%;
                margin-bottom: 10px;
            }
            }
        </style>
        
        
        <div class="signup-block ">
            <h3>Sign up (it's free)</h3>
            <div class="cnb-signup-block">
            
            <div class="benefits-section-signup">
                <?php echo CnbHeaderNotices::cnb_settings_email_activation_input() // phpcs:ignore WordPress.Security ?>
            </div>
            </div>
        </div>
        <h3 class="top-50">More features</h3>
        <div class="benefit-section">
            <div class="benefit-box">
            <span class="benefit-number">15</span>
            <span class="benefit-name">Actions</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number">5</span>
            <span class="benefit-name">Buttons</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><span class="dashicons dashicons-clipboard"></span></span>
            <span class="benefit-name">Rules</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><span class="dashicons dashicons-visibility"></span></span>
            <span class="benefit-name">Preview</span>
            </div>
        </div>
        <h3>More actions</h3>
        <p style="margin-bottom: 0">NowButtons brings you a full suite of actions to boost the overall conversion rate of your website. Sign up now to enable the following actions:</p>
        <div class="benefit-section">
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>call</i></span>
            <span class="benefit-name">Call</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>whatsapp</i></span>
            <span class="benefit-name">WhatsApp</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>email</i></span>
            <span class="benefit-name">Email</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>directions</i></span>
            <span class="benefit-name">Maps</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>link2</i></span>
            <span class="benefit-name">Links</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>anchor</i></span>
            <span class="benefit-name">Scrolls</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>facebook_messenger</i></span>
            <span class="benefit-name">Messenger</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>telegram</i></span>
            <span class="benefit-name">Telegram</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>signal</i></span>
            <span class="benefit-name">Signal</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>sms</i></span>
            <span class="benefit-name">SMS/Text</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>zalo</i></span>
            <span class="benefit-name">Zalo</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>skype</i></span>
            <span class="benefit-name">Skype</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>line</i></span>
            <span class="benefit-name">Line</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>wechat</i></span>
            <span class="benefit-name">WeChat</span>
            </div>
            <div class="benefit-box">
            <span class="benefit-number cnb-font-icon"><i>viber</i></span>
            <span class="benefit-name">Viber</span>
            </div>
        </div>
        <div class="signup-block top-50">
            <h3>Sign up now</h3>
            <div class="cnb-signup-block">
            <p class="cnb-not-on-mobile"><strong>Enter your email below</strong> and hit <em>Create account</em> to activate NowButtons:</p>
            <div class="benefits-section-signup">
                <?php echo CnbHeaderNotices::cnb_settings_email_activation_input() // phpcs:ignore WordPress.Security ?>
            </div>
            </div>
        </div>
        
        <div class="cnb-body-column top-50">
          
            <h2>Feature comparison</h2>
            <table class="cnb-nb-plans">
            <thead>
                <tr>
                <td></td>
                <th class="cnb-prod-cnb" style="border-radius:5px 5px 0 0;"><h3>No account<br><span class="cnb-not-on-mobile" style="font-weight:normal;">(Currently active)</span></h3></th>
                <th class="cnb-prod-nb" style="border-radius:5px 5px 0 0;"><h3>With account<br><span style="font-weight:normal;">(NowButtons.com)</span></h3></th>
                </tr>
                <tr class="font-16">
                <th style="border-radius: 5px 0 0 5px;"></th>
                <th><h4>Free</h4></th>
                <th><h4>Free</h4></th>
                </tr>
            </thead>
            <tbody>
                <tr class="line"><td>&nbsp;</td><td></td><td></td></tr>
                <tr>
                <th>No. of buttons</th>
                <td class="value">1</td>
                <td class="value">5</td>
                </tr>
                <tr>
                <th>Single button</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Buttonbar (full width)</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr class="line"><td>&nbsp;</td><td></td><td></td></tr>
                <tr>
                <th>Phone</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>SMS/Text</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Email</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Maps</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>URLs</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Scroll to point</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>WhatsApp</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Messenger</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Telegram</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Signal</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Skype</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Viber</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Line</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Zalo</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>WeChat</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>

                <tr class="line"><td>&nbsp;</td><td></td><td></td></tr>
                <tr>
                <th>Mobile</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Desktop</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Limit appearance</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Display rules</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Click tracking in GA</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Google Ads conversion tracking</th>
                <td class="yes">✓</td>
                <td class="yes">✓</td>
                </tr>
                <tr>
                <th>Live preview</th>
                <td>𐄂</td>
                <td class="yes">✓</td>
                </tr>
                <tr class="line"><td>&nbsp;</td><td></td><td></td></tr>
            </tbody>
            </table>
          
            <div class="signup-block">
            <h3>Sign up now!</h3>
            <div class="cnb-signup-block">
                <p class="cnb-not-on-mobile"><strong>Enter your email below</strong> and hit <em>Create account</em> to activate NowButtons:</p>
                <div class="benefits-section-signup">
                <?php echo CnbHeaderNotices::cnb_settings_email_activation_input() // phpcs:ignore WordPress.Security ?>
                </div>
            </div>
            </div>
        </div>
    <?php }

    function upgrade_faq() {
        ?>
        <div class="cnb-faq-section">
            <h1>FAQ</h1>
            <h3>Is a NowButtons.com account really free?</h3>
            <p>Yes. NowButtons has a paid plan as well, however the features described above are all part of the Starter plan which is free. Enter your email above and click <b>Create account</b> to sign up and enable the extra features.</p>
            <h3>Is there a PRO plan?</h3>
            <p>Yes, NowButtons offers a PRO plan with many advanced feature for even more buttons and more control.</p>
            <h3>What's included in PRO?</h3>
            <p>PRO turns your website into a conversion machine. It adds a big collection of premium features such as scheduling, multi-action buttons, animations, WhatsApp Chat window, and much much more. Checkout <a href="<?php echo esc_url(CNB_WEBSITE . 'pricing/') ?>" target="_blank"><?php echo esc_html(CNB_WEBSITE) ?>pricing/</a> for a full features overview.</p>
            <h3>Why do I have to sign up for <?php echo esc_html(CNB_CLOUD_NAME); ?>?</h3>
            <p>NowButtons is a cloud service and can be added to any website. Even those that do not have a WordPress powered website. Once you've signed up, you can continue to manage your buttons from your WordPress instance, but you could also do this via the web app found at <a href="<?php echo esc_url(CNB_APP) ?>" target="_blank"><?php echo esc_html(CNB_APP) ?></a>.</p><p>And should you ever move to a different CMS, your button(s) will just move with you.</p>
        </div>
    <?php }

    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        wp_enqueue_script( CNB_SLUG . '-settings' );
        wp_enqueue_script( CNB_SLUG . '-premium-activation' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );
        do_action( 'cnb_header' );
        ?>

        <div class="cnb-one-column-section">
            <div class="cnb-body-content">
                <div class="cnb-compare-features">
                    <?php $this->feature_comparison_free_promobox() ?>
                    <hr style="margin:50px 0">
                    <?php $this->upgrade_faq() ?>
                </div>
            </div>
        </div>
        <hr>
        <?php
        do_action( 'cnb_footer' );
        do_action( 'cnb_finish' );
    }
}
