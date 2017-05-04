var _app = {

    init: function() {

      $('[data-toggle="tooltip"]').tooltip();
      _app.docsAccordion();

    },

    docsAccordion: function() {
        $('.items-list a.item').on('click', function() {
            var targetID = $(this).attr('data-item-id');
            $(this).parent().addClass('active').siblings().removeClass('active');
            $(this).closest('.inner.list').find('.items-content').find('.item-content[id="' + targetID + '"]')
            .removeClass('hidden').addClass('active').hide().fadeIn()
            .siblings().removeAttr('style').removeClass('active').addClass('hidden');
            return false;
        });
    },
    
    menuToggle: function() {
        $('.toggler').on('click', function() {
            $(this).toggleClass('active');
            $('.mobile-menu-wrap').toggleClass('open');
            return false;
        });
    },

    formsValidation: function() {
        $('#contact-form').validate({
            focusCleanup: true,
            onkeyup: false
        });
    },

    hasChildren: function(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
                return true;
            }
        }
        return false;
    }
};

$(document).ready(_app.init);

/* General form (always, before) */
$('.phery-form').bind({
    'phery:always': function() {
        $(this).find(':input:not(button)').removeAttr('disabled');
        $(this).find('button').button('reset');
    },
    'phery:beforeSend': function() {
        $(this).find(':input:not(button)').attr('disabled', true);
        $(this).find('button').button('loading');
    }
});

/* General form (done) */
$('.contact-form').bind({
    'phery:done': function(event, data) {
        if (data.code == 200) {
            $(this).addClass('hidden').find(':input:not(button)').val('');
            $('.alert').removeClass('hidden alert-warning').addClass('alert-success').find('.message').html(data.data);
            $(this).fadeOut();
        } else {
            $('.alert').removeClass('hidden alert-success').addClass('alert-warning').find('.message').html(data.data);
        }
    }
});

/* Homepage selects (done) */
$('.ajax-select').bind({
    'phery:always': function() {
        $('body').find('.preloading').remove();
    },
    'phery:beforeSend': function() {
        var wHeight = $(window).height();
        var loading = $('<div>').addClass('preloading').html('Please wait...')
        .height(wHeight).css('line-height', wHeight + 'px');
        $('body').append(loading);
    },
    'phery:done': function(event, data) {
        var users_type_id = $('[name="users_type_id"]');
        var users_nationality_id = $('[name="users_nationality_id"]');
        $('.messages').addClass('hidden').empty();
        if (data.code == 500) {
            if ('content' in data.data && $('#content').length != 0) {
                $('#content').html(data.data.content);
            }
            if ('results' in data.data && $('.messages').length != 0) {
                $('.messages').removeClass('hidden').html(data.data.results);
            }
        }
        if ('users_nationality_id' in data.data) {
            users_type_id.add(users_nationality_id).find('option:gt(0)').remove();
            if (!_app.hasChildren(data.data.users_nationality_id)) {
                $('<option>').attr('value', '').html('- No nationalities found -').appendTo(users_nationality_id);
            } else {
                $.each(data.data.users_nationality_id, function(key, name) {
                    $('<option>').attr('value', key).html(name).appendTo(users_nationality_id);
                });
            }
        }

        if ('users_type_id' in data.data) {
            users_type_id.find('option:gt(0)').remove();
            if (!_app.hasChildren(data.data.users_type_id)) {
                $('<option>').attr('value', '').html('- No visa types found -').appendTo(users_type_id);
            } else {
                $.each(data.data.users_type_id, function(key, name) {
                    $('<option>').attr('value', key).html(name).appendTo(users_type_id);
                });
            }
        }
        if ('content' in data.data && $('#content').length != 0) {
            if (data.code == 200) {
                $('#content').html(data.data.content);
                $('[data-toggle="tooltip"]').tooltip();
                _app.docsAccordion();
            }
        }
        return false;
    }
}).phery('ajax_select');

/* Homepage selects (done) */
$('.configurator').bind({
    'phery:done': function(event, data) {
        if (data.code == 500) {
            $(this).find('.messages').removeClass('hidden').html(data.data);
            return false;
        } else {
            $(this).find('.messages').addClass('hidden').empty();
            location.href = data.data;
            return false;
        }
    }
}).phery('configurator');