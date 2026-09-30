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

ezie.gui.config.bind.menu_close_without_saving = function() {
    if (!ezie.gui.eziegui.isInstanciated()) { // TODO: also when the mainwindow is not open/visible
        return;
    }
    
    // TODO: call this when the user leaves the page (ie, fx et chrome)
    var question = $('#ezieMainContainer').attr('data-confirm-quit') || 'If you leave without saving, all your modifications will be definitely lost';
    // Only ask when there is something to lose
    if (ezie.history().version() > 0 && !confirm(question)) {
        return;
    }

    $.log('starting quit + no save');

    ezie.gui.config.zoom().reset();
    ezie.gui.eziegui.getInstance().desactivateUndo();
    ezie.gui.eziegui.getInstance().desactivateRedo();

    var closeEditor = function() {
        ezie.gui.config.bind.tool_select_remove();
        $('#main_image, #miniature').empty();
        ezie.gui.eziegui.getInstance().close();
        // update the frontend
        $('#ezieToolsWindow').find('.current').removeClass('current');
        $('#ezie_zoom').parent().addClass('current');
    };

    // The working copy is thrown away either way: when the server cannot
    // clean it up the editor still closes, nothing of the draft changes.
    ezie.ezconnect.connect.instance().action({
        'action': 'no_save_and_quit',
        'success': closeEditor,
        'error': function(xhr, textStatus, errorThrown) {
            $.log('no_save_and_quit failed: ' + textStatus + ' ' + errorThrown);
            closeEditor();
        }
    });

}
