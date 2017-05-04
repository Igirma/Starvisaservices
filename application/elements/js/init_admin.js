(function( $ ) {
    $.fn.restrict = function(regExp, additionalRestriction) {
        function restrictCharacters(myfield, e, restrictionType) {
            var code = e.which;
            var character = String.fromCharCode(code);
            if (code==27) { this.blur(); return false; }
            if (!e.originalEvent.ctrlKey && code!=9 && code!=8 && code!=36 && code!=37 && code!=38 && (code!=39 || (code==39 && character=="'")) && code!=40) {
                if (character.match(restrictionType)) {
                    return additionalRestriction(myfield.value, character);
                } else {
                    return false;
                }
            }
        }
        this.keypress(function(e){
            if (!restrictCharacters(this, e, regExp)) {
                e.preventDefault();
            }
        });
    };
})( jQuery );

function number_format(number, decimals, dec_point, thousands_sep) {
    decimals = 2;
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? '' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/*! INIT_ADMIN */
$(document).ready(function(){

var eventHandlers = {
  init: function(){
    
    
    if($( 'textarea.editor' ).val() == ''){
      $( this ).val( '<br>' );
    }
    
    $('.price_add').click(function(){
      $('.option_row:last').after('<tr class="option_row">'+replaceAll(replaceAll($('.option_row_add:last').html(), '[0]', '['+(parseInt($('.product_prices_id:last').val())+1)+']'), 'value="1"', 'value="'+(parseInt($('.product_prices_id:last').val())+1)+'"')+'</tr>');
      $('.option_row:last').find('.nummer').html(parseInt($('.option_row').size()) - 1);
      
      $('.option_row:last').find('.price_delete').click(function(e){
        e.preventDefault();
        
        var href = $(this).attr('href');
        
        deleteItemInline($(this).parent().parent());
      });
    
      return false;
    });	
    
    $('.price_delete').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItemInline($(this).parent().parent());
    });
    
    function replaceAll(string, token, newtoken) {
      string = string.replace(token, newtoken).replace(token, newtoken).replace(token, newtoken).replace(token, newtoken).replace(token, newtoken).replace(token, newtoken);
      return(string);
    }
    
    
    $('.order_product_add').click(function(){
      $('.option_row:last').after('<tr class="option_row">'+$('.option_row:last').html()+'</tr>');
      $('.order_product_add_delete').click(function(e){
        e.preventDefault();
        
        var href = $(this).attr('href');
        
        deleteItemInline($(this).parent().parent());
      });
      return false;
    });
    
    $('.order_product_add_delete').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItemInline($(this).parent().parent());
    });
    
    $('.sendingcosts_add').click(function(){
      var new_number = (parseInt($('.option_row:last').find('.nummer').html())+1);
      $('.option_row:last').after('<tr class="option_row">'+$('.option_row_add:last').html().split("[0]").join("["+new_number+"]")+'</tr>');
      $('.option_row:last').find('.nummer').html(new_number);
      
      $('.sendingcosts_delete').click(function(e){
        e.preventDefault();
        
        var href = $(this).attr('href');
        
        deleteItemInline($(this).parent().parent());
      });
      return false;
    });
    
    $('.sendingcosts_delete').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItemInline($(this).parent().parent());
    });

    $('.option_add').click(function(){
      $('.option_row:last').after('<tr class="option_row">'+$('.option_row:last').html()+'</tr>');
      $('.option_row:last').find('.nummer').html(parseInt($('.option_row:last').find('.nummer').html())+1);
      
      $('.product_options_delete').click(function(e){
        e.preventDefault();
        
        var href = $(this).attr('href');
        
        deleteItemInline($(this).parent().parent());
      });
      return false;
    });
    
    $('.product_options_delete').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItemInline($(this).parent().parent());
    });
    $('.prods_categories').change(function(){
      jQuery.ajax({
        type: "POST",
        url: "../../application/modules/admin/submodules/products/views/products_ajax.php",
        data: "cat="+$('.prods_categories').val()+"",
        success: function(msg){
          window.location = window.location;
        }
      });
    });
    
    $('.prod_search_button').click(function(){
      jQuery.ajax({
        type: "POST",
        url: "../../application/modules/admin/submodules/products/views/products_ajax.php",
        data: "search="+$('.prod_search').val()+"",
        success: function(msg){
          window.location = window.location;
        }
      });
    });
    
    $('.order_search_button').click(function(){
      jQuery.ajax({
        type: "POST",
        url: "../../application/modules/admin/submodules/order/views/order_ajax.php",
        data: "search="+$('.order_search').val()+"",
        success: function(msg){
          window.location = window.location;
        }
      });
    });
    
    $('.filter_button').click(function(){
      jQuery.ajax({
        type: "POST",
        url: "../../application/modules/admin/submodules/order/views/order_ajax.php",
        data: "filter="+$('.prods_quarter').val()+"&nr="+$('.prods_nr').val()+"&year="+$('.prods_year').val(),
        success: function(msg){
          window.location = window.location;
        }
      });
    });
    
    /* ======= START DESTINATIONS ======= */
    
    $.fn.multipleSelect = function() {
        var current = $(this);
        current.find('.multiple-select:eq(0) option').live('click', function() {
            var selected = $(this);
            current.find('.multiple-select:eq(1)').append(selected);
            current.find('.multiple-select:eq(1) option').attr('selected', true);
            return false;
        });
        current.find('.multiple-select:eq(1) option').live('click', function() {
            var selected = $(this);
            current.find('.multiple-select:eq(0)').prepend(selected);
            setTimeout(function() {
                current.find('.multiple-select:eq(1) option').attr('selected', true);
            }, 500);
            return false;
        });
        current.find('.select_all').live('click', function() {
            var sibling = $(this).closest('td').siblings();
            var selected = $(this).closest('td').find('option');
            if (selected.length == 0) {
                return false;
            }
            sibling.find('select').empty().append(selected);
            setTimeout(function() {
                sibling.find('option').attr('selected', true);
            }, 500);
            return false;
        });
    };

    $('.entries-list-parent').multipleSelect();
    $('.countries-list-parent').multipleSelect();
    $('.types-list-parent').multipleSelect();
    $('.groups-list-parent').multipleSelect();
    $('.nationalities-list-parent').multipleSelect();
    $('.prices-list-parent').multipleSelect();
    $('.notes-list-parent').multipleSelect();
    $('.documents-list-parent').multipleSelect();
    $('.entry-list-parent').each(function() {
        var id = $(this).attr('id');
        $('.entry-list-parent-' + id).multipleSelect();
    });
    $('.services-list-parent').each(function() {
        var id = $(this).attr('id');
        $('.services-list-parent-' + id).multipleSelect();
    });
    
    $('.switch.label a').click(function() {
        var target = $(this).attr('data-target');
        $(this).parent().siblings().toggleClass('hidden');
        $(this).parent().toggleClass('hidden');
        $('.target.' + target).removeClass('hidden')
          .siblings().addClass('hidden').find('select option').removeAttr('selected');
        $('.target.' + target).siblings().find('[type="text"]').val('');
        return false;
    });
    
    if (document.location.href.search('documents_anchor') != -1) {
        var editedDoc = $('#documents-list');
        var offsetDoc = editedDoc.offset();
        $('html, body').animate({scrollTop: offsetDoc.top}, 1000);
        editedDoc.addClass('edited');
        setTimeout(function() {
            editedDoc.removeClass('edited');
        }, 4000);
    }

    if (document.location.href.search('notes_anchor') != -1) {
        var editedNotes = $('#notes-list');
        var offsetNotes = editedNotes.offset();
        $('html, body').animate({scrollTop: offsetNotes.top}, 1000);
        editedNotes.addClass('edited');
        setTimeout(function() {
            editedNotes.removeClass('edited');
        }, 4000);
    }

    if (/\anchor_([^\/]*)/.test(location.pathname)) {
        var editedType = $('#type-parent-' + RegExp.$1);
        var offsetType = editedType.offset();
        $('html, body').animate({scrollTop: offsetType.top}, 1000);
        editedType.addClass('edited');
        setTimeout(function() {
            editedType.removeClass('edited');
        }, 4000);
    }

    if (/\entry_([^\/]*)_([^\/]*)/.test(location.pathname)) {
        var typeID = RegExp.$1;
        var entryID = RegExp.$2;
        var editedType = $('#type-parent-' + typeID);
        var editedEntry = $('#services-list-parent-' + typeID + '-' + entryID);
        var offsetType = editedEntry.offset();
        $('html, body').animate({scrollTop: offsetType.top - 20}, 1000);
        editedEntry.addClass('edited');
        setTimeout(function() {
            editedEntry.removeClass('edited');
        }, 4000);
    }
    
    if (/\price_([^\/]*)_([^\/]*)/.test(location.pathname)) {
        var typeID = RegExp.$1;
        var entryID = RegExp.$2;
        var editedType = $('#type-parent-' + typeID);
        var editedEntry = $('#prices-list-parent-' + typeID + '-' + entryID);
        var offsetType = editedEntry.offset();
        $('html, body').animate({scrollTop: offsetType.top}, 1000);
        editedEntry.addClass('edited');
        setTimeout(function() {
            editedEntry.removeClass('edited');
        }, 4000);
    }
    
    function price_percent(num, amount) {
        if (typeof amount == 'undefined') {
            amount = 20;
        }
        return num * amount / 100;
    }
    
    function _prices() {
        $('.price').restrict(/[0-9\.]/g, function (currentValue, newChar) {
            return !(currentValue.indexOf('.') != -1 && newChar == "."); 
        });

        $('.price').on('keyup change', function() {
            var parent = $(this).closest('.price-row');
            if  (parent.hasClass('last')) {
                return false;
            }
            var pricePercent, priceTotal;
            var type = $(this).attr('data-type');
            var subtotal = parent.find('.subtotal');
            var total = parent.find('.total');
            var vat = parent.find('.vat');
			var vat_state = vat.parent().find('.togglevat');
            if (type == 'total') {
                if (total.val() == '' || total.val() == 0) {
                    subtotal.add(total).add(vat).val('');
                    return false;
                }
				if(vat_state.is(':checked')) {
					pricePercent = price_percent(total.val());
					priceTotal = parseFloat(total.val()) - parseFloat(pricePercent);
					vat.val(number_format(pricePercent));
					subtotal.val(number_format(priceTotal));
				}
				else {
					priceTotal = parseFloat(total.val());
					vat.val(number_format(0));
					subtotal.val(number_format(priceTotal));
				}
            }
            else if (type == 'subtotal') {
                if (subtotal.val() == '' || subtotal.val() == 0) {
                    subtotal.add(total).add(vat).val('');
                    return false;
                }
				if(!vat_state.is(':checked')) {
					priceTotal = parseFloat(subtotal.val());
				}
				else {
					pricePercent = price_percent(subtotal.val());
					vat.val(number_format(pricePercent));
					priceTotal = parseFloat(pricePercent) + parseFloat(subtotal.val());
				}
                total.val(number_format(priceTotal));
            }
            else if (type == 'vat') {
				if (vat_state.is(':checked')) {
					if (vat.val() == '' || vat.val() == 0) {
						subtotal.add(total).add(subtotal).val('');
						return false;
					}
					if (subtotal.val() == '' || subtotal.val() == 0) {
						total.val('');
						return false;
					}
					priceTotal = parseFloat(vat.val()) + parseFloat(subtotal.val());
					total.val(number_format(priceTotal));
				}
				else {
					vat.val('0.00');
				}
			}
            var grandTotal = 0;
            parent.closest('.prices.full-width').find('.price-row').each(function() {
                var price = $(this).find('.total').val();
                price = price == '' ? '0' : price;
                grandTotal += parseFloat(price);
            });
            parent.closest('.prices.full-width').find('.grand_total').val(number_format(grandTotal));
            return false;
        });

        $('.price').on('focusout', function() {
            var price = $(this).val();
            if (price != '') {
                $(this).val(number_format(price));
            }
            return false;
        });
    }


    function _ajax_add_new_type() {
        $('.add-new-type').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    if ('target' in data.data) {
                        $(this).closest('.section').find('.col.right .target').html(data.data.target);
                    }
                    if ('content' in data.data) {
                        $('.visa-types-content').html(data.data.content);
                    }
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_new_type');
    }

    function _ajax_add_type() {
        $('.type-item.add').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('target' in data.data) {
                        t.html(data.data.target);
                    }
                    if ('content' in data.data) {
                        $('.visa-types-content').html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (s.find('.type-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No source data found.').appendTo(s);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_type');
    }

    function _ajax_remove_type() {
        $('.type-item.remove').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('source' in data.data) {
                        s.html(data.data.source);
                    }
                    if ('content' in data.data) {
                        $('.visa-types-content').html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (t.find('.type-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No target data found.').appendTo(t);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_remove_type');
    }

    function _ajax_add_entry() {
        $('.entry-item.add').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    var e = $(this).closest('.subcolumn').find('.entries');
                    if ('target' in data.data) {
                        t.html(data.data.target);
                    }
                    if ('content' in data.data) {
                        e.html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (s.find('.entry-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No source data found.').appendTo(s);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_entry');
    }

    function _ajax_remove_entry() {
        $('.entry-item.remove').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    var e = $(this).closest('.subcolumn').find('.entries');
                    if ('source' in data.data) {
                        s.html(data.data.source);
                    }
                    if ('content' in data.data) {
                        e.html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (t.find('.entry-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No target data found.').appendTo(t);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_remove_entry');
    }

    function _ajax_add_service() {
        $('.service-item.add').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    var e = $(this).closest('.entry-section').find('.services');
                    if ('target' in data.data) {
                        t.html(data.data.target);
                    }
                    if ('content' in data.data) {
                        e.html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (s.find('.service-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No source data found.').appendTo(s);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_service');
    }

    function _ajax_remove_service() {
        $('.service-item.remove').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    var e = $(this).closest('.entry-section').find('.services');
                    if ('source' in data.data) {
                        s.html(data.data.source);
                    }
                    if ('content' in data.data) {
                        e.html(data.data.content);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (t.find('.service-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No target data found.').appendTo(t);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_remove_service');
    }
    
    function _ajax_update_prices() {
        $('.prices-form').bind({
            'phery:always': function() {
                $(this).find(':input:not(button)').removeAttr('disabled');
                $(this).find('button').html('Save prices');
            },
            'phery:beforeSend': function() {
                $(this).find(':input:not(button)').attr('disabled', true);
                $(this).find('button').html('Saving...');
            },
            'phery:done': function(event, data) {
                if (data.code == 200) {
                
                }
            }
        }).phery('ajax_update_prices');
    }

    function _ajax_add_document() {
        $('.document-item.add').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('target' in data.data) {
                        t.html(data.data.target);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (s.find('.document-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No source data found.').appendTo(s);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_document');
    }

    function _ajax_remove_document() {
        $('.document-item.remove').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('source' in data.data) {
                        s.html(data.data.source);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (t.find('.document-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No target data found.').appendTo(t);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_remove_document');
    }

    function _ajax_add_note() {
        $('.note-item.add').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('target' in data.data) {
                        t.html(data.data.target);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (s.find('.note-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No source data found.').appendTo(s);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_add_note');
    }

    function _ajax_remove_note() {
        $('.note-item.remove').bind({
            'phery:done': function(event, data) {
                if (data.code == 200) {
                    var s = $(this).closest('.section').find('.col.left .source');
                    var t = $(this).closest('.section').find('.col.right .target');
                    if ('source' in data.data) {
                        s.html(data.data.source);
                    }
                    $(this).fadeOut('fast', function() {
                        $(this).remove();
                        if (t.find('.note-item').length == 0) {
                            $('<div>').addClass('none')
                            .html('No target data found.').appendTo(t);
                        }
                    });
                    _ajaxInit();
                }
            }
        }).phery('ajax_remove_note');
    }

    function _ajaxInit(){
        _ajax_add_new_type();
        _ajax_add_type();
        _ajax_remove_type();
        _ajax_add_entry();
        _ajax_remove_entry();
        _ajax_add_service();
        _ajax_remove_service();
        _ajax_update_prices();
        _ajax_add_document();
        _ajax_remove_document();
        _ajax_add_note();
        _ajax_remove_note();
        _prices();
    }
    _ajaxInit();


    /* ======= END DESTINATIONS ======= */

    deleteItemInline = function(ref){
    var content = {
      header : {
        en : 'Delete item?',
        nl : 'Verwijder item?'
      },
      ok_button : {
        en : 'Ok',
        nl : 'Ok'
      },
      cancel_button : {
        en : 'Cancel',
        nl : 'Annuleer'
      }
    };
  
    // set default language if no language specific translation exists
    if(!(LANG_CODE in content.header)){
      LANG_CODE = 'en';
    }
  
    noty({
      layout: 'center',
      animateOpen: {opacity: 'show'},
      text: content.header[LANG_CODE], 
      timeout: 10000,
      buttons: [{
        type: 'btn btn-danger', text: content.cancel_button[LANG_CODE], click: function($noty){
          $noty.close();
        }
      },{
        type: 'btn btn-primary', text: content.ok_button[LANG_CODE], click: function($noty){
          $(ref).remove();
          $noty.close();
        }
      }]
    });
  };
    $( ".draggable" ).draggable();
  
    $('#overview table input[type="radio"], #overview table input[type="checkbox"]').click(function(){
      $(this).parents('form').submit();
    });
    
    $('input[type="submit"].save, input[type="submit"].save_and_back,  input[name="upload"]').click(function(){
      var content = {
        success : {
          en : 'Your changes are being saved, one moment please',
          nl : 'Uw wijzigingen worden opgeslagen, een ogenblik a.u.b',
          ro : 'Your changes are being saved, one moment please'
        }
      };
      
      if(!(LANG_CODE in content.success)){
        LANG_CODE = 'en';
      }
      
      noty({animateOpen: {opacity: 'show'}, animateClose: {opacity: 'hide'}, layout: 'center', text: content.success[LANG_CODE], timeout: false});
    });
  
    $('a.delete').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItem(href);
    });
  
    $('select.status').change(function(e){
      e.preventDefault();
      
      var href = $(this).val();
      
      var content = {
        header : {
          en : 'Change status?',
          nl : 'Status wijzigen?',
          ro : 'Change status?'
        },
        ok_button : {
          en : 'Ok',
          nl : 'Ok',
          ro : 'Ok'
        },
        cancel_button : {
          en : 'Cancel',
          nl : 'Annuleer',
          ro : 'Cancel'
        }
      };
    
      // set default language if no language specific translation exists
      if(!(LANG_CODE in content.header)){
        LANG_CODE = 'en';
      }
    
      noty({
        layout: 'center',
        animateOpen: {opacity: 'show'},
        text: content.header[LANG_CODE], 
        timeout: 10000,
        buttons: [{
          type: 'btn btn-danger', text: content.cancel_button[LANG_CODE], click: function($noty){
            $noty.close();
          }
        },{
          type: 'btn btn-primary', text: content.ok_button[LANG_CODE], click: function($noty){
            window.location = href;
            $noty.close();
          }
        }]
      });
    });
  
    $('a.delete_button_fake').click(function(e){
      e.preventDefault();
      
      var href = $(this).attr('href');
      
      deleteItem(href, 'submit', $(this).next());
    });
    
/* 		$('.add_edit_languages a').click(function(e){
      e.preventDefault();
      $(this).after('<input type="hidden" name="save_and_language" value="' + $(this).attr('id') + '">');
      $('input[name="save"]').click();
    }); */
    
    $('input[type="file"]').on('change', function(){
      var input = $(this);
      var form = $('form');
      //var ie_filename = $(this).val();
      var files = $(this)[0].files;
      
      // set default language if no language specific translation exists
      var content = {
        error : {
          en : 'You have selected too many documents, please select less to continue',
          nl : 'U heeft te veel documenten geselecteerd, selecteer minder documenten om door te gaan',
          ro : 'You have selected too many documents, please select less to continue'
        }
      };
      
      if(!(LANG_CODE in content.error)){
        LANG_CODE = 'en';
      }
      
      if(typeof files !== 'undefined'){
        if(files.length > 6){
          noty({layout: 'topRight', type: 'error', text: content.error[LANG_CODE], timeout: false});
        }
        else{
          $(input).parents('td').find('.file_replace_text').val(files.length + ' bestand(en)');
        }
      }
      else{
        $(input).parents('td').find('.file_replace_text').val('1 bestand');
      }
    }); 
    
    $(window).scroll(function(){	
      $('.menu_float')
        .stop()
        .animate({'marginTop': ($(window).scrollTop()) + 'px'}, 'slow');
    });
    
    $('.subheader .options a.plus').each(function(){
      $(this).closest('.column').find('.subcolumn').css('display', 'none');
    });
    
    $('.subheader .options a.toggle_dash').click(function(){
      if($(this).hasClass('min')){
        $(this).removeClass('min');
        $(this).addClass('plus');

        $(this).find('div').removeClass('sprite sprite-min');
        $(this).find('div').addClass('sprite sprite-plus');
        
        $(this).closest('.dash_item').find('.subcolumn').css('display', 'none');
      }else{
        $(this).removeClass('plus');
        $(this).addClass('min');
        
        $(this).find('div').removeClass('sprite sprite-plus');
        $(this).find('div').addClass('sprite sprite-min');
        
        $(this).closest('.dash_item').find('.subcolumn').css('display', 'block');
      }
    });
    
    $('.subheader .options a.toggle').click(function(){
      if($(this).hasClass('min')){
        $(this).removeClass('min');
        $(this).addClass('plus');

        $(this).find('div').removeClass('sprite sprite-min');
        $(this).find('div').addClass('sprite sprite-plus');
        
        $(this).closest('.column').find('.subcolumn').css('display', 'none');
      }else{
        $(this).removeClass('plus');
        $(this).addClass('min');
        
        $(this).find('div').removeClass('sprite sprite-plus');
        $(this).find('div').addClass('sprite sprite-min');
        
        $(this).closest('.column').find('.subcolumn').css('display', 'block');
      }
    });
    
    $('.lang_select').click(function(){
      $('.lang_container').css('display', 'block');
    });
    
    $('.lang').click(function(){
      var lang_c = $(this).attr('id');
      
      if(lang_c == 'nl'){
        lang = 'Nederlands';
      }
      
      if(lang_c == 'en'){
        lang = 'English';
      }
      
      if(lang_c == 'ro'){
        lang = 'Romana';
      }
      
      $('.lang_select').val(lang);
      $('.language:eq(0)').attr('class', 'language sprite_' + lang_c);
      
      $('.lang_container').css('display', 'none');
    });
    
    $('.lang_container').mouseleave(function(){
      $(this).css('display', 'none');
    });
    
    if($('input[name="external"]:checked').val() == 1){
      $('input[name="form[page_content][content_title]"]').parents('tr').css('display', 'none');
      $('input[name="form[page_content][meta_title]"]').parents('tr').css('display', 'none');
      $('input[name="form[page_content][meta_keyw]"]').parents('tr').css('display', 'none');
      $('input[name="form[page_content][slug]"]').parents('tr').css('display', 'none');
      
      $('input[name="form[mobile_content][content_title]"]').parents('tr').css('display', 'none');
      $('input[name="form[mobile_content][meta_title]"]').parents('tr').css('display', 'none');
      $('input[name="form[mobile_content][meta_keyw]"]').parents('tr').css('display', 'none');
      $('input[name="form[mobile_content][slug]"]').parents('tr').css('display', 'none');
      
      $('textarea').parents('tr').css('display', 'none');
      $('.column:eq(4)').css('display', 'none');
      $('input[name="form[page_content][ex_url]"]').parents('tr').css('display', 'table-row');
      $('input[name="form[mobile_content][ex_url]"]').parents('tr').css('display', 'table-row');
    }
    else
    {
      $('input[name="form[page_content][ex_url]"]').parents('tr').css('display', 'none');
      $('input[name="form[mobile_content][ex_url]"]').parents('tr').css('display', 'none');
    }
    
    $('input[name="external"]').click(function(){
      var val = $(this).val();
      
      if(val == 1){
        $('input[name="form[page_content][content_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[page_content][meta_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[page_content][meta_keyw]"]').parents('tr').css('display', 'none');
        $('input[name="form[page_content][slug]"]').parents('tr').css('display', 'none');
        $('input[name="form[page_content][ex_url]"]').parents('tr').css('display', 'table-row');
        
        $('input[name="form[mobile_content][content_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[mobile_content][meta_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[mobile_content][meta_keyw]"]').parents('tr').css('display', 'none');
        $('input[name="form[mobile_content][slug]"]').parents('tr').css('display', 'none');
        $('input[name="form[mobile_content][ex_url]"]').parents('tr').css('display', 'table-row');
        
        $('textarea').parents('tr').css('display', 'none');
        $('.column:eq(4)').css('display', 'none');
      }else{
        $('input[name="form[page_content][content_title]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[page_content][meta_title]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[page_content][meta_keyw]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[page_content][slug]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[page_content][ex_url]"]').parents('tr').css('display', 'none');
        
        $('input[name="form[mobile_content][content_title]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[mobile_content][meta_title]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[mobile_content][meta_keyw]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[mobile_content][slug]"]').parents('tr').css('display', 'table-row');
        $('input[name="form[mobile_content][ex_url]"]').parents('tr').css('display', 'none');
        
        $('textarea').parents('tr').css('display', 'table-row');
        $('.column:eq(4)').css('display', 'block');
      }
    });
    
    if($('input[name="external"]').hasClass('landings'))
    {
      if($('input[name="external"]:checked').val() == 1){
        $('input[name="form[landingspage_content][content_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingspage_content][meta_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingspage_content][meta_keyw]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingspage_content][slug]"]').parents('tr').css('display', 'none');
        
        $('input[name="form[landingsmobile_content][content_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingsmobile_content][meta_title]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingsmobile_content][meta_keyw]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingsmobile_content][slug]"]').parents('tr').css('display', 'none');
        
        $('textarea').parents('tr').css('display', 'none');
        $('.column:eq(4)').css('display', 'none');
        $('input[name="form[landingspage_content][ex_url]"]').parents('tr').css('display', 'table-row');
      }
      else
      {
        $('input[name="form[landingspage_content][ex_url]"]').parents('tr').css('display', 'none');
        $('input[name="form[landingsmobile_content][ex_url]"]').parents('tr').css('display', 'none');
      }
      
      $('input[name="external"]').click(function(){
        var val = $(this).val();
        
        if(val == 1){
          $('input[name="form[landingspage_content][content_title]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingspage_content][meta_title]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingspage_content][meta_keyw]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingspage_content][slug]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingspage_content][ex_url]"]').parents('tr').css('display', 'table-row');
          
          $('input[name="form[landingsmobile_content][content_title]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingsmobile_content][meta_title]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingsmobile_content][meta_keyw]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingsmobile_content][slug]"]').parents('tr').css('display', 'none');
          $('input[name="form[landingsmobile_content][ex_url]"]').parents('tr').css('display', 'table-row');
          
          $('textarea').parents('tr').css('display', 'none');
          $('.column:eq(4)').css('display', 'none');
        }else{
          $('input[name="form[landingspage_content][content_title]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingspage_content][meta_title]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingspage_content][meta_keyw]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingspage_content][slug]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingspage_content][ex_url]"]').parents('tr').css('display', 'none');
          
          $('input[name="form[landingsmobile_content][content_title]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingsmobile_content][meta_title]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingsmobile_content][meta_keyw]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingsmobile_content][slug]"]').parents('tr').css('display', 'table-row');
          $('input[name="form[landingsmobile_content][ex_url]"]').parents('tr').css('display', 'none');
          
          $('textarea').parents('tr').css('display', 'table-row');
          $('.column:eq(4)').css('display', 'block');
        }
      });
    }
    
    $('a.preview').click(function(e){
      e.preventDefault();
      $('input[name="save"]').click();
      window.open($(this).attr('href'));
    });
    
    $('.docs li').hover(function(){
      $(this).find('a.delete').css('display', 'block');
    }, function(){
      $(this).find('a.delete').css('display', 'none');
    });
    
    $("[class^='count[']").each(function(){
      var elClass = $(this).attr('class');
      var minChars = 0;
      var maxChars = 0;
      var countControl = elClass.substring((elClass.indexOf('['))+1, elClass.lastIndexOf(']')).split(',');
      
      if(countControl.length > 1) {
        minChars = countControl[0];
        maxChars = countControl[1];
      } else {
        maxChars = countControl[0];
      }	
    
      $(this).parents('tr').find('th').append('<div class="char_count"><strong>0</strong> caracter(e)</div>');
      
      if(jQuery.trim($(this).val()).length > 0){
        $(this).parents('tr').find('th').find('.char_count').children('strong').text(jQuery.trim($(this).val()).length);
      }
      
      $(this).bind('keyup click blur focus change paste', function() {
        var numChars = jQuery.trim($(this).val()).length;
        if($(this).val() === '') {
          numChars = 0;
        }	
        $(this).parents('tr').find('th').find('.char_count').children('strong').text(numChars);
        
        if(numChars < minChars || (numChars > maxChars && maxChars != 0)) {
          $(this).parents('tr').find('th').find('.char_count').addClass('error');
        } else {
          $(this).parents('tr').find('th').find('.char_count').removeClass('error');	
        }
      });
    });
    
    $('a.question').click(function(){
      $(this).next('.answer').toggle();
    });
    
    $('#overview .column tbody tr').hover(function(){
      $(this).find('.edit:eq(0)').css('display', 'inline');
    }, function(){
      $(this).find('.edit:eq(0)').css('display', 'none');
    });
    
    $('a.toggle_menu').click(function(){
      $(this).next('ul').css('display', 'block');
    });
    
    $('.menu_child').each(function(){
      if($(this).hasClass('active')){
        $(this).parent('ul').css('display', 'block');
      }
    });
  }
}

eventHandlers.init();
  
  function anchor()
  {
    var target_offset = $("#anchor_media").offset();
    var target_top = target_offset.top;
    $('html, body').animate({scrollTop:target_top});
  }
  
  /** INIT FUNCTIONS */
  $('.editor').ckeditor(function(){
    var url = document.location.href;
    
    var check = url.search("anchor_media");
    
    if(check != -1)
    {
      anchor();
    }
    
    var check = url.search("anchor_items");
    
    if(check != -1)
    {
      anchor();
    }
  });
  
  $('.add_edit_languages a').click(function(e){
    e.preventDefault();
    
    var href = $(this).attr('href');
    
    var content = {
      header : {
        en : 'Dou you want to save?',
        nl : 'Dou you want to save?',
        ro : 'Dou you want to save?'
      },
      ok_button : {
        en : 'Save',
        nl : 'Save',
        ro : 'Save'
      },
      cancel_button : {
        en : 'Cancel',
        nl : 'Cancel',
        ro : 'Cancel'
      }
    };
    
    noty({
      layout: 'center',
      animateOpen: {opacity: 'show'},
      text: content.header[LANG_CODE], 
      timeout: 10000,
      buttons: [{
        type: 'btn btn-danger', text: content.cancel_button[LANG_CODE], click: function($noty){
          window.location = href;
          $noty.close();
        }
      },{
        type: 'btn btn-primary', text: content.ok_button[LANG_CODE], click: function($noty){
          $('input[name="save"]').click();
          $noty.close();
        }
      }]
    });
  });

  $('#overview table').tablesorter({dateFormat: "uk"});
  
  $.datepicker.regional.nl = {
    closeText: 'Inchide',
    prevText: '?',
    nextText: '?',
    currentText: 'Azi',
    monthNames: ['ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'],
    monthNamesShort: ['ian', 'feb', 'mar', 'apr', 'mai', 'iun', 'iul', 'aug', 'sep', 'oct', 'nov', 'dec'],
    dayNames: ['duminica', 'luni', 'marti', 'miercuri', 'joi', 'vineri', 'sambata'],
    dayNamesShort: ['dum', 'lun', 'mar', 'mie', 'joi', 'vin', 'sam'],
    dayNamesMin: ['du', 'lu', 'ma', 'mi', 'jo', 'vi', 'sa'],
    weekHeader: 'Sa',
    dateFormat: 'dd-mm-yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''};
  $.datepicker.setDefaults($.datepicker.regional.nl);
  
  $('.datepicker').datepicker({
    dateFormat: 'dd-mm-yy'
  });
  
  $.timepicker.regional['nl'] = {
      hourText: 'Ore',
      minuteText: 'Minute',
      amPmText: ['AM', 'PM'],
      closeButtonText: 'Inchide',
      nowButtonText: 'Ora curenta',
      deselectButtonText: 'Sterge' 
  };
  $.timepicker.setDefaults($.timepicker.regional['nl']);

  $('.timepicker').timepicker();
  
  deleteItem = function(href, submit, ref){
    var content = {
      header : {
        en : 'Delete item?',
        nl : 'Delete item?',
        ro : 'Delete item?'
      },
      ok_button : {
        en : 'Ok',
        nl : 'Ok',
        ro : 'Ok'
      },
      cancel_button : {
        en : 'Cancel',
        nl : 'Cancel',
        ro : 'Cancel'
      }
    };
  
    // set default language if no language specific translation exists
    if(!(LANG_CODE in content.header)){
      LANG_CODE = 'en';
    }
  
    noty({
      layout: 'center',
      animateOpen: {opacity: 'show'},
      text: content.header[LANG_CODE], 
      timeout: 10000,
      buttons: [{
        type: 'btn btn-danger', text: content.cancel_button[LANG_CODE], click: function($noty){
          $noty.close();
        }
      },{
        type: 'btn btn-primary', text: content.ok_button[LANG_CODE], click: function($noty){
          if(submit == 'submit')
            $(ref).trigger('click');
          else window.location = href;
          $noty.close();
        }
      }]
    });
  };
  
  $(".modal_ajax").fancybox({
    maxWidth	: 820,
    minWidth	: 820,
    fitToView	: false,
    height		: '100%',
    width		: '100%',
    autoSize	: true,
    closeClick	: false,
    openEffect	: 'none',
    closeEffect	: 'none',
    type		: 'ajax'
  });
  
/*  	$('a.modal_ajax').click(function(e){
    var self = $(this);
    e.preventDefault();
  
    $(self).colorbox({
      width: '840px',
      height: '600px',
      onComplete: function(){
        var col = $(this);
        var self = $(this).attr('href');
         setTimeout(function(){
          $(col).colorbox.resize();
         }, 750);
      },
      onClosed: function(){
        api.release();
      }
    });
  }); */
  
  setCoords = function(c){
    // c.x, c.y, c.x2, c.y2, c.w, c.h
    
    api.coords = new Array();
    api.coords['x'] = c.x;
    api.coords['y'] = c.y;
    api.coords['x2'] = c.x2;
    api.coords['y2'] = c.y2;
    api.coords['w'] = c.w;
    api.coords['h'] = c.h;
  }
  
  $('.info').qtip({
    content: {
      attr: 'title'
    }
  });
  
/*! INIT_ADMIN */
  
});

/*! MISC_FUNCTIONS */
function _prices_vat_toggle(element) {
	var price = number_format($(element).parents('.price').find('.subtotal').val());
	var total;
	
	if(!$(element).is(':checked')) {
		$(element).parent().find('.vat').val('0.00');
		var total = number_format(parseFloat(price));
		$(element).parents('.price').find('.total').val(total);
	}
	else {
		var vat = number_format((20 * price)/100);
		total = number_format(parseFloat(price) + parseFloat(vat));
		$(element).parents('.price').find('.total').val(total);
		$(element).parent().find('.vat').val(vat);
	}
	
	var parent = $(element).closest('.price-row');
	if  (parent.hasClass('last')) {
		return false;
	}

	var grandTotal = 0;
	parent.closest('.prices.full-width').find('.price-row').each(function() {
		var price = parseFloat($(this).find('.total').val());
		price = price == '' ? '0' : price;
		grandTotal += parseFloat(price);
	});
	parent.closest('.prices.full-width').find('.grand_total').val(number_format(grandTotal));
	return false;
}

function _prices_set_free(element) {
	if($(element).is(':checked')) {
		$(element).parent().parent().find('.grand_total').val('0.00');
		
		var parent = $(element).parent().parent().parent().parent().parent();

		if  (parent.hasClass('last')) {
			return false;
		}

		var grandTotal = 0;
		parent.closest('.prices.full-width').find('.price-row').each(function() {
			var price = parseFloat($(this).find('.subtotal').val()) + parseFloat($(this).find('.vat').val());
			if($(this).find('.total').val() != '') {
				$(this).find('.total').val(number_format(0));
				grandTotal += parseFloat(price);
			}
		});
	}
	else {
		var parent = $(element).parent().parent().parent().parent().parent();

		if  (parent.hasClass('last')) {
			return false;
		}

		var grandTotal = 0;
		parent.closest('.prices.full-width').find('.price-row').each(function() {
			var price = parseFloat($(this).find('.subtotal').val()) + parseFloat($(this).find('.vat').val());
			if($(this).find('.total').val() != '') {
				$(this).find('.total').val(number_format(price));
				grandTotal += parseFloat(price);
			}
		});

		parent.closest('.prices.full-width').find('.grand_total').val(number_format(grandTotal));
		return false;
	}
}