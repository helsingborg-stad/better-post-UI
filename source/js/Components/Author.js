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
