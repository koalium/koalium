/**
 * A Condition for a Button
 */
export interface CnbButtonCondition {
    /**
     * ID of the condition
     */
    id: string;
    /**
     * ID of the owner
     */
    user: string;

    /**
     * URL or GEO
     */
    conditionType: MATCH_TYPE;
    filterType: FILTER_TYPE;
    /**
     * EXACT (one of URL_MATCH_TYPE)
     */
    matchType: URL_MATCH_TYPE | GEO_MATCH_TYPE;
    /**
     * anything
     */
    matchValue: string;
}

/**
 * Parameters that flow throw the app and define a bit of the environment
 */
export interface CnbParams {
    /**
     * The domain of the current page (e.g. "callnowbutton.com")
     */
    domain: string;
    /**
     * The time this button is evaluated (This is a regular `Date` object, representing "now")
     */
    now: Date;
    /**
     * The HREF of the current page (this NEEDS to include the protocol as well!)
     */
    url: string;
    /**
     * The Window object for this environment
     */
    window?: Window;
    /**
     * Debug mode for the Button
     */
    debug: boolean;
    /**
     * (optional) IP of the user. Normally this is undefined, meaning 'use the current IP'
     */
    ip?: string;
    /**
     * (optional) geoInfo if needed and found
     */
    geoInfo: GeoInfo;
}

interface MATCH_TYPE {
    URL: "URL";
    GEO: "GEO";
}

interface FILTER_TYPE {
    EXCLUDE: "EXCLUDE";
    INCLUDE: "INCLUDE";
}

/**
 * - `SIMPLE`: matches path, ignores www subdomain, domain and params (using startsWith)
 * - `EXACT`: matches domain and path, ignores www subdomain and params (has to match fully)
 * - `REGEX`: matches path and params, ignores domain (using regular expressions)
 * - `SUBSTRING`: matches path and domain, ignores params (but substring only)
 */
interface URL_MATCH_TYPE {
    /**
     * matches domain and path, ignores www subdomain and params (has to match fully)
     */
    EXACT: "EXACT";
    /**
     * matches path and params, ignores domain (using regular expressions)
     */
    REGEX: "REGEX";
    /**
     * matches path, ignores www subdomain, domain and params (using startsWith)
     */
    SIMPLE: "SIMPLE";
    /**
     * matches path and domain, ignores params (but substring only)
     */
    SUBSTRING: "SUBSTRING";
}

interface GEO_MATCH_TYPE {
    /**
     * matches the 2-letter country code
     */
    COUNTRY_CODE: "COUNTRY_CODE";
}

export interface ACTION_TYPE {
    ANCHOR: "ANCHOR";
    EMAIL: "EMAIL";
    FACEBOOK: "FACEBOOK";
    IFRAME: "IFRAME";
    INTERCOM: "INTERCOM";
    LINE: "LINE";
    LINK: "LINK";
    MAP: "MAP";
    PHONE: "PHONE";
    SIGNAL: "SIGNAL";
    SKYPE: "SKYPE";
    SMS: "SMS";
    TALLY: "TALLY";
    TELEGRAM: "TELEGRAM";
    VIBER: "VIBER";
    WECHAT: "WECHAT";
    WHATSAPP: "WHATSAPP";
    ZALO: "ZALO";
}

export type BUTTON_PLACEMENT =
    | "DEFAULT"
    | "BOTTOM_CENTER"
    | "BOTTOM_LEFT"
    | "BOTTOM_RIGHT"
    | "MIDDLE_LEFT"
    | "MIDDLE_RIGHT"
    | "TOP_CENTER"
    | "TOP_LEFT"
    | "TOP_RIGHT";

export type BUTTON_POSITION = "DEFAULT" | "ABSOLUTE" | "FIXED";

export type BUTTON_TYPE = "FULL" | "MULTI" | "SINGLE" | "DOTS";

export interface ICON_TYPE {
    DEFAULT: "DEFAULT"; // == FONT
    CUSTOM: "CUSTOM";
    FONT: "FONT"; // CNB Design icon font (small custom subset, the default icons at launch)
    FONT_MATERIAL: "FONT_MATERIAL"; // Material Design icon font
    SVG: "SVG"; // data URI in CSS
}

