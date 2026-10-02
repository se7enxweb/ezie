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
ezie.ezconnect.failure_default = function(XMLHttpRequest, textStatus, errorThrown) {
    $.log("[ezie.ezconnect] request failed: " + textStatus + " " + errorThrown);

    // The server answers errors as JSON {"error": "..."}; show that text.
    var detail = '';
    if (XMLHttpRequest) {
        var json = XMLHttpRequest.responseJSON;
        if (!json && XMLHttpRequest.responseText) {
            try {
                json = JSON.parse(XMLHttpRequest.responseText);
            } catch (e) {
                json = null;
            }
        }
        if (json && json.error) {
            detail = json.error;
        } else if (XMLHttpRequest.status) {
            detail = XMLHttpRequest.status + ' ' + (XMLHttpRequest.statusText || '');
        }
    }
    $("#ezieConnectionError .ezieErrorDetail").text(detail);

    // When an image is already loaded the editor stays open, so the work
    // done so far is not lost; only a failed prepare closes it.
    var imageLoaded = ezie.history().current() != null;

    $("#ezieConnectionError").show();
    $("#ezieConfirmMessage").off('click.ezie').on('click.ezie', function() {
        $("#ezieConnectionError").hide();
        if (imageLoaded) {
            return;
        }
        ezie.gui.eziegui.getInstance().close();
        // update the frontend
        $('#ezieToolsWindow').find('.current').removeClass('current');
        $('#ezie_zoom').parent().addClass('current');
    });
}