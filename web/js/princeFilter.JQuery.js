/*
    princeFilter.JQuery.js
    ======================

    VERSION 2.0


    Created by Luis Valle (created date: 8/19/2014, updated v.2: 11/10/2014)
    www.evicore.com/princeFilter.aspx


    JQuery plugin for filtering table-data. Use the following operands to filter your table-data:
        • 'equals'
        • 'not-equals'
        • 'like'
        • 'starts with'
        • 'ends with'
        • 'less than'
        • 'greater than'
        • 'true' or 'false' (for cells that contain checkboxes).


    (this plugin can be used on multiple tables at the same time)




    HOW TO USE
    +++++++++++++++++++++++++++

        Instantiate princeFilter this way:

                $('#tbl_MytableID').princeFilter();




    Then just click the desired Column's "FUNNEL" button to filter that Column's data...

*/

$(document).ready(function () {
    checkForJSONstringify();
});
$(document).on('click', function (e) {
    $('#dv_prncFltr_FilterSettings').css('display', 'none');
});

(function ($) {
    $.fn.princeFilter = function (options) {
        var thisElement = this;

        if ($(thisElement)[0] != null) {
            if ($(thisElement)[0].rows.length > 0) {
                if ($(thisElement).find('th').length == 0) {
                    var tmpTDD = "";
                    for (var i = 0; i < $(thisElement)[0].rows[0].cells.length; i++) {
                        tmpTDD = tmpTDD + "<th>Column" + (i + 1) + "</th>";
                    }

                    $(thisElement).prepend("<tr>" + tmpTDD + "</tr>");
                }

                $(thisElement).find('th').each(function (indx) {
                    $(this).contents().wrap('<div></div>');

                    var thisTH = this;
                    var parmIDbtn = 'dv_prncFltr_' + $(thisElement).attr('id') + '_' + indx;

                    var btnHTML = "<div style=\"outline:1px solid #CFD2D6;font-size:0pt;min-width:33px;background-color:#EEF3E2 !important;height:13px;margin-top:1px;\">";
                    btnHTML = btnHTML + "<div id=\"dv_prncFltr_" + $(thisElement).attr("id") + "_" + indx + "\" fltrtxt=\"txt_prncFltr_" + $(thisElement).attr("id") + "_" + indx + "\" style=\"display:inline-block;float:left;font-size:0pt;margin-left:2px;margin-right:2px;padding:0px !important;padding:0px !important;outline:1px solid #4C9ED9;background-color:orange;cursor:pointer;\" onclick=\"prncFltr_ShowFilterBox(event,'" + parmIDbtn + "','" + $(thisElement).attr("id") + "','txt_prncFltr_" + $(thisElement).attr("id") + "_" + indx + "','" + indx + "')\" onmousedown=\"return false;\"><img src=\"data:image/png;base64," + prcn_img_filterFunnel64 + "\" alt=\"^\" /></div>";
                    btnHTML = btnHTML + "<input id=\"txt_prncFltr_" + $(thisElement).attr("id") + "_" + indx + "\" class=\"clss_prncFltr_" + $(thisElement).attr("id") + "\" type=\"text\" value=\"\" style=\"display:none;\"></input>";
                    btnHTML = btnHTML + "<img id=\"img_prncFltr_col_" + indx + "_" + $(thisElement).attr("id") + "\" src=\"data:image/png;base64," + prncImgFiltrSortASC + "\" sortOrder=\"\" sortTable=\"" + $(thisElement).attr("id") + "\" class=\"img_prncFltr_SortClss_" + $(thisElement).attr("id") + "\" colIndex=\"" + indx + "\" alt=\"^\" onmousedown=\"return false;\" style=\"cursor:pointer;float:right;\" />";
                    btnHTML = btnHTML + "</div>";

                    $(thisTH).append(btnHTML);

                    $('#dv_prncFltr_' + $(thisElement).attr('id') + '_' + indx).css('opacity', '0.4');
                    $('#img_prncFltr_col_' + indx + '_' + $(thisElement).attr('id')).css('opacity', '0.3');

                    $('#img_prncFltr_col_' + indx + '_' + $(thisElement).attr('id')).on('click', function () {
                        var xThisElmmmm = this;
                        var sortOrderx = $(xThisElmmmm).attr('sortOrder');
                        var xThisClass = $(xThisElmmmm).attr('class');
                        var xThisColIndex = $(xThisElmmmm).attr('colIndex');
                        var xThisTable = $(xThisElmmmm).attr('sortTable');
                        var xASCorDESC = false;

                        if (sortOrderx == "") {
                            sortOrderx = "asc";
                            $(xThisElmmmm).attr('sortOrder', 'asc');
                            xASCorDESC = false;
                        } else if (sortOrderx == "asc") {
                            sortOrderx = "desc";
                            $(xThisElmmmm).attr('sortOrder', 'desc');
                            xASCorDESC = true;
                        } else if (sortOrderx == "desc") {
                            sortOrderx = "asc";
                            $(xThisElmmmm).attr('sortOrder', 'asc');
                            xASCorDESC = false;
                        }

                        $('.' + xThisClass).attr('src', 'data:image/png;base64,' + prncImgFiltrSortASC);
                        $('.' + xThisClass).css('opacity', '0.3');

                        if (sortOrderx == "asc") {
                            $(xThisElmmmm).attr('src', 'data:image/png;base64,' + prncImgFiltrSortASC);
                        } else if (sortOrderx == "desc") {
                            $(xThisElmmmm).attr('src', 'data:image/png;base64,' + prncImgFiltrSortDESC);
                        }

                        $(xThisElmmmm).css('opacity', '1');

                        prncFltrsortTable(xThisTable, parseInt(xThisColIndex), xASCorDESC)
                    });
                });

                if ($('#dv_prncFltr_FilterSettings')[0] == null) {
                    var highest = 0;

                    $("*").each(function () {
                        var current = parseInt($(this).css("z-index"), 10);
                        if (current && highest < current) highest = current;
                    });

                    highest++;

                    var fltDV = "<div id=\"dv_prncFltr_FilterSettings\" style=\"position:absolute;z-index:" + highest + ";border:1px solid orange;background-color:white;display:none;\"><div style=\"padding:5px;\"><div style=\"padding:1px;background-color:#EBEBEB;border:1px solid orange;text-align:center;\"><span style=\"font-family:Arial;font-size:small;\">filter:</span><select id=\"cmb_prncFltr_FilterSettings\" style=\"background-color:#FFFFE1;\"></select><input id=\"txt_prncFltr_FilterSettings\" type=\"text\" style=\"width:100px;padding:1px;\"></input></div></div>";
                    fltDV = fltDV + "<div style=\"padding:3px;text-align:center;background-color:gray;border-top:1px solid #777571;\"><input id=\"btn_prncFltr_FilterSettings_Apply\" ctrlSrc=\"\" type=\"submit\" value=\"Apply Filter\" style=\"border:1px solid #4C9ED9;background-color:#CBE2F3;cursor:pointer;\" /><input id=\"btn_prncFltr_FilterSettings_Reset\" type=\"submit\" value=\"Clear Filter\" style=\"margin-left:5px;border:1px solid red;background-color:#FFBBBB;margin-left:15px;cursor:pointer;\" /></div></div>";

                    $(fltDV).insertAfter($(thisElement));

                    $('#dv_prncFltr_FilterSettings').on('click', function (eva) {
                        eva.preventDefault ? eva.preventDefault() : eva.returnValue = false;
                        eva.stopPropagation ? eva.stopPropagation() : eva.cancelBubble = true;
                        return false;
                    });

                    var xOpss = "<option value=\"(none)\">(none)</option>";
                    xOpss = xOpss + "<option value=\"equals\">equals</option>";
                    xOpss = xOpss + "<option value=\"not equals\">not equals</option>";
                    xOpss = xOpss + "<option value=\"like\">like</option>";
                    xOpss = xOpss + "<option value=\"starts with\">starts with</option>";
                    xOpss = xOpss + "<option value=\"ends with\">ends with</option>";
                    xOpss = xOpss + "<option value=\"less than\">less than</option>";
                    xOpss = xOpss + "<option value=\"greater than\">greater than</option>";
                    xOpss = xOpss + "<option value=\"true\" title=\"for checkboxes only - ''true'' means it's checked\" style=\"color:red;\">true</option>";
                    xOpss = xOpss + "<option value=\"false\" title=\"for checkboxes only - ''false'' means it's unchecked\" style=\"color:red;\">false</option>";
                    $('#cmb_prncFltr_FilterSettings').append(xOpss);

                    $('#cmb_prncFltr_FilterSettings').on('change', function (eva) {
                        var cOperand = $('#cmb_prncFltr_FilterSettings option:selected').val();

                        if (cOperand == 'true' || cOperand == 'false' || cOperand == '(none)') {
                            $('#txt_prncFltr_FilterSettings').css('display', 'none');
                            $('#btn_prncFltr_FilterSettings_Apply')[0].focus();
                        } else {
                            $('#txt_prncFltr_FilterSettings').css('display', '');
                            $('#txt_prncFltr_FilterSettings')[0].focus();
                        }
                    });

                    $('#btn_prncFltr_FilterSettings_Apply').on('click', function () {
                        var xApplF_val = $('#cmb_prncFltr_FilterSettings option:selected').val();

                        if (xApplF_val == '(none)') {
                            $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTxt')).val('');
                            $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceBtn')).css('opacity', '0.4');
                        } else {
                            var currAtIndx = $('#btn_prncFltr_FilterSettings_Apply').attr('sourceIndx');
                            var tempJSONobjctxxx = {
                                operand: $('#cmb_prncFltr_FilterSettings option:selected').val(),
                                value: $('#txt_prncFltr_FilterSettings').val(),
                                index: currAtIndx
                            };
                            $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTxt')).val(JSON.stringify(tempJSONobjctxxx));
                            $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceBtn')).css('opacity', '1');

                            var currClss = $('#btn_prncFltr_FilterSettings_Apply').attr('sourceClss');
                            var currTbl = $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTbl');
                            apply_prnc_filter(currTbl, currClss);
                        }

                        $('#dv_prncFltr_FilterSettings').css('display', 'none');
                    });

                    $('#btn_prncFltr_FilterSettings_Reset').on('click', function () {
                        $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTxt')).val('');
                        $('#' + $('#btn_prncFltr_FilterSettings_Apply').attr('sourceBtn')).css('opacity', '0.4');

                        var currClss = $('#btn_prncFltr_FilterSettings_Apply').attr('sourceClss');
                        var currTbl = $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTbl');

                        apply_prnc_filter(currTbl, currClss);

                        $('#dv_prncFltr_FilterSettings').css('display', 'none');
                    });

                    $('#txt_prncFltr_FilterSettings').live('keypress', function (evv) {
                        if (evv.keyCode == 13) {
                            evv.preventDefault ? evv.preventDefault() : evv.returnValue = false;
                            evv.stopPropagation ? evv.stopPropagation() : evv.cancelBubble = true;

                            $('#btn_prncFltr_FilterSettings_Apply').trigger('click');
                        }
                    });
                };
            }
        } else {
            //alert('This element doesnt exist!');
        };
    };
}(jQuery));