export interface CHAT_MODAL_TYPE {
    DEFAULT: "default";
    WHATSAPP: "whatsapp";
    FACEBOOK: "facebook";
    IFRAME: "iframe";
}

export type DISPLAY_MODE = "ALWAYS" | "MOBILE_ONLY" | "DESKTOP_ONLY";

export interface BUTTON_ANIMATION {
    NONE: "NONE";
    SHAKE: "SHAKE";
    TADA: "TADA";
    SONAR_LIGHT: "SONAR_LIGHT";
    SONAR_DARK: "SONAR_DARK";
}

// The keys should match the values of BUTTON_ANIMATION
export interface BUTTON_ANIMATION_VALUES {
    NONE: "";
    SHAKE: "shake";
    TADA: "tada";
    SONAR_LIGHT: "sonar-light";
    SONAR_DARK: "sonar-dark";
}

export interface SIMPLE_ORIENTATION {
    LANDSCAPE: "LANDSCAPE";
    PORTRAIT: "PORTRAIT";
}

export type DOMAIN_TYPE = "FREE" | "STARTER" | "PRO";

interface CnbDecision {
    actions: CnbAction[];
    button: CnbButton;
}

export interface CnbButton {
    /**
     * Determines whether the button should be shown at all
     */
    active: boolean;
    /**
     * ID of the button (used for reference purposes)
     */
    id: string;
    /**
     * Name of the button (used for display purposes)
     */
    name: string;
    /**
     * The actual domain name (including subdomains)
     */
    domain: CnbDomain;
    /**
     * The ID references a {CnbDomain} object
     */
    domainId: string;
    /**
     * Display property of this button (single icon, multi button or buttonbar)
     */
    type: BUTTON_TYPE;
    /**
     * The particular options for this button
     */
    options: CnbButtonOptions;
    /**
     * Options specific for the Multibutton
     */
    multiButtonOptions: CnbButtonMultiOptions;
    /**
     *   Associated {CnbAction}s for this button
     */
    actions: CnbAction[];
    /**
     *   Collection of Conditions which decide whether show this button
     */
    conditions: CnbButtonCondition[];
    /**
     *
     */
    actionIdMap: CnbAction[];
    /**
     *
     */
    buttonIdMap: CnbButton[];
    /**
     *
     */
    conditionIdMap: CnbButtonCondition[];
}

interface CnbButtonMultiOptions {
    /**
     * The auto-generated ID from the database
     */
    id: string;
    /**
     * for the open/close button
     */
    iconBackgroundColor: string;
    /**
     * for the open/close button
     */
    iconColor: string;
    /**
     * (PRO Only) Allow to set the subtext of the MultiButton
     */
    labelTextOpen: string;
    /**
     * (PRO Only) Allow to set the color of the subtext of the MultiButton
     */
    labelTextColorOpen: string;
    /**
     * Background color of the label
     */
    labelBackgroundColorOpen: string;
    /**
     * (PRO Only) Allow to set the subtext of the MultiButton
     */
    labelTextClose: string;
    /**
     * (PRO Only) Allow to set the color of the subtext of the MultiButton
     */
    labelTextColorClose: string;
    /**
     * Background color of the label
     */
    labelBackgroundColorClose: string;
    /**
     * font type for the open icon (the icon showed to OPEN the MultiButton - so the collapsed state)
     */
    iconTypeOpen: ICON_TYPE;
    /**
     * [FONT,FONT_MATERIAL] for the open icon (iconText is the ligature/name in the font icon)
     */
    iconTextOpen: string;
    /**
     * [CUSTOM] the URL for the icon (`url(..)`)
     */
    iconBackgroundImageOpen: string;
    /**
     * [SVG] Class containing the SVG resource (`cnb-svg-phone-1`)
     */
    iconClassOpen: string;
    /**
     * [FONT,FONT_MATERIAL] Color of the icon
     */
    iconColorOpen: string;
    /**
     * [ALL] Determines the background color of the icon/Action
     */
    iconBackgroundColorOpen: string;
    /**
     * font type for the close icon (the icon showed to CLOSE the MultiButton - so the expanded state)
     */
    iconTypeClose: ICON_TYPE;
    /**
     * [FONT,FONT_MATERIAL] for the open icon (iconText is the ligature/name in the font icon)
     */
    iconTextClose: string;
    /**
     * [CUSTOM] the URL for the icon (`url(..)`)
     */
    iconBackgroundImageClose: string;
    /**
     * [SVG] Class containing the SVG resource (`cnb-svg-phone-1`)
     */
    iconClassClose: string;
    /**
     * [FONT,FONT_MATERIAL] Color of the icon
     */
    iconColorClose: string;
    /**
     * [ALL] Determines the background color of the icon/Action
     *
     */
    iconBackgroundColorClose: string;
}

