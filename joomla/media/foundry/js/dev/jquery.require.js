/**
 * jquery.require.
 * Javascript loader with parallel script loading,
 * ordered script execution, sync/async callback &
 * intelligent path guessing.
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
	var defaultPath = Foundry.scriptPath ||
                      (function(){
                          // TODO: Will not work if src contains relative path.
                          var path = $("script").last().attr('src');
                          return path.substr(0, path.lastIndexOf('/') + 1);
                      })();

    var canAsync = document.createElement("script").async === true || "MozAppearance" in document.documentElement.style || window.opera;

    var IDLE       = 0,
        PRELOADING = 1,
        PRELOADED  = 2,
        LOADING    = 3,
        LOADED     = 4,
        FAILED     = 5;

    /*
     * Script class
     * var script = new Script([url|params]);
     */
    var Script = function(params)
    {
        this.url = params;
        this.state = IDLE;

        if ($.isPlainObject(params))
            $.extend(true, this, params);
    };

    Script.prototype.timeElapsed = function()
    {
        // If this function was called before the script had begun loading.
        if (this.timeStarted==undefined)
            return 0;

        var currentTime = new Date();
        return currentTime.getTime() - this.timeStarted.getTime();
    };

    Script.prototype.insert = function(node)
    {
        document.getElementsByTagName('head')[0].appendChild(node || this.node);
    };

    Script.prototype.load = function(callback, preload)
    {
        if (this.state == LOADED)
            return callback && callback();

        var _this = this,
            isEJS = this.type=='text/ejs';

        // EJS templates does not need preloading
        if (isEJS)
        {
            preload = false;
        } else {
            this.type = (preload) ? 'text/cache' : 'text/javascript';
        }

        // Set state to loading
        this.state = (preload) ? PRELOADING : LOADING;

        this.timeStarted = new Date();

        var node = this.node = document.createElement('script');

        node.type = this.type;

        node.charset = "utf-8";

        // Make sure scripts load in order on browsers that supports "async" property,
        // e.g. FF, Chrome
        node.async = false;

        // EJS templates uses AJAX method
        if (this.type=='text/ejs')
        {
            var id = _this.name.substr(1);

            $.ajax({
                url: this.url,
                dataType: 'text',
                error: function()
                {
                    _this.state = FAILED;

                    try { console.warn('$.require: Failed to load EJS template "' + id + '"') } catch(e) {};
                },
                success: function(data)
                {
                    _this.state = LOADED;

                    // IE7/8: Block element cannot be inserted into <script> tag.
                    _this.insert($('<script id="' + id +'" type="text/ejs">' + data + '</script>')[0]);

                    return callback && callback();
                }
            });

        } else {

            this.ready = function(event)
            {
                _this.state = (preload) ? PRELOADED : LOADED;
                _this.loaded.apply(_this, arguments);

                if (_this.node.type=='text/cache')
                    $(_this.node).remove();

                return callback && callback();
            };

            this.error = function(event)
            {
                try { console.warn('$.require: Failed to load "' + _this.node.src + '"'); } catch(e) {}
                _this.state = FAILED;
            };

            // On IE9, addEventListener() does not necessary fire the onload event
            // after the script is loaded, therefore we use the attachEvent() method,
            // as it behaves correctly.
            if (node.attachEvent && !$.isOpera)
            {
                node.attachEvent("onreadystatechange", this.ready);
                node.attachEvent("onerror", this.error); // IE9 only.
            } else {
                node.addEventListener("load", this.ready, false);
                node.addEventListener("error", this.error, false);
            }

            node.src = this.url;

            this.insert();
        }
    };

    Script.prototype.loaded = function(event)
    {
        var node = this.node;

        if (event.type === "load" || /loaded|complete/.test(node.readyState))
        {
            if (node.detachEvent && !$.isOpera)
            {
                node.detachEvent("onreadystatechange", this.ready);
                node.detachEvent("onerror", this.error);
            } else {
                node.removeEventListener("load", this.ready, false);
                node.removeEventListener("error", this.error, false);
            }
        }
    };

    /*
     * Global script library
     *
     */
    var $scripts = {};

    /*
     * Queue class
     * var queue = new Queue()
     */
    var Queue = function(scripts, callback, async, timeout)
    {
        this.scripts   = scripts;
        this.callback  = callback || function(){};
        this.async     = async;
        this.timeout   = timeout;
        this.completed = false;
    }

    Queue.prototype.allLoaded = function()
    {
        var _this = this;

        var allLoaded = true;
        $.each(this.scripts, function(i, url)
        {
            return allLoaded = $scripts[url].state==LOADED;
        });

        // Replace myself to avoid executing the loop above
        // on subsequent calls.
        if (allLoaded)
            this.allLoaded = function() { return true; }

        return allLoaded;
    }

    Queue.prototype.complete = function()
    {
        if (this.completed) return true;

        var _this = this;

        $(function(){
            // Ensure all queue callbacks are executed
            // after document is ready.
            if (_this.callback.executed) return;
            _this.callback.apply(null, [$]);
            _this.callback.executed = true;
        });

    	// Go through every promise, and execute them.
		$.each($promise, function(i, name) { this.apply(name,[]) });

		return this.completed = true;
    }

    /*
     * Global require queue
     *
     */
    var $queues = [];

    $queues.create = function(scripts, callback, async, timeout)
    {
        var queue = new Queue(scripts, callback, async, timeout);

        queue.id = this.push(queue)-1;

        return queue;
    }

    $queues.complete = function(id)
    {
        if (id===undefined)
            id=$queues.length-1;

        // By default, callbacks from subsequent $.require() queues
        // will wait until callbacks from all previous queues are completed.
        var queue = $queues[id];

        // However, if the async option is set the true,
        // then callback will execute straightaway once the queue is complete.
        if (queue.async && queue.allLoaded())
            return queue.complete();

        // Loop through every queue before the queue of interest
        // and make sure those queues are completed.
        var completed = false;

        for (var i=0; i<=id; i++)
        {
            var queue = $queues[id];

            // If this queue is marked as completed,
            // continue to the next queue in the loop
            if (queue.completed) continue;

            // If this queue is not marked as completed,
            // break the loop if the script is not loaded.
            if (!queue.allLoaded()) break;

            // Else, complete the queue
            // and execute the queue's callback.
            queue.complete();

            // If the queue belongs to the id of interest,
            // then consider this queue completed.
            if (i==id) completed = true;
        }

        return completed;
    }

    var $watcher = {
        start: function()
        {
            var _this = this;

            if (this.instance!=undefined) return;

            this.instance = setInterval(function()
            {
                if (!$.isEmptyObject($promise))
                {
                    $.each($promise, function(i, name) { this.apply(name,[]) });
                }

                if (!$queues.complete()) return;

                _this.stop();

            }, 500);
        },

        stop: function()
        {
            clearInterval(this.instance);
            delete this.instance;
        }
    };

    /*
     * Main $.require() function
     *
     */
    $.require = function(options, names, callback)
    {
	    var defaultPath = Foundry.scriptPath ||
	                      (function(){
	                          // TODO: Will not work if src contains relative path.
	                          var path = $("script").last().attr('src');
	                          return path.substr(0, path.lastIndexOf('/') + 1);
	                      })(),

	        defaultOptions = {
	            async: false,
	            defer: false,
	            path: defaultPath,
	            ejsPath: defaultPath,
	            jqueryPath: defaultPath,
	            timeout: 10000
	        };

        if ($.isArray(options)) { callback = names; names = options; options = {} };

        var options = $.extend({}, defaultOptions, options);

        // If script loading can be deferred,
        // then execute callback first.
        if (options.defer) {
            callback.apply(null, [$]);
            callback = function(){};
        }

        // Ensure all paths come with trailing slashes
        options.path       = $.String.addTrailingSlash(options.path);
        options.ejsPath    = $.String.addTrailingSlash(options.ejsPath);
        options.jqueryPath = $.String.addTrailingSlash(options.jqueryPath);

        var scripts;
        if (names.length < 1)
        {
            // If no scripts given, we will still create a queue for it.
            // The callback will be executed upon reaching the queue's turn.
            scripts = [];
            callback = callback || function() {};

        } else {

            // Convert names into url
            scripts = $.map(names, function(name)
            {
                // @rule: All filenames should be in lowercase
                var name = name.toLowerCase(),
                    params = {name: name};

                // EJS template
                if (/^@/.test(name))
                {
                    var templateName = name.substr(1);

                    params.url = options.ejsPath + templateName + '.ejs';

                    params.type = 'text/ejs';

                // Absolute url
                } else if ($.isUrl(name)) {
                    params.url = name;

                // Script name
                } else {

                    // If jQuery plugin, use options.jqueryPath.
                    // If others, use options.path.
                    params.url = ((/^jquery/.test(name)) ? options.jqueryPath : options.path) + name + '.js';
                }

                // If the script entry doesn't exist, create one.
                if ($scripts[params.url]===undefined)
                    $scripts[params.url] = new Script(params);

                return params.url;
            });
        }

        $watcher.start();

        // Create a new require queue
        var queue = $queues.create(scripts, callback, options.async, options.timeout);

        $.each(scripts, function(i, url)
        {
            // If the browser can supports asynchronous execution,
            // it also means that we can turn that feature off,
            // and let the browser can execute scripts synchronously,
            // in other words, proper ordering of script execution.
            if (canAsync)
            {
                $scripts[url].load(function()
                {
                    $queues.complete(queue.id);
                });

            // For IE, we will have to preload the script using text/cache hack,
            // then reload the script again using text/javascript.
            } else {

                $scripts[url].load(

                    // This function body is used for both
                    // preload callback & load callback.
                    function()
                    {
                        // This part checks if all script is loaded during load callback, and complete the queue.
                        // When this function body is executed during preload callback, it will always be false.
                        if ($queues.complete(queue.id)) return;

                        // Make a variable reference to this function body
                        var loadScriptInOrder = arguments.callee;

                        // Scripts are being preloaded in parallel,
                        // therefore preload callbacks aren't executed in order.
                        $.each(scripts, function(i, url)
                        {
                            // So everytime a preload callback is fired,
                            // we loop through the scripts from top down,
                            // and attempt to load the next preloaded script.
                            switch ($scripts[url].state)
                            {
                                // If the script is loaded, we'll continue the loop
                                // to search for the next preloaded script.
                                case LOADED:
                                    return true;
                                    break;

                                // If the script is preloaded, we'll load the script again
                                // and pass in loadScriptInOrder as its callback.
                                case PRELOADED:
                                    $scripts[url].load(loadScriptInOrder);
                                    break;
                            }

                            return false;
                        });
                    }

                , true);

            }
        });

        return queue;
    };

    // TODO: A proper deferred object
    var $promise = {};
    $.require.promise = function(name, func)
    {
        if (arguments.length < 1)
            return $promise;

        if (func===false)
            return delete $promise[name];

        if ($.isFunction(func))
            return $promise[name] = func;
    };

    // Return queues
    $.require.queues = function()
    {
        return $queues;
    }

    $.require.scripts = function()
    {
        return $scripts;
    }

});