function prncFltr_ShowFilterBox(evv, mybtnPressed, thisElement, thisFiltOptionsss, atIndx) {
    evv.preventDefault ? evv.preventDefault() : evv.returnValue = false;
    evv.stopPropagation ? evv.stopPropagation() : evv.cancelBubble = true;

    var clkTHIS = mybtnPressed;
    var clkID = $('#' + clkTHIS).attr('id');
    var clkClss = 'clss_prncFltr_' + thisElement;
    var txtFiltrThisColumn = thisFiltOptionsss;

    $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTxt', txtFiltrThisColumn);
    $('#btn_prncFltr_FilterSettings_Apply').attr('sourceBtn', clkTHIS);
    $('#btn_prncFltr_FilterSettings_Apply').attr('sourceClss', clkClss);
    $('#btn_prncFltr_FilterSettings_Apply').attr('sourceIndx', atIndx);
    $('#btn_prncFltr_FilterSettings_Apply').attr('sourceTbl', thisElement);

    if ($.trim($('#' + txtFiltrThisColumn).val()) != '') {
        var jxObjct = $.parseJSON($.trim($('#' + txtFiltrThisColumn).val()));

        $('#cmb_prncFltr_FilterSettings').val(jxObjct.operand)
        $('#txt_prncFltr_FilterSettings').val(jxObjct.value)
    } else {
        $('#cmb_prncFltr_FilterSettings').val('(none)')
        $('#txt_prncFltr_FilterSettings').val('')
    }

    $('#cmb_prncFltr_FilterSettings').trigger('change');

    $('#btn_prncFltr_FilterSettings_Apply').attr('ctrlSrc', $('#' + thisElement).attr('id'));

    $('#dv_prncFltr_FilterSettings').css({ 'display': '', 'opacity': '0' });
    $('#dv_prncFltr_FilterSettings').position({ my: 'left top', at: 'center center', of: $('#' + clkTHIS) });

    $('#dv_prncFltr_FilterSettings').animate({ opacity: 1 }, 'fast', function () {

    });
};

