const BetterPostUi = {};
BetterPostUi.Order = (function ($) {

   

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
                BetterPostUi.Order.reindex();
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
