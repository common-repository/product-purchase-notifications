(function ($) {
    var Notify_Me = {
        ajax_url: "",
        action: "",
        Order_ids: [],
        showNextIn_ms: 0,
        bannerDissappearAfter_ms: '',
        /**
         * init
         */
        init: function () {
            Notify_Me.showNextIn_ms = localizedData.timeDisapear;
            Notify_Me.bannerDissappearAfter_ms = localizedData.timeDisapear;
            Notify_Me.ajax_url = localizedData.url;
            Notify_Me.nonce = localizedData.nonce;
            if (document.hasFocus) {
                setTimeout(Notify_Me.generatepopup, Notify_Me.showNextIn_ms);
            }
        },
        /**
         * make an ajax call.
         */
        generatepopup: function () {
            $.ajax({
                type: 'post',
                url: Notify_Me.ajax_url,
                data: {
                    action: 'generatepopup',
                    ids: Notify_Me.Order_ids,
                    nonce: Notify_Me.nonce,
                },
                complete: function (status) {
                },
                error: function (status) {
                },
                success: function (data) {
                    if (data) {
                        result = data;
                        if (result.length !== 0) {
                            Notify_Me.showNextIn_ms = result.notify_time * 1000;
                            Notify_Me.showNextIn_ms = Notify_Me.showNextIn_ms + 5000;//Remove notifection display time
                            if (result.totalOrders - 1 > +Notify_Me.Order_ids.length) {
                                Notify_Me.Order_ids.push(result.id);
                            } else {
                                Notify_Me.Order_ids = [];
                            }
                            if (result.items.product_name) {
                                eleHtml = Notify_Me.editHtmlElement(result);
                            }
                            result = '';
                            if (document.hasFocus) {
                                setTimeout(Notify_Me.generatepopup, Notify_Me.showNextIn_ms);
                            }
                        }
                    }
                }
            });
        },
        /**
         * add container div
         */
        editHtmlElement: function (record) {
            var IsAllreadythere = document.querySelector(".notification-banner");
            if (!IsAllreadythere) {
                returnValue = Notify_Me.createAnHtmlElement(record);
            } else {
                returnValue = Notify_Me.dynamicHtml(record);
            }
            return returnValue;
        },
        /**
         * append child to container div with dynamic
         */
        createAnHtmlElement: function (record) {
            var bannerr = document.createElement("div");
            if (record.notify_positionOfTheBanner == 1) {
                positionClassCss = "bottom-left";
            } else {
                positionClassCss = "bottom-right";
            }
            bannerr.innerHTML = `<div class="notification-banner ` + positionClassCss + `"></div>`;
            document.querySelector("body").appendChild(bannerr);
            Notify_Me.dynamicHtml(record);
        },
        dynamicHtml: function (record) {
            if (record.buyer) {
                var buyerName = record.buyer.charAt(0).toUpperCase() + record.buyer.slice(1);
            } else {
                buyerName = "SomeOne";
            }
            var bannerElement = document.querySelector(".notification-banner");
            temp = bannerElement.innerHTML;
            imgURL = record.items.imgurl ? record.items.imgurl[0] : "#";
            if (record.notify_toggle_img == 1 && imgURL) {
                var tempHtml = `
        <div class="notification-img-div"><a href="`+ record.items.Permalink + `">
        <img src="`+ imgURL + `" width="70%" height="initial-scale"class="notification-img" /></a>
        </div><div class="notification-text"><div class='icon-div'><span onmousedown='return false;' onselectstart='return false;'>&#128473;<span></div>
        <span class='para' onmousedown='return false;' onselectstart='return false;'>`+ buyerName + ` in ` + record.get_billing_city + `  bought
        </span >
        <span class="heading" onmousedown='return false;' onselectstart='return false;'>`+ record.items.product_name + `</span><span class='small' onmousedown='return false;' onselectstart='return false;'>` + record.PlacedAt + `</span class='small'>
        </div>
        `;
            } else {
                var tempHtml = `
        <div class=".notification-text-full-width"><div class='icon-div'><span onmousedown='return false;' onselectstart='return false;'>&#128473;<span></div>
        <span class='para' onmousedown='return false;' onselectstart='return false;'>`+ buyerName + ` in ` + record.get_billing_city + `  bought
        </span><a href="`+ record.items.Permalink + `" style='color: inherit;'>
        <span class="heading" onmousedown='return false;' onselectstart='return false;'>`+ record.items.product_name + `</span></a><span class='small' onmousedown='return false;' onselectstart='return false;'>` + record.PlacedAt + `</span>

        </div>
        `;
            }
            bannerElement.innerHTML = tempHtml;
            $('.notification-banner').css("background-color", record.notify_color_background);
            $('.notification-banner').css("box-shadow", '0px 0px 2px 2px' + record.notify_color_shadow);
            $('.heading').css("color", record.notify_color_title);
            $('.para').css("color", record.notify_color_text);
            $('.small').css("color", record.notify_color_text);
            $(".notification-banner").fadeIn(600);
            setTimeout(() => { $(".notification-banner").fadeOut(600) }, 5000);
            // setTimeout(() => { $(".notification-banner").fadeOut(600) }, Notify_Me.bannerDissappearAfter_ms * 0.90);
            $(".icon-div").click(() => { $(".notification-banner").fadeOut(600) })
        }

    }// end class


    Notify_Me.init();
})(jQuery);