interface CnbButtonOptions {
    /**
     *  Where on the screen the single/multi button should be placed (if position is FIXED or DEFAULT) */
    placement: BUTTON_PLACEMENT;
    /**
     *   CSS position (fixed or absolute). Usually set to DEFAULT so the Button gets to decide
     */
    position: BUTTON_POSITION;
    /**
     *   empty/null or MOBILE_ONLY for the default "mobile-only" view, "ALWAYS" to display always, "DESKTOP_ONLY" for desktop-only mode
     */
    displayMode?: DISPLAY_MODE;
    /**
     *   The animation used for the Single or Multibutton
     */
    animation?: BUTTON_ANIMATION;
    /**
     *   - This is the color for the primary button (as opposed to Action.backgroundColor used for Actions) (These are ONLY used for the Multibutton, to determine the open/close colors (deprecated, will be replaced by "MultiButtonOptions"
     */
    iconBackgroundColor: string;
    /**
     *   - (basically "menuIconColor") This is the color used for the icon on the main button (as opposed to Action.iconColor used for Actions) (These are ONLY used for the Multibutton, to determine the open/close colors (deprecated, will be replaced by "MultiButtonOptions"
     */
    iconColor: string;
    /**
     *   Scales the button to a particular size (The below properties are not actually present on the Button Options - are they mapped via Settings???)
     */
    scale: number;
    /**
     *   "auto" or int (MIN_VALUE to MAX_VALUE) Sets the zIndex to a particular value (usually >=2) (The below properties are not actually present on the Button Options - are they mapped via Settings???)
     */
    zindex: string;
    /**
     *
     */
    scroll?: ButtonScrollOptions;
    /**
     * If position is absolute, this informs the button what to attach to
     */
    attachTo?: string;
    cssClasses?: string;
}

interface ButtonScrollOptions {
    /**
     * 100 or "100"
     */
    revealAtHeight: number | string;
    /**
     *
     * @property {number|string}
     */
    hideAtHeight: number | string;
    /**
     * Indicates if this element should be hidden again. In case of string, only "true" is considered true
     */
    neverHide: boolean | string;
}

export interface CnbAction {
    id: string;
    /**
     * The type of action (PHONE, LINK, etc)
     */
    actionType: ACTION_TYPE;
    /**
     * The value for the action, depends on the type what this means
     */
    actionValue: string;

    /**
     * A <string, string> map of additional properties
     */
    properties: CnbActionProperties;
    /**
     * ONLY used in MULTI/FULL (SINGLE uses button->iconBackgroundColor)
     */
    backgroundColor: string;
    /**
     * This is used in case the iconType is CUSTOM
     */
    iconBackgroundImage: string;
    /**
     * true by default, if false this hides the Action icon (leaving only the label if any)
     */
    iconEnabled: boolean;
    /**
     * - ONLY used in MULTI/FULL (SINGLE uses button->iconColor)
     */
    iconColor: string;
    /**
     * Which Font icon to use ("anchor", "call", etc)
     */
    iconText: string;
    /**
     * DEFAULT (== FONT), CUSTOM is used for "iconBackgroundImage" (SVG in currently unused)
     */
    iconType: ICON_TYPE;
    /**
     * If used, displays this label next to the Action
     */
    labelText: string;
    /**
     * Allow to set the color of the text of the label
     */
    labelTextColor: string;
    /**
     * UNUSED SVG icon (not used currently)
     */
    iconClass: string;
    /**
     * UNUSED Set a custom background color for a label
     */
    labelBackgroundColor: string;