function apply_prnc_filter(tblID, currClsss) {
    var allObjcts = [];
    $('.' + currClsss).each(function () {
        var currTxtClass = this;

        if ($(currTxtClass).val().match('{')) {
            allObjcts.push($.parseJSON($(currTxtClass).val()));
        }
    });

    $('#' + tblID + ' tr').css('display', '');

    for (var i = 1; i < $('#' + tblID)[0].rows.length; i++) {
        var showCell = true;

        for (var j = 0; j < allObjcts.length; j++) {
            var myAtIndx = parseInt(allObjcts[j].index);
            try {
                if (allObjcts[j].operand == 'equals') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase() == allObjcts[j].value.toLowerCase()) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'not equals') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase() != allObjcts[j].value.toLowerCase()) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'like') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().match(allObjcts[j].value.toLowerCase())) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'starts with') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().substring(0, allObjcts[j].value.length) == allObjcts[j].value.toLowerCase()) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'ends with') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().substring($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.length - allObjcts[j].value.length, $('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.length) == allObjcts[j].value.toLowerCase()) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'less than') {
                    if (parseFloat($.trim($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML)) < parseFloat($.trim(allObjcts[j].value))) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'greater than') {
                    if (parseFloat($.trim($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML)) > parseFloat($.trim(allObjcts[j].value))) {
                        //showCell = true;
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'true') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().match('type\="checkbox"')) {
                        if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().match('checked\="checked"')) {
                            //showCell = true;
                        } else {
                            showCell = false;
                        }
                    } else {
                        showCell = false;
                    }
                } else if (allObjcts[j].operand == 'false') {
                    if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().match('type\="checkbox"')) {
                        if ($('#' + tblID)[0].rows[i].cells[myAtIndx].innerHTML.toLowerCase().match('checked\="checked"')) {
                            showCell = false;
                        } else {
                            //showCell = true;
                        }
                    } else {
                        showCell = false;
                    }
                }
            } catch (e) {
                alert(e + ' -- ' + tblID + ' -- ' + myAtIndx);
            }
        }

        if (showCell == false) {
            $('#' + tblID)[0].rows[i].style.display = 'none';
        }
    }

    $('#dv_prncFltr_FilterSettings').css('display', 'none');

    while (allObjcts.length > 0) {
        allObjcts.pop();
    }
};

