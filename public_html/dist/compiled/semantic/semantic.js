/*
 * # Fomantic UI - 2.9.3
 * https://github.com/fomantic/Fomantic-UI
 * https://fomantic-ui.com/
 *
 * Copyright 2024 Contributors
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */
/*!
 * # Fomantic-UI 2.9.3 - Dropdown
 * https://github.com/fomantic/Fomantic-UI/
 *
 *
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */

(function ($, window, document) {
    'use strict';

    function isFunction(obj) {
        return typeof obj === 'function' && typeof obj.nodeType !== 'number';
    }

    window = window !== undefined && window.Math === Math
        ? window
        : globalThis;

    $.fn.dropdown = function (parameters) {
        var
            $allModules    = $(this),
            $document      = $(document),

            time           = Date.now(),
            performance    = [],

            query          = arguments[0],
            methodInvoked  = typeof query === 'string',
            queryArguments = [].slice.call(arguments, 1),
            contextCheck   = function (context, win) {
                var $context;
                if ([window, document].indexOf(context) >= 0) {
                    $context = $(context);
                } else {
                    $context = $(win.document).find(context);
                    if ($context.length === 0) {
                        $context = win.frameElement ? contextCheck(context, win.parent) : window;
                    }
                }

                return $context;
            },
            returnedValue
        ;

        $allModules.each(function (elementIndex) {
            var
                settings          = $.isPlainObject(parameters)
                    ? $.extend(true, {}, $.fn.dropdown.settings, parameters)
                    : $.extend({}, $.fn.dropdown.settings),

                className       = settings.className,
                message         = settings.message,
                fields          = settings.fields,
                keys            = settings.keys,
                metadata        = settings.metadata,
                namespace       = settings.namespace,
                regExp          = settings.regExp,
                selector        = settings.selector,
                error           = settings.error,
                templates       = settings.templates,

                eventNamespace  = '.' + namespace,
                moduleNamespace = 'module-' + namespace,

                $module         = $(this),
                $context        = contextCheck(settings.context, window),
                $text           = $module.find(selector.text),
                $search         = $module.find(selector.search),
                $sizer          = $module.find(selector.sizer),
                $input          = $module.find(selector.input),
                $icon           = $module.find(selector.icon),
                $clear          = $module.find(selector.clearIcon),

                $combo = $module.prev().find(selector.text).length > 0
                    ? $module.prev().find(selector.text)
                    : $module.prev(),

                $menu           = $module.children(selector.menu),
                $item           = $menu.find(selector.item),
                $divider        = settings.hideDividers
                    ? $item.parent().children(selector.divider)
                    : $(),

                activated       = false,
                itemActivated   = false,
                internalChange  = false,
                iconClicked     = false,
                element         = this,
                focused         = false,
                instance        = $module.data(moduleNamespace),

                selectActionActive,
                initialLoad,
                pageLostFocus,
                willRefocus,
                elementNamespace,
                id,
                selectObserver,
                menuObserver,
                classObserver,
                module,
                tempDisableApiCache = false
            ;

            module = {

                initialize: function () {
                    module.debug('Initializing dropdown', settings);

                    if (module.is.alreadySetup()) {
                        module.setup.reference();
                    } else {
                        if (settings.ignoreDiacritics && !String.prototype.normalize) {
                            settings.ignoreDiacritics = false;
                            module.error(error.noNormalize, element);
                        }

                        module.create.id();
                        module.setup.layout();

                        if (settings.values) {
                            module.set.initialLoad();
                            module.change.values(settings.values);
                            module.remove.initialLoad();
                        }

                        module.refreshData();

                        module.save.defaults();
                        module.restore.selected();

                        module.bind.events();

                        module.observeChanges();
                        module.instantiate();
                    }
                },

                instantiate: function () {
                    module.verbose('Storing instance of dropdown', module);
                    instance = module;
                    $module
                        .data(moduleNamespace, module)
                    ;
                },

                destroy: function () {
                    module.verbose('Destroying previous dropdown', $module);
                    module.remove.tabbable();
                    module.remove.active();
                    $menu.transition('stop all');
                    $menu.removeClass(className.visible).addClass(className.hidden);
                    $module
                        .off(eventNamespace)
                        .removeData(moduleNamespace)
                    ;
                    $menu
                        .off(eventNamespace)
                    ;
                    $document
                        .off(elementNamespace)
                    ;
                    module.disconnect.menuObserver();
                    module.disconnect.selectObserver();
                    module.disconnect.classObserver();
                },

                observeChanges: function () {
                    if ('MutationObserver' in window) {
                        selectObserver = new MutationObserver(module.event.select.mutation);
                        menuObserver = new MutationObserver(module.event.menu.mutation);
                        classObserver = new MutationObserver(module.event.class.mutation);
                        module.debug('Setting up mutation observer', selectObserver, menuObserver, classObserver);
                        module.observe.select();
                        module.observe.menu();
                        module.observe.class();
                    }
                },

                disconnect: {
                    menuObserver: function () {
                        if (menuObserver) {
                            menuObserver.disconnect();
                        }
                    },
                    selectObserver: function () {
                        if (selectObserver) {
                            selectObserver.disconnect();
                        }
                    },
                    classObserver: function () {
                        if (classObserver) {
                            classObserver.disconnect();
                        }
                    },
                },
                observe: {
                    select: function () {
                        if (module.has.input() && selectObserver) {
                            selectObserver.observe($module[0], {
                                attributes: true,
                                childList: true,
                                subtree: true,
                            });
                        }
                    },
                    menu: function () {
                        if (module.has.menu() && menuObserver) {
                            menuObserver.observe($menu[0], {
                                childList: true,
                                subtree: true,
                            });
                        }
                    },
                    class: function () {
                        if (module.has.search() && classObserver) {
                            classObserver.observe($module[0], {
                                attributes: true,
                            });
                        }
                    },
                },

                create: {
                    id: function () {
                        id = (Math.random().toString(16) + '000000000').slice(2, 10);
                        elementNamespace = '.' + id;
                        module.verbose('Creating unique id for element', id);
                    },
                    userChoice: function (values) {
                        var
                            $userChoices,
                            $userChoice,
                            html
                        ;
                        values = values || module.get.userValues();
                        if (!values) {
                            return false;
                        }
                        values = Array.isArray(values)
                            ? values
                            : [values];
                        $.each(values, function (index, value) {
                            if (module.get.item(value) === false) {
                                html = settings.templates.addition(module.add.variables(message.addResult, value));
                                $userChoice = $('<div />')
                                    .html(html)
                                    .attr('data-' + metadata.value, value)
                                    .attr('data-' + metadata.text, value)
                                    .addClass(className.addition)
                                    .addClass(className.item)
                                ;
                                if (settings.hideAdditions) {
                                    $userChoice.addClass(className.hidden);
                                }
                                $userChoices = $userChoices === undefined
                                    ? $userChoice
                                    : $userChoices.add($userChoice);
                                module.verbose('Creating user choices for value', value, $userChoice);
                            }
                        });

                        return $userChoices;
                    },
                    userLabels: function (value) {
                        var
                            userValues = module.get.userValues()
                        ;
                        if (userValues) {
                            module.debug('Adding user labels', userValues);
                            $.each(userValues, function (index, value) {
                                module.verbose('Adding custom user value');
                                module.add.label(value, value);
                            });
                        }
                    },
                    menu: function () {
                        $menu = $('<div />')
                            .addClass(className.menu)
                            .appendTo($module)
                        ;
                    },
                    sizer: function () {
                        $sizer = $('<span />')
                            .addClass(className.sizer)
                            .insertAfter($search)
                        ;
                    },
                },

                search: function (query) {
                    query = query !== undefined
                        ? query
                        : module.get.query();
                    module.verbose('Searching for query', query);
                    if (settings.fireOnInit === false && module.is.initialLoad()) {
                        module.verbose('Skipping callback on initial load', settings.onSearch);
                    } else if (module.has.minCharacters(query) && settings.onSearch.call(element, query) !== false) {
                        module.filter(query);
                    } else {
                        module.hide(null, true);
                    }
                },

                select: {
                    firstUnfiltered: function () {
                        module.verbose('Selecting first non-filtered element');
                        module.remove.selectedItem();
                        $item
                            .not(selector.unselectable)
                            .not(selector.addition + selector.hidden)
                            .eq(0)
                            .addClass(className.selected)
                        ;
                    },
                    nextAvailable: function ($selected) {
                        $selected = $selected.eq(0);
                        var
                            $nextAvailable = $selected.nextAll(selector.item).not(selector.unselectable).eq(0),
                            $prevAvailable = $selected.prevAll(selector.item).not(selector.unselectable).eq(0),
                            hasNext        = $nextAvailable.length > 0
                        ;
                        if (hasNext) {
                            module.verbose('Moving selection to', $nextAvailable);
                            $nextAvailable.addClass(className.selected);
                        } else {
                            module.verbose('Moving selection to', $prevAvailable);
                            $prevAvailable.addClass(className.selected);
                        }
                    },
                },

                setup: {
                    api: function () {
                        var
                            apiSettings = {
                                debug: settings.debug,
                                urlData: {
                                    value: module.get.value(),
                                    query: module.get.query(),
                                },
                                on: false,
                            }
                        ;
                        module.verbose('First request, initializing API');
                        $module
                            .api(apiSettings)
                        ;
                    },
                    layout: function () {
                        if ($module.is('select')) {
                            module.setup.select();
                            module.setup.returnedObject();
                        }
                        if (!module.has.menu()) {
                            module.create.menu();
                        }
                        if (module.is.clearable() && !module.has.clearItem()) {
                            module.verbose('Adding clear icon');
                            $clear = $('<i />')
                                .addClass('remove icon')
                                .insertAfter($icon)
                            ;
                        }
                        if (module.is.search() && !module.has.search()) {
                            module.verbose('Adding search input');
                            var
                                labelNode = $module.prev('label')
                            ;
                            $search = $('<input />')
                                .addClass(className.search)
                                .prop('autocomplete', module.is.chrome() ? 'fomantic-search' : 'off')
                            ;
                            if (labelNode.length > 0) {
                                if (!labelNode.attr('id')) {
                                    labelNode.attr('id', '_' + module.get.id() + '_formLabel');
                                }
                                $search.attr('aria-labelledby', labelNode.attr('id'));
                            }
                            $search.insertBefore($text);
                        }
                        if (module.is.multiple() && module.is.searchSelection() && !module.has.sizer()) {
                            module.create.sizer();
                        }
                        if (settings.allowTab) {
                            module.set.tabbable();
                        }
                    },
                    select: function () {
                        var
                            selectValues  = module.get.selectValues()
                        ;
                        module.debug('Dropdown initialized on a select', selectValues);
                        if ($module.is('select')) {
                            $input = $module;
                        }
                        // see if select is placed correctly already
                        if ($input.parent(selector.dropdown).length > 0) {
                            module.debug('UI dropdown already exists. Creating dropdown menu only');
                            $module = $input.closest(selector.dropdown);
                            if (!module.has.menu()) {
                                module.create.menu();
                            }
                            $menu = $module.children(selector.menu);
                            module.setup.menu(selectValues);
                        } else {
                            module.debug('Creating entire dropdown from select');
                            $module = $('<div />')
                                .attr('class', $input.attr('class'))
                                .addClass(className.selection)
                                .addClass(className.dropdown)
                                .html(templates.dropdown(selectValues, fields, settings.preserveHTML, settings.className))
                                .insertBefore($input)
                            ;
                            if ($input.hasClass(className.multiple) && $input.prop('multiple') === false) {
                                module.error(error.missingMultiple);
                                $input.prop('multiple', true);
                            }
                            if ($input.is('[multiple]')) {
                                module.set.multiple();
                            }
                            if ($input.prop('disabled')) {
                                module.debug('Disabling dropdown');
                                $module.addClass(className.disabled);
                            }
                            if ($input.is('[required]')) {
                                settings.forceSelection = true;
                            }
                            if (!settings.allowTab) {
                                $input.removeAttr('tabindex');
                            }
                            $input
                                .prop('required', false)
                                .removeAttr('class')
                                .detach()
                                .prependTo($module)
                            ;
                        }
                        module.refresh();
                    },
                    menu: function (values) {
                        $menu.html(templates.menu(values, fields, settings.preserveHTML, settings.className));
                        $item = $menu.find(selector.item);
                        $divider = settings.hideDividers ? $item.parent().children(selector.divider) : $();
                    },
                    reference: function () {
                        module.debug('Dropdown behavior was called on select, replacing with closest dropdown');
                        // replace module reference
                        $module = $module.parent(selector.dropdown);
                        instance = $module.data(moduleNamespace);
                        element = $module[0];
                        module.refresh();
                        module.setup.returnedObject();
                    },
                    returnedObject: function () {
                        var
                            $firstModules = $allModules.slice(0, elementIndex),
                            $lastModules  = $allModules.slice(elementIndex + 1)
                        ;
                        // adjust all modules to use correct reference
                        $allModules = $firstModules.add($module).add($lastModules);
                    },
                },

                refresh: function () {
                    module.refreshSelectors();
                    module.refreshData();
                },

                refreshItems: function () {
                    $item = $menu.find(selector.item);
                    $divider = settings.hideDividers ? $item.parent().children(selector.divider) : $();
                },

                refreshSelectors: function () {
                    module.verbose('Refreshing selector cache');
                    $text = $module.find(selector.text);
                    $search = $module.find(selector.search);
                    $input = $module.find(selector.input);
                    $icon = $module.find(selector.icon);
                    $combo = $module.prev().find(selector.text).length > 0
                        ? $module.prev().find(selector.text)
                        : $module.prev();
                    $menu = $module.children(selector.menu);
                    $item = $menu.find(selector.item);
                    $divider = settings.hideDividers ? $item.parent().children(selector.divider) : $();
                },

                refreshData: function () {
                    module.verbose('Refreshing cached metadata');
                    $item
                        .removeData(metadata.text)
                        .removeData(metadata.value)
                    ;
                },

                clearData: function () {
                    module.verbose('Clearing metadata');
                    $item
                        .removeData(metadata.text)
                        .removeData(metadata.value)
                    ;
                    $module
                        .removeData(metadata.defaultText)
                        .removeData(metadata.defaultValue)
                        .removeData(metadata.placeholderText)
                    ;
                },

                clearItems: function () {
                    $menu.empty();
                    module.refreshItems();
                },

                toggle: function () {
                    module.verbose('Toggling menu visibility');
                    if (!module.is.active()) {
                        module.show();
                    } else {
                        module.hide();
                    }
                },

                show: function (callback, preventFocus) {
                    callback = isFunction(callback)
                        ? callback
                        : function () {};
                    if ((focused || iconClicked) && module.is.remote() && module.is.noApiCache() && !module.has.maxSelections()) {
                        module.clearItems();
                    }
                    if (!module.can.show() && module.is.remote()) {
                        module.debug('No API results retrieved, searching before show');
                        module.queryRemote(module.get.query(), module.show, [callback, preventFocus]);
                    }
                    if (module.can.show() && !module.is.active()) {
                        module.debug('Showing dropdown');
                        if (module.has.message() && !(module.has.maxSelections() || module.has.allResultsFiltered())) {
                            module.remove.message();
                        }
                        if (module.is.allFiltered()) {
                            return true;
                        }
                        if (settings.onShow.call(element) !== false) {
                            module.remove.empty();
                            module.animate.show(function () {
                                module.bind.intent();
                                if (module.has.search() && !preventFocus) {
                                    module.focusSearch();
                                }
                                module.set.visible();
                                callback.call(element);
                            });
                        }
                    }
                },

                hide: function (callback, preventBlur) {
                    callback = isFunction(callback)
                        ? callback
                        : function () {};
                    if (module.is.active() && !module.is.animatingOutward()) {
                        module.debug('Hiding dropdown');
                        if (settings.onHide.call(element) !== false) {
                            module.animate.hide(function () {
                                module.remove.visible();
                                // hiding search focus
                                if (module.is.focusedOnSearch() && preventBlur !== true) {
                                    $search.trigger('blur');
                                }
                                callback.call(element);
                            });
                            // Hide submenus explicitly. On some browsers (esp. mobile), they will not automatically receive a
                            // mouseleave event
                            var $subMenu = $module.find(selector.menu);
                            if ($subMenu.length > 0) {
                                module.verbose('Hiding sub-menu', $subMenu);
                                $subMenu.each(function () {
                                    var $sub = $(this);
                                    if (!module.is.animating($sub)) {
                                        module.animate.hide(false, $sub);
                                    }
                                });
                            }
                        }
                    } else {
                        module.unbind.intent();
                    }
                    iconClicked = false;
                    focused = false;
                },

                hideOthers: function () {
                    module.verbose('Finding other dropdowns to hide');
                    $allModules
                        .not($module)
                        .has(selector.menu + '.' + className.visible)
                        .dropdown('hide')
                    ;
                },

                hideMenu: function () {
                    module.verbose('Hiding menu  instantaneously');
                    module.remove.active();
                    module.remove.visible();
                    $menu.transition('destroy').transition('hide');
                },

                hideSubMenus: function () {
                    var
                        $subMenus = $menu.children(selector.item).find(selector.menu)
                    ;
                    module.verbose('Hiding sub menus', $subMenus);
                    $subMenus.transition('hide');
                },

                bind: {
                    events: function () {
                        module.bind.keyboardEvents();
                        module.bind.inputEvents();
                        module.bind.mouseEvents();
                    },
                    keyboardEvents: function () {
                        module.verbose('Binding keyboard events');
                        $module
                            .on('keydown' + eventNamespace, module.event.keydown)
                        ;
                        if (module.has.search()) {
                            $module
                                .on(module.get.inputEvent() + eventNamespace, selector.search, module.event.input)
                            ;
                        }
                        if (module.is.multiple()) {
                            $document
                                .on('keydown' + elementNamespace, module.event.document.keydown)
                            ;
                        }
                    },
                    inputEvents: function () {
                        module.verbose('Binding input change events');
                        $module
                            .on('change' + eventNamespace, selector.input, module.event.change)
                        ;
                        if (module.is.multiple() && module.is.searchSelection()) {
                            $module
                                .on('paste' + eventNamespace, selector.search, module.event.paste)
                            ;
                        }
                    },
                    mouseEvents: function () {
                        module.verbose('Binding mouse events');
                        if (module.is.multiple()) {
                            $module
                                .on('click' + eventNamespace, selector.label, module.event.label.click)
                                .on('click' + eventNamespace, selector.remove, module.event.remove.click)
                            ;
                        }
                        if (module.is.searchSelection()) {
                            $module
                                .on('mousedown' + eventNamespace, module.event.mousedown)
                                .on('mouseup' + eventNamespace, module.event.mouseup)
                                .on('mousedown' + eventNamespace, selector.menu, module.event.menu.mousedown)
                                .on('mouseup' + eventNamespace, selector.menu, module.event.menu.mouseup)
                                .on('click' + eventNamespace, selector.icon, module.event.icon.click)
                                .on('click' + eventNamespace, selector.clearIcon, module.event.clearIcon.click)
                                .on('focus' + eventNamespace, selector.search, module.event.search.focus)
                                .on('click' + eventNamespace, selector.search, module.event.search.focus)
                                .on('blur' + eventNamespace, selector.search, module.event.search.blur)
                                .on('click' + eventNamespace, selector.text, module.event.text.focus)
                            ;
                            if (module.is.multiple()) {
                                $module
                                    .on('click' + eventNamespace, module.event.click)
                                    .on('click' + eventNamespace, module.event.search.focus)
                                ;
                            }
                        } else {
                            if (settings.on === 'click') {
                                $module
                                    .on('click' + eventNamespace, selector.icon, module.event.icon.click)
                                    .on('click' + eventNamespace, module.event.test.toggle)
                                ;
                            } else if (settings.on === 'hover') {
                                $module
                                    .on('mouseenter' + eventNamespace, module.delay.show)
                                    .on('mouseleave' + eventNamespace, module.delay.hide)
                                    .on('touchstart' + eventNamespace, module.event.test.toggle)
                                    .on('touchstart' + eventNamespace, selector.icon, module.event.icon.click)
                                ;
                            } else {
                                $module
                                    .on(settings.on + eventNamespace, module.toggle)
                                ;
                            }
                            $module
                                .on('mousedown' + eventNamespace, module.event.mousedown)
                                .on('mouseup' + eventNamespace, module.event.mouseup)
                                .on('focus' + eventNamespace, module.event.focus)
                                .on('click' + eventNamespace, selector.clearIcon, module.event.clearIcon.click)
                            ;
                            if (module.has.menuSearch()) {
                                $module
                                    .on('blur' + eventNamespace, selector.search, module.event.search.blur)
                                ;
                            } else {
                                $module
                                    .on('blur' + eventNamespace, module.event.blur)
                                ;
                            }
                        }
                        $menu
                            .on('mouseenter' + eventNamespace, selector.item, module.event.item.mouseenter)
                            .on('touchstart' + eventNamespace, selector.item, module.event.item.mouseenter)
                            .on('mouseleave' + eventNamespace, selector.item, module.event.item.mouseleave)
                            .on('click' + eventNamespace, selector.item, module.event.item.click)
                        ;
                    },
                    intent: function () {
                        module.verbose('Binding hide intent event to document');
                        $document
                            .on('click' + elementNamespace, module.event.test.hide)
                        ;
                    },
                },

                unbind: {
                    intent: function () {
                        module.verbose('Removing hide intent event from document');
                        $document
                            .off('click' + elementNamespace)
                        ;
                    },
                },

                filter: function (query) {
                    var
                        searchTerm = query !== undefined
                            ? query
                            : module.get.query(),
                        afterFiltered = function () {
                            if (module.is.multiple()) {
                                module.filterActive();
                            }
                            if (query || (!query && module.get.activeItem().length === 0)) {
                                module.select.firstUnfiltered();
                            }
                            if (module.has.allResultsFiltered()) {
                                if (settings.onNoResults.call(element, searchTerm)) {
                                    if (settings.allowAdditions) {
                                        if (settings.hideAdditions) {
                                            module.verbose('User addition with no menu, setting empty style');
                                            module.set.empty();
                                            module.hideMenu();
                                        }
                                    } else {
                                        module.verbose('All items filtered, showing message', searchTerm);
                                        module.add.message(message.noResults);
                                    }
                                } else {
                                    module.verbose('All items filtered, hiding dropdown', searchTerm);
                                    module.set.empty();
                                    module.hideMenu();
                                }
                            } else {
                                module.remove.empty();
                                module.remove.message();
                            }
                            if (settings.allowAdditions) {
                                module.add.userSuggestion(module.escape.htmlEntities(query));
                            }
                            if (module.is.searchSelection() && module.can.show() && module.is.focusedOnSearch() && !module.is.empty()) {
                                module.show();
                            }
                        }
                    ;
                    if (settings.useLabels && module.has.maxSelections()) {
                        module.show();

                        return;
                    }
                    if (settings.apiSettings) {
                        if (module.can.useAPI()) {
                            module.queryRemote(searchTerm, function () {
                                if (settings.filterRemoteData) {
                                    module.filterItems(searchTerm);
                                }
                                var preSelected = $input.val();
                                if (!Array.isArray(preSelected)) {
                                    preSelected = preSelected && preSelected !== '' ? preSelected.split(settings.delimiter) : [];
                                }
                                if (module.is.multiple()) {
                                    $.each(preSelected, function (index, value) {
                                        $item.filter('[data-value="' + value + '"]')
                                            .addClass(className.filtered)
                                        ;
                                    });
                                }
                                module.focusSearch(true);
                                afterFiltered();
                            });
                        } else {
                            module.error(error.noAPI);
                        }
                    } else {
                        module.filterItems(searchTerm);
                        afterFiltered();
                    }
                },

                queryRemote: function (query, callback, callbackParameters) {
                    if (!Array.isArray(callbackParameters)) {
                        callbackParameters = [callbackParameters];
                    }
                    var
                        apiSettings = {
                            errorDuration: false,
                            cache: 'local',
                            throttle: settings.throttle,
                            urlData: {
                                query: query,
                            },
                        },
                        apiCallbacks = {
                            onError: function (errorMessage, $module, xhr) {
                                module.add.message(message.serverError);
                                iconClicked = false;
                                focused = false;
                                callback.apply(null, callbackParameters);
                                if (typeof settings.apiSettings.onError === 'function') {
                                    settings.apiSettings.onError.call(this, errorMessage, $module, xhr);
                                }
                            },
                            onFailure: function (response, $module, xhr) {
                                module.add.message(message.serverError);
                                iconClicked = false;
                                focused = false;
                                callback.apply(null, callbackParameters);
                                if (typeof settings.apiSettings.onFailure === 'function') {
                                    settings.apiSettings.onFailure.call(this, response, $module, xhr);
                                }
                            },
                            onSuccess: function (response, $module, xhr) {
                                var
                                    values          = response[fields.remoteValues]
                                ;
                                if (!Array.isArray(values)) {
                                    values = [];
                                }
                                module.remove.message();
                                var menuConfig = {};
                                menuConfig[fields.values] = values;
                                module.setup.menu(menuConfig);

                                if (values.length === 0 && !settings.allowAdditions) {
                                    module.add.message(message.noResults);
                                } else {
                                    var value = module.is.multiple() ? module.get.values() : module.get.value();
                                    if (value !== '') {
                                        module.verbose('Value(s) present after click icon, select value(s) in items');
                                        module.set.selected(value, null, true, true);
                                    }
                                }
                                iconClicked = false;
                                focused = false;
                                callback.apply(null, callbackParameters);
                                if (typeof settings.apiSettings.onSuccess === 'function') {
                                    settings.apiSettings.onSuccess.call(this, response, $module, xhr);
                                }
                            },
                        }
                    ;
                    if (!$module.api('get request')) {
                        module.setup.api();
                    }
                    apiSettings = $.extend(true, {}, apiSettings, settings.apiSettings, apiCallbacks, tempDisableApiCache ? { cache: false } : {});
                    $module
                        .api('setting', apiSettings)
                        .api('query')
                    ;
                    tempDisableApiCache = false;
                },

                filterItems: function (query) {
                    var
                        searchTerm = module.remove.diacritics(
                            query !== undefined
                                ? query
                                : module.get.query()
                        ),
                        results          =  null,
                        escapedTerm      = module.escape.string(searchTerm),
                        regExpFlags      = (settings.ignoreSearchCase ? 'i' : '') + 'gm',
                        beginsWithRegExp = new RegExp('^' + escapedTerm, regExpFlags)
                    ;
                    // avoid loop if we're matching nothing
                    if (module.has.query()) {
                        results = [];

                        module.verbose('Searching for matching values', searchTerm);
                        $item
                            .each(function () {
                                var
                                    $choice = $(this),
                                    text,
                                    value
                                ;
                                if ($choice.hasClass(className.unfilterable)) {
                                    results.push(this);

                                    return true;
                                }
                                if (settings.match === 'both' || settings.match === 'text') {
                                    text = module.remove.diacritics(String(module.get.choiceText($choice, false)));
                                    if (text.search(beginsWithRegExp) !== -1
                                        || (settings.fullTextSearch === 'exact' && module.exactSearch(searchTerm, text))
                                        || (settings.fullTextSearch === true && module.fuzzySearch(searchTerm, text))
                                    ) {
                                        results.push(this);

                                        return true;
                                    }
                                }
                                if (settings.match === 'both' || settings.match === 'value') {
                                    value = module.remove.diacritics(String(module.get.choiceValue($choice, text)));
                                    if (value.search(beginsWithRegExp) !== -1
                                        || (settings.fullTextSearch === 'exact' && module.exactSearch(searchTerm, value))
                                        || (settings.fullTextSearch === true && module.fuzzySearch(searchTerm, value))
                                    ) {
                                        results.push(this);

                                        return true;
                                    }
                                }
                            })
                        ;
                    }
                    module.debug('Showing only matched items', searchTerm);
                    module.remove.filteredItem();
                    if (results) {
                        $item
                            .not(results)
                            .addClass(className.filtered)
                        ;
                    }

                    if (!module.has.query()) {
                        $divider
                            .removeClass(className.hidden)
                        ;
                    } else if (settings.hideDividers === true) {
                        $divider
                            .addClass(className.hidden)
                        ;
                    } else if (settings.hideDividers === 'empty') {
                        $divider
                            .removeClass(className.hidden)
                            .filter(function () {
                                // First find the last divider in this divider group
                                // Dividers which are direct siblings are considered a group
                                var $lastDivider = $(this).nextUntil(selector.item);

                                return ($lastDivider.length > 0 ? $lastDivider : $(this))
                                    // Count all non-filtered items until the next divider (or end of the dropdown)
                                    .nextUntil(selector.divider)
                                    .filter(selector.item + ':not(.' + className.filtered + ')')
                                    // Hide divider if no items are found
                                    .length === 0;
                            })
                            .addClass(className.hidden)
                        ;
                    }
                },

                fuzzySearch: function (query, term) {
                    var
                        termLength  = term.length,
                        queryLength = query.length
                    ;
                    query = settings.ignoreSearchCase ? query.toLowerCase() : query;
                    term = settings.ignoreSearchCase ? term.toLowerCase() : term;
                    if (queryLength > termLength) {
                        return false;
                    }
                    if (queryLength === termLength) {
                        return query === term;
                    }
                    for (var characterIndex = 0, nextCharacterIndex = 0; characterIndex < queryLength; characterIndex++) {
                        var
                            continueSearch = false,
                            queryCharacter = query.charCodeAt(characterIndex)
                        ;
                        while (nextCharacterIndex < termLength) {
                            if (term.charCodeAt(nextCharacterIndex++) === queryCharacter) {
                                continueSearch = true;

                                break;
                            }
                        }

                        if (!continueSearch) {
                            return false;
                        }
                    }

                    return true;
                },
                exactSearch: function (query, term) {
                    query = settings.ignoreSearchCase ? query.toLowerCase() : query;
                    term = settings.ignoreSearchCase ? term.toLowerCase() : term;

                    return term.indexOf(query) > -1;
                },
                filterActive: function () {
                    if (settings.useLabels) {
                        $item.filter('.' + className.active)
                            .addClass(className.filtered)
                        ;
                    }
                },

                focusSearch: function (skipHandler) {
                    if (module.has.search() && !module.is.focusedOnSearch()) {
                        if (skipHandler) {
                            $module.off('focus' + eventNamespace, selector.search);
                            $search.trigger('focus');
                            $module.on('focus' + eventNamespace, selector.search, module.event.search.focus);
                        } else {
                            $search.trigger('focus');
                        }
                    }
                },

                blurSearch: function () {
                    if (module.has.search()) {
                        $search.trigger('blur');
                    }
                },

                forceSelection: function () {
                    var
                        $currentlySelected = $item.not(className.filtered).filter('.' + className.selected).eq(0),
                        $activeItem        = $item.not(className.filtered).filter('.' + className.active).eq(0),
                        $selectedItem      = $currentlySelected.length > 0
                            ? $currentlySelected
                            : $activeItem,
                        hasSelected = $selectedItem.length > 0
                    ;
                    if (settings.allowAdditions || (hasSelected && !module.is.multiple())) {
                        module.debug('Forcing partial selection to selected item', $selectedItem);
                        module.event.item.click.call($selectedItem, {}, true);
                    } else {
                        module.remove.searchTerm();
                    }
                },

                change: {
                    values: function (values) {
                        if (!settings.allowAdditions) {
                            module.clear();
                        }
                        module.debug('Creating dropdown with specified values', values);
                        var menuConfig = {};
                        menuConfig[fields.values] = values;
                        module.setup.menu(menuConfig);
                        $.each(values, function (index, item) {
                            if (item.selected === true) {
                                module.debug('Setting initial selection to', item[fields.value]);
                                module.set.selected(item[fields.value]);
                                if (!module.is.multiple()) {
                                    return false;
                                }
                            }
                        });

                        if (module.has.selectInput()) {
                            module.disconnect.selectObserver();
                            $input.html('');
                            $input.append('<option disabled selected value></option>');
                            $.each(values, function (index, item) {
                                var
                                    value = settings.templates.deQuote(item[fields.value]),
                                    name = settings.templates.escape(
                                        item[fields.name] || '',
                                        settings.preserveHTML
                                    )
                                ;
                                $input.append('<option value="' + value + '"' + (item.selected === true ? ' selected' : '') + '>' + name + '</option>');
                            });
                            module.observe.select();
                        }
                    },
                },

                event: {
                    paste: function (event) {
                        var
                            pasteValue = (event.originalEvent.clipboardData || window.clipboardData).getData('text'),
                            tokens = pasteValue.split(settings.delimiter),
                            notFoundTokens = []
                        ;
                        tokens.forEach(function (value) {
                            if (module.set.selected(module.escape.htmlEntities(value.trim()), null, false, true) === false) {
                                notFoundTokens.push(value.trim());
                            }
                        });
                        event.preventDefault();
                        if (notFoundTokens.length > 0) {
                            var searchEl = $search[0],
                                startPos = searchEl.selectionStart,
                                endPos = searchEl.selectionEnd,
                                orgText = searchEl.value,
                                pasteText = notFoundTokens.join(settings.delimiter),
                                newEndPos = startPos + pasteText.length
                            ;
                            $search.val(orgText.slice(0, startPos) + pasteText + orgText.slice(endPos));
                            searchEl.selectionStart = newEndPos;
                            searchEl.selectionEnd = newEndPos;
                            module.event.input(event);
                        }
                    },
                    change: function () {
                        if (!internalChange) {
                            module.debug('Input changed, updating selection');
                            module.set.selected();
                        }
                    },
                    focus: function () {
                        if (settings.showOnFocus && !activated && module.is.hidden() && !pageLostFocus) {
                            focused = true;
                            module.show();
                        }
                    },
                    blur: function (event) {
                        pageLostFocus = document.activeElement === this;
                        if (!activated && !pageLostFocus) {
                            module.remove.activeLabel();
                            module.hide();
                        }
                    },
                    mousedown: function () {
                        if (module.is.searchSelection(true)) {
                            // prevent menu hiding on immediate re-focus
                            willRefocus = true;
                        } else {
                            // prevents focus callback from occurring on mousedown
                            activated = true;
                        }
                    },
                    mouseup: function () {
                        if (module.is.searchSelection(true)) {
                            // prevent menu hiding on immediate re-focus
                            willRefocus = false;
                        } else {
                            activated = false;
                        }
                    },
                    click: function (event) {
                        var
                            $target = $(event.target)
                        ;
                        // focus search
                        if ($target.is($module)) {
                            if (!module.is.focusedOnSearch()) {
                                module.focusSearch();
                            } else {
                                module.show();
                            }
                        }
                    },
                    search: {
                        focus: function (event) {
                            activated = true;
                            if (module.is.multiple()) {
                                module.remove.activeLabel();
                            }
                            if (!focused && !module.is.active() && (settings.showOnFocus || (event.type !== 'focus' && event.type !== 'focusin')) && event.type !== 'touchstart') {
                                focused = true;
                                module.search();
                            }
                        },
                        blur: function (event) {
                            pageLostFocus = document.activeElement === this;
                            if (module.is.searchSelection(true) && !willRefocus) {
                                if (!itemActivated && !pageLostFocus) {
                                    if (settings.forceSelection) {
                                        module.forceSelection();
                                    } else if (!settings.allowAdditions) {
                                        module.remove.searchTerm();
                                    }
                                    module.hide();
                                }
                            }
                            willRefocus = false;
                        },
                    },
                    clearIcon: {
                        click: function (event) {
                            module.clear();
                            if (module.is.searchSelection()) {
                                module.remove.searchTerm();
                            }
                            module.hide();
                            event.stopPropagation();
                        },
                    },
                    icon: {
                        click: function (event) {
                            iconClicked = true;
                            if (module.has.search()) {
                                if (!module.is.active()) {
                                    if (settings.showOnFocus) {
                                        module.focusSearch();
                                    } else {
                                        module.toggle();
                                    }
                                } else {
                                    module.blurSearch();
                                }
                            } else {
                                module.toggle();
                            }
                            event.stopPropagation();
                        },
                    },
                    text: {
                        focus: function (event) {
                            activated = true;
                            module.focusSearch();
                        },
                    },
                    input: function (event) {
                        if (module.is.multiple() || module.is.searchSelection()) {
                            module.set.filtered();
                        }
                        clearTimeout(module.timer);
                        module.timer = setTimeout(function () { module.search(); }, settings.delay.search);
                    },
                    label: {
                        click: function (event) {
                            var
                                $label        = $(this),
                                $labels       = $module.find(selector.label),
                                $activeLabels = $labels.filter('.' + className.active),
                                $nextActive   = $label.nextAll('.' + className.active),
                                $prevActive   = $label.prevAll('.' + className.active),
                                $range = $nextActive.length > 0
                                    ? $label.nextUntil($nextActive).add($activeLabels).add($label)
                                    : $label.prevUntil($prevActive).add($activeLabels).add($label)
                            ;
                            if (event.shiftKey) {
                                $activeLabels.removeClass(className.active);
                                $range.addClass(className.active);
                            } else if (event.ctrlKey) {
                                $label.toggleClass(className.active);
                            } else {
                                $activeLabels.removeClass(className.active);
                                $label.addClass(className.active);
                            }
                            settings.onLabelSelect.apply(this, $labels.filter('.' + className.active));
                            event.stopPropagation();
                        },
                    },
                    remove: {
                        click: function (event) {
                            var
                                $label = $(this).parent()
                            ;
                            if ($label.hasClass(className.active)) {
                                // remove all selected labels
                                module.remove.activeLabels();
                            } else {
                                // remove this label only
                                module.remove.activeLabels($label);
                            }
                            event.stopPropagation();
                        },
                    },
                    test: {
                        toggle: function (event) {
                            var
                                toggleBehavior = module.is.multiple()
                                    ? module.show
                                    : module.toggle
                            ;
                            if (module.is.bubbledLabelClick(event) || module.is.bubbledIconClick(event)) {
                                return;
                            }
                            if (!module.is.multiple() || (module.is.multiple() && !module.is.active())) {
                                focused = true;
                            }
                            if (module.determine.eventOnElement(event, toggleBehavior) && event.type !== 'touchstart') {
                                // do not preventDefault of touchstart; so emulated mouseenter is triggered on first touch and not later
                                // (when selecting an item). The double-showing of the dropdown through both events does not hurt.
                                event.preventDefault();
                            }
                        },
                        hide: function (event) {
                            if (module.determine.eventInModule(event, module.hide)) {
                                if (element.id && $(event.target).attr('for') === element.id) {
                                    event.preventDefault();
                                }
                            }
                        },
                    },
                    class: {
                        mutation: function (mutations) {
                            mutations.forEach(function (mutation) {
                                if (mutation.attributeName === 'class') {
                                    module.check.disabled();
                                }
                            });
                        },
                    },
                    select: {
                        mutation: function (mutations) {
                            if (module.is.selectMutation(mutations)) {
                                module.debug('<select> modified, recreating menu');
                                module.disconnect.selectObserver();
                                module.refresh();
                                module.setup.select();
                                module.set.selected();
                                module.observe.select();
                            }
                        },
                    },
                    menu: {
                        mutation: function (mutations) {
                            var
                                mutation   = mutations[0],
                                $addedNode = mutation.addedNodes
                                    ? $(mutation.addedNodes[0])
                                    : $(false),
                                $removedNode = mutation.removedNodes
                                    ? $(mutation.removedNodes[0])
                                    : $(false),
                                $changedNodes  = $addedNode.add($removedNode),
                                isUserAddition = $changedNodes.is(selector.addition) || $changedNodes.closest(selector.addition).length > 0,
                                isMessage      = $changedNodes.is(selector.message) || $changedNodes.closest(selector.message).length > 0
                            ;
                            if (isUserAddition || isMessage) {
                                module.debug('Updating item selector cache');
                                module.refreshItems();
                            } else {
                                module.debug('Menu modified, updating selector cache');
                                module.refresh();
                            }
                        },
                        mousedown: function () {
                            itemActivated = true;
                        },
                        mouseup: function () {
                            itemActivated = false;
                        },
                    },
                    item: {
                        mouseenter: function (event) {
                            var
                                $target        = $(event.target),
                                $item          = $(this),
                                $subMenu       = $item.children(selector.menu),
                                $otherMenus    = $item.siblings(selector.item).children(selector.menu),
                                hasSubMenu     = $subMenu.length > 0,
                                isBubbledEvent = $subMenu.find($target).length > 0
                            ;
                            if (!isBubbledEvent && hasSubMenu) {
                                clearTimeout(module.itemTimer);
                                module.itemTimer = setTimeout(function () {
                                    module.verbose('Showing sub-menu', $subMenu);
                                    $.each($otherMenus, function () {
                                        module.animate.hide(false, $(this));
                                    });
                                    module.animate.show(false, $subMenu);
                                }, settings.delay.show);
                                event.preventDefault();
                            }
                        },
                        mouseleave: function (event) {
                            var
                                $subMenu = $(this).find(selector.menu)
                            ;
                            if ($subMenu.length > 0) {
                                clearTimeout(module.itemTimer);
                                module.itemTimer = setTimeout(function () {
                                    module.verbose('Hiding sub-menu', $subMenu);
                                    $subMenu.each(function () {
                                        module.animate.hide(false, $(this));
                                    });
                                }, settings.delay.hide);
                            }
                        },
                        click: function (event, skipRefocus) {
                            var
                                $choice        = $(this),
                                $target        = event
                                    ? $(event.target || '')
                                    : $(''),
                                $subMenu       = $choice.find(selector.menu),
                                text           = module.get.choiceText($choice),
                                value          = module.get.choiceValue($choice, text),
                                hasSubMenu     = $subMenu.length > 0,
                                isBubbledEvent = $subMenu.find($target).length > 0
                            ;
                            // prevents IE11 bug where menu receives focus even though `tabindex=-1`
                            if (document.activeElement.tagName.toLowerCase() !== 'input') {
                                $(document.activeElement).trigger('blur');
                            }
                            if (!isBubbledEvent && (!hasSubMenu || settings.allowCategorySelection)) {
                                if (module.is.searchSelection()) {
                                    if (settings.allowAdditions) {
                                        module.remove.userAddition();
                                    }
                                    if (!settings.keepSearchTerm) {
                                        module.remove.filteredItem();
                                        module.remove.searchTerm();
                                    }
                                    if (!module.is.visible() && $target.length > 0) {
                                        module.show();
                                    }
                                    if (!module.is.focusedOnSearch() && skipRefocus !== true) {
                                        module.focusSearch(true);
                                    }
                                }
                                if (!settings.useLabels) {
                                    module.remove.filteredItem();
                                    module.set.scrollPosition($choice);
                                }
                                module.determine.selectAction.call(this, text, value);
                            }
                        },
                    },

                    document: {
                        // label selection should occur even when element has no focus
                        keydown: function (event) {
                            var
                                pressedKey    = event.which,
                                isShortcutKey = module.is.inObject(pressedKey, keys)
                            ;
                            if (isShortcutKey) {
                                var
                                    $label            = $module.find(selector.label),
                                    $activeLabel      = $label.filter('.' + className.active),
                                    activeValue       = $activeLabel.data(metadata.value),
                                    labelIndex        = $label.index($activeLabel),
                                    labelCount        = $label.length,
                                    hasActiveLabel    = $activeLabel.length > 0,
                                    hasMultipleActive = $activeLabel.length > 1,
                                    isFirstLabel      = labelIndex === 0,
                                    isLastLabel       = labelIndex + 1 === labelCount,
                                    isSearch          = module.is.searchSelection(),
                                    isFocusedOnSearch = module.is.focusedOnSearch(),
                                    isFocused         = module.is.focused(),
                                    caretAtStart      = isFocusedOnSearch && module.get.caretPosition(false) === 0,
                                    isSelectedSearch  = caretAtStart && module.get.caretPosition(true) !== 0
                                ;
                                if (isSearch && !hasActiveLabel && !isFocusedOnSearch) {
                                    return;
                                }

                                switch (pressedKey) {
                                    case keys.leftArrow: {
                                        // activate previous label
                                        if ((isFocused || caretAtStart) && !hasActiveLabel) {
                                            module.verbose('Selecting previous label');
                                            $label.last().addClass(className.active);
                                        } else if (hasActiveLabel) {
                                            if (!event.shiftKey) {
                                                module.verbose('Selecting previous label');
                                                $label.removeClass(className.active);
                                            } else {
                                                module.verbose('Adding previous label to selection');
                                            }
                                            if (isFirstLabel && !hasMultipleActive) {
                                                $activeLabel.addClass(className.active);
                                            } else {
                                                $activeLabel.prev(selector.siblingLabel)
                                                    .addClass(className.active)
                                                    .end()
                                                ;
                                            }
                                            event.preventDefault();
                                        }

                                        break;
                                    }
                                    case keys.rightArrow: {
                                        // activate first label
                                        if (isFocused && !hasActiveLabel) {
                                            $label.first().addClass(className.active);
                                        }
                                        // activate next label
                                        if (hasActiveLabel) {
                                            if (!event.shiftKey) {
                                                module.verbose('Selecting next label');
                                                $label.removeClass(className.active);
                                            } else {
                                                module.verbose('Adding next label to selection');
                                            }
                                            if (isLastLabel) {
                                                if (isSearch) {
                                                    if (!isFocusedOnSearch) {
                                                        module.focusSearch();
                                                    } else {
                                                        $label.removeClass(className.active);
                                                    }
                                                } else if (hasMultipleActive) {
                                                    $activeLabel.next(selector.siblingLabel).addClass(className.active);
                                                } else {
                                                    $activeLabel.addClass(className.active);
                                                }
                                            } else {
                                                $activeLabel.next(selector.siblingLabel).addClass(className.active);
                                            }
                                            event.preventDefault();
                                        }

                                        break;
                                    }
                                    case keys.deleteKey:
                                    case keys.backspace: {
                                        if (hasActiveLabel) {
                                            module.verbose('Removing active labels');
                                            if (isLastLabel) {
                                                if (isSearch && !isFocusedOnSearch) {
                                                    module.focusSearch();
                                                }
                                            }
                                            $activeLabel.last().next(selector.siblingLabel).addClass(className.active);
                                            module.remove.activeLabels($activeLabel);
                                            if (!module.is.visible()) {
                                                module.show();
                                            }
                                            event.preventDefault();
                                        } else if (caretAtStart && !isSelectedSearch && !hasActiveLabel && pressedKey === keys.backspace) {
                                            module.verbose('Removing last label on input backspace');
                                            $activeLabel = $label.last().addClass(className.active);
                                            module.remove.activeLabels($activeLabel);
                                            if (!module.is.visible()) {
                                                module.show();
                                            }
                                        }

                                        break;
                                    }
                                    default: {
                                        $activeLabel.removeClass(className.active);
                                    }
                                }
                            }
                        },
                    },

                    keydown: function (event) {
                        var
                            pressedKey    = event.which,
                            isShortcutKey = module.is.inObject(pressedKey, keys) || event.key === settings.delimiter
                        ;
                        if (isShortcutKey) {
                            var
                                $currentlySelected = $item.not(selector.unselectable).filter('.' + className.selected).eq(0),
                                $activeItem        = $menu.children('.' + className.active).eq(0),
                                $selectedItem      = $currentlySelected.length > 0
                                    ? $currentlySelected
                                    : $activeItem,
                                $visibleItems = $selectedItem.length > 0
                                    ? $selectedItem.siblings(':not(.' + className.filtered + ')').addBack()
                                    : $menu.children(':not(.' + className.filtered + ')'),
                                $subMenu              = $selectedItem.children(selector.menu),
                                $parentMenu           = $selectedItem.closest(selector.menu),
                                inVisibleMenu         = $parentMenu.hasClass(className.visible) || $parentMenu.hasClass(className.animating) || $parentMenu.parent(selector.menu).length > 0,
                                hasSubMenu            = $subMenu.length > 0,
                                hasSelectedItem       = $selectedItem.length > 0,
                                selectedIsSelectable  = $selectedItem.not(selector.unselectable).length > 0,
                                delimiterPressed      = event.key === settings.delimiter && module.is.multiple(),
                                isAdditionWithoutMenu = settings.allowAdditions && (pressedKey === keys.enter || delimiterPressed),
                                $nextItem,
                                isSubMenuItem
                            ;
                            // allow selection with menu closed
                            if (isAdditionWithoutMenu) {
                                if (selectedIsSelectable && settings.hideAdditions) {
                                    module.verbose('Selecting item from keyboard shortcut', $selectedItem);
                                    module.event.item.click.call($selectedItem, event);
                                }
                                if (module.is.searchSelection()) {
                                    module.remove.searchTerm();
                                }
                                if (module.is.multiple()) {
                                    event.preventDefault();
                                }
                            }

                            // visible menu keyboard shortcuts
                            if (module.is.visible()) {
                                // enter (select or open sub-menu)
                                if (pressedKey === keys.enter || delimiterPressed) {
                                    if (pressedKey === keys.enter && hasSelectedItem && hasSubMenu && !settings.allowCategorySelection) {
                                        module.verbose('Pressed enter on unselectable category, opening sub menu');
                                        pressedKey = keys.rightArrow;
                                    } else if (selectedIsSelectable) {
                                        module.verbose('Selecting item from keyboard shortcut', $selectedItem);
                                        module.event.item.click.call($selectedItem, event);
                                        if (module.is.searchSelection()) {
                                            if (!settings.keepSearchTerm) {
                                                module.remove.searchTerm();
                                            }
                                            if (module.is.multiple()) {
                                                $search.trigger('focus');
                                            }
                                        }
                                    }
                                    event.preventDefault();
                                }

                                // sub-menu actions
                                if (hasSelectedItem) {
                                    if (pressedKey === keys.leftArrow) {
                                        isSubMenuItem = $parentMenu[0] !== $menu[0];

                                        if (isSubMenuItem) {
                                            module.verbose('Left key pressed, closing sub-menu');
                                            module.animate.hide(false, $parentMenu);
                                            $selectedItem
                                                .removeClass(className.selected)
                                            ;
                                            $parentMenu
                                                .closest(selector.item)
                                                .addClass(className.selected)
                                            ;
                                            event.preventDefault();
                                        }
                                    }

                                    // right arrow (show sub-menu)
                                    if (pressedKey === keys.rightArrow) {
                                        if (hasSubMenu) {
                                            module.verbose('Right key pressed, opening sub-menu');
                                            module.animate.show(false, $subMenu);
                                            $selectedItem
                                                .removeClass(className.selected)
                                            ;
                                            $subMenu
                                                .find(selector.item).eq(0)
                                                .addClass(className.selected)
                                            ;
                                            event.preventDefault();
                                        }
                                    }
                                }

                                // up arrow (traverse menu up)
                                if (pressedKey === keys.upArrow) {
                                    $nextItem = hasSelectedItem && inVisibleMenu
                                        ? $selectedItem.prevAll(selector.item + ':not(' + selector.unselectable + ')').eq(0)
                                        : $item.eq(0);
                                    if ($visibleItems.index($nextItem) < 0) {
                                        module.verbose('Up key pressed but reached top of current menu');
                                        event.preventDefault();

                                        return;
                                    }

                                    module.verbose('Up key pressed, changing active item');
                                    $selectedItem
                                        .removeClass(className.selected)
                                    ;
                                    $nextItem
                                        .addClass(className.selected)
                                    ;
                                    module.set.scrollPosition($nextItem);
                                    if (settings.selectOnKeydown && module.is.single() && !$nextItem.hasClass(className.actionable)) {
                                        module.set.selectedItem($nextItem);
                                    }

                                    event.preventDefault();
                                }

                                // down arrow (traverse menu down)
                                if (pressedKey === keys.downArrow) {
                                    $nextItem = hasSelectedItem && inVisibleMenu
                                        ? $selectedItem.nextAll(selector.item + ':not(' + selector.unselectable + ')').eq(0)
                                        : $item.eq(0);
                                    if ($nextItem.length === 0) {
                                        module.verbose('Down key pressed but reached bottom of current menu');
                                        event.preventDefault();

                                        return;
                                    }

                                    module.verbose('Down key pressed, changing active item');
                                    $item
                                        .removeClass(className.selected)
                                    ;
                                    $nextItem
                                        .addClass(className.selected)
                                    ;
                                    module.set.scrollPosition($nextItem);
                                    if (settings.selectOnKeydown && module.is.single() && !$nextItem.hasClass(className.actionable)) {
                                        module.set.selectedItem($nextItem);
                                    }

                                    event.preventDefault();
                                }

                                // page down (show next page)
                                if (pressedKey === keys.pageUp) {
                                    module.scrollPage('up');
                                    event.preventDefault();
                                }
                                if (pressedKey === keys.pageDown) {
                                    module.scrollPage('down');
                                    event.preventDefault();
                                }

                                // escape (close menu)
                                if (pressedKey === keys.escape) {
                                    module.verbose('Escape key pressed, closing dropdown');
                                    module.hide();
                                    event.stopPropagation();
                                }
                            } else {
                                // delimiter key
                                if (pressedKey === keys.enter || delimiterPressed) {
                                    event.preventDefault();
                                }
                                // down arrow (open menu)
                                if (pressedKey === keys.downArrow && !module.is.visible()) {
                                    module.verbose('Down key pressed, showing dropdown');
                                    module.show();
                                    event.preventDefault();
                                }
                            }
                        } else {
                            if (!module.has.search()) {
                                module.set.selectedLetter(String.fromCharCode(pressedKey));
                            }
                        }
                    },
                },

                trigger: {
                    change: function () {
                        var
                            inputElement = $input[0]
                        ;
                        if (inputElement) {
                            var events = document.createEvent('HTMLEvents');
                            module.verbose('Triggering native change event');
                            events.initEvent('change', true, false);
                            inputElement.dispatchEvent(events);
                        }
                    },
                },

                determine: {
                    selectAction: function (text, value) {
                        selectActionActive = true;
                        module.verbose('Determining action', settings.action);
                        if (isFunction(module.action[settings.action])) {
                            module.verbose('Triggering preset action', settings.action, text, value);
                            module.action[settings.action].call(element, text, value, this);
                        } else if (isFunction(settings.action)) {
                            module.verbose('Triggering user action', settings.action, text, value);
                            settings.action.call(element, text, value, this);
                        } else {
                            module.error(error.action, settings.action);
                        }
                        selectActionActive = false;
                    },
                    eventInModule: function (event, callback) {
                        var
                            $target    = $(event.target),
                            inDocument = $target.closest(document.documentElement).length > 0,
                            inModule   = $target.closest($module).length > 0
                        ;
                        callback = isFunction(callback)
                            ? callback
                            : function () {};
                        if (inDocument && !inModule) {
                            module.verbose('Triggering event', callback);
                            callback();

                            return true;
                        }

                        module.verbose('Event occurred in dropdown, canceling callback');

                        return false;
                    },
                    eventOnElement: function (event, callback) {
                        var
                            $target      = $(event.target),
                            $label       = $target.closest(selector.siblingLabel),
                            inVisibleDOM = document.body.contains(event.target),
                            notOnLabel   = $module.find($label).length === 0 || !(module.is.multiple() && settings.useLabels),
                            notInMenu    = $target.closest($menu).length === 0
                        ;
                        callback = isFunction(callback)
                            ? callback
                            : function () {};
                        if (inVisibleDOM && notOnLabel && notInMenu) {
                            module.verbose('Triggering event', callback);
                            callback();

                            return true;
                        }

                        module.verbose('Event occurred in dropdown menu, canceling callback');

                        return false;
                    },
                },

                action: {

                    nothing: function () {},

                    activate: function (text, value, element) {
                        value = value !== undefined
                            ? value
                            : text;
                        if (module.can.activate($(element))) {
                            module.set.selected(value, $(element), false, settings.keepSearchTerm);
                            if (!module.is.multiple() && !(!settings.collapseOnActionable && $(element).hasClass(className.actionable))) {
                                module.hideAndClear();
                            }
                        }
                    },

                    select: function (text, value, element) {
                        value = value !== undefined
                            ? value
                            : text;
                        if (module.can.activate($(element))) {
                            module.set.value(value, text, $(element));
                            if (!module.is.multiple() && !(!settings.collapseOnActionable && $(element).hasClass(className.actionable))) {
                                module.hideAndClear();
                            }
                        }
                    },

                    combo: function (text, value, element) {
                        value = value !== undefined
                            ? value
                            : text;
                        module.set.selected(value, $(element));
                        module.hideAndClear();
                    },

                    hide: function (text, value, element) {
                        module.set.value(value, text, $(element));
                        module.hideAndClear();
                    },

                },

                get: {
                    id: function () {
                        return id;
                    },
                    defaultText: function () {
                        return $module.data(metadata.defaultText);
                    },
                    defaultValue: function () {
                        return $module.data(metadata.defaultValue);
                    },
                    placeholderText: function () {
                        if (settings.placeholder !== 'auto' && typeof settings.placeholder === 'string') {
                            return settings.placeholder;
                        }

                        return $module.data(metadata.placeholderText) || '';
                    },
                    text: function () {
                        return settings.preserveHTML ? $text.html() : $text.text();
                    },
                    query: function () {
                        return String($search.val()).trim();
                    },
                    searchWidth: function (value) {
                        value = value !== undefined
                            ? value
                            : $search.val();
                        $sizer.text(value);

                        // prevent rounding issues
                        return Math.ceil($sizer.width() + (module.is.edge() ? 3 : 1));
                    },
                    selectionCount: function () {
                        var
                            values = module.get.values(),
                            count
                        ;
                        count = module.is.multiple()
                            ? (Array.isArray(values) ? values.length : 0)
                            : (module.get.value() !== '' ? 1 : 0);

                        return count;
                    },
                    transition: function ($subMenu) {
                        return settings.transition === 'auto'
                            ? (module.is.upward($subMenu) ? 'slide up' : 'slide down')
                            : settings.transition;
                    },
                    userValues: function () {
                        var
                            values = module.get.values(true)
                        ;
                        if (!values) {
                            return false;
                        }
                        values = Array.isArray(values)
                            ? values
                            : [values];

                        return $.grep(values, function (value) {
                            return module.get.item(value) === false;
                        });
                    },
                    uniqueArray: function (array) {
                        return $.grep(array, function (value, index) {
                            return $.inArray(value, array) === index;
                        });
                    },
                    caretPosition: function (returnEndPos) {
                        var
                            input = $search[0],
                            range,
                            rangeLength
                        ;
                        if (returnEndPos && 'selectionEnd' in input) {
                            return input.selectionEnd;
                        }
                        if (!returnEndPos && 'selectionStart' in input) {
                            return input.selectionStart;
                        }
                        if (document.selection) {
                            input.focus();
                            range = document.selection.createRange();
                            rangeLength = range.text.length;
                            if (returnEndPos) {
                                return rangeLength;
                            }
                            range.moveStart('character', -input.value.length);

                            return range.text.length - rangeLength;
                        }
                    },
                    value: function () {
                        var
                            value = $input.length > 0
                                ? $input.val()
                                : $module.data(metadata.value),
                            isEmptyMultiselect = Array.isArray(value) && value.length === 1 && value[0] === ''
                        ;

                        // prevents placeholder element from being selected when multiple
                        return value === undefined || isEmptyMultiselect
                            ? ''
                            : value;
                    },
                    values: function (raw) {
                        var
                            value = module.get.value()
                        ;
                        if (value === '') {
                            return '';
                        }

                        return !module.has.selectInput() && module.is.multiple()
                            ? (typeof value === 'string' // delimited string
                                ? (raw
                                    ? value
                                    : module.escape.htmlEntities(value)).split(settings.delimiter)
                                : '')
                            : value;
                    },
                    remoteValues: function () {
                        var
                            values = module.get.values(),
                            remoteValues = false
                        ;
                        if (values) {
                            if (typeof values === 'string') {
                                values = [values];
                            }
                            $.each(values, function (index, value) {
                                var
                                    name = module.read.remoteData(value)
                                ;
                                module.verbose('Restoring value from session data', name, value);
                                if (name) {
                                    if (!remoteValues) {
                                        remoteValues = {};
                                    }
                                    remoteValues[value] = name;
                                }
                            });
                        }

                        return remoteValues;
                    },
                    choiceText: function ($choice, preserveHTML) {
                        preserveHTML = preserveHTML !== undefined
                            ? preserveHTML
                            : settings.preserveHTML;
                        if ($choice) {
                            if ($choice.find(selector.menu).length > 0) {
                                module.verbose('Retrieving text of element with sub-menu');
                                $choice = $choice.clone();
                                $choice.find(selector.menu).remove();
                                $choice.find(selector.menuIcon).remove();
                            }

                            return $choice.data(metadata.text) !== undefined
                                ? $choice.data(metadata.text)
                                : (preserveHTML
                                    ? $choice.html() && $choice.html().trim()
                                    : $choice.text() && $choice.text().trim());
                        }
                    },
                    choiceValue: function ($choice, choiceText) {
                        choiceText = choiceText || module.get.choiceText($choice);
                        if (!$choice) {
                            return false;
                        }

                        return $choice.data(metadata.value) !== undefined
                            ? String($choice.data(metadata.value))
                            : (typeof choiceText === 'string'
                                ? String(
                                    settings.ignoreSearchCase
                                        ? choiceText.toLowerCase()
                                        : choiceText
                                ).trim()
                                : String(choiceText));
                    },
                    inputEvent: function () {
                        var
                            input = $search[0]
                        ;
                        if (input) {
                            return input.oninput !== undefined
                                ? 'input'
                                : (input.onpropertychange !== undefined
                                    ? 'propertychange'
                                    : 'keyup');
                        }

                        return false;
                    },
                    selectValues: function () {
                        var
                            select = {},
                            oldGroup = [],
                            values = []
                        ;
                        $module
                            .find('option')
                            .each(function () {
                                var
                                    $option  = $(this),
                                    name     = $option.html(),
                                    disabled = $option.attr('disabled'),
                                    value    = $option.attr('value') !== undefined
                                        ? $option.attr('value')
                                        : name,
                                    text     = $option.data(metadata.text) !== undefined
                                        ? $option.data(metadata.text)
                                        : name,
                                    group = $option.parent('optgroup')
                                ;
                                if (settings.placeholder === 'auto' && value === '') {
                                    select.placeholder = name;
                                } else {
                                    if (group.length !== oldGroup.length || group[0] !== oldGroup[0]) {
                                        values.push({
                                            type: 'header',
                                            divider: settings.headerDivider,
                                            name: group.attr('label') || '',
                                        });
                                        oldGroup = group;
                                    }
                                    values.push({
                                        name: name,
                                        value: value,
                                        text: module.escape.htmlEntities(text, true),
                                        disabled: disabled,
                                    });
                                }
                            })
                        ;
                        if (settings.placeholder && settings.placeholder !== 'auto') {
                            module.debug('Setting placeholder value to', settings.placeholder);
                            select.placeholder = settings.placeholder;
                        }
                        if (settings.sortSelect) {
                            if (settings.sortSelect === true) {
                                values.sort(function (a, b) {
                                    return a.name.localeCompare(b.name);
                                });
                            } else if (settings.sortSelect === 'natural') {
                                values.sort(function (a, b) {
                                    return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
                                });
                            } else if (isFunction(settings.sortSelect)) {
                                values.sort(settings.sortSelect);
                            }
                            select[fields.values] = values;
                            module.debug('Retrieved and sorted values from select', select);
                        } else {
                            select[fields.values] = values;
                            module.debug('Retrieved values from select', select);
                        }

                        return select;
                    },
                    activeItem: function () {
                        return $item.filter('.' + className.active);
                    },
                    selectedItem: function () {
                        var
                            $selectedItem = $item.not(selector.unselectable).filter('.' + className.selected)
                        ;

                        return $selectedItem.length > 0
                            ? $selectedItem
                            : $item.eq(0);
                    },
                    itemWithAdditions: function (value) {
                        var
                            $items       = module.get.item(value),
                            $userItems   = module.create.userChoice(value),
                            hasUserItems = $userItems && $userItems.length > 0
                        ;
                        if (hasUserItems) {
                            $items = $items.length > 0
                                ? $items.add($userItems)
                                : $userItems;
                        }

                        return $items;
                    },
                    item: function (value, strict) {
                        var
                            $selectedItem = false,
                            shouldSearch,
                            isMultiple
                        ;
                        value = value !== undefined
                            ? value
                            : (module.get.values() !== undefined
                                ? module.get.values()
                                : module.get.text());
                        isMultiple = module.is.multiple() && Array.isArray(value);
                        shouldSearch = isMultiple
                            ? value.length > 0
                            : value !== undefined && value !== null;
                        strict = value === '' || value === false || value === true
                            ? true
                            : strict || false;
                        if (shouldSearch) {
                            $item
                                .each(function () {
                                    var
                                        $choice       = $(this),
                                        optionText    = module.get.choiceText($choice),
                                        optionValue   = module.get.choiceValue($choice, optionText)
                                    ;
                                    // safe early exit
                                    if (optionValue === null || optionValue === undefined) {
                                        return;
                                    }
                                    if (isMultiple) {
                                        if ($.inArray(module.escape.htmlEntities(String(optionValue)), value.map(String).map(module.escape.htmlEntities)) !== -1) {
                                            $selectedItem = $selectedItem
                                                ? $selectedItem.add($choice)
                                                : $choice;
                                        }
                                    } else if (strict) {
                                        module.verbose('Ambiguous dropdown value using strict type check', $choice, value);
                                        if (optionValue === value) {
                                            $selectedItem = $choice;

                                            return true;
                                        }
                                    } else {
                                        if (settings.ignoreCase) {
                                            optionValue = optionValue.toLowerCase();
                                            value = value.toLowerCase();
                                        }
                                        if (module.escape.htmlEntities(String(optionValue)) === module.escape.htmlEntities(String(value))) {
                                            module.verbose('Found select item by value', optionValue, value);
                                            $selectedItem = $choice;

                                            return true;
                                        }
                                    }
                                })
                            ;
                        }

                        return $selectedItem;
                    },
                    displayType: function () {
                        return $module.hasClass('column') ? 'flex' : settings.displayType;
                    },
                },

                check: {
                    maxSelections: function (selectionCount) {
                        if (settings.maxSelections) {
                            selectionCount = selectionCount !== undefined
                                ? selectionCount
                                : module.get.selectionCount();
                            if (selectionCount >= settings.maxSelections) {
                                module.debug('Maximum selection count reached');
                                if (settings.useLabels) {
                                    $item.addClass(className.filtered);
                                    module.add.message(message.maxSelections);
                                }

                                return true;
                            }

                            module.verbose('No longer at maximum selection count');
                            module.remove.message();
                            module.remove.filteredItem();
                            if (module.is.searchSelection()) {
                                module.filterItems();
                            }

                            return false;
                        }

                        return false;
                    },
                    disabled: function () {
                        $search.attr('tabindex', module.is.disabled() ? -1 : 0);
                    },
                },

                restore: {
                    defaults: function (preventChangeTrigger) {
                        module.clear(preventChangeTrigger);
                        module.restore.defaultText();
                        module.restore.defaultValue();
                    },
                    defaultText: function () {
                        var
                            defaultText     = module.get.defaultText(),
                            placeholderText = module.get.placeholderText
                        ;
                        if (defaultText === placeholderText) {
                            module.debug('Restoring default placeholder text', defaultText);
                            module.set.placeholderText(defaultText);
                        } else {
                            module.debug('Restoring default text', defaultText);
                            module.set.text(defaultText);
                        }
                    },
                    placeholderText: function () {
                        module.set.placeholderText();
                    },
                    defaultValue: function () {
                        var
                            defaultValue = module.get.defaultValue()
                        ;
                        if (defaultValue !== undefined) {
                            module.debug('Restoring default value', defaultValue);
                            if (defaultValue !== '') {
                                module.set.value(defaultValue);
                                module.set.selected();
                            } else {
                                module.remove.activeItem();
                                module.remove.selectedItem();
                            }
                        }
                    },
                    labels: function () {
                        if (settings.allowAdditions) {
                            if (!settings.useLabels) {
                                module.error(error.labels);
                                settings.useLabels = true;
                            }
                            module.debug('Restoring selected values');
                            module.create.userLabels();
                        }
                        module.check.maxSelections();
                    },
                    selected: function () {
                        module.restore.values();
                        if (module.is.multiple()) {
                            module.debug('Restoring previously selected values and labels');
                            module.restore.labels();
                        } else {
                            module.debug('Restoring previously selected values');
                        }
                    },
                    values: function () {
                        // prevents callbacks from occurring on initial load
                        module.set.initialLoad();
                        if (settings.apiSettings && settings.saveRemoteData && module.get.remoteValues()) {
                            module.restore.remoteValues();
                        } else {
                            module.set.selected();
                        }
                        var value = module.get.value();
                        if (value && value !== '' && !(Array.isArray(value) && value.length === 0)) {
                            $input.removeClass(className.noselection);
                        } else {
                            $input.addClass(className.noselection);
                        }
                        module.remove.initialLoad();
                    },
                    remoteValues: function () {
                        var
                            values = module.get.remoteValues()
                        ;
                        module.debug('Recreating selected from session data', values);
                        if (values) {
                            if (module.is.single()) {
                                $.each(values, function (value, name) {
                                    module.set.text(name);
                                });
                            } else if (settings.useLabels) {
                                $.each(values, function (value, name) {
                                    module.add.label(value, name);
                                });
                            }
                        }
                    },
                },

                read: {
                    remoteData: function (value) {
                        var
                            name
                        ;
                        if (window.Storage === undefined) {
                            module.error(error.noStorage);

                            return;
                        }
                        name = sessionStorage.getItem(value + elementNamespace);

                        return name !== undefined
                            ? name
                            : false;
                    },
                },

                save: {
                    defaults: function () {
                        module.save.defaultText();
                        module.save.placeholderText();
                        module.save.defaultValue();
                    },
                    defaultValue: function () {
                        var
                            value = module.get.value()
                        ;
                        module.verbose('Saving default value as', value);
                        $module.data(metadata.defaultValue, value);
                    },
                    defaultText: function () {
                        var
                            text = module.get.text()
                        ;
                        module.verbose('Saving default text as', text);
                        $module.data(metadata.defaultText, text);
                    },
                    placeholderText: function () {
                        var
                            text
                        ;
                        if (settings.placeholder !== false && $text.hasClass(className.placeholder)) {
                            text = module.get.text();
                            module.verbose('Saving placeholder text as', text);
                            $module.data(metadata.placeholderText, text);
                        }
                    },
                    remoteData: function (name, value) {
                        if (window.Storage === undefined) {
                            module.error(error.noStorage);

                            return;
                        }
                        module.verbose('Saving remote data to session storage', value, name);
                        sessionStorage.setItem(value + elementNamespace, name);
                    },
                },

                clear: function (preventChangeTrigger) {
                    if (module.is.multiple() && settings.useLabels) {
                        module.remove.labels($module.find(selector.label), preventChangeTrigger);
                    } else {
                        module.remove.activeItem();
                        module.remove.selectedItem();
                        module.remove.filteredItem();
                    }
                    module.set.placeholderText();
                    module.clearValue(preventChangeTrigger);
                },

                clearValue: function (preventChangeTrigger) {
                    module.set.value('', null, null, preventChangeTrigger);
                },

                clearCache: function () {
                    module.debug('Clearing API cache once');
                    tempDisableApiCache = true;
                },

                scrollPage: function (direction, $selectedItem) {
                    var
                        $currentItem  = $selectedItem || module.get.selectedItem(),
                        $menu         = $currentItem.closest(selector.menu),
                        menuHeight    = $menu.outerHeight(),
                        currentScroll = $menu.scrollTop(),
                        itemHeight    = $item.eq(0).outerHeight(),
                        itemsPerPage  = Math.floor(menuHeight / itemHeight),
                        newScroll     = direction === 'up'
                            ? currentScroll - (itemHeight * itemsPerPage)
                            : currentScroll + (itemHeight * itemsPerPage),
                        $selectableItem = $item.not(selector.unselectable),
                        isWithinRange,
                        $nextSelectedItem,
                        elementIndex
                    ;
                    elementIndex = direction === 'up'
                        ? $selectableItem.index($currentItem) - itemsPerPage
                        : $selectableItem.index($currentItem) + itemsPerPage;
                    isWithinRange = direction === 'up'
                        ? elementIndex >= 0
                        : elementIndex < $selectableItem.length;
                    $nextSelectedItem = isWithinRange
                        ? $selectableItem.eq(elementIndex)
                        : (direction === 'up'
                            ? $selectableItem.first()
                            : $selectableItem.last());
                    if ($nextSelectedItem.length > 0) {
                        module.debug('Scrolling page', direction, $nextSelectedItem);
                        $currentItem
                            .removeClass(className.selected)
                        ;
                        $nextSelectedItem
                            .addClass(className.selected)
                        ;
                        if (settings.selectOnKeydown && module.is.single() && !$nextSelectedItem.hasClass(className.actionable)) {
                            module.set.selectedItem($nextSelectedItem);
                        }
                        $menu
                            .scrollTop(newScroll)
                        ;
                    }
                },

                set: {
                    filtered: function () {
                        var
                            isMultiple       = module.is.multiple(),
                            isSearch         = module.is.searchSelection(),
                            isSearchMultiple = isMultiple && isSearch,
                            searchValue      = isSearch
                                ? module.get.query()
                                : '',
                            hasSearchValue   = typeof searchValue === 'string' && searchValue.length > 0,
                            searchWidth      = module.get.searchWidth(),
                            valueIsSet       = searchValue !== ''
                        ;
                        if (isMultiple && hasSearchValue) {
                            module.verbose('Adjusting input width', searchWidth);
                            $search.css('width', searchWidth + 'px');
                        }
                        if (hasSearchValue || (isSearchMultiple && valueIsSet)) {
                            module.verbose('Hiding placeholder text');
                            $text.addClass(className.filtered);
                        } else if (!isMultiple || (isSearchMultiple && !valueIsSet)) {
                            module.verbose('Showing placeholder text');
                            $text.removeClass(className.filtered);
                        }
                    },
                    empty: function () {
                        $module.addClass(className.empty);
                    },
                    loading: function () {
                        $module.addClass(className.loading);
                    },
                    placeholderText: function (text) {
                        text = text || module.get.placeholderText();
                        module.debug('Setting placeholder text', text);
                        module.set.text(text);
                        $text.addClass(className.placeholder);
                    },
                    tabbable: function () {
                        if (module.is.searchSelection()) {
                            module.debug('Added tabindex to searchable dropdown');
                            $search
                                .val('')
                            ;
                            module.check.disabled();
                            $menu
                                .attr('tabindex', -1)
                            ;
                        } else {
                            module.debug('Added tabindex to dropdown');
                            if ($module.attr('tabindex') === undefined) {
                                $module
                                    .attr('tabindex', $input.attr('tabindex') || 0)
                                ;
                                $menu
                                    .attr('tabindex', -1)
                                ;
                            }
                        }
                        $input.removeAttr('tabindex');
                    },
                    initialLoad: function () {
                        module.verbose('Setting initial load');
                        initialLoad = true;
                    },
                    activeItem: function ($item) {
                        if (settings.allowAdditions && $item.filter(selector.addition).length > 0) {
                            $item.addClass(className.filtered);
                        } else {
                            $item.addClass(className.active);
                        }
                    },
                    partialSearch: function (text) {
                        var
                            length = module.get.query().length
                        ;
                        $search.val(text.slice(0, length));
                    },
                    scrollPosition: function ($item, forceScroll) {
                        var
                            edgeTolerance = 5,
                            $menu,
                            hasActive,
                            offset,
                            itemOffset,
                            menuOffset,
                            menuScroll,
                            menuHeight,
                            abovePage,
                            belowPage
                        ;

                        $item = $item || module.get.selectedItem();
                        $menu = $item.closest(selector.menu);
                        hasActive = $item && $item.length > 0;
                        forceScroll = forceScroll !== undefined
                            ? forceScroll
                            : false;
                        if (module.get.activeItem().length === 0) {
                            forceScroll = false;
                        }
                        if ($item && $menu.length > 0 && hasActive) {
                            itemOffset = $item.position().top;

                            $menu.addClass(className.loading);
                            menuScroll = $menu.scrollTop();
                            menuOffset = $menu.offset().top;
                            itemOffset = $item.offset().top;
                            offset = menuScroll - menuOffset + itemOffset;
                            if (!forceScroll) {
                                menuHeight = $menu.height();
                                belowPage = menuScroll + menuHeight < (offset + edgeTolerance);
                                abovePage = (offset - edgeTolerance) < menuScroll;
                            }
                            module.debug('Scrolling to active item', offset);
                            if (forceScroll || abovePage || belowPage) {
                                $menu.scrollTop(offset);
                            }
                            $menu.removeClass(className.loading);
                        }
                    },
                    text: function (text, isNotPlaceholder) {
                        if (settings.action === 'combo') {
                            module.debug('Changing combo button text', text, $combo);
                            if (settings.preserveHTML) {
                                $combo.html(text);
                            } else {
                                $combo.text(text);
                            }
                        } else if (settings.action === 'activate') {
                            if (text !== module.get.placeholderText() || isNotPlaceholder) {
                                $text.removeClass(className.placeholder);
                            }
                            module.debug('Changing text', text, $text);
                            $text
                                .removeClass(className.filtered)
                            ;
                            if (settings.preserveHTML) {
                                $text.html(text);
                            } else {
                                $text.text(text);
                            }
                        }
                    },
                    selectedItem: function ($item) {
                        var
                            value      = module.get.choiceValue($item),
                            searchText = module.get.choiceText($item, false),
                            text       = module.get.choiceText($item)
                        ;
                        module.debug('Setting user selection to item', $item);
                        module.remove.activeItem();
                        module.set.partialSearch(searchText);
                        module.set.activeItem($item);
                        module.set.selected(value, $item);
                        module.set.text(text);
                    },
                    selectedLetter: function (letter) {
                        var
                            $selectedItem         = $item.filter('.' + className.selected),
                            alreadySelectedLetter = $selectedItem.length > 0 && module.has.firstLetter($selectedItem, letter),
                            $nextValue            = false,
                            $nextItem
                        ;
                        // check next of same letter
                        if (alreadySelectedLetter) {
                            $nextItem = $selectedItem.nextAll($item).eq(0);
                            if (module.has.firstLetter($nextItem, letter)) {
                                $nextValue = $nextItem;
                            }
                        }
                        // check all values
                        if (!$nextValue) {
                            $item
                                .each(function () {
                                    if (module.has.firstLetter($(this), letter)) {
                                        $nextValue = $(this);

                                        return false;
                                    }
                                })
                            ;
                        }
                        // set next value
                        if ($nextValue) {
                            module.verbose('Scrolling to next value with letter', letter);
                            module.set.scrollPosition($nextValue);
                            $selectedItem.removeClass(className.selected);
                            $nextValue.addClass(className.selected);
                            if (settings.selectOnKeydown && module.is.single() && !$nextItem.hasClass(className.actionable)) {
                                module.set.selectedItem($nextValue);
                            }
                        }
                    },
                    direction: function ($menu) {
                        if (settings.direction === 'auto') {
                            // reset position, remove upward if it's base menu
                            if (!$menu) {
                                module.remove.upward();
                            } else if (module.is.upward($menu)) {
                                // we need make sure when make assertion openDownward for $menu, $menu does not have upward class
                                module.remove.upward($menu);
                            }

                            if (module.can.openDownward($menu)) {
                                module.remove.upward($menu);
                            } else {
                                module.set.upward($menu);
                            }
                            if (!module.is.leftward($menu) && !module.can.openRightward($menu)) {
                                module.set.leftward($menu);
                            }
                        } else if (settings.direction === 'upward') {
                            module.set.upward($menu);
                        }
                    },
                    upward: function ($currentMenu) {
                        var $element = $currentMenu || $module;
                        $element.addClass(className.upward);
                    },
                    leftward: function ($currentMenu) {
                        var $element = $currentMenu || $menu;
                        $element.addClass(className.leftward);
                    },
                    value: function (value, text, $selected, preventChangeTrigger) {
                        if (typeof text === 'boolean') {
                            preventChangeTrigger = text;
                            $selected = undefined;
                            text = undefined;
                        }
                        if (value !== undefined && value !== '' && !(Array.isArray(value) && value.length === 0)) {
                            $input.removeClass(className.noselection);
                        } else {
                            $input.addClass(className.noselection);
                        }
                        var
                            escapedValue = module.escape.value(value),
                            hasInput     = $input.length > 0,
                            currentValue = module.get.values(),
                            stringValue  = value !== undefined
                                ? String(value)
                                : value
                        ;
                        if (hasInput) {
                            if (!settings.allowReselection && stringValue == currentValue) {
                                module.verbose('Skipping value update already same value', value, currentValue);
                                if (!module.is.initialLoad()) {
                                    return;
                                }
                            }

                            if (module.is.single() && module.has.selectInput() && module.can.extendSelect()) {
                                module.debug('Adding user option', value);
                                module.add.optionValue(value);
                            }
                            module.debug('Updating input value', escapedValue, currentValue);
                            internalChange = true;
                            $input
                                .val(escapedValue)
                            ;
                            if (settings.fireOnInit === false && module.is.initialLoad()) {
                                module.debug('Input native change event ignored on initial load');
                            } else if (preventChangeTrigger !== true) {
                                module.trigger.change();
                            }
                            internalChange = false;
                        } else {
                            module.verbose('Storing value in metadata', escapedValue, $input);
                            if (escapedValue !== currentValue) {
                                $module.data(metadata.value, stringValue);
                            }
                        }
                        if (settings.fireOnInit === false && module.is.initialLoad()) {
                            module.verbose('No callback on initial load', settings.onChange);
                        } else if (preventChangeTrigger !== true) {
                            settings.onChange.call(element, value, text, $selected);
                        }
                    },
                    active: function () {
                        $module
                            .addClass(className.active)
                        ;
                    },
                    multiple: function () {
                        $module.addClass(className.multiple);
                    },
                    visible: function () {
                        $module.addClass(className.visible);
                    },
                    exactly: function (value, $selectedItem, preventChangeTrigger) {
                        if (typeof $selectedItem === 'boolean') {
                            preventChangeTrigger = $selectedItem;
                            $selectedItem = undefined;
                        }
                        module.debug('Setting selected to exact values');
                        module.clear();
                        module.set.selected(value, $selectedItem, preventChangeTrigger);
                    },
                    selected: function (value, $selectedItem, preventChangeTrigger, keepSearchTerm) {
                        if (typeof $selectedItem === 'boolean') {
                            keepSearchTerm = preventChangeTrigger;
                            preventChangeTrigger = $selectedItem;
                            $selectedItem = undefined;
                        }
                        var
                            isMultiple = module.is.multiple()
                        ;
                        $selectedItem = settings.allowAdditions
                            ? $selectedItem || module.get.itemWithAdditions(value)
                            : $selectedItem || module.get.item(value);
                        if (!$selectedItem) {
                            return false;
                        }
                        module.debug('Setting selected menu item to', $selectedItem);
                        if (module.is.multiple() && !keepSearchTerm) {
                            module.remove.searchWidth();
                        }
                        if (module.is.single()) {
                            module.remove.activeItem();
                            module.remove.selectedItem();
                        } else if (settings.useLabels) {
                            module.remove.selectedItem();
                        }
                        // select each item
                        $selectedItem
                            .each(function () {
                                var
                                    $selected      = $(this),
                                    selectedText   = module.get.choiceText($selected),
                                    selectedValue  = module.get.choiceValue($selected, selectedText),

                                    isFiltered     = $selected.hasClass(className.filtered),
                                    isActive       = $selected.hasClass(className.active),
                                    isActionable   = $selected.hasClass(className.actionable),
                                    isUserValue    = $selected.hasClass(className.addition),
                                    shouldAnimate  = isMultiple && $selectedItem && $selectedItem.length === 1
                                ;
                                if (isActionable) {
                                    if ((!isMultiple || (!isActive || isUserValue)) && settings.apiSettings && settings.saveRemoteData) {
                                        module.save.remoteData(selectedText, selectedValue);
                                    }
                                    settings.onActionable.call(element, selectedValue, selectedText, $selected);
                                } else if (isMultiple) {
                                    if (!isActive || isUserValue) {
                                        if (settings.apiSettings && settings.saveRemoteData) {
                                            module.save.remoteData(selectedText, selectedValue);
                                        }
                                        if (settings.useLabels) {
                                            module.add.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                            module.add.label(selectedValue, selectedText, shouldAnimate);
                                            module.set.activeItem($selected);
                                            module.filterActive();
                                            module.select.nextAvailable($selectedItem);
                                        } else {
                                            module.add.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                            module.set.text(module.add.variables(message.count));
                                            module.set.activeItem($selected);
                                        }
                                    } else if (!isFiltered && (settings.useLabels || selectActionActive)) {
                                        module.debug('Selected active value, removing label');
                                        module.remove.selected(selectedValue);
                                    }
                                } else {
                                    if (settings.apiSettings && settings.saveRemoteData) {
                                        module.save.remoteData(selectedText, selectedValue);
                                    }
                                    if (!keepSearchTerm && !$selected.hasClass(className.actionable)) {
                                        module.set.text(selectedText, true);
                                    }
                                    module.set.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                    $selected
                                        .addClass(className.active)
                                        .addClass(className.selected)
                                    ;
                                }
                            })
                        ;
                        if (!keepSearchTerm) {
                            module.remove.searchTerm();
                        }
                        if (module.is.allFiltered()) {
                            module.set.empty();
                            module.hideMenu();
                        }
                    },
                },

                add: {
                    label: function (value, text, shouldAnimate) {
                        var
                            $next  = module.is.searchSelection()
                                ? $search
                                : $text,
                            escapedValue = module.escape.value(value),
                            $label
                        ;
                        if (settings.ignoreCase) {
                            escapedValue = escapedValue.toLowerCase();
                        }
                        $label = $('<a />')
                            .addClass(className.label)
                            .attr('data-' + metadata.value, escapedValue)
                            .html(templates.label(escapedValue, text, settings.preserveHTML, settings.className))
                        ;
                        $label = settings.onLabelCreate.call($label, escapedValue, text);

                        if (module.has.label(value)) {
                            module.debug('User selection already exists, skipping', escapedValue);

                            return;
                        }
                        if (settings.label.variation) {
                            $label.addClass(settings.label.variation);
                        }
                        if (shouldAnimate === true && settings.label.transition) {
                            module.debug('Animating in label', $label);
                            $label
                                .addClass(className.hidden)
                                .insertBefore($next)
                                .transition({
                                    animation: settings.label.transition,
                                    debug: settings.debug,
                                    verbose: settings.verbose,
                                    silent: settings.silent,
                                    duration: settings.label.duration,
                                })
                            ;
                        } else {
                            module.debug('Adding selection label', $label);
                            $label
                                .insertBefore($next)
                            ;
                        }
                    },
                    message: function (message) {
                        var
                            $message = $menu.children(selector.message),
                            html     = settings.templates.message(module.add.variables(message))
                        ;
                        if ($message.length > 0) {
                            $message
                                .html(html)
                            ;
                        } else {
                            $('<div/>')
                                .html(html)
                                .addClass(className.message)
                                .appendTo($menu)
                            ;
                        }
                    },
                    optionValue: function (value) {
                        var
                            escapedValue = module.escape.value(value),
                            $option      = $input.find('option[value="' + module.escape.string(escapedValue) + '"]'),
                            hasOption    = $option.length > 0
                        ;
                        if (hasOption) {
                            return;
                        }
                        // temporarily disconnect observer
                        module.disconnect.selectObserver();
                        if (module.is.single()) {
                            module.verbose('Removing previous user addition');
                            $input.find('option.' + className.addition).remove();
                        }
                        $('<option/>')
                            .prop('value', escapedValue)
                            .addClass(className.addition)
                            .text(value)
                            .appendTo($input)
                        ;
                        module.verbose('Adding user addition as an <option>', value);
                        module.observe.select();
                    },
                    userSuggestion: function (value) {
                        var
                            $addition         = $menu.children(selector.addition),
                            $existingItem     = module.get.item(value),
                            alreadyHasValue   = $existingItem && $existingItem.not(selector.addition).length > 0,
                            hasUserSuggestion = $addition.length > 0,
                            html
                        ;
                        if (settings.useLabels && module.has.maxSelections()) {
                            return;
                        }
                        if (value === '' || alreadyHasValue) {
                            $addition.remove();

                            return;
                        }
                        if (hasUserSuggestion) {
                            $addition
                                .data(metadata.value, value)
                                .data(metadata.text, value)
                                .attr('data-' + metadata.value, value)
                                .attr('data-' + metadata.text, value)
                                .removeClass(className.filtered)
                            ;
                            if (!settings.hideAdditions) {
                                html = settings.templates.addition(module.add.variables(message.addResult, value));
                                $addition
                                    .html(html)
                                ;
                            }
                            module.verbose('Replacing user suggestion with new value', $addition);
                        } else {
                            $addition = module.create.userChoice(value);
                            $addition
                                .prependTo($menu)
                            ;
                            module.verbose('Adding item choice to menu corresponding with user choice addition', $addition);
                        }
                        if (!settings.hideAdditions || module.is.allFiltered()) {
                            $addition
                                .addClass(className.selected)
                                .siblings()
                                .removeClass(className.selected)
                            ;
                        }
                        module.refreshItems();
                    },
                    variables: function (message, term) {
                        var
                            hasCount    = message.search('{count}') !== -1,
                            hasMaxCount = message.search('{maxCount}') !== -1,
                            hasTerm     = message.search('{term}') !== -1,
                            query
                        ;
                        module.verbose('Adding templated variables to message', message);
                        if (hasCount) {
                            message = message.replace('{count}', module.get.selectionCount());
                        }
                        if (hasMaxCount) {
                            message = message.replace('{maxCount}', settings.maxSelections);
                        }
                        if (hasTerm) {
                            query = term || module.get.query();
                            message = message.replace('{term}', query);
                        }

                        return message;
                    },
                    value: function (addedValue, addedText, $selectedItem, preventChangeTrigger) {
                        if (typeof addedText === 'boolean') {
                            preventChangeTrigger = addedText;
                            $selectedItem = undefined;
                            addedText = undefined;
                        }
                        var
                            currentValue = module.get.values(true),
                            newValue
                        ;
                        if (module.has.value(addedValue)) {
                            module.debug('Value already selected');

                            return;
                        }
                        if (addedValue === '') {
                            module.debug('Cannot select blank values from multiselect');

                            return;
                        }
                        // extend current array
                        if (Array.isArray(currentValue)) {
                            newValue = $selectedItem && $selectedItem.hasClass(className.actionable) ? currentValue : currentValue.concat([addedValue]);
                            newValue = module.get.uniqueArray(newValue);
                        } else {
                            newValue = [addedValue];
                        }
                        // add values
                        if (module.has.selectInput()) {
                            if (module.can.extendSelect()) {
                                module.debug('Adding value to select', addedValue, newValue, $input);
                                module.add.optionValue(addedValue);
                            }
                        } else {
                            newValue = newValue.join(settings.delimiter);
                            module.debug('Setting hidden input to delimited value', newValue, $input);
                        }

                        if (settings.fireOnInit === false && module.is.initialLoad()) {
                            module.verbose('Skipping onadd callback on initial load', settings.onAdd);
                        } else {
                            settings.onAdd.call(element, addedValue, addedText, $selectedItem);
                        }
                        module.set.value(newValue, addedText, $selectedItem, preventChangeTrigger);
                        module.check.maxSelections();
                    },
                },

                remove: {
                    active: function () {
                        $module.removeClass(className.active);
                    },
                    activeLabel: function () {
                        $module.find(selector.label).removeClass(className.active);
                    },
                    empty: function () {
                        $module.removeClass(className.empty);
                    },
                    loading: function () {
                        $module.removeClass(className.loading);
                    },
                    initialLoad: function () {
                        initialLoad = false;
                    },
                    upward: function ($currentMenu) {
                        var $element = $currentMenu || $module;
                        $element.removeClass(className.upward);
                    },
                    leftward: function ($currentMenu) {
                        var $element = $currentMenu || $menu;
                        $element.removeClass(className.leftward);
                    },
                    visible: function () {
                        $module.removeClass(className.visible);
                    },
                    activeItem: function () {
                        $item.removeClass(className.active);
                    },
                    filteredItem: function () {
                        if (settings.useLabels && module.has.maxSelections()) {
                            return;
                        }
                        if (settings.useLabels && module.is.multiple()) {
                            $item.not('.' + className.active).removeClass(className.filtered);
                        } else {
                            $item.removeClass(className.filtered);
                        }
                        if (settings.hideDividers) {
                            $divider.removeClass(className.hidden);
                        }
                        module.remove.empty();
                    },
                    optionValue: function (value) {
                        var
                            escapedValue = module.escape.value(value),
                            $option      = $input.find('option[value="' + module.escape.string(escapedValue) + '"]'),
                            hasOption    = $option.length > 0
                        ;
                        if (!hasOption || !$option.hasClass(className.addition)) {
                            return;
                        }
                        // temporarily disconnect observer
                        module.disconnect.selectObserver();
                        $option.remove();
                        module.verbose('Removing user addition as an <option>', escapedValue);
                        module.observe.select();
                    },
                    message: function () {
                        $menu.children(selector.message).remove();
                    },
                    searchWidth: function () {
                        $search.css('width', '');
                    },
                    searchTerm: function () {
                        module.verbose('Cleared search term');
                        $search.val('');
                        module.set.filtered();
                    },
                    userAddition: function () {
                        $item.filter(selector.addition).remove();
                    },
                    selected: function (value, $selectedItem, preventChangeTrigger) {
                        $selectedItem = settings.allowAdditions
                            ? $selectedItem || module.get.itemWithAdditions(value)
                            : $selectedItem || module.get.item(value);

                        if (!$selectedItem) {
                            return false;
                        }

                        $selectedItem
                            .each(function () {
                                var
                                    $selected     = $(this),
                                    selectedText  = module.get.choiceText($selected),
                                    selectedValue = module.get.choiceValue($selected, selectedText)
                                ;
                                if (module.is.multiple()) {
                                    if (settings.useLabels) {
                                        module.remove.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                        module.remove.label(selectedValue);
                                    } else {
                                        module.remove.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                        if (module.get.selectionCount() === 0) {
                                            module.set.placeholderText();
                                        } else {
                                            module.set.text(module.add.variables(message.count));
                                        }
                                    }
                                } else {
                                    module.remove.value(selectedValue, selectedText, $selected, preventChangeTrigger);
                                }
                                $selected
                                    .removeClass(className.filtered)
                                    .removeClass(className.active)
                                ;
                                if (settings.useLabels) {
                                    $selected.removeClass(className.selected);
                                }
                            })
                        ;
                    },
                    selectedItem: function () {
                        $item.removeClass(className.selected);
                    },
                    value: function (removedValue, removedText, $removedItem, preventChangeTrigger) {
                        var
                            values = module.get.values(true),
                            newValue
                        ;
                        if (module.has.selectInput()) {
                            module.verbose('Input is <select> removing selected option', removedValue);
                            newValue = module.remove.arrayValue(removedValue, values);
                            module.remove.optionValue(removedValue);
                        } else {
                            module.verbose('Removing from delimited values', removedValue);
                            newValue = module.remove.arrayValue(removedValue, values);
                            newValue = newValue.join(settings.delimiter);
                        }
                        if (settings.fireOnInit === false && module.is.initialLoad()) {
                            module.verbose('No callback on initial load', settings.onRemove);
                        } else {
                            settings.onRemove.call(element, removedValue, removedText, $removedItem);
                        }
                        module.set.value(newValue, removedText, $removedItem, preventChangeTrigger);
                        module.check.maxSelections();
                    },
                    arrayValue: function (removedValue, values) {
                        if (!Array.isArray(values)) {
                            values = [values];
                        }
                        values = $.grep(values, function (value) {
                            return removedValue != value;
                        });
                        module.verbose('Removed value from delimited string', removedValue, values);

                        return values;
                    },
                    label: function (value, shouldAnimate) {
                        var
                            escapedValue  = module.escape.value(value),
                            $labels       = $module.find(selector.label),
                            $removedLabel = $labels.filter('[data-' + metadata.value + '="' + module.escape.string(settings.ignoreCase ? escapedValue.toLowerCase() : escapedValue) + '"]')
                        ;
                        module.verbose('Removing label', $removedLabel);
                        $removedLabel.remove();
                    },
                    activeLabels: function ($activeLabels) {
                        $activeLabels = $activeLabels || $module.find(selector.label).filter('.' + className.active);
                        module.verbose('Removing active label selections', $activeLabels);
                        module.remove.labels($activeLabels);
                    },
                    labels: function ($labels, preventChangeTrigger) {
                        $labels = $labels || $module.find(selector.label);
                        module.verbose('Removing labels', $labels);
                        $labels
                            .each(function () {
                                var
                                    $label      = $(this),
                                    value       = $label.data(metadata.value),
                                    stringValue = value !== undefined
                                        ? String(value)
                                        : value,
                                    isUserValue = module.is.userValue(stringValue)
                                ;
                                if (settings.onLabelRemove.call($label, value) === false) {
                                    module.debug('Label remove callback cancelled removal');

                                    return;
                                }
                                module.remove.message();
                                if (isUserValue) {
                                    module.remove.value(stringValue, stringValue, module.get.item(stringValue), preventChangeTrigger);
                                    module.remove.label(stringValue);
                                } else {
                                    // selected will also remove label
                                    module.remove.selected(stringValue, false, preventChangeTrigger);
                                }
                            })
                        ;
                    },
                    tabbable: function () {
                        if (module.is.searchSelection()) {
                            module.debug('Searchable dropdown initialized');
                            $search
                                .removeAttr('tabindex')
                            ;
                            $menu
                                .removeAttr('tabindex')
                            ;
                        } else {
                            module.debug('Simple selection dropdown initialized');
                            $module
                                .removeAttr('tabindex')
                            ;
                            $menu
                                .removeAttr('tabindex')
                            ;
                        }
                    },
                    diacritics: function (text) {
                        return settings.ignoreDiacritics ? text.normalize('NFD').replace(/[\u0300-\u036F]/g, '') : text;
                    },
                },

                has: {
                    menuSearch: function () {
                        return module.has.search() && $search.closest($menu).length > 0;
                    },
                    clearItem: function () {
                        return $clear.length > 0;
                    },
                    search: function () {
                        return $search.length > 0;
                    },
                    sizer: function () {
                        return $sizer.length > 0;
                    },
                    selectInput: function () {
                        return $input.is('select');
                    },
                    minCharacters: function (searchTerm) {
                        if (settings.minCharacters && !iconClicked) {
                            searchTerm = searchTerm !== undefined
                                ? String(searchTerm)
                                : String(module.get.query());

                            return searchTerm.length >= settings.minCharacters;
                        }
                        iconClicked = false;

                        return true;
                    },
                    firstLetter: function ($item, letter) {
                        var
                            text,
                            firstLetter
                        ;
                        if (!$item || $item.length === 0 || typeof letter !== 'string') {
                            return false;
                        }
                        text = module.get.choiceText($item, false);
                        letter = letter.toLowerCase();
                        firstLetter = String(text).charAt(0).toLowerCase();

                        return letter == firstLetter;
                    },
                    input: function () {
                        return $input.length > 0;
                    },
                    items: function () {
                        return $item.length > 0;
                    },
                    menu: function () {
                        return $menu.length > 0;
                    },
                    subMenu: function ($currentMenu) {
                        return ($currentMenu || $menu).find(selector.menu).length > 0;
                    },
                    message: function () {
                        return $menu.children(selector.message).length > 0;
                    },
                    label: function (value) {
                        var
                            escapedValue = module.escape.value(value),
                            $labels      = $module.find(selector.label)
                        ;
                        if (settings.ignoreCase) {
                            escapedValue = escapedValue.toLowerCase();
                        }

                        return $labels.filter('[data-' + metadata.value + '="' + module.escape.string(escapedValue) + '"]').length > 0;
                    },
                    maxSelections: function () {
                        return settings.maxSelections && module.get.selectionCount() >= settings.maxSelections;
                    },
                    allResultsFiltered: function () {
                        var
                            $normalResults = $item.not(selector.addition)
                        ;

                        return $normalResults.filter(selector.unselectable).length === $normalResults.length;
                    },
                    userSuggestion: function () {
                        return $menu.children(selector.addition).length > 0;
                    },
                    query: function () {
                        return module.get.query() !== '';
                    },
                    value: function (value) {
                        return settings.ignoreCase
                            ? module.has.valueIgnoringCase(value)
                            : module.has.valueMatchingCase(value);
                    },
                    valueMatchingCase: function (value) {
                        var
                            values   = module.get.values(true),
                            hasValue = Array.isArray(values)
                                ? values && ($.inArray(value, values) !== -1)
                                : values == value
                        ;

                        return !!hasValue;
                    },
                    valueIgnoringCase: function (value) {
                        var
                            values   = module.get.values(true),
                            hasValue = false
                        ;
                        if (!Array.isArray(values)) {
                            values = [values];
                        }
                        $.each(values, function (index, existingValue) {
                            if (String(value).toLowerCase() === String(existingValue).toLowerCase()) {
                                hasValue = true;

                                return false;
                            }
                        });

                        return hasValue;
                    },
                },

                is: {
                    active: function () {
                        return $module.hasClass(className.active);
                    },
                    animatingInward: function () {
                        return $menu.transition('is inward');
                    },
                    animatingOutward: function () {
                        return $menu.transition('is outward');
                    },
                    bubbledLabelClick: function (event) {
                        return $(event.target).is('select, input') && $module.closest('label').length > 0;
                    },
                    bubbledIconClick: function (event) {
                        return $(event.target).closest($icon).length > 0;
                    },
                    edge: function () {
                        return !!window.chrome && !!window.StyleMedia;
                    },
                    empty: function () {
                        return $module.hasClass(className.empty);
                    },
                    chrome: function () {
                        return !!window.chrome && !window.StyleMedia;
                    },
                    alreadySetup: function () {
                        return $module.is('select') && $module.parent(selector.dropdown).data(moduleNamespace) !== undefined && $module.prev().length === 0;
                    },
                    animating: function ($subMenu) {
                        return $subMenu
                            ? $subMenu.transition && $subMenu.transition('is animating')
                            : $menu.transition && $menu.transition('is animating');
                    },
                    leftward: function ($subMenu) {
                        var $selectedMenu = $subMenu || $menu;

                        return $selectedMenu.hasClass(className.leftward);
                    },
                    clearable: function () {
                        return $module.hasClass(className.clearable) || settings.clearable;
                    },
                    disabled: function () {
                        return $module.hasClass(className.disabled);
                    },
                    focused: function () {
                        return document.activeElement === $module[0];
                    },
                    focusedOnSearch: function () {
                        return document.activeElement === $search[0];
                    },
                    allFiltered: function () {
                        return (module.is.multiple() || module.has.search()) && !(!settings.hideAdditions && module.has.userSuggestion()) && !module.has.message() && module.has.allResultsFiltered();
                    },
                    hidden: function ($subMenu) {
                        return !module.is.visible($subMenu);
                    },
                    initialLoad: function () {
                        return initialLoad;
                    },
                    inObject: function (needle, object) {
                        var
                            found = false
                        ;
                        $.each(object, function (index, property) {
                            if (property == needle) {
                                found = true;

                                return true;
                            }
                        });

                        return found;
                    },
                    multiple: function () {
                        return $module.hasClass(className.multiple);
                    },
                    remote: function () {
                        return settings.apiSettings && module.can.useAPI();
                    },
                    noApiCache: function () {
                        return tempDisableApiCache || (settings.apiSettings && !settings.apiSettings.cache);
                    },
                    single: function () {
                        return !module.is.multiple();
                    },
                    selectMutation: function (mutations) {
                        var
                            selectChanged = false
                        ;
                        $.each(mutations, function (index, mutation) {
                            if ($(mutation.target).is('option, optgroup') || $(mutation.addedNodes).is('select') || ($(mutation.target).is('select') && mutation.type !== 'attributes')) {
                                selectChanged = true;

                                return false;
                            }
                        });

                        return selectChanged;
                    },
                    search: function () {
                        return $module.hasClass(className.search);
                    },
                    searchSelection: function (deep) {
                        return module.has.search() && (deep ? $search.parents(selector.dropdown) : $search.parent(selector.dropdown)).length === 1;
                    },
                    selection: function () {
                        return $module.hasClass(className.selection);
                    },
                    userValue: function (value) {
                        return $.inArray(value, module.get.userValues()) !== -1;
                    },
                    upward: function ($menu) {
                        var $element = $menu || $module;

                        return $element.hasClass(className.upward);
                    },
                    visible: function ($subMenu) {
                        return $subMenu
                            ? $subMenu.hasClass(className.visible)
                            : $menu.hasClass(className.visible);
                    },
                    verticallyScrollableContext: function () {
                        var
                            overflowY = $context[0] !== window
                                ? $context.css('overflow-y')
                                : false
                        ;

                        return overflowY === 'auto' || overflowY === 'scroll';
                    },
                    horizontallyScrollableContext: function () {
                        var
                            overflowX = $context[0] !== window
                                ? $context.css('overflow-X')
                                : false
                        ;

                        return overflowX === 'auto' || overflowX === 'scroll';
                    },
                },

                can: {
                    activate: function ($item) {
                        return (
                            settings.useLabels
                                || !module.has.maxSelections()
                                || (module.has.maxSelections() && $item.hasClass(className.active))
                        );
                    },
                    openDownward: function ($subMenu) {
                        var
                            $currentMenu    = $subMenu || $menu,
                            canOpenDownward,
                            onScreen,
                            calculations
                        ;
                        $currentMenu
                            .addClass(className.loading)
                        ;
                        calculations = {
                            context: {
                                offset: $context[0] === window
                                    ? { top: 0, left: 0 }
                                    : $context.offset(),
                                scrollTop: $context.scrollTop(),
                                height: $context.outerHeight(),
                            },
                            menu: {
                                offset: $currentMenu.offset(),
                                height: $currentMenu.outerHeight(),
                            },
                        };
                        if (module.is.verticallyScrollableContext()) {
                            calculations.menu.offset.top += calculations.context.scrollTop;
                        }
                        if (module.has.subMenu($currentMenu)) {
                            calculations.menu.height += $currentMenu.find(selector.menu).first().outerHeight();
                        }
                        onScreen = {
                            above: calculations.context.scrollTop <= calculations.menu.offset.top - calculations.context.offset.top - calculations.menu.height,
                            below: (calculations.context.scrollTop + calculations.context.height) >= calculations.menu.offset.top - calculations.context.offset.top + calculations.menu.height,
                        };
                        if (onScreen.below) {
                            module.verbose('Dropdown can fit in context downward', onScreen);
                            canOpenDownward = true;
                        } else if (!onScreen.below && !onScreen.above) {
                            module.verbose('Dropdown cannot fit in either direction, favoring downward', onScreen);
                            canOpenDownward = true;
                        } else {
                            module.verbose('Dropdown cannot fit below, opening upward', onScreen);
                            canOpenDownward = false;
                        }
                        $currentMenu.removeClass(className.loading);

                        return canOpenDownward;
                    },
                    openRightward: function ($subMenu) {
                        var
                            $currentMenu     = $subMenu || $menu,
                            canOpenRightward = true,
                            isOffscreenRight = false,
                            calculations
                        ;
                        $currentMenu
                            .addClass(className.loading)
                        ;
                        calculations = {
                            context: {
                                offset: $context[0] === window
                                    ? { top: 0, left: 0 }
                                    : $context.offset(),
                                scrollLeft: $context.scrollLeft(),
                                width: $context.outerWidth(),
                            },
                            menu: {
                                offset: $currentMenu.offset(),
                                width: $currentMenu.outerWidth(),
                            },
                        };
                        if (module.is.horizontallyScrollableContext()) {
                            calculations.menu.offset.left += calculations.context.scrollLeft;
                        }
                        isOffscreenRight = calculations.menu.offset.left - calculations.context.offset.left + calculations.menu.width >= calculations.context.scrollLeft + calculations.context.width;
                        if (isOffscreenRight) {
                            module.verbose('Dropdown cannot fit in context rightward', isOffscreenRight);
                            canOpenRightward = false;
                        }
                        $currentMenu.removeClass(className.loading);

                        return canOpenRightward;
                    },
                    extendSelect: function () {
                        return settings.allowAdditions || settings.apiSettings;
                    },
                    show: function () {
                        return !module.is.disabled() && (module.has.items() || module.has.message());
                    },
                    useAPI: function () {
                        return $.fn.api !== undefined;
                    },
                    useElement: function (element) {
                        if ($.fn[element] !== undefined) {
                            return true;
                        }
                        module.error(error.noElement.replace('{element}', element));

                        return false;
                    },
                },

                animate: {
                    show: function (callback, $subMenu) {
                        var
                            $currentMenu = $subMenu || $menu,
                            start = $subMenu
                                ? function () {}
                                : function () {
                                    module.hideSubMenus();
                                    module.hideOthers();
                                    module.set.active();
                                },
                            transition
                        ;
                        callback = isFunction(callback)
                            ? callback
                            : function () {};
                        module.verbose('Doing menu show animation', $currentMenu);
                        module.set.direction($subMenu);
                        transition = settings.transition.showMethod || module.get.transition($subMenu);
                        if (module.is.selection()) {
                            module.set.scrollPosition(module.get.selectedItem(), true);
                        }
                        if (module.is.hidden($currentMenu) || module.is.animating($currentMenu)) {
                            if (transition === 'none') {
                                start();
                                $currentMenu.transition({
                                    displayType: module.get.displayType(),
                                }).transition('show');
                                callback.call(element);
                            } else if (module.can.useElement('transition')) {
                                $currentMenu
                                    .transition({
                                        animation: transition + ' in',
                                        debug: settings.debug,
                                        verbose: settings.verbose,
                                        silent: settings.silent,
                                        duration: settings.transition.showDuration || settings.duration,
                                        queue: true,
                                        onStart: start,
                                        displayType: module.get.displayType(),
                                        onComplete: function () {
                                            callback.call(element);
                                        },
                                    })
                                ;
                            }
                        }
                    },
                    hide: function (callback, $subMenu) {
                        var
                            $currentMenu = $subMenu || $menu,
                            start = $subMenu
                                ? function () {}
                                : function () {
                                    module.unbind.intent();
                                    module.remove.active();
                                },
                            transition = settings.transition.hideMethod || module.get.transition($subMenu)
                        ;
                        callback = isFunction(callback)
                            ? callback
                            : function () {};
                        if (module.is.visible($currentMenu) || module.is.animating($currentMenu)) {
                            module.verbose('Doing menu hide animation', $currentMenu);

                            if (transition === 'none') {
                                start();
                                $currentMenu.transition({
                                    displayType: module.get.displayType(),
                                }).transition('hide');
                                callback.call(element);
                            } else if ($.fn.transition !== undefined) {
                                $currentMenu
                                    .transition({
                                        animation: transition + ' out',
                                        duration: settings.transition.hideDuration || settings.duration,
                                        debug: settings.debug,
                                        verbose: settings.verbose,
                                        silent: settings.silent,
                                        queue: false,
                                        onStart: start,
                                        displayType: module.get.displayType(),
                                        onComplete: function () {
                                            callback.call(element);
                                        },
                                    })
                                ;
                            } else {
                                module.error(error.transition);
                            }
                        }
                    },
                },

                hideAndClear: function () {
                    module.remove.searchTerm();
                    if (module.has.maxSelections()) {
                        return;
                    }
                    if (module.has.search()) {
                        module.hide(function () {
                            module.remove.filteredItem();
                        });
                    } else {
                        module.hide();
                    }
                },

                delay: {
                    show: function () {
                        module.verbose('Delaying show event to ensure user intent');
                        clearTimeout(module.timer);
                        module.timer = setTimeout(function () { module.show(); }, settings.delay.show);
                    },
                    hide: function () {
                        module.verbose('Delaying hide event to ensure user intent');
                        clearTimeout(module.timer);
                        module.timer = setTimeout(function () { module.hide(); }, settings.delay.hide);
                    },
                },

                escape: {
                    value: function (value) {
                        var
                            multipleValues = Array.isArray(value),
                            stringValue    = typeof value === 'string',
                            isUnparsable   = !stringValue && !multipleValues,
                            hasQuotes      = stringValue && value.search(regExp.quote) !== -1,
                            values         = []
                        ;
                        if (isUnparsable || !hasQuotes) {
                            return value;
                        }
                        module.debug('Encoding quote values for use in select', value);
                        if (multipleValues) {
                            $.each(value, function (index, value) {
                                values.push(value.replace(regExp.quote, '&quot;'));
                            });

                            return values;
                        }

                        return value.replace(regExp.quote, '&quot;');
                    },
                    string: function (text) {
                        text = String(text);

                        return text.replace(regExp.escape, '\\$&');
                    },
                    htmlEntities: function (string, forceAmpersand) {
                        var
                            badChars     = /["'<>`]/g,
                            shouldEscape = /["&'<>`]/,
                            escape       = {
                                '<': '&lt;',
                                '>': '&gt;',
                                '"': '&quot;',
                                "'": '&#x27;',
                                '`': '&#x60;',
                            },
                            escapedChar  = function (chr) {
                                return escape[chr];
                            }
                        ;
                        if (shouldEscape.test(string)) {
                            string = string.replace(forceAmpersand ? /&/g : /&(?![\d#a-z]{1,12};)/gi, '&amp;');

                            return string.replace(badChars, escapedChar);
                        }

                        return string;
                    },
                },

                setting: function (name, value) {
                    module.debug('Changing setting', name, value);
                    if ($.isPlainObject(name)) {
                        $.extend(true, settings, name);
                    } else if (value !== undefined) {
                        if ($.isPlainObject(settings[name])) {
                            $.extend(true, settings[name], value);
                        } else {
                            settings[name] = value;
                        }
                    } else {
                        return settings[name];
                    }
                },
                internal: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, module, name);
                    } else if (value !== undefined) {
                        module[name] = value;
                    } else {
                        return module[name];
                    }
                },
                debug: function () {
                    if (!settings.silent && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.debug = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.debug.apply(console, arguments);
                        }
                    }
                },
                verbose: function () {
                    if (!settings.silent && settings.verbose && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.verbose = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.verbose.apply(console, arguments);
                        }
                    }
                },
                error: function () {
                    if (!settings.silent) {
                        module.error = Function.prototype.bind.call(console.error, console, settings.name + ':');
                        module.error.apply(console, arguments);
                    }
                },
                performance: {
                    log: function (message) {
                        var
                            currentTime,
                            executionTime,
                            previousTime
                        ;
                        if (settings.performance) {
                            currentTime = Date.now();
                            previousTime = time || currentTime;
                            executionTime = currentTime - previousTime;
                            time = currentTime;
                            performance.push({
                                Name: message[0],
                                Arguments: [].slice.call(message, 1) || '',
                                Element: element,
                                'Execution Time': executionTime,
                            });
                        }
                        clearTimeout(module.performance.timer);
                        module.performance.timer = setTimeout(function () { module.performance.display(); }, 500);
                    },
                    display: function () {
                        var
                            title = settings.name + ':',
                            totalTime = 0
                        ;
                        time = false;
                        clearTimeout(module.performance.timer);
                        $.each(performance, function (index, data) {
                            totalTime += data['Execution Time'];
                        });
                        title += ' ' + totalTime + 'ms';
                        if (performance.length > 0) {
                            console.groupCollapsed(title);
                            if (console.table) {
                                console.table(performance);
                            } else {
                                $.each(performance, function (index, data) {
                                    console.log(data.Name + ': ' + data['Execution Time'] + 'ms');
                                });
                            }
                            console.groupEnd();
                        }
                        performance = [];
                    },
                },
                invoke: function (query, passedArguments, context) {
                    var
                        object = instance,
                        maxDepth,
                        found,
                        response
                    ;
                    passedArguments = passedArguments || queryArguments;
                    context = context || element;
                    if (typeof query === 'string' && object !== undefined) {
                        query = query.split(/[ .]/);
                        maxDepth = query.length - 1;
                        $.each(query, function (depth, value) {
                            var camelCaseValue = depth !== maxDepth
                                ? value + query[depth + 1].charAt(0).toUpperCase() + query[depth + 1].slice(1)
                                : query
                            ;
                            if ($.isPlainObject(object[camelCaseValue]) && (depth !== maxDepth)) {
                                object = object[camelCaseValue];
                            } else if (object[camelCaseValue] !== undefined) {
                                found = object[camelCaseValue];

                                return false;
                            } else if ($.isPlainObject(object[value]) && (depth !== maxDepth)) {
                                object = object[value];
                            } else if (object[value] !== undefined) {
                                found = object[value];

                                return false;
                            } else {
                                module.error(error.method, query);

                                return false;
                            }
                        });
                    }
                    if (isFunction(found)) {
                        response = found.apply(context, passedArguments);
                    } else if (found !== undefined) {
                        response = found;
                    }
                    if (Array.isArray(returnedValue)) {
                        returnedValue.push(response);
                    } else if (returnedValue !== undefined) {
                        returnedValue = [returnedValue, response];
                    } else if (response !== undefined) {
                        returnedValue = response;
                    }

                    return found;
                },
            };

            if (methodInvoked) {
                if (instance === undefined) {
                    module.initialize();
                }
                module.invoke(query);
            } else {
                if (instance !== undefined) {
                    instance.invoke('destroy');
                }
                module.initialize();
            }
        });

        return returnedValue !== undefined
            ? returnedValue
            : $allModules;
    };

    $.fn.dropdown.settings = {

        silent: false,
        debug: false,
        verbose: false,
        performance: true,

        on: 'click', // what event should show menu action on item selection
        action: 'activate', // action on item selection (nothing, activate, select, combo, hide, function(){})

        values: false, // specify values to use for dropdown

        clearable: false, // whether the value of the dropdown can be cleared

        apiSettings: false,
        selectOnKeydown: true, // Whether selection should occur automatically when keyboard shortcuts used
        minCharacters: 0, // Minimum characters required to trigger API call

        filterRemoteData: false, // Whether API results should be filtered after being returned for query term
        saveRemoteData: true, // Whether remote name/value pairs should be stored in sessionStorage to allow remote data to be restored on page refresh

        throttle: 200, // How long to wait after last user input to search remotely

        context: window, // Context to use when determining if on screen
        direction: 'auto', // Whether dropdown should always open in one direction
        keepOnScreen: true, // Whether dropdown should check whether it is on screen before showing

        match: 'both', // what to match against with search selection (both, text, or label)
        fullTextSearch: 'exact', // search anywhere in value (set to 'exact' to require exact matches)
        ignoreDiacritics: false, // match results also if they contain diacritics of the same base character (for example searching for "a" will also match "á" or "â" or "à", etc...)
        hideDividers: false, // Whether to hide any divider elements (specified in selector.divider) that are sibling to any items when searched (set to true will hide all dividers, set to 'empty' will hide them when they are not followed by a visible item)

        placeholder: 'auto', // whether to convert blank <select> values to placeholder text
        preserveHTML: true, // preserve html when selecting value
        sortSelect: false, // sort selection on init

        forceSelection: false, // force a choice on blur with search selection

        allowAdditions: false, // whether multiple select should allow user added values
        keepSearchTerm: false, // whether the search value should be kept and menu stays filtered on item selection
        ignoreCase: false, // whether to consider case sensitivity when creating labels
        ignoreSearchCase: true, // whether to consider case sensitivity when filtering items
        hideAdditions: true, // whether or not to hide special message prompting a user they can enter a value

        maxSelections: false, // When set to a number limits the number of selections to this count
        useLabels: true, // whether multiple select should filter currently active selections from choices
        delimiter: ',', // when multiselect uses normal <input> the values will be delimited with this character

        showOnFocus: false, // show menu on focus
        allowReselection: false, // whether current value should trigger callbacks when reselected
        allowTab: true, // add tabindex to element
        allowCategorySelection: false, // allow elements with sub-menus to be selected

        fireOnInit: false, // Whether callbacks should fire when initializing dropdown values

        transition: 'auto', // auto transition will slide down or up based on direction
        duration: 200, // duration of transition
        displayType: false, // displayType of transition

        headerDivider: true, // whether option headers should have an additional divider line underneath when converted from <select> <optgroup>

        collapseOnActionable: true, // whether the dropdown should collapse upon selection of an actionable item

        // label settings on multi-select
        label: {
            transition: 'scale',
            duration: 200,
            variation: false,
        },

        // delay before event
        delay: {
            hide: 300,
            show: 200,
            search: 20,
        },

        /* Callbacks */
        onChange: function (value, text, $selected) {},
        onAdd: function (value, text, $selected) {},
        onRemove: function (value, text, $selected) {},
        onActionable: function (value, text, $selected) {},
        onSearch: function (searchTerm) {},

        onLabelSelect: function ($selectedLabels) {},
        onLabelCreate: function (value, text) {
            return $(this);
        },
        onLabelRemove: function (value) {
            return true;
        },
        onNoResults: function (searchTerm) {
            return true;
        },
        onShow: function () {},
        onHide: function () {},

        /* Component */
        name: 'Dropdown',
        namespace: 'dropdown',

        message: {
            addResult: 'Add <b>{term}</b>',
            count: '{count} selected',
            maxSelections: 'Max {maxCount} selections',
            noResults: 'No results found.',
            serverError: 'There was an error contacting the server',
        },

        error: {
            action: 'You called a dropdown action that was not defined',
            alreadySetup: 'Once a select has been initialized behaviors must be called on the created ui dropdown',
            labels: 'Allowing user additions currently requires the use of labels.',
            missingMultiple: '<select> requires multiple property to be set to correctly preserve multiple values',
            method: 'The method you called is not defined.',
            noAPI: 'The API module is required to load resources remotely',
            noStorage: 'Saving remote data requires session storage',
            noElement: 'This module requires ui {element}',
            noNormalize: '"ignoreDiacritics" setting will be ignored. Browser does not support String().normalize(). You may consider including <https://cdn.jsdelivr.net/npm/unorm@1.4.1/lib/unorm.min.js> as a polyfill.',
        },

        regExp: {
            escape: /[\s#$()*+,.:=?@[\\\]^{|}-]/g,
            quote: /"/g,
        },

        metadata: {
            defaultText: 'defaultText',
            defaultValue: 'defaultValue',
            placeholderText: 'placeholder',
            text: 'text',
            value: 'value',
        },

        // property names for remote query
        fields: {
            remoteValues: 'results', // grouping for api results
            values: 'values', // grouping for all dropdown values
            disabled: 'disabled', // whether value should be disabled
            name: 'name', // displayed dropdown text
            description: 'description', // displayed dropdown description
            descriptionVertical: 'descriptionVertical', // whether description should be vertical
            value: 'value', // actual dropdown value
            text: 'text', // displayed text when selected
            type: 'type', // type of dropdown element
            image: 'image', // optional image path
            imageClass: 'imageClass', // optional individual class for image
            icon: 'icon', // optional icon name
            iconClass: 'iconClass', // optional individual class for icon (for example to use flag instead)
            class: 'class', // optional individual class for item/header
            divider: 'divider', // optional divider append for group headers
            actionable: 'actionable', // optional actionable item
        },

        keys: {
            backspace: 8,
            deleteKey: 46,
            enter: 13,
            escape: 27,
            pageUp: 33,
            pageDown: 34,
            leftArrow: 37,
            upArrow: 38,
            rightArrow: 39,
            downArrow: 40,
        },

        selector: {
            addition: '.addition',
            divider: '.divider, .header',
            dropdown: '.ui.dropdown',
            hidden: '.hidden',
            icon: '> .dropdown.icon',
            input: '> input[type="hidden"], > select',
            item: '.item',
            label: '> .label',
            remove: '> .label > .delete.icon',
            siblingLabel: '.label',
            menu: '.menu',
            message: '.message',
            menuIcon: '.dropdown.icon',
            search: 'input.search, .menu > .search > input, .menu input.search',
            sizer: '> span.sizer',
            text: '> .text:not(.icon)',
            unselectable: '.disabled, .filtered',
            clearIcon: '> .remove.icon',
        },

        className: {
            active: 'active',
            addition: 'addition',
            animating: 'animating',
            description: 'description',
            descriptionVertical: 'vertical',
            disabled: 'disabled',
            empty: 'empty',
            dropdown: 'ui dropdown',
            filtered: 'filtered',
            hidden: 'hidden transition',
            icon: 'icon',
            image: 'image',
            item: 'item',
            label: 'ui label',
            loading: 'loading',
            menu: 'menu',
            message: 'message',
            multiple: 'multiple',
            placeholder: 'default',
            sizer: 'sizer',
            search: 'search',
            selected: 'selected',
            selection: 'selection',
            text: 'text',
            upward: 'upward',
            leftward: 'left',
            visible: 'visible',
            clearable: 'clearable',
            noselection: 'noselection',
            delete: 'delete',
            header: 'header',
            divider: 'divider',
            groupIcon: '',
            unfilterable: 'unfilterable',
            actionable: 'actionable',
        },

    };

    /* Templates */
    $.fn.dropdown.settings.templates = {
        deQuote: function (string, encode) {
            return String(string).replace(/"/g, encode ? '&quot;' : '');
        },
        escape: function (string, preserveHTML) {
            if (preserveHTML) {
                return string;
            }
            var
                badChars     = /["'<>`]/g,
                shouldEscape = /["&'<>`]/,
                escape       = {
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#x27;',
                    '`': '&#x60;',
                },
                escapedChar  = function (chr) {
                    return escape[chr];
                }
            ;
            if (shouldEscape.test(string)) {
                string = string.replace(/&(?![\d#a-z]{1,12};)/gi, '&amp;');

                return string.replace(badChars, escapedChar);
            }

            return string;
        },
        // generates dropdown from select values
        dropdown: function (select, fields, preserveHTML, className) {
            var
                placeholder = select.placeholder || false,
                html        = '',
                escape = $.fn.dropdown.settings.templates.escape,
                deQuote = $.fn.dropdown.settings.templates.deQuote
            ;
            html += '<i class="dropdown icon"></i>';
            html += placeholder
                ? '<div class="default text">' + escape(placeholder, preserveHTML) + '</div>'
                : '<div class="text"></div>';
            html += '<div class="' + deQuote(className.menu) + '">';
            html += $.fn.dropdown.settings.templates.menu(select, fields, preserveHTML, className);
            html += '</div>';

            return html;
        },

        // generates just menu from select
        menu: function (response, fields, preserveHTML, className) {
            var
                values = response[fields.values] || [],
                html   = '',
                escape = $.fn.dropdown.settings.templates.escape,
                deQuote = $.fn.dropdown.settings.templates.deQuote
            ;
            $.each(values, function (index, option) {
                var
                    itemType = option[fields.type] || 'item',
                    isMenu = itemType.indexOf('menu') !== -1
                ;

                if (itemType === 'item' || isMenu) {
                    var
                        maybeText = option[fields.text]
                            ? ' data-text="' + deQuote(option[fields.text], true) + '"'
                            : '',
                        maybeActionable = option[fields.actionable]
                            ? className.actionable + ' '
                            : '',
                        maybeDisabled = option[fields.disabled]
                            ? className.disabled + ' '
                            : '',
                        maybeDescriptionVertical = option[fields.descriptionVertical]
                            ? className.descriptionVertical + ' '
                            : '',
                        hasDescription = escape(option[fields.description] || '', preserveHTML) !== ''
                    ;
                    html += '<div class="' + deQuote(maybeActionable + maybeDisabled + maybeDescriptionVertical + (option[fields.class] || className.item)) + '" data-value="' + deQuote(option[fields.value], true) + '"' + maybeText + '>';
                    if (isMenu) {
                        html += '<i class="' + (itemType.indexOf('left') !== -1 ? 'left' : '') + ' dropdown icon"></i>';
                    }
                    if (option[fields.image]) {
                        html += '<img class="' + deQuote(option[fields.imageClass] || className.image) + '" src="' + deQuote(option[fields.image]) + '">';
                    }
                    if (option[fields.icon]) {
                        html += '<i class="' + deQuote(option[fields.icon] + ' ' + (option[fields.iconClass] || className.icon)) + '"></i>';
                    }
                    if (hasDescription) {
                        html += '<span class="' + deQuote(className.description) + '">' + escape(option[fields.description] || '', preserveHTML) + '</span>';
                        html += !isMenu ? '<span class="' + deQuote(className.text) + '">' : '';
                    }
                    if (isMenu) {
                        html += '<span class="' + deQuote(className.text) + '">';
                    }
                    html += escape(option[fields.name] || '', preserveHTML);
                    if (isMenu) {
                        html += '</span>';
                        html += '<div class="' + deQuote(itemType) + '">';
                        html += $.fn.dropdown.settings.templates.menu(option, fields, preserveHTML, className);
                        html += '</div>';
                    } else if (hasDescription) {
                        html += '</span>';
                    }
                    html += '</div>';
                } else if (itemType === 'header') {
                    var
                        groupName = escape(option[fields.name] || '', preserveHTML),
                        groupIcon = deQuote(option[fields.icon] || className.groupIcon)
                    ;
                    if (groupName !== '' || groupIcon !== '') {
                        html += '<div class="' + deQuote(option[fields.class] || className.header) + '">';
                        if (groupIcon !== '') {
                            html += '<i class="' + deQuote(groupIcon + ' ' + (option[fields.iconClass] || className.icon)) + '"></i>';
                        }
                        html += groupName;
                        html += '</div>';
                    }
                    if (option[fields.divider]) {
                        html += '<div class="' + deQuote(className.divider) + '"></div>';
                    }
                }
            });

            return html;
        },

        // generates label for multiselect
        label: function (value, text, preserveHTML, className) {
            var
                escape = $.fn.dropdown.settings.templates.escape,
                deQuote = $.fn.dropdown.settings.templates.deQuote
            ;

            return escape(text, preserveHTML) + '<i class="' + deQuote(className.delete) + ' icon"></i>';
        },

        // generates messages like "No results"
        message: function (message) {
            return message;
        },

        // generates user addition to selection menu
        addition: function (choice) {
            return choice;
        },

    };
})(jQuery, window, document);

/*!
 * # Fomantic-UI 2.9.3 - Transition
 * https://github.com/fomantic/Fomantic-UI/
 *
 *
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */

(function ($, window, document) {
    'use strict';

    function isFunction(obj) {
        return typeof obj === 'function' && typeof obj.nodeType !== 'number';
    }

    window = window !== undefined && window.Math === Math
        ? window
        : globalThis;

    $.fn.transition = function () {
        var
            $allModules     = $(this),

            time            = Date.now(),
            performance     = [],

            moduleArguments = arguments,
            query           = moduleArguments[0],
            queryArguments  = [].slice.call(arguments, 1),
            methodInvoked   = typeof query === 'string',

            returnedValue
        ;
        $allModules.each(function (index) {
            var
                $module  = $(this),
                element  = this,

                // set at run time
                settings,
                instance,

                error,
                className,
                metadata,

                moduleNamespace,
                eventNamespace,
                module
            ;

            module = {

                initialize: function () {
                    // get full settings
                    settings = module.get.settings.apply(element, moduleArguments);

                    // shorthand
                    className = settings.className;
                    error = settings.error;
                    metadata = settings.metadata;

                    // define namespace
                    eventNamespace = '.' + settings.namespace;
                    moduleNamespace = 'module-' + settings.namespace;
                    instance = $module.data(moduleNamespace) || module;

                    if (methodInvoked) {
                        methodInvoked = module.invoke(query);
                    }

                    // method not invoked, lets run an animation
                    if (methodInvoked === false) {
                        module.verbose('Converted arguments into settings object', settings);
                        if (settings.interval) {
                            module.delay(settings.interval);
                        } else {
                            module.animate();
                        }
                        module.instantiate();
                    }
                },

                instantiate: function () {
                    module.verbose('Storing instance of module', module);
                    instance = module;
                    $module
                        .data(moduleNamespace, instance)
                    ;
                },

                destroy: function () {
                    module.verbose('Destroying previous module for', element);
                    $module
                        .removeData(moduleNamespace)
                    ;
                },

                refresh: function () {
                    module.verbose('Refreshing display type on next animation');
                    delete module.displayType;
                },

                forceRepaint: function () {
                    module.verbose('Forcing element repaint');
                    var
                        $parentElement = $module.parent(),
                        $nextElement = $module.next()
                    ;
                    if ($nextElement.length === 0) {
                        $module.detach().appendTo($parentElement);
                    } else {
                        $module.detach().insertBefore($nextElement);
                    }
                },

                repaint: function () {
                    module.verbose('Repainting element');
                    var
                        fakeAssignment = element.offsetWidth
                    ;
                },

                delay: function (interval) {
                    var
                        direction = module.get.animationDirection(),
                        shouldReverse,
                        delay
                    ;
                    if (!direction) {
                        direction = module.can.transition()
                            ? module.get.direction()
                            : 'static';
                    }
                    interval = interval !== undefined
                        ? interval
                        : settings.interval;
                    shouldReverse = settings.reverse === 'auto' && direction === className.outward;
                    delay = shouldReverse || settings.reverse === true
                        ? ($allModules.length - index) * interval
                        : index * interval;
                    module.debug('Delaying animation by', delay);
                    setTimeout(function () { module.animate(); }, delay);
                },

                animate: function (overrideSettings) {
                    settings = overrideSettings || settings;

                    module.debug('Preparing animation', settings.animation);
                    if (module.is.animating()) {
                        if (settings.queue) {
                            if (!settings.allowRepeats && module.has.direction() && module.is.occurring() && module.queuing !== true) {
                                module.debug('Animation is currently occurring, preventing queueing same animation', settings.animation);
                            } else {
                                module.queue(settings.animation);
                            }

                            return false;
                        }
                        if (!settings.allowRepeats && module.is.occurring()) {
                            module.debug('Animation is already occurring, will not execute repeated animation', settings.animation);

                            return false;
                        }

                        module.debug('New animation started, completing previous early', settings.animation);
                        instance.complete();
                    }
                    if (module.can.animate()) {
                        module.set.animating(settings.animation);
                    } else {
                        module.error(error.noAnimation, settings.animation, element);
                    }
                },

                reset: function () {
                    module.debug('Resetting animation to beginning conditions');
                    module.remove.animationCallbacks();
                    module.restore.conditions();
                    module.remove.animating();
                },

                queue: function (animation) {
                    module.debug('Queueing animation of', animation);
                    module.queuing = true;
                    $module
                        .one('animationend.queue' + eventNamespace, function () {
                            module.queuing = false;
                            module.repaint();
                            module.animate.apply(this, settings);
                        })
                    ;
                },

                complete: function (event) {
                    if (event && event.target === element) {
                        event.stopPropagation();
                    }
                    module.debug('Animation complete', settings.animation);
                    module.remove.completeCallback();
                    module.remove.failSafe();
                    if (!module.is.looping()) {
                        if (module.is.outward()) {
                            module.verbose('Animation is outward, hiding element');
                            module.restore.conditions();
                            module.hide();
                        } else if (module.is.inward()) {
                            module.verbose('Animation is inward, showing element');
                            module.restore.conditions();
                            module.show();
                        } else {
                            module.verbose('Static animation completed');
                            module.restore.conditions();
                            settings.onComplete.call(element);
                        }
                    }
                },

                force: {
                    visible: function () {
                        var
                            style          = $module.attr('style'),
                            userStyle      = module.get.userStyle(style),
                            displayType    = module.get.displayType(),
                            overrideStyle  = userStyle + 'display: ' + displayType + ' !important;',
                            inlineDisplay  = $module[0].style.display,
                            mustStayHidden = !displayType || (inlineDisplay === 'none' && settings.skipInlineHidden) || $module[0].tagName.match(/(script|link|style)/i)
                        ;
                        if (mustStayHidden) {
                            module.remove.transition();

                            return false;
                        }
                        module.verbose('Overriding default display to show element', displayType);
                        $module
                            .attr('style', overrideStyle)
                        ;

                        return true;
                    },
                    hidden: function () {
                        var
                            style          = $module.attr('style'),
                            currentDisplay = $module.css('display'),
                            emptyStyle     = style === undefined || style === ''
                        ;
                        if (currentDisplay !== 'none' && !module.is.hidden()) {
                            module.verbose('Overriding default display to hide element');
                            $module
                                .css('display', 'none')
                            ;
                        } else if (emptyStyle) {
                            $module
                                .removeAttr('style')
                            ;
                        }
                    },
                },

                has: {
                    direction: function (animation) {
                        var
                            hasDirection = false
                        ;
                        animation = animation || settings.animation;
                        if (typeof animation === 'string') {
                            animation = animation.split(' ');
                            $.each(animation, function (index, word) {
                                if (word === className.inward || word === className.outward) {
                                    hasDirection = true;
                                }
                            });
                        }

                        return hasDirection;
                    },
                    inlineDisplay: function () {
                        var
                            style = $module.attr('style') || ''
                        ;

                        return Array.isArray(style.match(/display.*?;/, ''));
                    },
                },

                set: {
                    animating: function (animation) {
                        // remove previous callbacks
                        module.remove.completeCallback();

                        // determine exact animation
                        animation = animation || settings.animation;
                        var animationClass = module.get.animationClass(animation);

                        // save animation class in cache to restore class names
                        module.save.animation(animationClass);

                        if (module.force.visible()) {
                            module.remove.hidden();
                            module.remove.direction();

                            module.start.animation(animationClass);
                        }
                    },
                    duration: function (animationName, duration) {
                        duration = duration || settings.duration;
                        duration = typeof duration === 'number'
                            ? duration + 'ms'
                            : duration;
                        if (duration || duration === 0) {
                            module.verbose('Setting animation duration', duration);
                            $module
                                .css({
                                    'animation-duration': duration,
                                })
                            ;
                        }
                    },
                    direction: function (direction) {
                        direction = direction || module.get.direction();
                        if (direction === className.inward) {
                            module.set.inward();
                        } else {
                            module.set.outward();
                        }
                    },
                    looping: function () {
                        module.debug('Transition set to loop');
                        $module
                            .addClass(className.looping)
                        ;
                    },
                    hidden: function () {
                        $module
                            .addClass(className.transition)
                            .addClass(className.hidden)
                        ;
                    },
                    inward: function () {
                        module.debug('Setting direction to inward');
                        $module
                            .removeClass(className.outward)
                            .addClass(className.inward)
                        ;
                    },
                    outward: function () {
                        module.debug('Setting direction to outward');
                        $module
                            .removeClass(className.inward)
                            .addClass(className.outward)
                        ;
                    },
                    visible: function () {
                        $module
                            .addClass(className.transition)
                            .addClass(className.visible)
                        ;
                    },
                },

                start: {
                    animation: function (animationClass) {
                        animationClass = animationClass || module.get.animationClass();
                        module.debug('Starting tween', animationClass);
                        $module
                            .addClass(animationClass)
                            .one('animationend.complete' + eventNamespace, module.complete)
                        ;
                        if (settings.useFailSafe) {
                            module.add.failSafe();
                        }
                        module.set.duration(settings.duration);
                        settings.onStart.call(element);
                    },
                },

                save: {
                    animation: function (animation) {
                        if (!module.cache) {
                            module.cache = {};
                        }
                        module.cache.animation = animation;
                    },
                    displayType: function (displayType) {
                        if (displayType !== 'none') {
                            $module.data(metadata.displayType, displayType);
                        }
                    },
                    transitionExists: function (animation, exists) {
                        $.fn.transition.exists[animation] = exists;
                        module.verbose('Saving existence of transition', animation, exists);
                    },
                },

                restore: {
                    conditions: function () {
                        var
                            animation = module.get.currentAnimation()
                        ;
                        if (animation) {
                            $module
                                .removeClass(animation)
                            ;
                            module.verbose('Removing animation class', module.cache);
                        }
                        module.remove.duration();
                    },
                },

                add: {
                    failSafe: function () {
                        var
                            duration = module.get.duration()
                        ;
                        module.timer = setTimeout(function () {
                            $module.triggerHandler('animationend');
                        }, duration + settings.failSafeDelay);
                        module.verbose('Adding fail safe timer', module.timer);
                    },
                },

                remove: {
                    animating: function () {
                        $module.removeClass(className.animating);
                    },
                    animationCallbacks: function () {
                        module.remove.queueCallback();
                        module.remove.completeCallback();
                    },
                    queueCallback: function () {
                        $module.off('.queue' + eventNamespace);
                    },
                    completeCallback: function () {
                        $module.off('.complete' + eventNamespace);
                    },
                    display: function () {
                        $module.css('display', '');
                    },
                    direction: function () {
                        $module
                            .removeClass(className.inward)
                            .removeClass(className.outward)
                        ;
                    },
                    duration: function () {
                        $module
                            .css('animation-duration', '')
                        ;
                    },
                    failSafe: function () {
                        module.verbose('Removing fail safe timer', module.timer);
                        if (module.timer) {
                            clearTimeout(module.timer);
                        }
                    },
                    hidden: function () {
                        $module.removeClass(className.hidden);
                    },
                    visible: function () {
                        $module.removeClass(className.visible);
                    },
                    looping: function () {
                        module.debug('Transitions are no longer looping');
                        if (module.is.looping()) {
                            module.reset();
                            $module
                                .removeClass(className.looping)
                            ;
                        }
                    },
                    transition: function () {
                        $module
                            .removeClass(className.transition)
                            .removeClass(className.visible)
                            .removeClass(className.hidden)
                        ;
                    },
                },
                get: {
                    settings: function (animation, duration, onComplete) {
                        if (typeof animation === 'object') { // single settings object
                            return $.extend(true, {}, $.fn.transition.settings, animation);
                        }
                        if (typeof onComplete === 'function') { // all arguments provided
                            return $.extend({}, $.fn.transition.settings, {
                                animation: animation,
                                onComplete: onComplete,
                                duration: duration,
                            });
                        }
                        if (typeof duration === 'string' || typeof duration === 'number') { // only duration provided
                            return $.extend({}, $.fn.transition.settings, {
                                animation: animation,
                                duration: duration,
                            });
                        }
                        if (typeof duration === 'object') { // duration is actually settings object
                            return $.extend({}, $.fn.transition.settings, duration, {
                                animation: animation,
                            });
                        }
                        if (typeof duration === 'function') { // duration is actually callback
                            return $.extend({}, $.fn.transition.settings, {
                                animation: animation,
                                onComplete: duration,
                            });
                        }

                        // only animation provided
                        return $.extend({}, $.fn.transition.settings, {
                            animation: animation,
                        });
                    },
                    animationClass: function (animation) {
                        var
                            animationClass = animation || settings.animation,
                            directionClass = module.can.transition() && !module.has.direction()
                                ? module.get.direction() + ' '
                                : ''
                        ;

                        return className.animating + ' '
                            + className.transition + ' '
                            + directionClass
                            + animationClass;
                    },
                    currentAnimation: function () {
                        return module.cache && module.cache.animation !== undefined
                            ? module.cache.animation
                            : false;
                    },
                    currentDirection: function () {
                        return module.is.inward()
                            ? className.inward
                            : className.outward;
                    },
                    direction: function () {
                        return module.is.hidden() || !module.is.visible()
                            ? className.inward
                            : className.outward;
                    },
                    animationDirection: function (animation) {
                        var
                            direction
                        ;
                        animation = animation || settings.animation;
                        if (typeof animation === 'string') {
                            animation = animation.split(' ');
                            // search animation name for out/in class
                            $.each(animation, function (index, word) {
                                if (word === className.inward) {
                                    direction = className.inward;
                                } else if (word === className.outward) {
                                    direction = className.outward;
                                }
                            });
                        }
                        // return found direction
                        if (direction) {
                            return direction;
                        }

                        return false;
                    },
                    duration: function (duration) {
                        duration = duration || settings.duration;
                        if (duration === false) {
                            duration = $module.css('animation-duration') || 0;
                        }

                        return typeof duration === 'string'
                            ? (duration.indexOf('ms') > -1
                                ? parseFloat(duration)
                                : parseFloat(duration) * 1000)
                            : duration;
                    },
                    displayType: function (shouldDetermine) {
                        shouldDetermine = shouldDetermine !== undefined
                            ? shouldDetermine
                            : true;
                        if (settings.displayType) {
                            return settings.displayType;
                        }
                        if (shouldDetermine && $module.data(metadata.displayType) === undefined) {
                            var currentDisplay = $module.css('display');
                            if (currentDisplay === '' || currentDisplay === 'none') {
                                // create fake element to determine display state
                                module.can.transition(true);
                            } else {
                                module.save.displayType(currentDisplay);
                            }
                        }

                        return $module.data(metadata.displayType);
                    },
                    userStyle: function (style) {
                        style = style || $module.attr('style') || '';

                        return style.replace(/display.*?;/, '');
                    },
                    transitionExists: function (animation) {
                        return $.fn.transition.exists[animation];
                    },
                },

                can: {
                    transition: function (forced) {
                        var
                            animation         = settings.animation,
                            transitionExists  = module.get.transitionExists(animation),
                            displayType       = module.get.displayType(false),
                            elementClass,
                            tagName,
                            $clone,
                            currentAnimation,
                            inAnimation,
                            directionExists
                        ;
                        if (transitionExists === undefined || forced) {
                            module.verbose('Determining whether animation exists');
                            elementClass = $module.attr('class');
                            tagName = $module.prop('tagName');

                            $clone = $('<' + tagName + ' />').addClass(elementClass).insertAfter($module);
                            currentAnimation = $clone
                                .addClass(animation)
                                .removeClass(className.inward)
                                .removeClass(className.outward)
                                .addClass(className.animating)
                                .addClass(className.transition)
                                .css('animationName')
                            ;
                            $clone.detach().insertAfter($module);
                            inAnimation = $clone
                                .addClass(className.inward)
                                .css('animationName')
                            ;
                            if (!displayType) {
                                $clone.detach().insertAfter($module);
                                displayType = $clone
                                    .attr('class', elementClass)
                                    .removeAttr('style')
                                    .removeClass(className.hidden)
                                    .removeClass(className.visible)
                                    .show()
                                    .css('display')
                                ;
                                module.verbose('Determining final display state', displayType);
                                module.save.displayType(displayType);
                            }

                            $clone.remove();
                            if (currentAnimation !== inAnimation) {
                                module.debug('Direction exists for animation', animation);
                                directionExists = true;
                            } else if (currentAnimation === 'none' || !currentAnimation) {
                                module.debug('No animation defined in css', animation);

                                return;
                            } else {
                                module.debug('Static animation found', animation, displayType);
                                directionExists = false;
                            }
                            module.save.transitionExists(animation, directionExists);
                        }

                        return transitionExists !== undefined
                            ? transitionExists
                            : directionExists;
                    },
                    animate: function () {
                        // can transition does not return a value if animation does not exist
                        return module.can.transition() !== undefined;
                    },
                },

                is: {
                    animating: function () {
                        return $module.hasClass(className.animating);
                    },
                    inward: function () {
                        return $module.hasClass(className.inward);
                    },
                    outward: function () {
                        return $module.hasClass(className.outward);
                    },
                    looping: function () {
                        return $module.hasClass(className.looping);
                    },
                    occurring: function (animation) {
                        animation = animation || settings.animation;
                        animation = '.' + animation.replace(' ', '.');

                        return $module.filter(animation).length > 0;
                    },
                    visible: function () {
                        return $module.is(':visible');
                    },
                    hidden: function () {
                        return $module.css('visibility') === 'hidden';
                    },
                    supported: function () {
                        // keep method for backward compatibility until 2.10.0
                        return true;
                    },
                },

                hide: function () {
                    if (settings.onHide.call(element) === false) {
                        module.verbose('Hide callback returned false cancelling hide');

                        return false;
                    }
                    module.verbose('Hiding element');
                    if (module.is.animating()) {
                        module.reset();
                    }
                    element.blur(); // IE will trigger focus change if element is not blurred before hiding
                    module.remove.display();
                    module.remove.visible();
                    settings.onBeforeHide.call(element, module.hideNow);
                },

                hideNow: function () {
                    module.set.hidden();
                    module.force.hidden();
                    settings.onHidden.call(element);
                    settings.onComplete.call(element);
                },

                show: function (display) {
                    if (module.force.visible() && settings.onShow.call(element) !== false) {
                        module.verbose('Showing element', display);
                        module.remove.hidden();
                        settings.onBeforeShow.call(element, module.showNow);
                    }
                },

                showNow: function () {
                    module.set.visible();
                    settings.onVisible.call(element);
                    settings.onComplete.call(element);
                },

                toggle: function () {
                    if (module.is.visible()) {
                        module.hide();
                    } else {
                        module.show();
                    }
                },

                stop: function () {
                    module.debug('Stopping current animation');
                    $module.triggerHandler('animationend');
                },

                stopAll: function () {
                    module.debug('Stopping all animation');
                    module.remove.queueCallback();
                    $module.triggerHandler('animationend');
                },

                clear: {
                    queue: function () {
                        module.debug('Clearing animation queue');
                        module.remove.queueCallback();
                    },
                },

                enable: function () {
                    module.verbose('Starting animation');
                    $module.removeClass(className.disabled);
                },

                disable: function () {
                    module.debug('Stopping animation');
                    $module.addClass(className.disabled);
                },

                setting: function (name, value) {
                    module.debug('Changing setting', name, value);
                    if ($.isPlainObject(name)) {
                        $.extend(true, settings, name);
                    } else if (value !== undefined) {
                        if ($.isPlainObject(settings[name])) {
                            $.extend(true, settings[name], value);
                        } else {
                            settings[name] = value;
                        }
                    } else {
                        return settings[name];
                    }
                },
                internal: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, module, name);
                    } else if (value !== undefined) {
                        module[name] = value;
                    } else {
                        return module[name];
                    }
                },
                debug: function () {
                    if (!settings.silent && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.debug = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.debug.apply(console, arguments);
                        }
                    }
                },
                verbose: function () {
                    if (!settings.silent && settings.verbose && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.verbose = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.verbose.apply(console, arguments);
                        }
                    }
                },
                error: function () {
                    if (!settings.silent) {
                        module.error = Function.prototype.bind.call(console.error, console, settings.name + ':');
                        module.error.apply(console, arguments);
                    }
                },
                performance: {
                    log: function (message) {
                        var
                            currentTime,
                            executionTime,
                            previousTime
                        ;
                        if (settings.performance) {
                            currentTime = Date.now();
                            previousTime = time || currentTime;
                            executionTime = currentTime - previousTime;
                            time = currentTime;
                            performance.push({
                                Name: message[0],
                                Arguments: [].slice.call(message, 1) || '',
                                Element: element,
                                'Execution Time': executionTime,
                            });
                        }
                        clearTimeout(module.performance.timer);
                        module.performance.timer = setTimeout(function () { module.performance.display(); }, 500);
                    },
                    display: function () {
                        var
                            title = settings.name + ':',
                            totalTime = 0
                        ;
                        time = false;
                        clearTimeout(module.performance.timer);
                        $.each(performance, function (index, data) {
                            totalTime += data['Execution Time'];
                        });
                        title += ' ' + totalTime + 'ms';
                        if ($allModules.length > 1) {
                            title += ' (' + $allModules.length + ')';
                        }
                        if (performance.length > 0) {
                            console.groupCollapsed(title);
                            if (console.table) {
                                console.table(performance);
                            } else {
                                $.each(performance, function (index, data) {
                                    console.log(data.Name + ': ' + data['Execution Time'] + 'ms');
                                });
                            }
                            console.groupEnd();
                        }
                        performance = [];
                    },
                },
                // modified for transition to return invoke success
                invoke: function (query, passedArguments, context) {
                    var
                        object = instance,
                        maxDepth,
                        found,
                        response
                    ;
                    passedArguments = passedArguments || queryArguments;
                    context = context || element;
                    if (typeof query === 'string' && object !== undefined) {
                        query = query.split(/[ .]/);
                        maxDepth = query.length - 1;
                        $.each(query, function (depth, value) {
                            var camelCaseValue = depth !== maxDepth
                                ? value + query[depth + 1].charAt(0).toUpperCase() + query[depth + 1].slice(1)
                                : query;
                            if ($.isPlainObject(object[camelCaseValue]) && (depth !== maxDepth)) {
                                object = object[camelCaseValue];
                            } else if (object[camelCaseValue] !== undefined) {
                                found = object[camelCaseValue];

                                return false;
                            } else if ($.isPlainObject(object[value]) && (depth !== maxDepth)) {
                                object = object[value];
                            } else if (object[value] !== undefined) {
                                found = object[value];

                                return false;
                            } else {
                                return false;
                            }
                        });
                    }
                    if (isFunction(found)) {
                        response = found.apply(context, passedArguments);
                    } else if (found !== undefined) {
                        response = found;
                    }

                    if (Array.isArray(returnedValue)) {
                        returnedValue.push(response);
                    } else if (returnedValue !== undefined) {
                        returnedValue = [returnedValue, response];
                    } else if (response !== undefined) {
                        returnedValue = response;
                    }

                    return found !== undefined
                        ? found
                        : false;
                },
            };
            module.initialize();
        });

        return returnedValue !== undefined
            ? returnedValue
            : this;
    };

    // Records if CSS transition is available
    $.fn.transition.exists = {};

    $.fn.transition.settings = {

        // module info
        name: 'Transition',

        // hide all output from this component regardless of other settings
        silent: false,

        // debug content outputted to console
        debug: false,

        // verbose debug output
        verbose: false,

        // performance data output
        performance: true,

        // event namespace
        namespace: 'transition',

        // delay between animations in group
        interval: 0,

        // whether group animations should be reversed
        reverse: 'auto',

        // animation callback event
        onStart: function () {},
        onComplete: function () {},
        onShow: function () {},
        onBeforeShow: function (callback) {
            callback.call(this);
        },
        onVisible: function () {},
        onHide: function () {},
        onHidden: function () {},
        onBeforeHide: function (callback) {
            callback.call(this);
        },

        // whether timeout should be used to ensure callback fires in cases animationend does not
        useFailSafe: true,

        // delay in ms for fail safe
        failSafeDelay: 100,

        // whether EXACT animation can occur twice in a row
        allowRepeats: false,

        // Override final display type on visible
        displayType: false,

        // animation duration
        animation: 'fade',
        duration: false,

        // new animations will occur after previous ones
        queue: true,

        // whether initially inline hidden objects should be skipped for transition
        skipInlineHidden: false,

        metadata: {
            displayType: 'display',
        },

        className: {
            animating: 'animating',
            disabled: 'disabled',
            hidden: 'hidden',
            inward: 'in',
            loading: 'loading',
            looping: 'looping',
            outward: 'out',
            transition: 'transition',
            visible: 'visible',
        },

        // possible errors
        error: {
            noAnimation: 'Element is no longer attached to DOM. Unable to animate.  Use silent setting to suppress this warning in production.',
        },

    };
})(jQuery, window, document);

/*!
 * # Fomantic-UI 2.9.3 - Tab
 * https://github.com/fomantic/Fomantic-UI/
 *
 *
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */

(function ($, window, document) {
    'use strict';

    function isWindow(obj) {
        return obj !== null && obj === obj.window;
    }

    function isFunction(obj) {
        return typeof obj === 'function' && typeof obj.nodeType !== 'number';
    }

    window = window !== undefined && window.Math === Math
        ? window
        : globalThis;

    $.fn.tab = function (parameters) {
        var
            // use window context if none specified
            $allModules     = isFunction(this)
                ? $(window)
                : $(this),
            $document      = $(document),
            time            = Date.now(),
            performance     = [],

            query           = arguments[0],
            methodInvoked   = typeof query === 'string',
            queryArguments  = [].slice.call(arguments, 1),
            contextCheck   = function (context, win) {
                var $context;
                if ([window, document].indexOf(context) >= 0) {
                    $context = $(context);
                } else {
                    $context = $(win.document).find(context);
                    if ($context.length === 0) {
                        $context = win.frameElement ? contextCheck(context, win.parent) : window;
                    }
                }

                return $context;
            },
            initializedHistory = false,
            returnedValue
        ;

        $allModules.each(function () {
            var
                settings        = $.isPlainObject(parameters)
                    ? $.extend(true, {}, $.fn.tab.settings, parameters)
                    : $.extend({}, $.fn.tab.settings),

                className       = settings.className,
                metadata        = settings.metadata,
                selector        = settings.selector,
                error           = settings.error,
                regExp          = settings.regExp,

                eventNamespace  = '.' + settings.namespace,
                moduleNamespace = 'module-' + settings.namespace,

                $module         = $(this),
                $context,
                $tabs,

                cache           = {},
                firstLoad       = true,
                recursionDepth  = 0,
                element         = this,
                instance        = $module.data(moduleNamespace),

                activeTabPath,
                parameterArray,
                module,

                historyEvent
            ;

            module = {

                initialize: function () {
                    module.debug('Initializing tab menu item', $module);
                    module.determineTabs();

                    module.debug('Determining tabs', settings.context, $tabs);
                    // set up automatic routing
                    if (settings.auto) {
                        module.set.auto();
                    }
                    module.bind.events();

                    if (settings.history && !initializedHistory) {
                        settings.history = module.initializeHistory();
                        initializedHistory = true;
                    }

                    var activeTab = module.determine.activeTab();
                    if (settings.autoTabActivation && instance === undefined && activeTab === null) {
                        activeTab = settings.autoTabActivation === true ? module.get.initialPath() : settings.autoTabActivation;
                        module.debug('No active tab detected, setting tab active', activeTab);
                        module.changeTab(activeTab);
                    }
                    if (activeTab !== null && settings.history && settings.historyType === 'state') {
                        var autoUpdate = $.address.autoUpdate();
                        $.address.autoUpdate(false);
                        $.address.value(activeTab);
                        $.address.autoUpdate(autoUpdate);
                    }

                    module.instantiate();
                },

                instantiate: function () {
                    module.verbose('Storing instance of module', module);
                    instance = module;
                    $module
                        .data(moduleNamespace, module)
                    ;
                },

                destroy: function () {
                    module.debug('Destroying tabs', $module);
                    $module
                        .removeData(moduleNamespace)
                        .off(eventNamespace)
                    ;
                },

                bind: {
                    events: function () {
                        // if using $.tab don't add events
                        if (!isWindow(element)) {
                            module.debug('Attaching tab activation events to element', $module);
                            $module
                                .on('click' + eventNamespace, module.event.click)
                            ;
                        }
                    },
                },

                determineTabs: function () {
                    var
                        $reference
                    ;

                    // determine tab context
                    if (settings.context === 'parent') {
                        if ($module.closest(selector.ui).length > 0) {
                            $reference = $module.closest(selector.ui);
                            module.verbose('Using closest UI element as parent', $reference);
                        } else {
                            $reference = $module;
                        }
                        $context = $reference.parent();
                        module.verbose('Determined parent element for creating context', $context);
                    } else if (settings.context) {
                        $context = contextCheck(settings.context, window);
                        module.verbose('Using selector for tab context', settings.context, $context);
                    } else {
                        $context = $('body');
                    }
                    // find tabs
                    if (settings.childrenOnly) {
                        $tabs = $context.children(selector.tabs);
                        module.debug('Searching tab context children for tabs', $context, $tabs);
                    } else {
                        $tabs = $context.find(selector.tabs);
                        module.debug('Searching tab context for tabs', $context, $tabs);
                    }
                },

                initializeHistory: function () {
                    module.debug('Initializing page state');
                    if ($.address === undefined) {
                        module.error(error.state);

                        return false;
                    }
                    if (settings.historyType === 'state') {
                        module.debug('Using HTML5 to manage state');
                        if (settings.path !== false) {
                            $.address
                                .history(true)
                                .state(settings.path)
                            ;
                            $(window).trigger('popstate');
                        } else {
                            module.error(error.path);

                            return false;
                        }
                    }
                    $.address
                        .bind('change', module.event.history.change)
                    ;

                    return true;
                },

                event: {
                    click: function (event) {
                        var
                            tabPath = $(this).data(metadata.tab)
                        ;
                        if (tabPath !== undefined) {
                            if (settings.history) {
                                module.verbose('Updating page state', event);
                                $.address.value(tabPath);
                            } else {
                                module.verbose('Changing tab', event);
                                module.changeTab(tabPath);
                            }
                            event.preventDefault();
                        } else {
                            module.debug('No tab specified');
                        }
                    },
                    history: {
                        change: function (event) {
                            var
                                tabPath   = event.pathNames.join('/') || module.get.initialPath(),
                                pageTitle = settings.templates.determineTitle(tabPath) || false
                            ;
                            module.performance.display();
                            module.debug('History change event', tabPath, event);
                            historyEvent = event;
                            if (tabPath !== undefined) {
                                module.changeTab(tabPath);
                            }
                            if (pageTitle) {
                                $.address.title(pageTitle);
                            }
                        },
                    },
                },

                refresh: function () {
                    if (activeTabPath) {
                        module.debug('Refreshing tab', activeTabPath);
                        module.changeTab(activeTabPath);
                    }
                },

                cache: {

                    read: function (cacheKey) {
                        return cacheKey !== undefined
                            ? cache[cacheKey]
                            : false;
                    },
                    add: function (cacheKey, content) {
                        cacheKey = cacheKey || activeTabPath;
                        module.debug('Adding cached content for', cacheKey);
                        cache[cacheKey] = content;
                    },
                    remove: function (cacheKey) {
                        cacheKey = cacheKey || activeTabPath;
                        module.debug('Removing cached content for', cacheKey);
                        delete cache[cacheKey];
                    },
                },

                escape: {
                    string: function (text) {
                        text = String(text);

                        return text.replace(regExp.escape, '\\$&');
                    },
                },

                set: {
                    auto: function () {
                        var
                            url = typeof settings.path === 'string'
                                ? settings.path.replace(/\/$/, '') + '/{$tab}'
                                : '/{$tab}'
                        ;
                        module.verbose('Setting up automatic tab retrieval from server', url);
                        if ($.isPlainObject(settings.apiSettings)) {
                            settings.apiSettings.url = url;
                        } else {
                            settings.apiSettings = {
                                url: url,
                            };
                        }
                    },
                    loading: function (tabPath) {
                        var
                            $tab      = module.get.tabElement(tabPath),
                            isLoading = $tab.hasClass(className.loading)
                        ;
                        if (!isLoading) {
                            module.verbose('Setting loading state for', $tab);
                            $tab
                                .addClass(className.loading)
                                .siblings($tabs)
                                .removeClass(className.active + ' ' + className.loading)
                            ;
                            if ($tab.length > 0) {
                                settings.onRequest.call($tab[0], tabPath);
                            }
                        }
                    },
                    state: function (state) {
                        $.address.value(state);
                    },
                },

                changeTab: function (tabPath) {
                    var
                        pushStateAvailable = window.history && window.history.pushState,
                        shouldIgnoreLoad   = pushStateAvailable && settings.ignoreFirstLoad && firstLoad,
                        remoteContent      = settings.auto || $.isPlainObject(settings.apiSettings),
                        // only add default path if not remote content
                        pathArray = remoteContent && !shouldIgnoreLoad
                            ? module.utilities.pathToArray(tabPath)
                            : module.get.defaultPathArray(tabPath)
                    ;
                    tabPath = module.utilities.arrayToPath(pathArray);
                    $.each(pathArray, function (index, tab) {
                        var
                            currentPathArray   = pathArray.slice(0, index + 1),
                            currentPath        = module.utilities.arrayToPath(currentPathArray),

                            isTab              = module.is.tab(currentPath),
                            isLastIndex        = index + 1 === pathArray.length,

                            $tab               = module.get.tabElement(currentPath),
                            $anchor,
                            nextPathArray,
                            nextPath,
                            isLastTab
                        ;
                        module.verbose('Looking for tab', tab);
                        if (isTab) {
                            module.verbose('Tab was found', tab);
                            // scope up
                            activeTabPath = currentPath;
                            parameterArray = module.utilities.filterArray(pathArray, currentPathArray);

                            if (isLastIndex) {
                                isLastTab = true;
                            } else {
                                nextPathArray = pathArray.slice(0, index + 2);
                                nextPath = module.utilities.arrayToPath(nextPathArray);
                                isLastTab = !module.is.tab(nextPath);
                                if (isLastTab) {
                                    module.verbose('Tab parameters found', nextPathArray);
                                }
                            }
                            if (settings.onBeforeChange.call(element, currentPath) === false) {
                                module.debug('onBeforeChange returned false, cancelling tab change', $tab);

                                return false;
                            }
                            if (isLastTab && remoteContent) {
                                if (!shouldIgnoreLoad) {
                                    module.activate.navigation(currentPath);
                                    module.fetch.content(currentPath, tabPath);
                                } else {
                                    module.debug('Ignoring remote content on first tab load', currentPath);
                                    firstLoad = false;
                                    module.cache.add(tabPath, $tab.html());
                                    module.activate.all(currentPath);
                                    settings.onFirstLoad.call($tab[0], currentPath, parameterArray, historyEvent);
                                    settings.onLoad.call($tab[0], currentPath, parameterArray, historyEvent);
                                }

                                return false;
                            }

                            module.debug('Opened local tab', currentPath);
                            module.activate.all(currentPath);
                            if (!module.cache.read(currentPath)) {
                                module.cache.add(currentPath, true);
                                module.debug('First time tab loaded calling tab init');
                                settings.onFirstLoad.call($tab[0], currentPath, parameterArray, historyEvent);
                            }
                            settings.onLoad.call($tab[0], currentPath, parameterArray, historyEvent);
                        } else if (tabPath.search('/') === -1 && tabPath !== '') {
                            // look for in page anchor
                            tabPath = module.escape.string(tabPath);
                            $anchor = $('#' + tabPath + ', a[name="' + tabPath + '"]');
                            currentPath = $anchor.closest('[data-tab]').data(metadata.tab);
                            $tab = module.get.tabElement(currentPath);
                            // if anchor exists use parent tab
                            if ($anchor && $anchor.length > 0 && currentPath) {
                                module.debug('Anchor link used, opening parent tab', $tab, $anchor);
                                if (settings.onBeforeChange.call(element, currentPath) === false) {
                                    module.debug('onBeforeChange returned false, cancelling tab change', $tab);

                                    return false;
                                }
                                if (!$tab.hasClass(className.active)) {
                                    setTimeout(function () {
                                        module.scrollTo($anchor);
                                    }, 0);
                                }
                                module.activate.all(currentPath);
                                if (!module.cache.read(currentPath)) {
                                    module.cache.add(currentPath, true);
                                    module.debug('First time tab loaded calling tab init');
                                    settings.onFirstLoad.call($tab[0], currentPath, parameterArray, historyEvent);
                                }
                                settings.onLoad.call($tab[0], currentPath, parameterArray, historyEvent);

                                return false;
                            }
                        } else {
                            module.error(error.missingTab, $module, $context, currentPath);

                            return false;
                        }
                    });
                },

                scrollTo: function ($element) {
                    var
                        scrollOffset = $element && $element.length > 0
                            ? $element.offset().top
                            : false
                    ;
                    if (scrollOffset !== false) {
                        module.debug('Forcing scroll to an in-page link in a hidden tab', scrollOffset, $element);
                        $document.scrollTop(scrollOffset);
                    }
                },

                update: {
                    content: function (tabPath, html, evaluateScripts) {
                        var
                            $tab = module.get.tabElement(tabPath),
                            tab  = $tab[0]
                        ;
                        evaluateScripts = evaluateScripts !== undefined
                            ? evaluateScripts
                            : settings.evaluateScripts;
                        if (typeof settings.cacheType === 'string' && settings.cacheType.toLowerCase() === 'dom' && typeof html !== 'string') {
                            $tab
                                .empty()
                                .append($(html).clone(true))
                            ;
                        } else {
                            if (evaluateScripts) {
                                module.debug('Updating HTML and evaluating inline scripts', tabPath, html);
                                $tab.html(html);
                            } else {
                                module.debug('Updating HTML', tabPath, html);
                                tab.innerHTML = html;
                            }
                        }
                    },
                },

                fetch: {

                    content: function (tabPath, fullTabPath) {
                        var
                            $tab        = module.get.tabElement(tabPath),
                            apiSettings = {
                                dataType: 'html',
                                encodeParameters: false,
                                on: 'now',
                                cache: settings.alwaysRefresh,
                                headers: {
                                    'X-Remote': true,
                                },
                                onSuccess: function (response) {
                                    if (settings.cacheType === 'response') {
                                        module.cache.add(fullTabPath, response);
                                    }
                                    module.update.content(tabPath, response);
                                    if (tabPath == activeTabPath) {
                                        module.debug('Content loaded', tabPath);
                                        module.activate.tab(tabPath);
                                    } else {
                                        module.debug('Content loaded in background', tabPath);
                                    }
                                    settings.onFirstLoad.call($tab[0], tabPath, parameterArray, historyEvent);
                                    settings.onLoad.call($tab[0], tabPath, parameterArray, historyEvent);

                                    if (settings.loadOnce) {
                                        module.cache.add(fullTabPath, true);
                                    } else if (typeof settings.cacheType === 'string' && settings.cacheType.toLowerCase() === 'dom' && $tab.children().length > 0) {
                                        setTimeout(function () {
                                            var
                                                $clone = $tab.children().clone(true)
                                            ;
                                            $clone = $clone.not('script');
                                            module.cache.add(fullTabPath, $clone);
                                        }, 0);
                                    } else {
                                        module.cache.add(fullTabPath, $tab.html());
                                    }
                                },
                                urlData: {
                                    tab: fullTabPath,
                                },
                            },
                            request         = $tab.api('get request') || false,
                            existingRequest = request && request.state() === 'pending',
                            requestSettings,
                            cachedContent
                        ;

                        fullTabPath = fullTabPath || tabPath;
                        cachedContent = module.cache.read(fullTabPath);

                        if (settings.cache && cachedContent) {
                            module.activate.tab(tabPath);
                            module.debug('Adding cached content', fullTabPath);
                            if (!settings.loadOnce) {
                                if (settings.evaluateScripts === 'once') {
                                    module.update.content(tabPath, cachedContent, false);
                                } else {
                                    module.update.content(tabPath, cachedContent);
                                }
                            }
                            settings.onLoad.call($tab[0], tabPath, parameterArray, historyEvent);
                        } else if (existingRequest) {
                            module.set.loading(tabPath);
                            module.debug('Content is already loading', fullTabPath);
                        } else if ($.api !== undefined) {
                            requestSettings = $.extend(true, {}, settings.apiSettings, apiSettings);
                            module.debug('Retrieving remote content', fullTabPath, requestSettings);
                            module.set.loading(tabPath);
                            $tab.api(requestSettings);
                        } else {
                            module.error(error.api);
                        }
                    },
                },

                activate: {
                    all: function (tabPath) {
                        module.activate.tab(tabPath);
                        module.activate.navigation(tabPath);
                    },
                    tab: function (tabPath) {
                        var
                            $tab          = module.get.tabElement(tabPath),
                            $deactiveTabs = settings.deactivate === 'siblings'
                                ? $tab.siblings($tabs)
                                : $tabs.not($tab),
                            isActive      = $tab.hasClass(className.active)
                        ;
                        module.verbose('Showing tab content for', $tab);
                        if (!isActive) {
                            $tab
                                .addClass(className.active)
                            ;
                            $deactiveTabs
                                .removeClass(className.active + ' ' + className.loading)
                            ;
                            if ($tab.length > 0) {
                                settings.onVisible.call($tab[0], tabPath);
                            }
                        }
                    },
                    navigation: function (tabPath) {
                        var
                            $navigation         = module.get.navElement(tabPath),
                            $deactiveNavigation = settings.deactivate === 'siblings'
                                ? $navigation.siblings($allModules)
                                : $allModules.not($navigation),
                            isActive    = $navigation.hasClass(className.active)
                        ;
                        module.verbose('Activating tab navigation for', $navigation, tabPath);
                        if (!isActive) {
                            $navigation
                                .addClass(className.active)
                            ;
                            $deactiveNavigation
                                .removeClass(className.active + ' ' + className.loading)
                            ;
                        }
                    },
                },

                deactivate: {
                    all: function () {
                        module.deactivate.navigation();
                        module.deactivate.tabs();
                    },
                    navigation: function () {
                        $allModules
                            .removeClass(className.active)
                        ;
                    },
                    tabs: function () {
                        $tabs
                            .removeClass(className.active + ' ' + className.loading)
                        ;
                    },
                },

                is: {
                    tab: function (tabName) {
                        return tabName !== undefined
                            ? module.get.tabElement(tabName).length > 0
                            : false;
                    },
                },

                get: {
                    initialPath: function () {
                        return $allModules.eq(0).data(metadata.tab) || $tabs.eq(0).data(metadata.tab);
                    },
                    path: function () {
                        return $.address.value();
                    },
                    // adds default tabs to tab path
                    defaultPathArray: function (tabPath) {
                        return module.utilities.pathToArray(module.get.defaultPath(tabPath));
                    },
                    defaultPath: function (tabPath) {
                        var
                            $defaultNav = $allModules.filter('[data-' + metadata.tab + '^="' + module.escape.string(tabPath) + '/"]').eq(0),
                            defaultTab  = $defaultNav.data(metadata.tab) || false
                        ;
                        if (defaultTab) {
                            module.debug('Found default tab', defaultTab);
                            if (recursionDepth < settings.maxDepth) {
                                recursionDepth++;

                                return module.get.defaultPath(defaultTab);
                            }
                            module.error(error.recursion);
                        } else {
                            module.debug('No default tabs found for', tabPath, $tabs);
                        }
                        recursionDepth = 0;

                        return tabPath;
                    },
                    navElement: function (tabPath) {
                        tabPath = tabPath || activeTabPath;

                        return $allModules.filter('[data-' + metadata.tab + '="' + module.escape.string(tabPath) + '"]');
                    },
                    tabElement: function (tabPath) {
                        var
                            $fullPathTab,
                            $simplePathTab,
                            tabPathArray,
                            lastTab
                        ;
                        tabPath = tabPath || activeTabPath;
                        tabPathArray = module.utilities.pathToArray(tabPath);
                        lastTab = module.utilities.last(tabPathArray);
                        $fullPathTab = $tabs.filter('[data-' + metadata.tab + '="' + module.escape.string(tabPath) + '"]');
                        $simplePathTab = $tabs.filter('[data-' + metadata.tab + '="' + module.escape.string(lastTab) + '"]');

                        return $fullPathTab.length > 0
                            ? $fullPathTab
                            : $simplePathTab;
                    },
                    tab: function () {
                        return activeTabPath;
                    },
                },

                determine: {
                    activeTab: function () {
                        var activeTab = null;

                        $tabs.each(function (_index, tab) {
                            var $tab = $(tab);

                            if ($tab.hasClass(className.active)) {
                                var
                                    tabPath = $(this).data(metadata.tab),
                                    $anchor = $allModules.filter('[data-' + metadata.tab + '="' + module.escape.string(tabPath) + '"]')
                                ;

                                if ($anchor.hasClass(className.active)) {
                                    activeTab = tabPath;
                                }
                            }
                        });

                        return activeTab;
                    },
                },

                utilities: {
                    filterArray: function (keepArray, removeArray) {
                        return $.grep(keepArray, function (keepValue) {
                            return $.inArray(keepValue, removeArray) === -1;
                        });
                    },
                    last: function (array) {
                        return Array.isArray(array)
                            ? array[array.length - 1]
                            : false;
                    },
                    pathToArray: function (pathName) {
                        if (pathName === undefined) {
                            pathName = activeTabPath;
                        }

                        return typeof pathName === 'string'
                            ? pathName.split('/')
                            : [pathName];
                    },
                    arrayToPath: function (pathArray) {
                        return Array.isArray(pathArray)
                            ? pathArray.join('/')
                            : false;
                    },
                },

                setting: function (name, value) {
                    module.debug('Changing setting', name, value);
                    if ($.isPlainObject(name)) {
                        $.extend(true, settings, name);
                    } else if (value !== undefined) {
                        if ($.isPlainObject(settings[name])) {
                            $.extend(true, settings[name], value);
                        } else {
                            settings[name] = value;
                        }
                    } else {
                        return settings[name];
                    }
                },
                internal: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, module, name);
                    } else if (value !== undefined) {
                        module[name] = value;
                    } else {
                        return module[name];
                    }
                },
                debug: function () {
                    if (!settings.silent && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.debug = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.debug.apply(console, arguments);
                        }
                    }
                },
                verbose: function () {
                    if (!settings.silent && settings.verbose && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.verbose = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.verbose.apply(console, arguments);
                        }
                    }
                },
                error: function () {
                    if (!settings.silent) {
                        module.error = Function.prototype.bind.call(console.error, console, settings.name + ':');
                        module.error.apply(console, arguments);
                    }
                },
                performance: {
                    log: function (message) {
                        var
                            currentTime,
                            executionTime,
                            previousTime
                        ;
                        if (settings.performance) {
                            currentTime = Date.now();
                            previousTime = time || currentTime;
                            executionTime = currentTime - previousTime;
                            time = currentTime;
                            performance.push({
                                Name: message[0],
                                Arguments: [].slice.call(message, 1) || '',
                                Element: element,
                                'Execution Time': executionTime,
                            });
                        }
                        clearTimeout(module.performance.timer);
                        module.performance.timer = setTimeout(function () { module.performance.display(); }, 500);
                    },
                    display: function () {
                        var
                            title = settings.name + ':',
                            totalTime = 0
                        ;
                        time = false;
                        clearTimeout(module.performance.timer);
                        $.each(performance, function (index, data) {
                            totalTime += data['Execution Time'];
                        });
                        title += ' ' + totalTime + 'ms';
                        if (performance.length > 0) {
                            console.groupCollapsed(title);
                            if (console.table) {
                                console.table(performance);
                            } else {
                                $.each(performance, function (index, data) {
                                    console.log(data.Name + ': ' + data['Execution Time'] + 'ms');
                                });
                            }
                            console.groupEnd();
                        }
                        performance = [];
                    },
                },
                invoke: function (query, passedArguments, context) {
                    var
                        object = instance,
                        maxDepth,
                        found,
                        response
                    ;
                    passedArguments = passedArguments || queryArguments;
                    context = context || element;
                    if (typeof query === 'string' && object !== undefined) {
                        query = query.split(/[ .]/);
                        maxDepth = query.length - 1;
                        $.each(query, function (depth, value) {
                            var camelCaseValue = depth !== maxDepth
                                ? value + query[depth + 1].charAt(0).toUpperCase() + query[depth + 1].slice(1)
                                : query
                            ;
                            if ($.isPlainObject(object[camelCaseValue]) && (depth !== maxDepth)) {
                                object = object[camelCaseValue];
                            } else if (object[camelCaseValue] !== undefined) {
                                found = object[camelCaseValue];

                                return false;
                            } else if ($.isPlainObject(object[value]) && (depth !== maxDepth)) {
                                object = object[value];
                            } else if (object[value] !== undefined) {
                                found = object[value];

                                return false;
                            } else {
                                module.error(error.method, query);

                                return false;
                            }
                        });
                    }
                    if (isFunction(found)) {
                        response = found.apply(context, passedArguments);
                    } else if (found !== undefined) {
                        response = found;
                    }
                    if (Array.isArray(returnedValue)) {
                        returnedValue.push(response);
                    } else if (returnedValue !== undefined) {
                        returnedValue = [returnedValue, response];
                    } else if (response !== undefined) {
                        returnedValue = response;
                    }

                    return found;
                },
            };
            if (methodInvoked) {
                if (instance === undefined) {
                    module.initialize();
                }
                module.invoke(query);
            } else {
                if (instance !== undefined) {
                    instance.invoke('destroy');
                }
                module.initialize();
            }
        });

        return returnedValue !== undefined
            ? returnedValue
            : this;
    };

    // shortcut for tabbed content with no defined navigation
    $.tab = function () {
        $(window).tab.apply(this, arguments);
    };

    $.fn.tab.settings = {

        name: 'Tab',
        namespace: 'tab',

        silent: false,
        debug: false,
        verbose: false,
        performance: true,

        auto: false, // uses pjax style endpoints fetching content from same url with remote-content headers
        history: false, // use browser history
        historyType: 'hash', // #/ or html5 state
        path: false, // base path of url

        context: false, // specify a context that tabs must appear inside
        childrenOnly: false, // use only tabs that are children of context
        maxDepth: 25, // max depth a tab can be nested

        deactivate: 'siblings', // whether tabs should deactivate sibling menu elements or all elements initialized together

        alwaysRefresh: false, // load tab content new every tab click
        cache: true, // cache the content requests to pull locally
        loadOnce: false, // Whether tab data should only be loaded once when using remote content
        cacheType: 'response', // Whether to cache exact response, or to html cache contents after scripts execute
        ignoreFirstLoad: false, // don't load remote content on first load

        apiSettings: false, // settings for api call
        evaluateScripts: 'once', // whether inline scripts should be parsed (true/false/once). Once will not re-evaluate on cached content
        autoTabActivation: true, // whether a non existing active tab will auto activate the first available tab

        onFirstLoad: function (tabPath, parameterArray, historyEvent) {}, // called first time loaded
        onLoad: function (tabPath, parameterArray, historyEvent) {}, // called on every load
        onVisible: function (tabPath, parameterArray, historyEvent) {}, // called every time tab visible
        onRequest: function (tabPath, parameterArray, historyEvent) {}, // called ever time a tab beings loading remote content
        onBeforeChange: function (tabPath) {}, // called before a tab is about to be changed. Returning false will cancel the tab change

        templates: {
            determineTitle: function (tabArray) {}, // returns page title for path
        },

        error: {
            api: 'You attempted to load content without API module',
            method: 'The method you called is not defined',
            missingTab: 'Activated tab cannot be found. Tabs are case-sensitive.',
            noContent: 'The tab you specified is missing a content url.',
            path: 'History enabled, but no path was specified',
            recursion: 'Max recursive depth reached',
            state: 'History requires Asual\'s Address library <https://github.com/asual/jquery-address>',
        },

        regExp: {
            escape: /[\s#$()*+,.:=?@[\\\]^{|}-]/g,
        },

        metadata: {
            tab: 'tab',
            loaded: 'loaded',
            promise: 'promise',
        },

        className: {
            loading: 'loading',
            active: 'active',
        },

        selector: {
            tabs: '.ui.tab',
            ui: '.ui',
        },

    };
})(jQuery, window, document);