    schedule: CnbSchedule;
}


interface CnbActionProperties {
    /**
     * [Generic] (currently only used by WHATSAPP) if > 0, show that count as a bubble
     */
    "show-notification-count": string;
    /**
     * [Email]
     */
    cc: string;
    /**
     * [Email]
     */
    bcc: string;
    /**
     * [Email]
     */
    subject: string;
    /**
     * [Email]
     */
    body: string;
    /**
     * [Email, Whatsapp, SMS] The message to send
     */
    message: string;
    /**
     *  Set the CSS "width" (auto, 0 or a number > 0)
     */
    "modal-width": string;
    /**
     *  Set the CSS "height" (empty, 0 or a number > 0)
     */
    "modal-height": string;
    /**
     *  Set the CSS color (used by iframe only)
     */
    "modal-background-color": string;
    /**
     *  Set the CSS color (used by iframe only)
     */
    "modal-header-background-color": string;
    /**
     *  Set the CSS color (used by iframe only)
     */
    "modal-header-text-color": string;
    /**
     *  [WhatsApp] (either empty for the regular link, or "popout" for the dialog)
     */
    "whatsapp-dialog-type": string;
    /**
     *  [WhatsApp]
     */
    "whatsapp-title": string;
    /**
     *  [WhatsApp]
     */
    "whatsapp-welcomeMessage": string;
    /**
     *  [WhatsApp]
     */
    "whatsapp-placeholderMessage": string;
    /**
     *  [WhatsApp]
     */
    "whatsapp-customerMessage": string;
    /**
     *  [WhatsApp]
     */
    "whatsapp-animateWelcomeMessages": string;
    /**
     *  [Facebook] (either empty for the regular link, or "popout" for our own chatmodal, "widget" for the dialog)
     */
    "facebook-dialog-type": string;
    /**
     *  [Facebook]
     */
    "facebook-title": string;
    /**
     *  [Facebook]
     */
    "facebook-welcomeMessage": string;
    /**
     *  [Facebook]
     */
    "facebook-animateWelcomeMessages": string;
    /**
     *  [Facebook]
     */
    "facebook-ref": string;
    /**
     *  [Facebook] Widget only, default state of the Facebook widget (open or closed)
     */
    "facebook-widget-default-state": string;
    /**
     *  [Facebook] Widget only, placeholder for an App ID (different from the Page ID)
     */
    "facebook-widget-app-id": string;
    /**
     *  [Link] _blank or _self, usually
     */
    "link-target": string;
    /**
     *  [Link] true or false, whether the link-download property should be used
     */
    "link-download-enabled": string;
    /**
     *  [Link] usually a filename to name the file being downloaded (example.gif)
     */
    "link-download": string;
    /**
     *  [MAP] q or daddr (default: q)
     */
    "map-query-type": string;
    /**
     *  Title of the iframe
     */
    "iframe-title": string;
    /**
     *  if you don't want to display it on your website.
     */
    "tally-hide-title": string;
    /**
     *  remove the default white background to make your form blend into your website.
     */
    "tally-transparent-background": string;
    /**
     *  embedded Tally forms will be displayed in the center of your website by default. Use this option to align the form content on the left of your screen.
     */
    "tally-align-left": string;
    /**
     *  "left" or "right"
     */
    "intercom-alignment": string;
    /**
     *  0
     */
    "intercom-horizontal-padding": string;
    /**
     *  0
     */
    "intercom-vertical-padding": string;
}

