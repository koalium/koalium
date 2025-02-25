<?php

namespace FluentForm\App\Modules\Form;

use Cleantalk\Antispam\CleantalkRequest;
use FluentForm\Framework\Helpers\ArrayHelper;

class CleanTalkHandler
{
    public static function validate($accessKey)
    {
        $cleanTalkRequest = [
            'method_name' => 'check_newuser',
            'auth_key' => $accessKey,
            'sender_email' => wp_get_current_user()->user_email,
            'sender_ip' => wpFluentForm()->request->getIp(),
            'js_on' => 1,
            'submit_time' => time(),
            'sender_nickname' => wp_get_current_user()->user_login,
            'sender_info' => [
                'REFFERRER' => $_SERVER['HTTP_REFERER'],
                'USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
            ]
        ];

        $response = wp_remote_post(
            'https://moderate.cleantalk.org/api2.0',
            [
                'body'    => \json_encode($cleanTalkRequest, true),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        if (is_wp_error($response)) {
            return false;
        }

        $response = json_decode(wp_remote_retrieve_body($response));

        if ($response->allow == 1 && $response->inactive == 0 && $response->account_status == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function spamSubmissionCheckWithApi($formData, $form)
    {
        $accessKey = ArrayHelper::get(get_option('_fluentform_cleantalk_details'), 'accessKey');

        if (!$accessKey) {
            return false;
        }

        $cleanTalkRequest = [
            'method_name'     => 'check_message',
            'auth_key'        => $accessKey,
            'sender_ip'       => wpFluentForm()->request->getIp(),
            'event_token'     => hash('sha256', uniqid(mt_rand(), true)),
            'submit_time'     => time(),
            'sender_info'     => [
                'REFERRER'   => urlencode($formData['_wp_http_referer']),
                'USER_AGENT' => htmlspecialchars(@$_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'])
            ],
            'js_on'           => 1,
            'sender_nickname' => '',
            'sender_email'    => '',
            'message'         => '',
            'phone'           => ''
        ];

        $maps = [
            'input_name'  => 'sender_nickname',
            'input_email' => 'sender_email',
            'textarea'    => 'message',
            'phone'       => 'phone',
        ];
        
        $inputs = FormFieldsParser::getInputs($form, ['attributes']);

        foreach ($inputs as $input) {
            $element = ArrayHelper::get($input, 'element');
            $key = ArrayHelper::get($input, 'attributes.name');
            if (isset($maps[$element]) && !$cleanTalkRequest[$maps[$element]]) {
                $value = ArrayHelper::get($formData, $key);
                if ($value) {
                    if (is_array($value)) {
                        $value = implode(' ', $value);
                    }
                    $cleanTalkRequest[$maps[$element]] = $value;
                }
            }
        }

        $cleanTalkRequest = apply_filters('fluentform/cleantalk_fields', $cleanTalkRequest, $formData, $form);

        $response = wp_remote_post(
            'https://moderate.cleantalk.org/api2.0',
            [
                'body'    => json_encode($cleanTalkRequest),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        if (is_wp_error($response)) {
            return false;
        }

        $response = json_decode(wp_remote_retrieve_body($response));

        if ($response->allow == 1 && $response->spam == 0 && $response->account_status == 1) {
            return false;
        } else {
            return true;
        }
    }

    public static function isCleantalkActivated()
    {
        $settings = get_option('_fluentform_cleantalk_details');
        return $settings && ArrayHelper::get($settings, 'status');
    }
    
    public static function isEnabled()
    {
        if (!self::isPluginEnabled()) {
            return false;
        }

        $settings = get_option('_fluentform_global_form_settings');
        return $settings && 'yes' == ArrayHelper::get($settings, 'misc.cleantalk_status');
    }

    public static function isPluginEnabled()
    {
        $exists = method_exists('Cleantalk\Antispam\Cleantalk', 'isAllowMessage');
        if ($exists) {
            global $apbct;
            return !!$apbct->data['key_is_ok'];
        }
        return false;
    }

    public static function isSpamSubmission($formData, $form)
    {
        $cleanTalkRequest = self::getCleanTalkRequest($formData, $form);
        $cleanTalk = new \Cleantalk\Antispam\Cleantalk();
        $cleanTalk->server_url = 'https://moderate.cleantalk.org';
        $response = $cleanTalk->isAllowMessage($cleanTalkRequest);

        return 0 == $response->allow;
    }

    protected static function getCleanTalkRequest($data, $form)
    {
        global $apbct;
        $app = wpFluentForm();
        $ip = $app->request->getIp();
        
        $info = [
            'auth_key'             => $apbct->settings['apikey'],
            'sender_ip'            => $ip,
            'contact_form_subject' => $form->title,
            'referrer'             => urlencode($data['_wp_http_referer']),
            'page_url'             => htmlspecialchars(@$_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI']),
            'submit_time'          => isset($_SESSION['ct_submit_time']) ? time() - (int)$_SESSION['ct_submit_time'] : null, //@todo Improve this
            'agent'                => 'php-api',
            'js_on'                => 1,
            'sender_nickname'      => '',
            'sender_email'         => '',
            'message'              => '',
            'phone'                => ''
        ];

        $maps = [
            'input_name'  => 'sender_nickname',
            'input_email' => 'sender_email',
            'textarea'    => 'message',
            'phone'       => 'phone',
        ];
        $inputs = FormFieldsParser::getInputs($form, ['attributes']);

        foreach ($inputs as $input) {
            $element = ArrayHelper::get($input, 'element');
            $key = ArrayHelper::get($input, 'attributes.name');
            if (isset($maps[$element]) && !$info[$maps[$element]]) {
                $value = ArrayHelper::get($data, $key);
                if ($value) {
                    if (is_array($value)) {
                        $value = implode(' ', $value);
                    }
                    $info[$maps[$element]] = $value;
                }
            }
        }

        $info = apply_filters('fluentform/cleantalk_fields', $info, $data, $form);

        return new CleantalkRequest($info);
    }
}
