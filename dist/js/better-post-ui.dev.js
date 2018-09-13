var BetterPostUi = {};

BetterPostUi = BetterPostUi || {};
BetterPostUi.Components = BetterPostUi.Components || {};

BetterPostUi.Components.Author = (function ($) {

    var inputTimer = false;
    var isTyping = false;

    function Author() {
        // Select author from list
        $(document).on('click', '.better-post-ui-author-select li', function (e) {
            this.setSelected(e.target);
        }.bind(this));

        // Filter list of authors
        $('[name="better-post-ui-author-select-filter"]').on('input', function (e) {
            clearTimeout(inputTimer);

            inputTimer = setTimeout(function () {
                var query = $(e.target).closest(':input').val();
                this.searchAuthor(query);
            }.bind(this), 300);
        }.bind(this));
    }

    /**
     * Sets a author as selected
     * @param {element} element The element to set selected
     */
    Author.prototype.setSelected = function (element) {
        $('.better-post-ui-author-select li.selected').removeClass('selected');
        $(element).closest('li').addClass('selected');

        $('[name="post_author_override"]').val($(element).closest('li').attr('data-user-id'));
    };

    Author.prototype.searchAuthor = function(q) {
        var $container = $('.better-post-ui-author-select');

        if (q.length === 0) {
            $container.html('');
            return;
        }

        $container.html('<li style="text-align:left;"><span class="spinner" style="visibility:visible;float:none;vertical-align:none;"></span></li>');

        var data = {
            action: 'better_post_ui_author',
            q: q,
            post_id: $('[name="post_ID"]').val()
        };

        $.post(ajaxurl, data, function (res) {
            var html = '';

            if (res.length === 0) {
                $container.html('');
                return;
            }

            $.each(res, function (index, user) {
                html = html + '<li data-user-id="' + user.ID + '">';

                if (user.data.profile_image !== null && user.data.profile_image.lenght > 0) {
                    html = html + '<div class="profile-image" style="background-image:url(\'' + user.data.profile_image + '\');"></div>';
                } else {
                    html = html + '<div class="profile-image"></div>';
                }

                html = html + '<div class="profile-info">';
                    html = html + '<span class="user-fullname">' + user.data.first_name + ' ' + user.data.last_name + '</span>';
                    html = html + '<span class="user-login">' + user.data.user_login + '</span>';
                html = html + '</div>';

                html = html + '</li>';
            });

            $container.html(html);
        }, 'JSON');
    };

    /**
     * Filters the list of authors
     * @param  {string} query Filter query
     * @return {void}
     */
    Author.prototype.filterList = function(query) {
        if (query === '') {
            $('.better-post-ui-author-select li').show();
            return;
        }

        $('.better-post-ui-author-select li:not(:contains(' + query + '))').hide();
        $('.better-post-ui-author-select li:contains(' + query + ')').show();
    };

    return new Author();

})(jQuery);

jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

BetterPostUi = BetterPostUi || {};
BetterPostUi.Components = BetterPostUi.Components || {};

BetterPostUi.Components.Order = (function ($) {

    var ajaxPostTimer;
    var ajaxData;

    function Order() {
        this.init();

        $('[data-action="better-post-ui-order-up"]').on('click', function (e) {
            var li = $(e.target).parents('li').first()[0];
            this.moveUp(li);
        }.bind(this));

        $('[data-action="better-post-ui-order-down"]').on('click', function (e) {
            var li = $(e.target).parents('li').first()[0];
            this.moveDown(li);
        }.bind(this));
    }

    Order.prototype.init = function () {
        $('.better-post-ui-menu-order-list').sortable({
            stop: function (e, ui) {
                BetterPostUi.Components.Order.reindex();
            }
        }).bind(this);
    };

    Order.prototype.moveUp = function(element) {
        var current = $(element);
        current.prev().before(current);
        this.reindex();
    };

    Order.prototype.moveDown = function(element) {
        var current = $(element);
        current.next().after(current);
        this.reindex();
    };

    Order.prototype.reindex = function() {
        $('.better-post-ui-menu-order-list').find('li').each(function (index, element) {
            $(this).find('[name*="menu_order"]').val(index); // Sets default behaviour
            $(this).attr('data-order-id', index); // Async driven data
        });

        this.asyncSave();
    };

    Order.prototype.asyncSave = function() {

        //Clear timer
        clearTimeout(ajaxPostTimer);

        //Var declerations
        var pageOrder = [];

        //Get new data
        $('.better-post-ui-menu-order-list').find('li').each(function (index, element) {
            pageOrder[index] = {postId: $(element).attr('data-post-id'), orderId: $(element).attr('data-order-id')};
        });

        //Define data post object
        this.ajaxData = {
            'action': 'better_post_ui_order_pages',
            'jsonPageOrder': JSON.stringify(pageOrder)
        };

        //Delay the actual sending of the ajax, in case of further changes by the user
        ajaxPostTimer = window.setTimeout(function() {
            $.post(ajaxurl, this.ajaxData, function(response) {});
        }.bind(this), 700);
    };

    return new Order();

})(jQuery);