function prncFltrsortTable(table, col, ASCorDESC) {
    var reverse = ASCorDESC;

    try {
        var arrRws = new Array();
        for (var j = 1; j < $('#' + table)[0].rows.length; j++) {
            arrRws.push($('#' + table)[0].rows[j]);
        }

        var tb = $('#' + table)[0].tBodies[0],
            tr = arrRws,
            i;
        reverse = -((+reverse) || -1);
        tr = tr.sort(function (a, b) {
            if ('textContent' in a.cells[col]) {
                return reverse // `-1 *` if want opposite order
                * (a.cells[col].textContent.trim()
                    .localeCompare(b.cells[col].textContent.trim())
                );
            } else {
                return reverse // `-1 *` if want opposite order
                * (a.cells[col].innerText.trim()
                    .localeCompare(b.cells[col].innerText.trim())
                );
            }
        });
        for (i = 0; i < tr.length; ++i) tb.appendChild(tr[i]);
    } catch (e) {
        alert(e.message);
    }
};

var prncImgFiltrSortASC = "iVBORw0KGgoAAAANSUhEUgAAAA0AAAANCAYAAABy6+R8AAAABmJLR0QA/wD/AP+gvaeTAAAACXBI"
    + "WXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gsMDS4BrT7zvwAAAM5JREFUKM+F0j1KBEEQBeBv1z8Q"
    + "RGE1E0RBzLyArJmRglcwEj2WiRcQjAxM1MRcENZcNjcRXQNfYzPMjA+Kouvn1evq5heHmGDTH5aw"
    + "pgXD+BF2sFzlzvGGhWbTfPx3/KzK3WGKr66mNrzECgaFdNjTdIbPFF7XKvomTfGQhby2FZyGabeS"
    + "0okueTMc4x6PeMpi9v+Tt4GDquYDKyW5jcuwH+XciwFucJL3mMNVNreHcWL1dd6LjOdMusViCi4S"
    + "a9qkMIxz6a3G3xs1bB2rPxZDKMj4Z7xMAAAAAElFTkSuQmCC";