/*!
 * # Fomantic-UI 2.9.3 - Checkbox
 * https://github.com/fomantic/Fomantic-UI/
 *
 *
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */

(function ($, window, document) {
    'use strict';

    function isFunction(obj) {
        return typeof obj === 'function' && typeof obj.nodeType !== 'number';
    }

    window = window !== undefined && window.Math === Math
        ? window
        : globalThis;

    $.fn.checkbox = function (parameters) {
        var
            $allModules    = $(this),

            time           = Date.now(),
            performance    = [],

            query          = arguments[0],
            methodInvoked  = typeof query === 'string',
            queryArguments = [].slice.call(arguments, 1),
            returnedValue
        ;

        $allModules.each(function () {
            var
                settings        = $.extend(true, {}, $.fn.checkbox.settings, parameters),

                className       = settings.className,
                namespace       = settings.namespace,
                selector        = settings.selector,
                error           = settings.error,

                eventNamespace  = '.' + namespace,
                moduleNamespace = 'module-' + namespace,

                $module         = $(this),
                $label          = $(this).children(selector.label),
                $input          = $(this).children(selector.input),
                input           = $input[0],

                initialLoad     = false,
                shortcutPressed = false,
                instance        = $module.data(moduleNamespace),

                observer,
                element         = this,
                module
            ;

            module = {

                initialize: function () {
                    module.verbose('Initializing checkbox', settings);

                    module.create.label();
                    module.bind.events();

                    module.set.tabbable();
                    module.hide.input();

                    module.observeChanges();
                    module.instantiate();
                    module.setup();
                },

                instantiate: function () {
                    module.verbose('Storing instance of module', module);
                    instance = module;
                    $module
                        .data(moduleNamespace, module)
                    ;
                },

                destroy: function () {
                    module.verbose('Destroying module');
                    module.unbind.events();
                    module.show.input();
                    $module.removeData(moduleNamespace);
                },

                fix: {
                    reference: function () {
                        if ($module.is(selector.input)) {
                            module.debug('Behavior called on <input> adjusting invoked element');
                            $module = $module.closest(selector.checkbox);
                            module.refresh();
                        }
                    },
                },

                setup: function () {
                    module.set.initialLoad();
                    if (module.is.indeterminate()) {
                        module.debug('Initial value is indeterminate');
                        module.indeterminate();
                    } else if (module.is.checked()) {
                        module.debug('Initial value is checked');
                        module.check();
                    } else {
                        module.debug('Initial value is unchecked');
                        module.uncheck();
                    }
                    module.remove.initialLoad();
                },

                refresh: function () {
                    $label = $module.children(selector.label);
                    $input = $module.children(selector.input);
                    input = $input[0];
                },

                hide: {
                    input: function () {
                        module.verbose('Modifying <input> z-index to be unselectable');
                        $input.addClass(className.hidden);
                    },
                },
                show: {
                    input: function () {
                        module.verbose('Modifying <input> z-index to be selectable');
                        $input.removeClass(className.hidden);
                    },
                },

                observeChanges: function () {
                    if ('MutationObserver' in window) {
                        observer = new MutationObserver(function (mutations) {
                            module.debug('DOM tree modified, updating selector cache');
                            module.refresh();
                        });
                        observer.observe(element, {
                            childList: true,
                            subtree: true,
                        });
                        module.debug('Setting up mutation observer', observer);
                    }
                },

                attachEvents: function (selector, event) {
                    var
                        $element = $(selector)
                    ;
                    event = isFunction(module[event])
                        ? module[event]
                        : module.toggle;
                    if ($element.length > 0) {
                        module.debug('Attaching checkbox events to element', selector, event);
                        $element
                            .on('click' + eventNamespace, event)
                        ;
                    } else {
                        module.error(error.notFound);
                    }
                },

                preventDefaultOnInputTarget: function () {
                    if (event !== undefined && event !== null && $(event.target).is(selector.input)) {
                        module.verbose('Preventing default check action after manual check action');
                        event.preventDefault();
                    }
                },

                event: {
                    change: function (event) {
                        if (!module.should.ignoreCallbacks()) {
                            settings.onChange.call(input);
                        }
                    },
                    click: function (event) {
                        var
                            $target = $(event.target)
                        ;
                        if ($target.is(selector.input)) {
                            module.verbose('Using default check action on initialized checkbox');

                            return;
                        }
                        if ($target.is(selector.link)) {
                            module.debug('Clicking link inside checkbox, skipping toggle');

                            return;
                        }
                        module.toggle();
                        $input.trigger('focus');
                        event.preventDefault();
                    },
                    keydown: function (event) {
                        var
                            key     = event.which,
                            keyCode = {
                                enter: 13,
                                space: 32,
                                escape: 27,
                                left: 37,
                                up: 38,
                                right: 39,
                                down: 40,
                            }
                        ;

                        var
                            r = module.get.radios(),
                            rIndex = r.index($module),
                            rLen = r.length,
                            checkIndex = false
                        ;

                        if (key === keyCode.left || key === keyCode.up) {
                            checkIndex = (rIndex === 0 ? rLen : rIndex) - 1;
                        } else if (key === keyCode.right || key === keyCode.down) {
                            checkIndex = rIndex === rLen - 1 ? 0 : rIndex + 1;
                        }

                        if (!module.should.ignoreCallbacks() && checkIndex !== false) {
                            if (settings.beforeUnchecked.apply(input) === false) {
                                module.verbose('Option not allowed to be unchecked, cancelling key navigation');

                                return false;
                            }
                            if (settings.beforeChecked.apply($(r[checkIndex]).children(selector.input)[0]) === false) {
                                module.verbose('Next option should not allow check, cancelling key navigation');

                                return false;
                            }
                        }

                        shortcutPressed = false;
                        if (key === keyCode.escape) {
                            module.verbose('Escape key pressed blurring field');
                            $input.trigger('blur');
                            shortcutPressed = true;
                            event.stopPropagation();
                        } else if (!module.can.change()) {
                            shortcutPressed = true;
                        } else if (!event.ctrlKey) {
                            if (key === keyCode.space || (key === keyCode.enter && settings.enableEnterKey)) {
                                module.verbose('Enter/space key pressed, toggling checkbox');
                                module.toggle();
                                shortcutPressed = true;
                            } else if ($module.is('.toggle, .slider') && !module.is.radio()) {
                                if (key === keyCode.left && module.is.checked()) {
                                    module.uncheck();
                                    shortcutPressed = true;
                                } else if (key === keyCode.right && module.is.unchecked()) {
                                    module.check();
                                    shortcutPressed = true;
                                }
                            }
                        }
                    },
                    keyup: function (event) {
                        if (shortcutPressed) {
                            event.preventDefault();
                        }
                    },
                },

                check: function () {
                    if (!module.should.allowCheck()) {
                        return;
                    }
                    module.debug('Checking checkbox', $input);
                    module.set.checked();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onChecked.call(input);
                        module.trigger.change();
                    }
                    module.preventDefaultOnInputTarget();
                },

                uncheck: function () {
                    if (!module.should.allowUncheck()) {
                        return;
                    }
                    module.debug('Unchecking checkbox');
                    module.set.unchecked();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onUnchecked.call(input);
                        module.trigger.change();
                    }
                    module.preventDefaultOnInputTarget();
                },

                indeterminate: function () {
                    if (module.should.allowIndeterminate()) {
                        module.debug('Checkbox is already indeterminate');

                        return;
                    }
                    module.debug('Making checkbox indeterminate');
                    module.set.indeterminate();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onIndeterminate.call(input);
                        module.trigger.change();
                    }
                },

                determinate: function () {
                    if (module.should.allowDeterminate()) {
                        module.debug('Checkbox is already determinate');

                        return;
                    }
                    module.debug('Making checkbox determinate');
                    module.set.determinate();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onDeterminate.call(input);
                        module.trigger.change();
                    }
                },

                enable: function () {
                    if (module.is.enabled()) {
                        module.debug('Checkbox is already enabled');

                        return;
                    }
                    module.debug('Enabling checkbox');
                    module.set.enabled();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onEnable.call(input);
                        // preserve legacy callbacks
                        settings.onEnabled.call(input);
                    }
                },

                disable: function () {
                    if (module.is.disabled()) {
                        module.debug('Checkbox is already disabled');

                        return;
                    }
                    module.debug('Disabling checkbox');
                    module.set.disabled();
                    if (!module.should.ignoreCallbacks()) {
                        settings.onDisable.call(input);
                        // preserve legacy callbacks
                        settings.onDisabled.call(input);
                    }
                },

                get: {
                    radios: function () {
                        var
                            name = module.get.name()
                        ;

                        return $('input[name="' + name + '"]').closest(selector.checkbox);
                    },
                    otherRadios: function () {
                        return module.get.radios().not($module);
                    },
                    name: function () {
                        return $input.attr('name');
                    },
                },

                is: {
                    initialLoad: function () {
                        return initialLoad;
                    },
                    radio: function () {
                        return $input.hasClass(className.radio) || $input.attr('type') === 'radio';
                    },
                    indeterminate: function () {
                        return $input.prop('indeterminate') !== undefined && $input.prop('indeterminate');
                    },
                    checked: function () {
                        return $input.prop('checked') !== undefined && $input.prop('checked');
                    },
                    disabled: function () {
                        return $input.prop('disabled') !== undefined && $input.prop('disabled');
                    },
                    enabled: function () {
                        return !module.is.disabled();
                    },
                    determinate: function () {
                        return !module.is.indeterminate();
                    },
                    unchecked: function () {
                        return !module.is.checked();
                    },
                },

                should: {
                    allowCheck: function () {
                        if (module.is.determinate() && module.is.checked() && !module.is.initialLoad()) {
                            module.debug('Should not allow check, checkbox is already checked');

                            return false;
                        }
                        if (!module.should.ignoreCallbacks() && settings.beforeChecked.apply(input) === false) {
                            module.debug('Should not allow check, beforeChecked cancelled');

                            return false;
                        }

                        return true;
                    },
                    allowUncheck: function () {
                        if (module.is.determinate() && module.is.unchecked() && !module.is.initialLoad()) {
                            module.debug('Should not allow uncheck, checkbox is already unchecked');

                            return false;
                        }
                        if (!module.should.ignoreCallbacks() && settings.beforeUnchecked.apply(input) === false) {
                            module.debug('Should not allow uncheck, beforeUnchecked cancelled');

                            return false;
                        }

                        return true;
                    },
                    allowIndeterminate: function () {
                        if (module.is.indeterminate() && !module.is.initialLoad()) {
                            module.debug('Should not allow indeterminate, checkbox is already indeterminate');

                            return false;
                        }
                        if (!module.should.ignoreCallbacks() && settings.beforeIndeterminate.apply(input) === false) {
                            module.debug('Should not allow indeterminate, beforeIndeterminate cancelled');

                            return false;
                        }

                        return true;
                    },
                    allowDeterminate: function () {
                        if (module.is.determinate() && !module.is.initialLoad()) {
                            module.debug('Should not allow determinate, checkbox is already determinate');

                            return false;
                        }
                        if (!module.should.ignoreCallbacks() && settings.beforeDeterminate.apply(input) === false) {
                            module.debug('Should not allow determinate, beforeDeterminate cancelled');

                            return false;
                        }

                        return true;
                    },
                    ignoreCallbacks: function () {
                        return initialLoad && !settings.fireOnInit;
                    },
                },

                can: {
                    change: function () {
                        return !($module.hasClass(className.disabled) || $module.hasClass(className.readOnly) || $input.prop('disabled') || $input.prop('readonly'));
                    },
                    uncheck: function () {
                        return typeof settings.uncheckable === 'boolean'
                            ? settings.uncheckable
                            : !module.is.radio();
                    },
                },

                set: {
                    initialLoad: function () {
                        initialLoad = true;
                    },
                    checked: function () {
                        module.verbose('Setting class to checked');
                        $module
                            .removeClass(className.indeterminate)
                            .addClass(className.checked)
                        ;
                        if (module.is.radio()) {
                            module.uncheckOthers();
                        }
                        if (!module.is.indeterminate() && module.is.checked()) {
                            module.debug('Input is already checked, skipping input property change');

                            return;
                        }
                        module.verbose('Setting state to checked', input);
                        $input
                            .prop('indeterminate', false)
                            .prop('checked', true)
                        ;
                    },
                    unchecked: function () {
                        module.verbose('Removing checked class');
                        $module
                            .removeClass(className.indeterminate)
                            .removeClass(className.checked)
                        ;
                        if (!module.is.indeterminate() && module.is.unchecked()) {
                            module.debug('Input is already unchecked');

                            return;
                        }
                        module.debug('Setting state to unchecked');
                        $input
                            .prop('indeterminate', false)
                            .prop('checked', false)
                        ;
                    },
                    indeterminate: function () {
                        module.verbose('Setting class to indeterminate');
                        $module
                            .addClass(className.indeterminate)
                        ;
                        if (module.is.indeterminate()) {
                            module.debug('Input is already indeterminate, skipping input property change');

                            return;
                        }
                        module.debug('Setting state to indeterminate');
                        $input
                            .prop('indeterminate', true)
                        ;
                    },
                    determinate: function () {
                        module.verbose('Removing indeterminate class');
                        $module
                            .removeClass(className.indeterminate)
                        ;
                        if (module.is.determinate()) {
                            module.debug('Input is already determinate, skipping input property change');

                            return;
                        }
                        module.debug('Setting state to determinate');
                        $input
                            .prop('indeterminate', false)
                        ;
                    },
                    disabled: function () {
                        module.verbose('Setting class to disabled');
                        $module
                            .addClass(className.disabled)
                        ;
                        if (module.is.disabled()) {
                            module.debug('Input is already disabled, skipping input property change');

                            return;
                        }
                        module.debug('Setting state to disabled');
                        $input
                            .prop('disabled', 'disabled')
                        ;
                    },
                    enabled: function () {
                        module.verbose('Removing disabled class');
                        $module.removeClass(className.disabled);
                        if (module.is.enabled()) {
                            module.debug('Input is already enabled, skipping input property change');

                            return;
                        }
                        module.debug('Setting state to enabled');
                        $input
                            .prop('disabled', false)
                        ;
                    },
                    tabbable: function () {
                        module.verbose('Adding tabindex to checkbox');
                        if ($input.attr('tabindex') === undefined) {
                            $input.attr('tabindex', 0);
                        }
                    },
                },

                remove: {
                    initialLoad: function () {
                        initialLoad = false;
                    },
                },

                trigger: {
                    change: function () {
                        var
                            inputElement = $input[0]
                        ;
                        if (inputElement) {
                            var events = document.createEvent('HTMLEvents');
                            module.verbose('Triggering native change event');
                            events.initEvent('change', true, false);
                            inputElement.dispatchEvent(events);
                        }
                    },
                },

                create: {
                    label: function () {
                        if ($input.prevAll(selector.label).length > 0) {
                            $input.prev(selector.label).detach().insertAfter($input);
                            module.debug('Moving existing label', $label);
                        } else if (!module.has.label()) {
                            $label = $('<label>').insertAfter($input);
                            module.debug('Creating label', $label);
                        }
                    },
                },

                has: {
                    label: function () {
                        return $label.length > 0;
                    },
                },

                bind: {
                    events: function () {
                        module.verbose('Attaching checkbox events');
                        $module
                            .on('click' + eventNamespace, module.event.click)
                            .on('change' + eventNamespace, module.event.change)
                            .on('keydown' + eventNamespace, selector.input, module.event.keydown)
                            .on('keyup' + eventNamespace, selector.input, module.event.keyup)
                        ;
                    },
                },

                unbind: {
                    events: function () {
                        module.debug('Removing events');
                        $module
                            .off(eventNamespace)
                        ;
                    },
                },

                uncheckOthers: function () {
                    var
                        $radios = module.get.otherRadios()
                    ;
                    module.debug('Unchecking other radios', $radios);
                    $radios.removeClass(className.checked);
                },

                toggle: function () {
                    if (!module.can.change()) {
                        if (!module.is.radio()) {
                            module.debug('Checkbox is read-only or disabled, ignoring toggle');
                        }

                        return;
                    }
                    if (module.is.indeterminate() || module.is.unchecked()) {
                        module.debug('Currently unchecked');
                        module.check();
                    } else if (module.is.checked() && module.can.uncheck()) {
                        module.debug('Currently checked');
                        module.uncheck();
                    }
                },
                setting: function (name, value) {
                    module.debug('Changing setting', name, value);
                    if ($.isPlainObject(name)) {
                        $.extend(true, settings, name);
                    } else if (value !== undefined) {
                        if ($.isPlainObject(settings[name])) {
                            $.extend(true, settings[name], value);
                        } else {
                            settings[name] = value;
                        }
                    } else {
                        return settings[name];
                    }
                },
                internal: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, module, name);
                    } else if (value !== undefined) {
                        module[name] = value;
                    } else {
                        return module[name];
                    }
                },
                debug: function () {
                    if (!settings.silent && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.debug = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.debug.apply(console, arguments);
                        }
                    }
                },
                verbose: function () {
                    if (!settings.silent && settings.verbose && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.verbose = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.verbose.apply(console, arguments);
                        }
                    }
                },
                error: function () {
                    if (!settings.silent) {
                        module.error = Function.prototype.bind.call(console.error, console, settings.name + ':');
                        module.error.apply(console, arguments);
                    }
                },
                performance: {
                    log: function (message) {
                        var
                            currentTime,
                            executionTime,
                            previousTime
                        ;
                        if (settings.performance) {
                            currentTime = Date.now();
                            previousTime = time || currentTime;
                            executionTime = currentTime - previousTime;
                            time = currentTime;
                            performance.push({
                                Name: message[0],
                                Arguments: [].slice.call(message, 1) || '',
                                Element: element,
                                'Execution Time': executionTime,
                            });
                        }
                        clearTimeout(module.performance.timer);
                        module.performance.timer = setTimeout(function () { module.performance.display(); }, 500);
                    },
                    display: function () {
                        var
                            title = settings.name + ':',
                            totalTime = 0
                        ;
                        time = false;
                        clearTimeout(module.performance.timer);
                        $.each(performance, function (index, data) {
                            totalTime += data['Execution Time'];
                        });
                        title += ' ' + totalTime + 'ms';
                        if (performance.length > 0) {
                            console.groupCollapsed(title);
                            if (console.table) {
                                console.table(performance);
                            } else {
                                $.each(performance, function (index, data) {
                                    console.log(data.Name + ': ' + data['Execution Time'] + 'ms');
                                });
                            }
                            console.groupEnd();
                        }
                        performance = [];
                    },
                },
                invoke: function (query, passedArguments, context) {
                    var
                        object = instance,
                        maxDepth,
                        found,
                        response
                    ;
                    passedArguments = passedArguments || queryArguments;
                    context = context || element;
                    if (typeof query === 'string' && object !== undefined) {
                        query = query.split(/[ .]/);
                        maxDepth = query.length - 1;
                        $.each(query, function (depth, value) {
                            var camelCaseValue = depth !== maxDepth
                                ? value + query[depth + 1].charAt(0).toUpperCase() + query[depth + 1].slice(1)
                                : query
                            ;
                            if ($.isPlainObject(object[camelCaseValue]) && (depth !== maxDepth)) {
                                object = object[camelCaseValue];
                            } else if (object[camelCaseValue] !== undefined) {
                                found = object[camelCaseValue];

                                return false;
                            } else if ($.isPlainObject(object[value]) && (depth !== maxDepth)) {
                                object = object[value];
                            } else if (object[value] !== undefined) {
                                found = object[value];

                                return false;
                            } else {
                                module.error(error.method, query);

                                return false;
                            }
                        });
                    }
                    if (isFunction(found)) {
                        response = found.apply(context, passedArguments);
                    } else if (found !== undefined) {
                        response = found;
                    }
                    if (Array.isArray(returnedValue)) {
                        returnedValue.push(response);
                    } else if (returnedValue !== undefined) {
                        returnedValue = [returnedValue, response];
                    } else if (response !== undefined) {
                        returnedValue = response;
                    }

                    return found;
                },
            };

            if (methodInvoked) {
                if (instance === undefined) {
                    module.initialize();
                }
                module.invoke(query);
            } else {
                if (instance !== undefined) {
                    instance.invoke('destroy');
                }
                module.initialize();
            }
        });

        return returnedValue !== undefined
            ? returnedValue
            : this;
    };

    $.fn.checkbox.settings = {

        name: 'Checkbox',
        namespace: 'checkbox',

        silent: false,
        debug: false,
        verbose: false,
        performance: true,

        // delegated event context
        uncheckable: 'auto',
        fireOnInit: false,
        enableEnterKey: true,

        onChange: function () {},

        beforeChecked: function () {},
        beforeUnchecked: function () {},
        beforeDeterminate: function () {},
        beforeIndeterminate: function () {},

        onChecked: function () {},
        onUnchecked: function () {},

        onDeterminate: function () {},
        onIndeterminate: function () {},

        onEnable: function () {},
        onDisable: function () {},

        // preserve misspelled callbacks (will be removed in 3.0)
        onEnabled: function () {},
        onDisabled: function () {},

        className: {
            checked: 'checked',
            indeterminate: 'indeterminate',
            disabled: 'disabled',
            hidden: 'hidden',
            radio: 'radio',
            readOnly: 'read-only',
        },

        error: {
            method: 'The method you called is not defined',
        },

        selector: {
            checkbox: '.ui.checkbox',
            label: 'label',
            input: 'input[type="checkbox"], input[type="radio"]',
            link: 'a[href]',
        },

    };
})(jQuery, window, document);