BetterPostUi = BetterPostUi || {};
BetterPostUi.Components = BetterPostUi.Components || {};

BetterPostUi.Components.Parent = (function ($) {

    var typingTimer;

    function Parent() {
        $('[data-action="better-post-ui-parent-show-all"]').on('click', function (e) {
            e.preventDefault();
            this.showList();
        }.bind(this));

        $('[data-action="better-post-ui-parent-show-search"]').on('click', function (e) {
            e.preventDefault();
            this.showSearch();
        }.bind(this));

        $('[data-action="better-post-ui-parent-search"]').on('input', function (e) {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function () {
                this.search($(e.target).closest('[data-action="better-post-ui-parent-search"]').val());
            }.bind(this), 300);
        }.bind(this));

        $(document).on('click', '[data-better-post-ui-set-parent-id]', function (e) {
            var $element = $(e.target).closest('[data-better-post-ui-set-parent-id]');
            var id = $element.attr('data-better-post-ui-set-parent-id');
            var title = $element.text();

            this.setParent(id, title);
        }.bind(this));
    }

    Parent.prototype.showList = function() {
        $('.better-post-ui-parent-search').hide();
        $('.better-post-ui-parent-list').show();
    };

    Parent.prototype.showSearch = function() {
        $('.better-post-ui-parent-list').hide();
        $('.better-post-ui-parent-search').show();
    };

    Parent.prototype.setParent = function(id, title) {
        $('[data-action="better-post-ui-parent-search"]').val('');
        $('.better-post-ui-search-parent-list').remove();

        $('.better-post-ui-parent-list #parent_id').val(id);

        this.showList();
    };

    Parent.prototype.search = function (query, postType) {
        clearTimeout(typingTimer);
        $('.better-post-ui-search-parent-list').remove();

        if (query === '') {
            return;
        }

        $.post(ajaxurl, {action: 'better_post_ui_search_parent', query: query, postType: $('[name="post_type"]').val()}, function (res) {
            clearTimeout(typingTimer);
            $('[data-action="better-post-ui-parent-search"]').after('<ul class="better-post-ui-search-parent-list"></ul>');

            $.each(res, function (index, item) {
                $('.better-post-ui-search-parent-list').append('<li data-better-post-ui-set-parent-id="' + item.ID + '">\
                    ' + item.post_title + '\
                </li>');
            });
        }, 'JSON');
    };

    return new Parent();

})(jQuery);

BetterPostUi = BetterPostUi || {};
BetterPostUi.Components = BetterPostUi.Components || {};

BetterPostUi.Components.Publish = (function ($) {

    function Publish() {
        if ($('#misc-publishing-actions').length === 0) {
            return;
        }

        this.initDatepicker();
        this.handleEvents();
    }

    Publish.prototype.handleEvents = function () {
        $('.save-timestamp', '#timestampdiv').on('click', function (event) {
            var attemptedDate,
                currentDate,
                postVisibility = $('#post-visibility-select').find('input:radio:checked').val(),
                aa = $('#aa').val(),
                mm = $('#mm').val(),
                jj = $('#jj').val(),
                hh = $('#hh').val(),
                mn = $('#mn').val();

            attemptedDate = new Date(aa, mm - 1, jj, hh, mn);
            currentDate = new Date($('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val());

            if (attemptedDate > currentDate && postVisibility == 'private') {
                setTimeout(function() {
                    $('#publish').val(postL10n.schedule);
                }, 10);

            }
        });
    };

    Publish.prototype.initDatepicker = function () {
        $('#aa, #mm, #jj').hide();

        var timestamp_wrap_text = $('.misc-pub-curtime .timestamp-wrap').html();
        timestamp_wrap_text = timestamp_wrap_text.replace(/(,|@)/g, '');
        $('.misc-pub-curtime .timestamp-wrap').html(timestamp_wrap_text);

        $('#hh').before('<span class="municipio-admin-datepicker-time dashicons dashicons-clock"></span>')

        $('#timestampdiv').prepend('<div id="timestamp-datepicker" class="municipio-admin-datepicker"></div>');
        $('#timestamp-datepicker').datepicker({
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            onSelect: function (selectedDate) {
                selectedDate = selectedDate.split('-');

                $('#aa').val(selectedDate[0]);
                $('#mm').val(selectedDate[1]);
                $('#jj').val(selectedDate[2]);
            }
        });

        var initialDate = $('#aa').val() + '-' + $('#mm').val() + '-' + $('#jj').val();
        $('#timestamp-datepicker').datepicker('setDate', initialDate);
    };

    return new Publish();

})(jQuery);