var prncImgFiltrSortDESC = "iVBORw0KGgoAAAANSUhEUgAAAA0AAAANCAYAAABy6+R8AAAABmJLR0QA/wD/AP+gvaeTAAAACXBI"
    + "WXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gsMDS4duT+v8AAAAM1JREFUKM990r1OQkEQBeAPuEiJ"
    + "idCRGAoM70C0s7IgdpY2Fha+A4/CO5DQQmNofAAbQk1IbIyVxmvhENcL955kMpmfM3tmd/nFFdbo"
    + "+UMLZwXroL1vuEWOi4T0GLmirbNo+A6fJ6QFHtBIcnVsM+V4DTtAvYJ0j89E1gdGUHXSDs84QS1I"
    + "72nDOKYNIq5VDCuVl+MGS6wwSYtV8rqxQ4ZNsdhP3uQ64mP4J3kWhK/w08gPcVe2QhcvQZjHbcET"
    + "3tAs038ZS58X/t7pseYfnoMpRl4xWBoAAAAASUVORK5CYII=";

var prcn_img_filterFunnel64 = "iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAABmJLR0QA/wD/AP+gvaeTAAAACXBI"
    + "WXMAAArwAAAK8AFCrDSYAAAAB3RJTUUH3gsKDiYTsXPpegAAAa1JREFUKM99j8+rEgEUhb9xnIQG"
    + "XwuNIQMZaBFCuwpfIPJW5cqNG8EWbyf1H7TqXwhaSNDCnRtDI94iaBMiOCAuJNIkBcmZRh0Gyx+D"
    + "48yzTYZEdOAuLvc753CFxWJxRdO0u7ZtP3Zd94Esy3FJkiTf9xemaX4ZDofvDcN4U6lUJoAnKory"
    + "QtO0V7PZ7L4syzdisdhVVVVDiUTiWiqVupXJZB5Go9FzXde/jUajQWA+n5+1Wi2WyyWO4+C6Lp7n"
    + "/RmA09PTk+l0+gi4GQgGg08LhcJOEAQsy8JxHERRRJIkNpsN1WqVZDL5tdvtjoBLAQiVy+Vn+Xz+"
    + "+WAwwDRNbNum3+/T6/WYTCbDZrN5AbwDGiLg1+v1djgcvpPNZm97nofruui6jmVZ3xuNRtN13Q/A"
    + "R2DDkaKlUumzYRj7Wq22LxaL23g8fgE8Aa4fIPHIsPF9/5OiKOe6rtPpdKx2u10F3gL6AQoeV+Ry"
    + "ubGqqux2O7bb7QIYAZNjJnC8jMfjn6vVqiuKIuv1+gfgAJf8T6FQ6F46nX4diUReAmd/hwr/8AR/"
    + "P3kCzIAFsD8cfwER28tNpVXUwwAAAABJRU5ErkJggg==";

