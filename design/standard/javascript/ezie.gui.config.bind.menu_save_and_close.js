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
// Replaces the edit blocks of the image attribute (current image, upload,
// alternative text) with the ones the server rendered after saving. Works
// wherever the admin puts them: in a fieldset with a legend, in the
// translation column, or directly in the attribute block.
ezie.gui.config.bind.reload_saved = function(new_block) {
    var button = ezie.gui.eziegui.getInstance().button();
    var first = button.closest('div.block');
    var container = first.parent();
    var old_blocks = container.children('div.block');
    var nodes = $($.parseHTML(String(new_block).trim(), document, false));

    first.before(nodes);
    old_blocks.remove();

    container.find(".ezieEditButton").ezie();
}

ezie.gui.config.bind.menu_save_and_close = function() {
    if (!ezie.gui.eziegui.isInstanciated()) {
        return;
    }

    $.log('starting save + close');

    ezie.gui.config.zoom().reset();

    ezie.ezconnect.connect.instance().action({
        'action': 'save_and_quit',
        'success': function(response) {
            ezie.gui.config.bind.reload_saved(response);
            $('#main_image, #miniature').empty();
            ezie.gui.eziegui.getInstance().close();

            $('#ezieToolsWindow').find('.current').removeClass('current');
            $('#ezie_zoom').parent().addClass('current');

            ezie.gui.eziegui.getInstance().desactivateUndo();
            ezie.gui.eziegui.getInstance().desactivateRedo();
        },
        dataType: 'html'
    });
}