/*!
 * # Fomantic-UI 2.9.3 - Popup
 * https://github.com/fomantic/Fomantic-UI/
 *
 *
 * Released under the MIT license
 * https://opensource.org/licenses/MIT
 *
 */

(function ($, window, document) {
    'use strict';

    function isFunction(obj) {
        return typeof obj === 'function' && typeof obj.nodeType !== 'number';
    }

    window = window !== undefined && window.Math === Math
        ? window
        : globalThis;

    $.fn.popup = function (parameters) {
        var
            $allModules    = $(this),
            $document      = $(document),
            $window        = $(window),
            $body          = $('body'),

            clickEvent      = 'ontouchstart' in document.documentElement
                ? 'touchstart'
                : 'click',

            time           = Date.now(),
            performance    = [],

            query          = arguments[0],
            methodInvoked  = typeof query === 'string',
            queryArguments = [].slice.call(arguments, 1),
            contextCheck   = function (context, win) {
                var $context;
                if ([window, document].indexOf(context) >= 0) {
                    $context = $(context);
                } else {
                    $context = $(win.document).find(context);
                    if ($context.length === 0) {
                        $context = win.frameElement ? contextCheck(context, win.parent) : $body;
                    }
                }

                return $context;
            },

            returnedValue
        ;
        $allModules.each(function () {
            var
                settings        = $.isPlainObject(parameters)
                    ? $.extend(true, {}, $.fn.popup.settings, parameters)
                    : $.extend({}, $.fn.popup.settings),

                selector           = settings.selector,
                className          = settings.className,
                error              = settings.error,
                metadata           = settings.metadata,
                namespace          = settings.namespace,

                eventNamespace     = '.' + settings.namespace,
                moduleNamespace    = 'module-' + namespace,

                $module            = $(this),
                $context           = contextCheck(settings.context, window),
                $scrollContext     = contextCheck(settings.scrollContext, window),
                $boundary          = contextCheck(settings.boundary, window),
                $target            = settings.target ? contextCheck(settings.target, window) : $module,

                $popup,
                $offsetParent,

                searchDepth        = 0,
                triedPositions     = false,
                openedWithTouch    = false,

                element            = this,
                instance           = $module.data(moduleNamespace),

                documentObserver,
                elementNamespace,
                id,
                module
            ;

            module = {

                // binds events
                initialize: function () {
                    module.debug('Initializing', $module);
                    module.createID();
                    module.bind.events();
                    if (!module.exists() && settings.preserve) {
                        module.create();
                    }
                    if (settings.observeChanges) {
                        module.observeChanges();
                    }
                    module.instantiate();
                },

                instantiate: function () {
                    module.verbose('Storing instance', module);
                    instance = module;
                    $module
                        .data(moduleNamespace, instance)
                    ;
                },

                observeChanges: function () {
                    if ('MutationObserver' in window) {
                        documentObserver = new MutationObserver(module.event.documentChanged);
                        documentObserver.observe(document, {
                            childList: true,
                            subtree: true,
                        });
                        module.debug('Setting up mutation observer', documentObserver);
                    }
                },

                refresh: function () {
                    if (settings.popup) {
                        $popup = $document.find(settings.popup).eq(0);
                    } else {
                        if (settings.inline) {
                            $popup = $target.nextAll(selector.popup).eq(0);
                            settings.popup = $popup;
                        }
                    }
                    if (settings.popup) {
                        module.set.invisible();
                        $offsetParent = module.get.offsetParent();
                        module.remove.invisible();
                        if (settings.movePopup && module.has.popup() && module.get.offsetParent($popup)[0] !== $offsetParent[0]) {
                            module.debug('Moving popup to the same offset parent as target');
                            $popup
                                .detach()
                                .appendTo($offsetParent)
                            ;
                        }
                    } else {
                        $offsetParent = settings.inline
                            ? module.get.offsetParent($target)
                            : (module.has.popup()
                                ? module.get.offsetParent($popup)
                                : $body);
                    }
                    if ($offsetParent.is('html') && $offsetParent[0] !== $body[0]) {
                        module.debug('Setting page as offset parent');
                        $offsetParent = $body;
                    }
                    if (module.get.variation()) {
                        module.set.variation();
                    }
                },

                reposition: function () {
                    module.refresh();
                    module.set.position();
                },

                destroy: function () {
                    module.debug('Destroying previous module');
                    if (documentObserver) {
                        documentObserver.disconnect();
                    }
                    // remove element only if was created dynamically
                    if ($popup && !settings.preserve) {
                        module.removePopup();
                    }
                    // clear all timeouts
                    clearTimeout(module.hideTimer);
                    clearTimeout(module.showTimer);
                    // remove events
                    module.unbind.close();
                    module.unbind.events();
                    $module
                        .removeData(moduleNamespace)
                    ;
                },

                event: {
                    start: function (event) {
                        var
                            delay = $.isPlainObject(settings.delay)
                                ? settings.delay.show
                                : settings.delay
                        ;
                        clearTimeout(module.hideTimer);
                        if (!openedWithTouch || (openedWithTouch && settings.addTouchEvents)) {
                            module.showTimer = setTimeout(function () { module.show(); }, delay);
                        }
                    },
                    end: function () {
                        var
                            delay = $.isPlainObject(settings.delay)
                                ? settings.delay.hide
                                : settings.delay
                        ;
                        clearTimeout(module.showTimer);
                        module.hideTimer = setTimeout(function () { module.hide(); }, delay);
                    },
                    touchstart: function (event) {
                        openedWithTouch = true;
                        if (settings.addTouchEvents) {
                            module.show();
                        }
                    },
                    resize: function () {
                        if (module.is.visible()) {
                            module.set.position();
                        }
                    },
                    documentChanged: function (mutations) {
                        [].forEach.call(mutations, function (mutation) {
                            if (mutation.removedNodes) {
                                [].forEach.call(mutation.removedNodes, function (node) {
                                    if (node === element || $(node).find(element).length > 0) {
                                        module.debug('Element removed from DOM, tearing down events');
                                        module.destroy();
                                    }
                                });
                            }
                        });
                    },
                    hideGracefully: function (event) {
                        var
                            $target = $(event.target),
                            isInDOM = $.contains(document.documentElement, event.target),
                            inPopup = $target.closest(selector.popup).length > 0
                        ;
                        // don't close on clicks inside popup
                        if (event && !inPopup && isInDOM) {
                            module.debug('Click occurred outside popup hiding popup');
                            module.hide();
                        } else {
                            module.debug('Click was inside popup, keeping popup open');
                        }
                    },
                },

                // generates popup html from metadata
                create: function () {
                    var
                        targetSibling = $target.next(selector.popup),
                        contentFallback = !settings.popup && targetSibling.length === 0 ? $module.attr('title') : false,
                        html      = module.get.html(),
                        title     = module.get.title(),
                        content   = module.get.content(contentFallback)
                    ;

                    if (html || content || title) {
                        module.debug('Creating pop-up html');
                        if (!html) {
                            html = settings.templates.popup({
                                title: title,
                                content: content,
                            });
                        }
                        $popup = $('<div/>')
                            .addClass(className.popup)
                            .data(metadata.activator, $module)
                            .html(html)
                        ;
                        if (settings.inline) {
                            module.verbose('Inserting popup element inline', $popup);
                            $popup
                                .insertAfter($module)
                            ;
                        } else {
                            module.verbose('Appending popup element to body', $popup);
                            $popup
                                .appendTo($context)
                            ;
                        }
                        module.refresh();
                        module.set.variation();

                        if (settings.hoverable) {
                            module.bind.popup();
                        }
                        settings.onCreate.call($popup, element);
                    } else if (settings.popup) {
                        $document.find(settings.popup).data(metadata.activator, $module);
                        module.verbose('Used popup specified in settings');
                        module.refresh();
                        if (settings.hoverable) {
                            module.bind.popup();
                        }
                    } else if (targetSibling.length > 0) {
                        module.verbose('Pre-existing popup found');
                        settings.inline = true;
                        settings.popup = targetSibling.data(metadata.activator, $module);
                        module.refresh();
                        if (settings.hoverable) {
                            module.bind.popup();
                        }
                    } else {
                        module.debug('No content specified skipping display', element);
                    }
                },

                createID: function () {
                    id = (Math.random().toString(16) + '000000000').slice(2, 10);
                    elementNamespace = '.' + id;
                    module.verbose('Creating unique id for element', id);
                },

                // determines popup state
                toggle: function () {
                    module.debug('Toggling pop-up');
                    if (module.is.hidden()) {
                        module.debug('Popup is hidden, showing pop-up');
                        module.unbind.close();
                        module.show();
                    } else {
                        module.debug('Popup is visible, hiding pop-up');
                        module.hide();
                    }
                },

                show: function (callback) {
                    callback = callback || function () {};
                    module.debug('Showing pop-up', settings.transition);
                    if (module.is.hidden() && !(module.is.active() && module.is.dropdown())) {
                        if (!module.exists()) {
                            module.create();
                        }
                        if (settings.onShow.call($popup, element) === false) {
                            module.debug('onShow callback returned false, cancelling popup animation');

                            return;
                        }
                        if (!settings.preserve && !settings.popup) {
                            module.refresh();
                        }
                        if ($popup && module.set.position()) {
                            module.save.conditions();
                            if (settings.exclusive) {
                                module.hideAll();
                            }
                            module.animate.show(callback);
                        }
                    }
                },

                hide: function (callback) {
                    callback = callback || function () {};
                    if (module.is.visible() || module.is.animating()) {
                        if (settings.onHide.call($popup, element) === false) {
                            module.debug('onHide callback returned false, cancelling popup animation');

                            return;
                        }
                        module.remove.visible();
                        module.unbind.close();
                        module.restore.conditions();
                        module.animate.hide(callback);
                    }
                },

                hideAll: function () {
                    $document.find(selector.popup)
                        .filter('.' + className.popupVisible)
                        .each(function () {
                            $(this)
                                .data(metadata.activator)
                                .popup('hide')
                            ;
                        })
                    ;
                },
                exists: function () {
                    if (!$popup) {
                        return false;
                    }
                    if (settings.inline || settings.popup) {
                        return module.has.popup();
                    }

                    return $popup.closest($context).length > 0;
                },

                removePopup: function () {
                    if (module.has.popup() && !settings.popup) {
                        module.debug('Removing popup', $popup);
                        $popup.remove();
                        $popup = undefined;
                        settings.onRemove.call($popup, element);
                    }
                },

                save: {
                    conditions: function () {
                        module.cache = {
                            title: $module.attr('title'),
                        };
                        if (module.cache.title) {
                            $module.removeAttr('title');
                        }
                        module.verbose('Saving original attributes', module.cache.title);
                    },
                },
                restore: {
                    conditions: function () {
                        if (module.cache && module.cache.title) {
                            $module.attr('title', module.cache.title);
                            module.verbose('Restoring original attributes', module.cache.title);
                        }

                        return true;
                    },
                },
                supports: {
                    svg: function () {
                        return typeof SVGGraphicsElement !== 'undefined';
                    },
                },
                animate: {
                    show: function (callback) {
                        callback = isFunction(callback) ? callback : function () {};
                        if (settings.transition && module.can.useElement('transition')) {
                            module.set.visible();
                            $popup
                                .transition({
                                    animation: (settings.transition.showMethod || settings.transition) + ' in',
                                    queue: false,
                                    debug: settings.debug,
                                    verbose: settings.verbose,
                                    silent: settings.silent,
                                    duration: settings.transition.showDuration || settings.duration,
                                    onComplete: function () {
                                        module.bind.close();
                                        callback.call($popup, element);
                                        settings.onVisible.call($popup, element);
                                    },
                                })
                            ;
                        }
                    },
                    hide: function (callback) {
                        callback = isFunction(callback) ? callback : function () {};
                        module.debug('Hiding pop-up');
                        if (settings.transition && $.fn.transition !== undefined) {
                            $popup
                                .transition({
                                    animation: (settings.transition.hideMethod || settings.transition) + ' out',
                                    queue: false,
                                    duration: settings.transition.hideDuration || settings.duration,
                                    debug: settings.debug,
                                    verbose: settings.verbose,
                                    silent: settings.silent,
                                    onComplete: function () {
                                        module.reset();
                                        callback.call($popup, element);
                                        settings.onHidden.call($popup, element);
                                    },
                                })
                            ;
                        } else {
                            module.error(error.noTransition);
                        }
                    },
                },

                change: {
                    content: function (html) {
                        $popup.html(html);
                    },
                },

                get: {
                    html: function () {
                        $module.removeData(metadata.html);

                        return $module.data(metadata.html) || settings.html;
                    },
                    title: function () {
                        $module.removeData(metadata.title);

                        return $module.data(metadata.title) || settings.title;
                    },
                    content: function (fallback) {
                        $module.removeData(metadata.content);

                        return $module.data(metadata.content) || settings.content || fallback;
                    },
                    variation: function () {
                        $module.removeData(metadata.variation);

                        return $module.data(metadata.variation) || settings.variation;
                    },
                    popup: function () {
                        return $popup;
                    },
                    popupOffset: function () {
                        return $popup.offset();
                    },
                    calculations: function () {
                        var
                            $popupOffsetParent = module.get.offsetParent($popup),
                            targetElement      = $target[0],
                            isWindowEl         = $boundary[0] === window,
                            targetOffset       = $target.offset(),
                            parentOffset       = settings.inline || (settings.popup && settings.movePopup)
                                ? $target.offsetParent().offset()
                                : { top: 0, left: 0 },
                            screenPosition = isWindowEl
                                ? { top: 0, left: 0 }
                                : $boundary.offset(),
                            calculations   = {},
                            scroll = isWindowEl
                                ? { top: $window.scrollTop(), left: $window.scrollLeft() }
                                : { top: 0, left: 0 },
                            screen
                        ;
                        calculations = {
                            // element which is launching popup
                            target: {
                                element: $target[0],
                                width: $target.outerWidth(),
                                height: $target.outerHeight(),
                                top: targetOffset.top - parentOffset.top,
                                left: targetOffset.left - parentOffset.left,
                                margin: {},
                            },
                            // popup itself
                            popup: {
                                width: $popup.outerWidth(),
                                height: $popup.outerHeight(),
                            },
                            // offset container (or 3d context)
                            parent: {
                                width: $offsetParent.outerWidth(),
                                height: $offsetParent.outerHeight(),
                            },
                            // screen boundaries
                            screen: {
                                top: screenPosition.top,
                                left: screenPosition.left,
                                scroll: {
                                    top: scroll.top,
                                    left: scroll.left,
                                },
                                width: $boundary.width(),
                                height: $boundary.height(),
                            },
                        };

                        // if popup offset context is not same as target, then adjust calculations
                        if ($popupOffsetParent[0] !== $offsetParent[0]) {
                            var
                                popupOffset        = $popupOffsetParent.offset()
                            ;
                            calculations.target.top -= popupOffset.top;
                            calculations.target.left -= popupOffset.left;
                            calculations.parent.width = $popupOffsetParent.outerWidth();
                            calculations.parent.height = $popupOffsetParent.outerHeight();
                        }

                        // add in container calcs if fluid
                        if (settings.setFluidWidth && module.is.fluid()) {
                            calculations.container = {
                                width: $popup.parent().outerWidth(),
                            };
                            calculations.popup.width = calculations.container.width;
                        }

                        // add in margins if inline
                        calculations.target.margin.top = settings.inline
                            ? parseInt(window.getComputedStyle(targetElement).getPropertyValue('margin-top'), 10)
                            : 0;
                        calculations.target.margin.left = settings.inline
                            ? (module.is.rtl()
                                ? parseInt(window.getComputedStyle(targetElement).getPropertyValue('margin-right'), 10)
                                : parseInt(window.getComputedStyle(targetElement).getPropertyValue('margin-left'), 10))
                            : 0;
                        // calculate screen boundaries
                        screen = calculations.screen;
                        calculations.boundary = {
                            top: screen.top + screen.scroll.top,
                            bottom: screen.top + screen.scroll.top + screen.height,
                            left: screen.left + screen.scroll.left,
                            right: screen.left + screen.scroll.left + screen.width,
                        };

                        return calculations;
                    },
                    id: function () {
                        return id;
                    },
                    startEvent: function () {
                        if (settings.on === 'hover') {
                            return 'mouseenter';
                        }
                        if (settings.on === 'focus') {
                            return 'focus';
                        }

                        return false;
                    },
                    scrollEvent: function () {
                        return 'scroll';
                    },
                    endEvent: function () {
                        if (settings.on === 'hover') {
                            return 'mouseleave';
                        }
                        if (settings.on === 'focus') {
                            return 'blur';
                        }

                        return false;
                    },
                    distanceFromBoundary: function (offset, calculations) {
                        var
                            distanceFromBoundary = {},
                            popup,
                            boundary
                        ;
                        calculations = calculations || module.get.calculations();

                        // shorthand
                        popup = calculations.popup;
                        boundary = calculations.boundary;

                        if (offset) {
                            distanceFromBoundary = {
                                top: offset.top - boundary.top,
                                left: offset.left - boundary.left,
                                right: boundary.right - (offset.left + popup.width),
                                bottom: boundary.bottom - (offset.top + popup.height),
                            };
                            module.verbose('Distance from boundaries determined', offset, distanceFromBoundary);
                        }

                        return distanceFromBoundary;
                    },
                    offsetParent: function ($element) {
                        var
                            element = $element !== undefined
                                ? $element[0]
                                : $target[0],
                            parentNode = element.parentNode,
                            $node    = $(parentNode)
                        ;
                        if (parentNode) {
                            var
                                is2D     = $node.css('transform') === 'none',
                                isStatic = $node.css('position') === 'static',
                                isBody   = $node.is('body')
                            ;
                            while (parentNode && !isBody && isStatic && is2D) {
                                parentNode = parentNode.parentNode;
                                $node = $(parentNode);
                                is2D = $node.css('transform') === 'none';
                                isStatic = $node.css('position') === 'static';
                                isBody = $node.is('body');
                            }
                        }

                        return $node && $node.length > 0
                            ? $node
                            : $();
                    },
                    positions: function () {
                        return {
                            'top left': false,
                            'top center': false,
                            'top right': false,
                            'bottom left': false,
                            'bottom center': false,
                            'bottom right': false,
                            'left center': false,
                            'right center': false,
                        };
                    },
                    nextPosition: function (position) {
                        var
                            positions          = position.split(' '),
                            verticalPosition   = positions[0],
                            horizontalPosition = positions[1],
                            opposite = {
                                top: 'bottom',
                                bottom: 'top',
                                left: 'right',
                                right: 'left',
                            },
                            adjacent = {
                                left: 'center',
                                center: 'right',
                                right: 'left',
                            },
                            backup = {
                                'top left': 'top center',
                                'top center': 'top right',
                                'top right': 'right center',
                                'right center': 'bottom right',
                                'bottom right': 'bottom center',
                                'bottom center': 'bottom left',
                                'bottom left': 'left center',
                                'left center': 'top left',
                            },
                            adjacentsAvailable = verticalPosition === 'top' || verticalPosition === 'bottom',
                            oppositeTried = false,
                            adjacentTried = false,
                            nextPosition  = false
                        ;
                        if (!triedPositions) {
                            module.verbose('All available positions available');
                            triedPositions = module.get.positions();
                        }

                        module.debug('Recording last position tried', position);
                        triedPositions[position] = true;

                        if (settings.prefer === 'opposite') {
                            nextPosition = [opposite[verticalPosition], horizontalPosition];
                            nextPosition = nextPosition.join(' ');
                            oppositeTried = triedPositions[nextPosition] === true;
                            module.debug('Trying opposite strategy', nextPosition);
                        }
                        if ((settings.prefer === 'adjacent') && adjacentsAvailable) {
                            nextPosition = [verticalPosition, adjacent[horizontalPosition]];
                            nextPosition = nextPosition.join(' ');
                            adjacentTried = triedPositions[nextPosition] === true;
                            module.debug('Trying adjacent strategy', nextPosition);
                        }
                        if (adjacentTried || oppositeTried) {
                            module.debug('Using backup position', nextPosition);
                            nextPosition = backup[position];
                        }

                        return nextPosition;
                    },
                },

                set: {
                    position: function (position, calculations) {
                        // exit conditions
                        if ($target.length === 0 || $popup.length === 0) {
                            module.error(error.notFound);

                            return;
                        }
                        var
                            offset,
                            distanceAway,
                            target,
                            popup,
                            parent,
                            positioning,
                            popupOffset,
                            distanceFromBoundary
                        ;

                        calculations = calculations || module.get.calculations();
                        position = position || $module.data(metadata.position) || settings.position;

                        offset = $module.data(metadata.offset) || settings.offset;
                        distanceAway = settings.distanceAway;

                        // shorthand
                        target = calculations.target;
                        popup = calculations.popup;
                        parent = calculations.parent;

                        if (module.should.centerArrow(calculations)) {
                            module.verbose('Adjusting offset to center arrow on small target element');
                            if (position === 'top left' || position === 'bottom left') {
                                offset += target.width / 2;
                                offset -= settings.arrowPixelsFromEdge;
                            }
                            if (position === 'top right' || position === 'bottom right') {
                                offset -= target.width / 2;
                                offset += settings.arrowPixelsFromEdge;
                            }
                        }

                        if (target.width === 0 && target.height === 0 && !module.is.svg(target.element)) {
                            module.debug('Popup target is hidden, no action taken');

                            return false;
                        }

                        if (settings.inline) {
                            module.debug('Adding margin to calculation', target.margin);
                            if (position === 'left center' || position === 'right center') {
                                offset += target.margin.top;
                                distanceAway += -target.margin.left;
                            } else if (position === 'top left' || position === 'top center' || position === 'top right') {
                                offset += target.margin.left;
                                distanceAway -= target.margin.top;
                            } else {
                                offset += target.margin.left;
                                distanceAway += target.margin.top;
                            }
                        }

                        module.debug('Determining popup position from calculations', position, calculations);

                        if (module.is.rtl()) {
                            position = position.replace(/left|right/g, function (match) {
                                return match === 'left'
                                    ? 'right'
                                    : 'left';
                            });
                            module.debug('RTL: Popup position updated', position);
                        }

                        // if last attempt use specified last resort position
                        if (searchDepth === settings.maxSearchDepth && typeof settings.lastResort === 'string') {
                            position = settings.lastResort;
                        }

                        switch (position) {
                            case 'top left': {
                                positioning = {
                                    top: 'auto',
                                    bottom: parent.height - target.top + distanceAway,
                                    left: target.left + offset,
                                    right: 'auto',
                                };

                                break;
                            }
                            case 'top center': {
                                positioning = {
                                    bottom: parent.height - target.top + distanceAway,
                                    left: target.left + (target.width / 2) - (popup.width / 2) + offset,
                                    top: 'auto',
                                    right: 'auto',
                                };

                                break;
                            }
                            case 'top right': {
                                positioning = {
                                    bottom: parent.height - target.top + distanceAway,
                                    right: parent.width - target.left - target.width - offset,
                                    top: 'auto',
                                    left: 'auto',
                                };

                                break;
                            }
                            case 'left center': {
                                positioning = {
                                    top: target.top + (target.height / 2) - (popup.height / 2) + offset,
                                    right: parent.width - target.left + distanceAway,
                                    left: 'auto',
                                    bottom: 'auto',
                                };

                                break;
                            }
                            case 'right center': {
                                positioning = {
                                    top: target.top + (target.height / 2) - (popup.height / 2) + offset,
                                    left: target.left + target.width + distanceAway,
                                    bottom: 'auto',
                                    right: 'auto',
                                };

                                break;
                            }
                            case 'bottom left': {
                                positioning = {
                                    top: target.top + target.height + distanceAway,
                                    left: target.left + offset,
                                    bottom: 'auto',
                                    right: 'auto',
                                };

                                break;
                            }
                            case 'bottom center': {
                                positioning = {
                                    top: target.top + target.height + distanceAway,
                                    left: target.left + (target.width / 2) - (popup.width / 2) + offset,
                                    bottom: 'auto',
                                    right: 'auto',
                                };

                                break;
                            }
                            case 'bottom right': {
                                positioning = {
                                    top: target.top + target.height + distanceAway,
                                    right: parent.width - target.left - target.width - offset,
                                    left: 'auto',
                                    bottom: 'auto',
                                };

                                break;
                            }
                        }
                        if (positioning === undefined) {
                            module.error(error.invalidPosition, position);
                        }

                        module.debug('Calculated popup positioning values', positioning);

                        // tentatively place on stage
                        $popup
                            .css(positioning)
                            .removeClass(className.position)
                            .addClass(position)
                        ;
                        module.set.invisible();

                        popupOffset = module.get.popupOffset();

                        // see if any boundaries are surpassed with this tentative position
                        distanceFromBoundary = module.get.distanceFromBoundary(popupOffset, calculations);

                        if (!settings.forcePosition && module.is.offstage(distanceFromBoundary, position)) {
                            module.debug('Position is outside viewport', position);
                            if (searchDepth < settings.maxSearchDepth) {
                                searchDepth++;
                                position = module.get.nextPosition(position);
                                module.debug('Trying new position', position);

                                return $popup
                                    ? module.set.position(position, calculations)
                                    : false;
                            }
                            if (settings.lastResort) {
                                module.debug('No position found, showing with last position');
                            } else {
                                module.debug('Popup could not find a position to display', $popup);
                                module.error(error.cannotPlace, element);
                                module.remove.attempts();
                                module.remove.invisible();
                                module.reset();
                                settings.onUnplaceable.call($popup, element);

                                return false;
                            }
                        }
                        module.debug('Position is on stage', position);
                        module.remove.attempts();
                        module.remove.invisible();
                        if (settings.setFluidWidth && module.is.fluid()) {
                            module.set.fluidWidth(calculations);
                        }

                        return true;
                    },

                    fluidWidth: function (calculations) {
                        calculations = calculations || module.get.calculations();
                        module.debug('Automatically setting element width to parent width', calculations.parent.width);
                        $popup.css('width', calculations.container.width);
                    },

                    loading: function () {
                        $popup.addClass(className.loading);
                    },

                    invisible: function () {
                        $popup.addClass(className.invisible);
                    },

                    variation: function (variation) {
                        variation = variation || module.get.variation();
                        if (variation && module.has.popup()) {
                            module.verbose('Adding variation to popup', variation);
                            $popup.addClass(variation);
                        }
                    },

                    visible: function () {
                        $module.addClass(className.visible);
                    },
                },

                remove: {
                    loading: function () {
                        $popup.removeClass(className.loading);
                    },
                    invisible: function () {
                        $popup.removeClass(className.invisible);
                    },
                    variation: function (variation) {
                        variation = variation || module.get.variation();
                        if (variation) {
                            module.verbose('Removing variation', variation);
                            $popup.removeClass(variation);
                        }
                    },
                    visible: function () {
                        $module.removeClass(className.visible);
                    },
                    attempts: function () {
                        module.verbose('Resetting all searched positions');
                        searchDepth = 0;
                        triedPositions = false;
                    },
                },

                bind: {
                    events: function () {
                        module.debug('Binding popup events to module');
                        if (settings.on === 'click') {
                            $module
                                .on(clickEvent + eventNamespace, module.toggle)
                            ;
                        }
                        if (settings.on === 'hover') {
                            $module
                                .on('touchstart' + eventNamespace, module.event.touchstart)
                            ;
                        }
                        if (module.get.startEvent()) {
                            $module
                                .on(module.get.startEvent() + eventNamespace, module.event.start)
                                .on(module.get.endEvent() + eventNamespace, module.event.end)
                            ;
                        }
                        if (settings.target) {
                            module.debug('Target set to element', $target);
                        }
                        $window.on('resize' + elementNamespace, module.event.resize);
                    },
                    popup: function () {
                        module.verbose('Allowing hover events on popup to prevent closing');
                        if ($popup && module.has.popup()) {
                            $popup
                                .on('mouseenter' + eventNamespace, module.event.start)
                                .on('mouseleave' + eventNamespace, module.event.end)
                            ;
                        }
                    },
                    close: function () {
                        if (settings.hideOnScroll === true || (settings.hideOnScroll === 'auto' && settings.on !== 'click')) {
                            module.bind.closeOnScroll();
                        }
                        if (module.is.closable()) {
                            module.bind.clickaway();
                        } else if (settings.on === 'hover' && openedWithTouch) {
                            module.bind.touchClose();
                        }
                    },
                    closeOnScroll: function () {
                        module.verbose('Binding scroll close event to document');
                        $scrollContext
                            .one(module.get.scrollEvent() + elementNamespace, module.event.hideGracefully)
                        ;
                    },
                    touchClose: function () {
                        module.verbose('Binding popup touchclose event to document');
                        $document
                            .on('touchstart' + elementNamespace, function (event) {
                                module.verbose('Touched away from popup');
                                module.event.hideGracefully.call(element, event);
                            })
                        ;
                    },
                    clickaway: function () {
                        module.verbose('Binding popup close event to document');
                        $document
                            .on(clickEvent + elementNamespace, function (event) {
                                module.verbose('Clicked away from popup');
                                module.event.hideGracefully.call(element, event);
                            })
                        ;
                    },
                },

                unbind: {
                    events: function () {
                        $window
                            .off(elementNamespace)
                        ;
                        $module
                            .off(eventNamespace)
                        ;
                    },
                    close: function () {
                        $document
                            .off(elementNamespace)
                        ;
                        $scrollContext
                            .off(elementNamespace)
                        ;
                    },
                },

                can: {
                    useElement: function (element) {
                        if ($.fn[element] !== undefined) {
                            return true;
                        }
                        module.error(error.noElement.replace('{element}', element));

                        return false;
                    },
                },

                has: {
                    popup: function () {
                        return $popup && $popup.length > 0;
                    },
                },

                should: {
                    centerArrow: function (calculations) {
                        return !module.is.basic() && calculations.target.width <= (settings.arrowPixelsFromEdge * 2);
                    },
                },

                is: {
                    closable: function () {
                        if (settings.closable === 'auto') {
                            return settings.on !== 'hover';
                        }

                        return settings.closable;
                    },
                    offstage: function (distanceFromBoundary, position) {
                        var
                            offstage = []
                        ;
                        // return boundaries that have been surpassed
                        $.each(distanceFromBoundary, function (direction, distance) {
                            if (distance < -settings.jitter) {
                                module.debug('Position exceeds allowable distance from edge', direction, distance, position);
                                offstage.push(direction);
                            }
                        });

                        return offstage.length > 0;
                    },
                    svg: function (element) {
                        return module.supports.svg() && (element instanceof SVGGraphicsElement);
                    },
                    basic: function () {
                        return $module.hasClass(className.basic);
                    },
                    active: function () {
                        return $module.hasClass(className.active);
                    },
                    animating: function () {
                        return $popup !== undefined && $popup.hasClass(className.animating);
                    },
                    fluid: function () {
                        return $popup !== undefined && $popup.hasClass(className.fluid);
                    },
                    visible: function () {
                        return $popup !== undefined && $popup.hasClass(className.popupVisible);
                    },
                    dropdown: function () {
                        return $module.hasClass(className.dropdown);
                    },
                    hidden: function () {
                        return !module.is.visible();
                    },
                    rtl: function () {
                        return $module.attr('dir') === 'rtl' || $module.css('direction') === 'rtl' || $body.attr('dir') === 'rtl' || $body.css('direction') === 'rtl' || $context.attr('dir') === 'rtl' || $context.css('direction') === 'rtl';
                    },
                },

                reset: function () {
                    module.remove.visible();
                    if (settings.preserve) {
                        if ($.fn.transition !== undefined) {
                            $popup
                                .transition('remove transition')
                            ;
                        }
                    } else {
                        module.removePopup();
                    }
                },

                setting: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, settings, name);
                    } else if (value !== undefined) {
                        settings[name] = value;
                    } else {
                        return settings[name];
                    }
                },
                internal: function (name, value) {
                    if ($.isPlainObject(name)) {
                        $.extend(true, module, name);
                    } else if (value !== undefined) {
                        module[name] = value;
                    } else {
                        return module[name];
                    }
                },
                debug: function () {
                    if (!settings.silent && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.debug = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.debug.apply(console, arguments);
                        }
                    }
                },
                verbose: function () {
                    if (!settings.silent && settings.verbose && settings.debug) {
                        if (settings.performance) {
                            module.performance.log(arguments);
                        } else {
                            module.verbose = Function.prototype.bind.call(console.info, console, settings.name + ':');
                            module.verbose.apply(console, arguments);
                        }
                    }
                },
                error: function () {
                    if (!settings.silent) {
                        module.error = Function.prototype.bind.call(console.error, console, settings.name + ':');
                        module.error.apply(console, arguments);
                    }
                },
                performance: {
                    log: function (message) {
                        var
                            currentTime,
                            executionTime,
                            previousTime
                        ;
                        if (settings.performance) {
                            currentTime = Date.now();
                            previousTime = time || currentTime;
                            executionTime = currentTime - previousTime;
                            time = currentTime;
                            performance.push({
                                Name: message[0],
                                Arguments: [].slice.call(message, 1) || '',
                                Element: element,
                                'Execution Time': executionTime,
                            });
                        }
                        clearTimeout(module.performance.timer);
                        module.performance.timer = setTimeout(function () { module.performance.display(); }, 500);
                    },
                    display: function () {
                        var
                            title = settings.name + ':',
                            totalTime = 0
                        ;
                        time = false;
                        clearTimeout(module.performance.timer);
                        $.each(performance, function (index, data) {
                            totalTime += data['Execution Time'];
                        });
                        title += ' ' + totalTime + 'ms';
                        if (performance.length > 0) {
                            console.groupCollapsed(title);
                            if (console.table) {
                                console.table(performance);
                            } else {
                                $.each(performance, function (index, data) {
                                    console.log(data.Name + ': ' + data['Execution Time'] + 'ms');
                                });
                            }
                            console.groupEnd();
                        }
                        performance = [];
                    },
                },
                invoke: function (query, passedArguments, context) {
                    var
                        object = instance,
                        maxDepth,
                        found,
                        response
                    ;
                    passedArguments = passedArguments || queryArguments;
                    context = context || element;
                    if (typeof query === 'string' && object !== undefined) {
                        query = query.split(/[ .]/);
                        maxDepth = query.length - 1;
                        $.each(query, function (depth, value) {
                            var camelCaseValue = depth !== maxDepth
                                ? value + query[depth + 1].charAt(0).toUpperCase() + query[depth + 1].slice(1)
                                : query
                            ;
                            if ($.isPlainObject(object[camelCaseValue]) && (depth !== maxDepth)) {
                                object = object[camelCaseValue];
                            } else if (object[camelCaseValue] !== undefined) {
                                found = object[camelCaseValue];

                                return false;
                            } else if ($.isPlainObject(object[value]) && (depth !== maxDepth)) {
                                object = object[value];
                            } else if (object[value] !== undefined) {
                                found = object[value];

                                return false;
                            } else {
                                module.error(error.method, query);

                                return false;
                            }
                        });
                    }
                    if (isFunction(found)) {
                        response = found.apply(context, passedArguments);
                    } else if (found !== undefined) {
                        response = found;
                    }
                    if (Array.isArray(returnedValue)) {
                        returnedValue.push(response);
                    } else if (returnedValue !== undefined) {
                        returnedValue = [returnedValue, response];
                    } else if (response !== undefined) {
                        returnedValue = response;
                    }

                    return found;
                },
            };

            if (methodInvoked) {
                if (instance === undefined) {
                    module.initialize();
                }
                module.invoke(query);
            } else {
                if (instance !== undefined) {
                    instance.invoke('destroy');
                }
                module.initialize();
            }
        });

        return returnedValue !== undefined
            ? returnedValue
            : this;
    };

    $.fn.popup.settings = {

        name: 'Popup',

        // module settings
        silent: false,
        debug: false,
        verbose: false,
        performance: true,
        namespace: 'popup',

        // whether it should use dom mutation observers
        observeChanges: true,

        // callback only when element added to dom
        onCreate: function () {},

        // callback before element removed from dom
        onRemove: function () {},

        // callback before show animation
        onShow: function () {},

        // callback after show animation
        onVisible: function () {},

        // callback before hide animation
        onHide: function () {},

        // callback when popup cannot be positioned in visible screen
        onUnplaceable: function () {},

        // callback after hide animation
        onHidden: function () {},

        // when to show popup
        on: 'hover',

        // element to use to determine if popup is out of boundary
        boundary: window,

        // whether to add touchstart events when using hover
        addTouchEvents: true,

        // default position relative to element
        position: 'top left',

        // if given position should be used regardless if popup fits
        forcePosition: false,

        // name of variation to use
        variation: '',

        // whether popup should be moved to context
        movePopup: true,

        // element which popup should be relative to
        target: false,

        // jq selector or element that should be used as popup
        popup: false,

        // popup should remain inline next to activator
        inline: false,

        // popup should be removed from page on hide
        preserve: false,

        // popup should not close when being hovered on
        hoverable: false,

        // explicitly set content
        content: false,

        // explicitly set html
        html: false,

        // explicitly set title
        title: false,

        // whether automatically close on clickaway when on click
        closable: true,

        // automatically hide on scroll
        hideOnScroll: 'auto',

        // hide other popups on show
        exclusive: false,

        // context to attach popups
        context: 'body',

        // context for binding scroll events
        scrollContext: window,

        // position to prefer when calculating new position
        prefer: 'opposite',

        // specify position to appear even if it doesn't fit
        lastResort: false,

        // number of pixels from edge of popup to pointing arrow center (used from centering)
        arrowPixelsFromEdge: 20,

        // delay used to prevent accidental refiring of animations due to user error
        delay: {
            show: 50,
            hide: 70,
        },

        // whether fluid variation should assign width explicitly
        setFluidWidth: true,

        // transition settings
        duration: 200,
        transition: 'scale',

        // distance away from activating element in px
        distanceAway: 0,

        // number of pixels an element is allowed to be "offstage" for a position to be chosen (allows for rounding)
        jitter: 2,

        // offset on aligning axis from calculated position
        offset: 0,

        // maximum times to look for a position before failing (9 positions total)
        maxSearchDepth: 15,

        error: {
            invalidPosition: 'The position you specified is not a valid position',
            cannotPlace: 'Popup does not fit within the boundaries of the viewport',
            method: 'The method you called is not defined.',
            noElement: 'This module requires ui {element}',
            notFound: 'The target or popup you specified does not exist on the page',
        },

        metadata: {
            activator: 'activator',
            content: 'content',
            html: 'html',
            offset: 'offset',
            position: 'position',
            title: 'title',
            variation: 'variation',
        },

        className: {
            active: 'active',
            basic: 'basic',
            animating: 'animating',
            dropdown: 'dropdown',
            invisible: 'invisible',
            fluid: 'fluid',
            loading: 'loading',
            popup: 'ui popup',
            position: 'top left center bottom right',
            visible: 'visible',
            popupVisible: 'visible',
        },

        selector: {
            popup: '.ui.popup',
        },

        templates: {
            escape: function (string) {
                var
                    badChars     = /["'<>`]/g,
                    shouldEscape = /["&'<>`]/,
                    escape       = {
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#x27;',
                        '`': '&#x60;',
                    },
                    escapedChar  = function (chr) {
                        return escape[chr];
                    }
                ;
                if (shouldEscape.test(string)) {
                    string = string.replace(/&(?![\d#a-z]{1,12};)/gi, '&amp;');

                    return string.replace(badChars, escapedChar);
                }

                return string;
            },
            popup: function (text) {
                var
                    html   = '',
                    escape = $.fn.popup.settings.templates.escape
                ;
                if (text !== undefined) {
                    if (text.title) {
                        text.title = escape(text.title);
                        html += '<div class="header">' + text.title + '</div>';
                    }
                    if (text.content) {
                        text.content = escape(text.content);
                        html += '<div class="content">' + text.content + '</div>';
                    }
                }

                return html;
            },
        },

    };
})(jQuery, window, document);