export interface CnbDomain {
    /**
     * ID of the domain (domain_abc_def or domain_test_abc_def, or abc-def)
     */
    id: string;
    /**
     * ID of the user (user_foo_bar, user_test_foo_bar or foo-bar)
     */
    user: string;
    /**
     * Domain name (example.org)
     */
    name: string;
    /**
     * Subscription type of the Domain
     */
    type: DOMAIN_TYPE;
    /**
     * Date that the PRO subscription expires (basically useless for the client)
     */
    expires: number;
    /**
     * If the subscription auto-renews (basically useless for the client)
     */
    renew: boolean;
    /**
     * Timezone of this button, used for Schedule (Europe/Amsterdam)
     */
    timezone: string;
    /**
     * Should we call the Google Analytics code if set to "true"
     */
    trackGA: boolean;
    /**
     * Should we call the Ads code if set to "true"
     */
    trackConversion: boolean;
    /**
     * Alternative names of this domain
     */
    aliases: string[];
    /**
     * Map (key/value), e.g. "debug": "true"
     */
    properties?: CnbDomainProperties;
}

interface CnbDomainProperties {
    /**
     * true/false
     */
    debug: string;
    /**
     * true/false
     */
    allowMultipleButtons: string;
    /**
     * "auto" or  1 to 2147483647
     */
    zindex: string;
    /**
     * 0.7 to 1.3
     */
    scale: string;
}

interface CnbSchedule {
    /**
     * if true, the rest is ignored. If false, the other properties are evaluated together
     */
    showAlways: boolean;
    /**
     * (0 = Monday, 6 = Sunday)
     */
    daysOfWeek: number[];
    /**
     * format: HH:mm
     */
    start: string;
    /**
     * format: HH:mm
     */
    stop: string;
    /**
     * "Europe/Amsterdam"
     */
    timezone: string;
    /**
     * if true, the schedule should match hours outside start-stop
     */
    outsideHours: boolean;
}

export interface CnbOptions {
    cssLocation: string;
    jsLocation: string;
    /**
     * ms since epoch
     */
    date?: number;
    /**
     * IP of the user
     */
    ip?: string;
}

/**
 * This is the format of the client JS (basically, what the API outputs)
 */
export interface CnbData {
    userId: string;
    options: CnbOptions;
    buttons: CnbButton[];
    actions: CnbAction[];
    conditions: CnbButtonCondition[];
    domains: CnbDomain[];
}

export interface ConfigInstanceType {
    clear: () => void;
    parseDomainProperties: (button: CnbButton) => void;
    init: (data: CnbData) => boolean;
    getAction: (id: string) => CnbAction;
    getButton: (id: string) => CnbButton;
    getCondition: (id: string) => CnbButtonCondition;
    getOption: (name: string) => string[] | string | boolean | undefined;
    isDebug: (decision?: CnbDecision) => boolean;
}

export interface CnbDebugData {
    data: CnbData;
    params: CnbParams;
    config: ConfigInstanceType;
    decision: CnbDecision[];
    result: HTMLElement[];
}

interface GeoInfo {
    /**
     * Requested IP
     */
    ip: string;
    /**
     * Radius in kilometers around the specified location where the IP address is likely to be
     */
    accuracy: number;
    /**
     * IP latitude (Note: this is a string due to historic reasons)
     */
    latitude: string;
    /**
     * IP longitude (Note: this is a string due to historic reasons)
     */
    longitude: string;
    /**
     * Time zone as specified by the IANA Time Zone Database
     */
    timezone: string;
    /**
     * The autonomous system number associated with the IP address (Note: 64512 is returned when the ASN is unknown)
     */
    asn: number;
    /**
     * (deprecated) The ASN and organization field combined (Note: this field is deprecated)
     */
    organization: string;
    /**
     * The organization that the IP is registered to (Note: Unknown is returned when this field is unknown)
     */
    name: string;
    /**
     * City name in English
     */
    city: string;
    /**
     * (deprecated) always "0"
     */
    area_code: string;
    /**
     * (or province) Subdivision of the country the IP is within (State, region etc.)
     */
    region: string;
    /**
     * Country name in English
     */
    country: string;
    /**
     * Two-letter country code
     */
    country_code: string;
    /**
     * Three-letter country code
     */
    country_code3: string;
    /**
     * Two-letter continent code
     */
    continent_code: string;
}
