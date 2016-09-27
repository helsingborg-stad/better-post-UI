BetterPostUi = BetterPostUi || {};
BetterPostUi.Components = BetterPostUi.Components || {};

BetterPostUi.Components.Order = (function ($) {

    function Order() {
        this.init();
    }

    Order.prototype.init = function () {
        console.log("TEST");
        $('.better-post-ui-menu-order-list').sortable({
            stop: function (e, ui) {

            }
        }).bind(this);
    };

    return new Order();

})(jQuery);
