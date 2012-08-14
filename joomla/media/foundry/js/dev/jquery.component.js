/**
 * jquery.component.
 * Creates component. One component, many MVCs.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

Foundry.run(function($)
{

var Component = function(name, options, callback)
{
    var self = this;

    this.options    = options;

    this.name = name;
    this.componentName = "com_" + this.name.toLowerCase();
    this.version    = options.version;

    this.debug      = Foundry.debug || options.debug || false;
    this.baseUrl    = options.baseUrl || Foundry.indexUrl + "?option=" + this.componentName;
    this.scriptPath = options.scriptPath || Foundry.rootPath + "media/" + this.componentName + "/js/";

    this.isReady    = false;
    this.readyList  = [];

    this.require(
        {immediateCallback: true},
        options.require || [],
        function()
        {
            self.isReady = true;

            if ($.isFunction(callback))
                callback();

            self.ready();
        });

    // TODO: Destroy controller function
    this.Controllers.destroy = function(){};
}

Component.prototype.ready = function(callback)
{
    var self = this;

    if ($.isFunction(callback))
        return (self.isReady) ? callback.apply(null, [$]) : self.readyList.push(callback);

    if (!self.isReady) return;

    while (self.readyList.length > 0)
    {
        self.readyList.shift().apply(null, [$]);
    }
};

Component.prototype.require = function(options, names, callback)
{
    var self = this;

    if ($.isArray(options)) { callback = names; names = options; options = {} };

    if (names.length < 1)
        return self.ready(callback);

    var defaultOptions = {
        path: self.scriptPath + ((self.debug) ? 'dev/' : ''),
        ejsPath: self.scriptPath + ((self.debug) ? 'dev/' : ''),
        immediateCallback: false
    };

    var options = $.extend({}, defaultOptions, options);

    // To ensure all require callbacks are executed after the component is ready,
    // every callback made through %COMPONENT%.require() is actually wrapped in
    // %COMPONENT%.ready() function, before passing it back to $.require();
    //
    // To execute a require callback immediately without checking if
    // component is ready, set the immediateCallback option to true, e.g.
    //
    // %COMPONENT%.ready({immediateCallback: true});

    return $.require.apply(null, [options, names, (options.immediateCallback) ? callback : function(){ self.ready(callback) }]);
};

Component.prototype.ajax = function(namespace, params, callback)
{
    var self = this;

    var options = {
            url: Foundry.indexUrl,
            data: $.extend(
                params,
                {
                    option: self.componentName,
                    namespace: namespace
                }
            )
        };

    options = $.extend(true, options, this.options.ajax);

    // This is for server-side function arguments
    if (options.data.hasOwnProperty('args'))
        options.data.args = $.toJSON(options.data.args);

    if (callback.type=='jsonp')
    {
        delete callback.type;

        callback.dataType = 'jsonp';

        // This ensure jQuery doesn't use XHR should it detect the ajax url is a local domain.
        callback.crossDomain = true;
    }

    if ($.isPlainObject(callback))
        $.extend(options, callback);

    if ($.isFunction(callback))
        options.success = callback;

    return $.server(options);
};

Component.prototype.Controllers = function()
{
    var args = arguments;
    args[0] = this.name + '.Controllers.' + args[0];
    $.Controller.apply(this, args);
};

var Components = {};
$.Component = function(name, options, callback)
{
    if (arguments.length < 1)
        return Components;

    if (arguments.length < 2)
        return Components[name];

    // Create a global namespace for this component
    return window[name] = Components[name] = new Component(name, options, callback);
};

});
