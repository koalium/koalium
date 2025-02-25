<?php

namespace cnb\admin\models;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbUser implements JsonSerializable {
    /**
     * @var string UUID of the User
     */
    public $id;

    /**
     * @var boolean
     */
    public $active;
    /**
     * @var string Name of the User
     */
    public $name;

    /**
     * Usually the same as admin_email
     *
     * @var string email address of the User
     */
    public $email;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var CnbUserAddress
     */
    public $address;

    /**
     * @var array{CnbUserTaxId}
     */
    public $taxIds = array();

    /**
     * @var CnbUserStripeDetails
     */
    public $stripeDetails;
    /**
     * transient variable (not sent to the API)
     * @var int
     */
    public $euvatbusiness;

    /**
     * @var CnbUserMarketingData
     */
    public $marketingData;
	/**
	 * @var array of roles
	 */
	public $roles;

	public function __construct() {
	}

	public function has_role( $role ) {
		return in_array( $role, $this->roles );
	}

    public function is_pro_user() {
        return $this->has_role( 'ROLE_PRO_USER' );
    }

	/**
     * If a stdClass is passed, it is transformed into a CnbUser.
     * a WP_Error is ignored and returned immediately
     * a null if converted into an (empty) CnbUser
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUser|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $user              = new CnbUser();
        $user->active      = CnbUtils::getPropertyOrNull( $object, 'active' );
        $user->id          = CnbUtils::getPropertyOrNull( $object, 'id' );
        $user->name        = CnbUtils::getPropertyOrNull( $object, 'name' );
        $user->email       = CnbUtils::getPropertyOrNull( $object, 'email' );
        $user->companyName = CnbUtils::getPropertyOrNull( $object, 'companyName' );
        $address           = CnbUserAddress::fromObject( CnbUtils::getPropertyOrNull( $object, 'address' ) );
        $user->address     = $address;
        $taxIds            = CnbUserTaxId::fromObject( CnbUtils::getPropertyOrNull( $object, 'taxIds' ) );
        $user->taxIds      = $taxIds;
        // This is only set via the form, but is used for some checks (but not submitted to the API)
        $user->euvatbusiness = CnbUtils::getPropertyOrNull( $object, 'euvatbusiness' );
        $stripeDetails       = CnbUserStripeDetails::fromObject( CnbUtils::getPropertyOrNull( $object, 'stripeDetails' ) );
        $user->stripeDetails = $stripeDetails;
        $marketing_data      = CnbUserMarketingData::fromObject( CnbUtils::getPropertyOrNull( $object, 'marketingData' ) );
	    $user->marketingData = $marketing_data;
	    $user->roles         = CnbUtils::getPropertyOrNull( $object, 'roles' );

        return $user;
    }

    public function toArray() {
        // Note:
	    // Do not export "euvatbusiness", since that is only used internally
	    // Do not export "roles", this is only used internally
        // Do not export "marketingData", since that is handled via CnbAppRemote::enable_email_opt_in/disable_email_opt_in
        return array(
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'companyName' => $this->companyName,
            'address'     => $this->address,
            'taxIds'      => $this->taxIds,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbUserTaxId implements JsonSerializable {
    public $value;
    public $type;
    /**
     * @var CnbUserTaxIdVerification
     */
    public $verification;

    /**
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUserTaxId[]|WP_Error
     */
    public static function fromObject( $object ) {
        if ( !$object ||  is_wp_error( $object ) ) {
            return $object;
        }
        $userTaxIds = array();
        if ( ! is_array($object) ) {
            return $userTaxIds;
        }

        foreach ( $object as $taxId ) {
            $userTaxId               = new CnbUserTaxId();
            $userTaxId->value        = CnbUtils::getPropertyOrNull( $taxId, 'value' );
            $userTaxId->type         = CnbUtils::getPropertyOrNull( $taxId, 'type' );
            $userTaxId->verification = CnbUserTaxIdVerification::fromObject( CnbUtils::getPropertyOrNull( $taxId, 'verification' ) );
            $userTaxIds[]            = $userTaxId;
        }

        return $userTaxIds;
    }

    public function toArray() {
        return array(
            'value'        => $this->value,
            'type'         => $this->type,
            'verification' => $this->verification,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbUserTaxIdVerification implements JsonSerializable {
    /**
     * @var string either "verified" or "pending"
     */
    public $status;

    /**
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUserTaxIdVerification|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $userTaxIdVerification         = new CnbUserTaxIdVerification();
        $userTaxIdVerification->status = CnbUtils::getPropertyOrNull( $object, 'status' );

        return $userTaxIdVerification;
    }

    public function toArray() {
        return array(
            'status' => $this->status,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbUserAddress implements JsonSerializable {
    public $line1;
    public $line2;
    public $postalCode;
    public $city;
    public $state;
    public $country;

    /**
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUserAddress|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
        $address             = new CnbUserAddress();
        $address->line1      = CnbUtils::getPropertyOrNull( $object, 'line1' );
        $address->line2      = CnbUtils::getPropertyOrNull( $object, 'line2' );
        $address->postalCode = CnbUtils::getPropertyOrNull( $object, 'postalCode' );
        $address->city       = CnbUtils::getPropertyOrNull( $object, 'city' );
        $address->state      = CnbUtils::getPropertyOrNull( $object, 'state' );
        $address->country    = CnbUtils::getPropertyOrNull( $object, 'country' );

        return $address;
    }

    public function toArray() {
        return array(
            'line1'      => $this->line1,
            'line2'      => $this->line2,
            'postalCode' => $this->postalCode,
            'city'       => $this->city,
            'state'      => $this->state,
            'country'    => $this->country,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbUserStripeDetails implements JsonSerializable {
    public $customerId;
    public $subscriptions = array();
    public $currency;

    /**
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUserStripeDetails|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
        $stripeDetails             = new CnbUserStripeDetails();
        $stripeDetails->customerId = CnbUtils::getPropertyOrNull( $object, 'customerId' );
        $stripeDetails->currency   = CnbUtils::getPropertyOrNull( $object, 'currency' );

        return $stripeDetails;
    }

    public function toArray() {
        return array(
            'customerId'    => $this->customerId,
            'subscriptions' => $this->subscriptions,
            'currency'      => $this->currency,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbUserMarketingData implements JsonSerializable {

    /**
     * @var string
     */
    public $signupSource;

    /**
     * @var boolean
     */
    public $emailOptIn = false;

    /**
     * @var string (date)
     */
    public $emailOptInDate;

    /**
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbUserMarketingData|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
        $marketing_data             = new CnbUserMarketingData();
        $marketing_data->signupSource = CnbUtils::getPropertyOrNull( $object, 'signupSource' );
        $marketing_data->emailOptIn   = CnbUtils::getPropertyOrNull( $object, 'emailOptIn' );
        $marketing_data->emailOptInDate   = CnbUtils::getPropertyOrNull( $object, 'emailOptInDate' );

        return $marketing_data;
    }

    public function toArray() {
        return array(
            'signupSource'   => $this->signupSource,
            'emailOptIn'     => $this->emailOptIn,
            'emailOptInDate' => $this->emailOptInDate,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
