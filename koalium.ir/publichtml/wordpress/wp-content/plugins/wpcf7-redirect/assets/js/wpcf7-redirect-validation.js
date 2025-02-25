/**
 * Validation for fields
 * @constructor
 */
function Wpcf7_admin_validations($){

    this.rules = {

    	rules: {

    	}

    };

    this.init = function(){

        this.addMethods();

        $('#wpcf7-contact-form-editor form').validate({

            rules: this.rules,

            onfocusout: function(element) {

                this.element(element);

            }

        });

    };

    this.addMethods = function(){

        this.nospaces();

        this.englishAndNumbersOnly();

    };

    /**

     * Allow only enlish and numbers

     * @return {[type]} [description]

     */

    this.englishAndNumbersOnly = function(){

        $.validator.addMethod("validateenglishnumbers", function(value, element) {

            return this.optional(element) || /^[a-z0-9_\-," "]+$/i.test(value);

        }, "English and numbers only");

        $.validator.addClassRules("validateenglishnumbers", {

            validateenglishnumbers: true

        });

    };

    /**

     * Disallow spaces

     */

    this.nospaces = function(){

        $.validator.addMethod("validatenospace", function(value, element) {

            return value.indexOf(" ") < 0 && value != "";

        }, "No spaces please");

        $.validator.addClassRules("validatenospace", {

            validatenospace: true

        });

    };

    this.init();

}