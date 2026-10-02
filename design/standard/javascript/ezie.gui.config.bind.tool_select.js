// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Image Editor extension for eZ Publish
// SOFTWARE RELEASE: 0.1 (preview only)
// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 7x & Exponential Foundation
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

ezie.gui.config.bind.tool_select_api = null;
ezie.gui.config.select_custom_opts = null;
ezie.gui.config.bind.select_method_has_changed = true;
ezie.gui.config.bind.select_last_was_wm = false;

ezie.gui.config.bind.tool_select = function(selection, options) {
    ezie.gui.eziegui.getInstance().opts().showOpts("#optsSelect");

    ezie.gui.config.bind.set_tool_select(selection, options, false);
}

ezie.gui.config.bind.set_tool_select = function(selection, options, wm) {
    ezie.gui.config.bind.select_method_has_changed = true;

    var settings = {
        onSelect: ezie.gui.selection().set,
        onChange: ezie.gui.selection().set
    };

    if (typeof options != "undefined") {
        ezie.gui.config.select_custom_opts = options;
        $.extend(settings, options);
    } else {
        ezie.gui.config.select_custom_opts = null;
    }

    if (ezie.gui.config.bind.tool_select_api != null) {
        if (((!wm && !ezie.gui.config.bind.select_last_was_wm) || (ezie.gui.config.bind.select_last_was_wm && wm)) && selection) {
            ezie.gui.config.bind.tool_select_api.setSelect([selection.x, selection.y,
                selection.x + selection.w, selection.y + selection.h])
            ezie.gui.selection().set(selection);
            ezie.gui.config.bind.select_last_was_wm = wm;
            return;
        }

    }

    if (!wm && $('#optsSelect input[type="radio"]:checked:first').val() == 'ratio') {
        var selectWidth = $('#optsSelect input[type="text"][name="selection_width"]:first').val();
        var selectHeight = $('#optsSelect input[type="text"][name="selection_height"]:first').val();

        settings.aspectRatio = selectWidth / selectHeight;
    }

    if (ezie.gui.config.bind.tool_select_api != null)
        ezie.gui.config.bind.tool_select_api.destroy();
    ezie.gui.config.bind.tool_select_api  = $.Jcrop("#main_image img:first", settings);

    if (selection != null) {
        ezie.gui.config.bind.tool_select_api.setSelect([selection.x, selection.y,
            selection.x + selection.w, selection.y + selection.h])
        ezie.gui.selection().set(selection);
    }

    ezie.gui.config.bind.select_last_was_wm = wm;
}

ezie.gui.config.bind.tool_select_remove = function (){
    if (ezie.gui.config.bind.tool_select_api  != null) {
        ezie.gui.config.bind.tool_select_api.destroy();
        ezie.gui.selection().deactivate();
        $.log('on unset custop opts-1');
        ezie.gui.config.select_custom_opts = null;
        ezie.gui.config.bind.tool_select_api = null;
    }
}

ezie.gui.config.bind.tool_select_method = function( e ) {

    var selectMethod = $('#optsSelect input[type="radio"]:checked:first').val();
    var selectWidth = parseInt($('#optsSelect input[type="text"][name="selection_width"]:first').val(), 10) || 0;
    var selectHeight = parseInt($('#optsSelect input[type="text"][name="selection_height"]:first').val(), 10) || 0;

    var settings = {
        onSelect: ezie.gui.selection().set,
        onChange: ezie.gui.selection().set
    };
    var dims = null;

    $.log('selection method: ' + selectMethod);
    $.log('   w: ' + selectWidth);
    $.log('   h: ' + selectHeight);

    if (ezie.gui.selection().isSelectionActive()) {
        dims = ezie.gui.selection().selection();
        settings.setSelect = [dims.x, dims.y, dims.x + dims.w, dims.y + dims.h];
    } else {
        settings.setSelect = [0, 0, selectWidth, selectHeight];
    }

    switch(selectMethod) {
        case 'ratio':
            if (selectHeight == 0) {
                selectHeight = 1;
            }

            settings.aspectRatio = selectWidth / selectHeight;

            settings.setSelect[2] = settings.setSelect[0] + (settings.setSelect[3] - settings.setSelect[1]) * settings.aspectRatio;

            break;
        case 'free':
            settings.aspectRatio = null;
            if( e!=null && e.type=='keyup' ){
            settings.setSelect[2] = selectWidth;
            settings.setSelect[3] = selectHeight;
            }
            $.log('on entre dans free');
            break;
    }

    if( ezie.gui.config.bind.tool_select_api != null ) {
        ezie.gui.config.bind.tool_select_api.setOptions( { aspectRatio: settings.aspectRatio } );
        ezie.gui.config.bind.tool_select_api.setSelect( settings.setSelect );
    }else{
        ezie.gui.config.bind.tool_select_api  = $.Jcrop("#main_image img:first", settings);
    }

    // true lets the click through, so the radio buttons and their labels
    // change their checked state (see setBindsForButtons in ezie.gui.opts_window.js)
    return true;
}
