/**
 * Copied from https://bugs.jquery.com/ticket/2207#comment:7
 */
function formToJson() {
    (function($){
        $.extend({
            isArray: function (arr){
                if (typeof arr == 'object'){
                    if (arr.constructor === Array){
                        return true;
                    }
                }
                return false;
            },
            arrayMerge: function (){
                let a = {};
                let n = 0;
                const argv = $.arrayMerge.arguments;
                for (let i = 0; i < argv.length; i++){
                    if ($.isArray(argv[i])){
                        for (let j = 0; j < argv[i].length; j++){
                            a[n++] = argv[i][j];
                        }
                        a = $.makeArray(a);
                    } else {
                        for (const k in argv[i]){
                            if (isNaN(k)){
                                let v = argv[i][k];
                                if (typeof v == 'object' && a[k]){
                                    v = $.arrayMerge(a[k], v);
                                }
                                a[k] = v;
                            } else {
                                a[n++] = argv[i][k];
                            }
                        }
                    }
                }
                return a;
            },
            count: function (arr){
                if ($.isArray(arr)){
                    return arr.length;
                } else {
                    let n = 0;
                    for (const k in arr){
                        if (!isNaN(k)){
                            n++;
                        }
                    }
                    return n;
                }
            }
        });
        $.fn.extend({
            serializeAssoc: function (){
                const o = {
                    aa: {},
                    add: function (name, value) {
                        const tmp = name.match(/^(.*)\[([^\]]*)\]$/);
                        if (tmp) {
                            const v = {};
                            if (tmp[2])
                                v[tmp[2]] = value;
                            else
                                v[$.count(v)] = value;
                            this.add(tmp[1], v);
                        } else if (typeof value == 'object') {
                            if (typeof this.aa[name] != 'object') {
                                this.aa[name] = {};
                            }
                            this.aa[name] = $.arrayMerge(this.aa[name], value);
                        } else {
                            this.aa[name] = value;
                        }
                    }
                };
                const a = $(this).serializeArray();
                for (let i = 0; i < a.length; i++){
                    o.add(a[i].name, a[i].value);
                }
                return o.aa;
            }
        });
    })(jQuery);
}
