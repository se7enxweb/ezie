// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Image Editor extension for eZ Publish
// SOFTWARE RELEASE: 0.1 (preview only)
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##

ezie.gui.tools_window = function() {
    var jWindow = null;

    // returns the jQuery Dom element corresponding to
    // the window
    var getJWindow = function() {
        return jWindow;
    };

    var setBinds = function () {
        $.each(ezie.gui.config.bindings.tools_window, function() {
            var config = this;
            var item = $(config.selector);
            if (!item.length || typeof config.click !== 'function') {
                return;
            }
            // The window is shown (and bound) again every time the editor opens,
            // so every handler is namespaced and removed again in unsetBinds.
            item.on('click.ezieTool', function () {
                if (! ezie.gui.eziegui.getInstance().isFrozen()) {
                    config.click();
                }
                return false;

            });

            if (config.shortcut) {
                if (item.data('ezieTitle') === undefined) {
                    item.data('ezieTitle', item.attr("title"));
                }
                item.attr("title", item.data('ezieTitle') + " (" + config.shortcut + ")");
                $(document).on('keydown.ezie', null, config.shortcut, function (e) {
                    if (!ezie.gui.eziegui.getInstance().isFrozen()) {
                        config.click();
                        e.stopPropagation( );
                        e.preventDefault( );
                    }

                    return false;
                } );
            }

            if (item.attr('title') != undefined) {
                var p = item.closest('div.ezieBox').find('div.bottomBarContent p')
                var oldcontent = p.html()

                item.on('mouseenter.ezieTool', function (){
                    p.html($(this).attr('title'))
                }).on('mouseleave.ezieTool', function () {
                    p.html(oldcontent)
                });
            }

        })

    };

    var unsetBinds = function () {
        $(document).off('keydown.ezie');
        $.each(ezie.gui.config.bindings.tools_window, function() {
            $(this.selector).off('.ezieTool');
        });
    }

    var freeze = function() {
        $(".filters").add(".tools").freeze();
    }
    var unfreeze = function() {
        $(".filters").add(".tools").unfreeze();
    }

    var hide = function () {
        unsetBinds();
        jWindow.fadeOut('fast');
    };

    var show = function () {
        setBinds();
        jWindow = $("#ezieToolsWindow");
        jWindow.fadeIn('fast');
    }

    return {
        jWindow:getJWindow,
        show:show,
        hide:hide,
        freeze:freeze,
        unfreeze:unfreeze
    };
};
