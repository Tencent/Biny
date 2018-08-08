!function (a) {
    "use strict";
    a(function () {
        var b = a(window), c = a(document.body);
        c.scrollspy({target: ".bs-docs-sidebar"}), b.on("load", function () {
            c.scrollspy("refresh")
        }), a(".bs-docs-container [href=#]").click(function (a) {
            a.preventDefault()
        }), setTimeout(function () {
            var b = a(".bs-docs-sidebar");
            b.affix({offset: {top: function () {
                var c = b.offset().top, d = parseInt(b.children(0).css("margin-top"), 10), e = a(".bs-docs-nav").height();
                return this.top = c - e - d
            }, bottom: function () {
                return this.bottom = a(".bs-docs-footer").outerHeight(!0)
            }}})
        }, 100), setTimeout(function () {
            a(".bs-top").affix()
        }, 100), a(".tooltip-demo").tooltip({selector: '[data-toggle="tooltip"]', container: "body"}), a(".popover-demo").popover({selector: '[data-toggle="popover"]', container: "body"}), a(".tooltip-test").tooltip(), a(".popover-test").popover(), a(".bs-docs-popover").popover(), a("#loading-example-btn").on("click", function () {
            var b = a(this);
            b.button("loading"), setTimeout(function () {
                b.button("reset")
            }, 3e3)
        }), a("#exampleModal").on("show.bs.modal", function (b) {
            var c = a(b.relatedTarget), d = c.data("whatever"), e = a(this);
            e.find(".modal-title").text("New message to " + d), e.find(".modal-body input").val(d)
        }), a(".bs-docs-activate-animated-progressbar").on("click", function () {
            a(this).siblings(".progress").find(".progress-bar-striped").toggleClass("active")
        });
    })
}(jQuery);

function changeLanguage(lan){
    setCookie('biny_language', lan, 24*60*30);
    window.location.reload();
}