function checkForJSONstringify() {
    /*
        I did not write this function!

        This function adds JSON.stringify support to browsers that don't support it (caugh! caugh! IE!)

        Credit goes out to 'chicagoworks' for this JSON code. https://gist.github.com/chicagoworks/754454
    */

    jQuery.extend({
        stringify: function stringify(obj) {
            if ("JSON" in window) {
                return JSON.stringify(obj);
            }

            var t = typeof (obj);
            if (t != "object" || obj === null) {
                // simple data type
                if (t == "string") obj = '"' + obj + '"';
                return String(obj);
            } else {
                // recurse array or object
                var n, v, json = [], arr = (obj && obj.constructor == Array);

                for (n in obj) {
                    v = obj[n];
                    t = typeof (v);
                    if (obj.hasOwnProperty(n)) {
                        if (t == "string") v = '"' + v + '"'; else if (t == "object" && v !== null) v = jQuery.stringify(v);
                        json.push((arr ? "" : '"' + n + '":') + String(v));
                    }
                }
                return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
            }
        }
    });

    /*
        I did not write this function!

        Credit goes out to 'Google' for this JSON code. https://code.google.com/p/jquery-ui/source/browse/trunk/ui/jquery.ui.position.js?r=3897
    */

    /*
     * jQuery UI Position @VERSION
     *
     * Copyright (c) 2010 AUTHORS.txt (http://jqueryui.com/about)
     * Dual licensed under the MIT (MIT-LICENSE.txt)
     * and GPL (GPL-LICENSE.txt) licenses.
     *
     * http://docs.jquery.com/UI/Position
     */
    (function ($) {

        $.ui = $.ui || {};

        var horizontalPositions = /left|center|right/,
                horizontalDefault = "center",
                verticalPositions = /top|center|bottom/,
                verticalDefault = "center",
                _position = $.fn.position,
                _offset = $.fn.offset;

        $.fn.position = function (options) {
            if (!options || !options.of) {
                return _position.apply(this, arguments);
            }

            // make a copy, we don't want to modify arguments
            options = $.extend({}, options);

            var target = $(options.of),
                    collision = (options.collision || "flip").split(" "),
                    offset = options.offset ? options.offset.split(" ") : [0, 0],
                    targetWidth,
                    targetHeight,
                    basePosition;

            if (options.of.nodeType === 9) {
                targetWidth = target.width();
                targetHeight = target.height();
                basePosition = { top: 0, left: 0 };
            } else if (options.of.scrollTo && options.of.document) {
                targetWidth = target.width();
                targetHeight = target.height();
                basePosition = { top: target.scrollTop(), left: target.scrollLeft() };
            } else if (options.of.preventDefault) {
                // force left top to allow flipping
                options.at = "left top";
                targetWidth = targetHeight = 0;
                basePosition = { top: options.of.pageY, left: options.of.pageX };
            } else {
                targetWidth = target.outerWidth();
                targetHeight = target.outerHeight();
                basePosition = target.offset();
            }

            // force my and at to have valid horizontal and veritcal positions
            // if a value is missing or invalid, it will be converted to center
            $.each(["my", "at"], function () {
                var pos = (options[this] || "").split(" ");
                if (pos.length === 1) {
                    pos = horizontalPositions.test(pos[0]) ?
                            pos.concat([verticalDefault]) :
                            verticalPositions.test(pos[0]) ?
                                    [horizontalDefault].concat(pos) :
                                    [horizontalDefault, verticalDefault];
                }
                pos[0] = horizontalPositions.test(pos[0]) ? pos[0] : horizontalDefault;
                pos[1] = verticalPositions.test(pos[1]) ? pos[1] : verticalDefault;
                options[this] = pos;
            });

            // normalize collision option
            if (collision.length === 1) {
                collision[1] = collision[0];
            }

            // normalize offset option
            offset[0] = parseInt(offset[0], 10) || 0;
            if (offset.length === 1) {
                offset[1] = offset[0];
            }
            offset[1] = parseInt(offset[1], 10) || 0;

            if (options.at[0] === "right") {
                basePosition.left += targetWidth;
            } else if (options.at[0] === horizontalDefault) {
                basePosition.left += targetWidth / 2;
            }

            if (options.at[1] === "bottom") {
                basePosition.top += targetHeight;
            } else if (options.at[1] === verticalDefault) {
                basePosition.top += targetHeight / 2;
            }

            basePosition.left += offset[0];
            basePosition.top += offset[1];

            return this.each(function () {
                var elem = $(this),
                        elemWidth = elem.outerWidth(),
                        elemHeight = elem.outerHeight(),
                        position = $.extend({}, basePosition);

                if (options.my[0] === "right") {
                    position.left -= elemWidth;
                } else if (options.my[0] === horizontalDefault) {
                    position.left -= elemWidth / 2;
                }

                if (options.my[1] === "bottom") {
                    position.top -= elemHeight;
                } else if (options.my[1] === verticalDefault) {
                    position.top -= elemHeight / 2;
                }

                $.each(["left", "top"], function (i, dir) {
                    if ($.ui.position[collision[i]]) {
                        $.ui.position[collision[i]][dir](position, {
                            targetWidth: targetWidth,
                            targetHeight: targetHeight,
                            elemWidth: elemWidth,
                            elemHeight: elemHeight,
                            offset: offset,
                            my: options.my,
                            at: options.at
                        });
                    }
                });

                if ($.fn.bgiframe) {
                    elem.bgiframe();
                }
                elem.offset($.extend(position, { using: options.using }));
            });
        };

        $.ui.position = {
            fit: {
                left: function (position, data) {
                    var win = $(window),
                            over = position.left + data.elemWidth - win.width() - win.scrollLeft();
                    position.left = over > 0 ? position.left - over : Math.max(0, position.left);
                },
                top: function (position, data) {
                    var win = $(window),
                            over = position.top + data.elemHeight - win.height() - win.scrollTop();
                    position.top = over > 0 ? position.top - over : Math.max(0, position.top);
                }
            },

            flip: {
                left: function (position, data) {
                    if (data.at[0] === "center") {
                        return;
                    }
                    var win = $(window),
                            over = position.left + data.elemWidth - win.width() - win.scrollLeft(),
                            myOffset = data.my[0] === "left" ?
                                    -data.elemWidth :
                                    data.my[0] === "right" ?
                                            data.elemWidth :
                                            0,
                            offset = -2 * data.offset[0];
                    position.left += position.left < 0 ?
                            myOffset + data.targetWidth + offset :
                            over > 0 ?
                                    myOffset - data.targetWidth + offset :
                                    0;
                },
                top: function (position, data) {
                    if (data.at[1] === "center") {
                        return;
                    }
                    var win = $(window),
                            over = position.top + data.elemHeight - win.height() - win.scrollTop(),
                            myOffset = data.my[1] === "top" ?
                                    -data.elemHeight :
                                    data.my[1] === "bottom" ?
                                            data.elemHeight :
                                            0,
                            atOffset = data.at[1] === "top" ?
                                    data.targetHeight :
                                    -data.targetHeight,
                            offset = -2 * data.offset[1];
                    position.top += position.top < 0 ?
                            myOffset + data.targetHeight + offset :
                            over > 0 ?
                                    myOffset + atOffset + offset :
                                    0;
                }
            }
        };

        // offset setter from jQuery 1.4
        if (!$.offset.setOffset) {
            $.offset.setOffset = function (elem, options) {
                // set position first, in-case top/left are set even on static elem
                if (/static/.test($.curCSS(elem, "position"))) {
                    elem.style.position = "relative";
                }
                var curElem = $(elem),
                        curOffset = curElem.offset(),
                        curTop = parseInt($.curCSS(elem, "top", true), 10) || 0,
                        curLeft = parseInt($.curCSS(elem, "left", true), 10) || 0,
                        props = {
                            top: (options.top - curOffset.top) + curTop,
                            left: (options.left - curOffset.left) + curLeft
                        };

                if ('using' in options) {
                    options.using.call(elem, props);
                } else {
                    curElem.css(props);
                }
            };

            $.fn.offset = function (options) {
                var elem = this[0];
                if (!elem || !elem.ownerDocument) { return null; }
                if (options) {
                    return this.each(function () {
                        $.offset.setOffset(this, options);
                    });
                }
                return _offset.call(this);
            };
        }

    }(jQuery));
}
