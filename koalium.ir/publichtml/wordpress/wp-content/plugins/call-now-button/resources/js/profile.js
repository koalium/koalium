/**
 * Logic for the profile form.
 * Toggles visibility and requirements based on selected country and VAT setting.
 * Erases values when switching to a different country during data entry (e.g. state value will be dropped when country is switched from USA to Belgium)
 */
function cnb_profile_edit_setup() {
    const countryEle = jQuery('#cnb_profile_country')
    const euVatEle = jQuery("#cnb-euvatbusiness")
    // First time setup of page
    const currentCountry = countryEle.val();
    cnb_profile_show_hide_fields(currentCountry);

    cnb_profile_show_hide_tax_fields(euVatEle)

    countryEle.on('change',function() {
        const currentCountry = jQuery(this).val();
        cnb_profile_show_hide_fields(currentCountry);
    });
    euVatEle.on('change',function() {
        const element = jQuery(this);
        cnb_profile_show_hide_tax_fields(element)
    });
}

function cnb_profile_show_hide_tax_fields(element) {
    if(element.is(":checked")) {
        jQuery(".cnb_vat_companies_show").show();
        jQuery(".cnb_vat_companies_required").attr("required","required");
    } else {
        jQuery(".cnb_vat_companies_show").hide();
        jQuery(".cnb_vat_companies_required").removeAttr("required");
    }
}

function cnb_profile_show_hide_fields(currentCountry) {
    const euCountries = [
        "AT",
        "BE",
        "BG",
        "HR",
        "CY",
        "CZ",
        "DK",
        "EE",
        "FI",
        "FR",
        "DE",
        "GR",
        "HU",
        "IE",
        "IT",
        "LV",
        "LT",
        "LU",
        "MT",
        "NL",
        "PL",
        "PT",
        "RO",
        "SK",
        "SI",
        "ES",
        "SE",
    ]; // source https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/zakelijk/btw/zakendoen_met_het_buitenland/goederen_en_diensten_naar_andere_eu_landen/eu-landen_en_-gebieden/

    if(jQuery.inArray(currentCountry,euCountries) !== -1) {
        jQuery(".cnb_show_vat_toggle").show();
        jQuery(".cnb_us_required").removeAttr("required");
        //jQuery(".cnb_us_values_only").val('');
        if(currentCountry === 'IE') {
            jQuery(".cnb_ie_only").show();
        }
    } else if(currentCountry === 'US') {
        jQuery(".cnb_show_vat_toggle, .cnb_vat_companies_show").hide();
        jQuery(".cnb_us_show").show();
        jQuery(".cnb_us_required").attr("required","required");
        jQuery("#cnb-euvatbusiness, .cnb_vat_companies_required, #cnb_profile_vat").removeAttr("required checked");
        //jQuery(".cnb_eu_values_only").val('');
    } else {
        jQuery(".cnb_us_show, .cnb_show_vat_toggle, .cnb_vat_companies_show").hide();
        jQuery("#cnb-euvatbusiness, .cnb_us_required, .cnb_vat_companies_required, #cnb_profile_vat").removeAttr("required checked");
        //jQuery(".cnb_eu_values_only, .cnb_us_values_only, .cnb_useu_values_only").val('');
    }
}

jQuery( function() {
    // page: Profile edit (and domain-upgrade, since it's in a modal there)
    cnb_profile_edit_setup();
})