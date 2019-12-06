$(document).ready(function () {

    $("#close-wrap").click(function () {
        $("#gallery-wrap").hide();
        $("body").removeClass("overflow-hidden");
    });
    $("#gallery-wrap #left-wrap").on("click", function () {
        var wrap = $("#gallery-wrap");
        var curImage = wrap.find("img");
        var imageId = curImage.attr("data-id");
        var targetEl = $("#lightgallery").find('img[data-id="' + imageId + '"]').parent();
        var prevEl = targetEl.prev();
        if (prevEl[0]) {
            replaceGalleryWrapImage(prevEl);
        }
    });
    $("#gallery-wrap #right-wrap").on("click", function () {
        var wrap = $("#gallery-wrap");
        var curImage = wrap.find("img");
        var imageId = curImage.attr("data-id");
        var targetEl = $("#lightgallery").find('img[data-id="' + imageId + '"]').parent();
        var nextEl = targetEl.next();
        if (nextEl[0]) {
            replaceGalleryWrapImage(nextEl);
        }
    });
    $(".gallery-image").on("click", function (e) {
        var curEl = $(this);
        e.preventDefault();
        $(window).scrollTop(0);

        replaceGalleryWrapImage(curEl);
    });

});

function replaceGalleryWrapImage(curEl) {
    var image = curEl.find("img").clone();

    $("#gallery-wrap #image").html(image);
    var info = curEl.find(".competition-image-info").clone();
    $("#gallery-wrap #description").html(info.show().html());
    $("#gallery-wrap").show();

    $("body").addClass("overflow-hidden");
    var galleryContent = $("#gallery-wrap #content #description");
    var galleryContentHeight = galleryContent.height();

    var maxWidth = 1200;
    var imageWidth = parseFloat(image.attr("data-width"));
    var imageHeight = parseFloat(image.attr("data-height"))
    if (imageWidth > maxWidth) {
        var ratio = maxWidth / imageWidth;
        imageHeight = imageHeight * ratio
    }
    var windowHeight = $(window).height();
    if  (imageHeight >windowHeight ) {
        imageHeight = windowHeight - (windowHeight/10);
        $("#gallery-wrap #content").css("padding-top", 0)
        $("#gallery-wrap #content img").css("height", imageHeight)
    } else {
        var contentHeight = imageHeight + parseFloat(galleryContentHeight);
        if (windowHeight > contentHeight) {
            var heightDiff = windowHeight - contentHeight;
            $("#gallery-wrap #content").css("padding-top", (heightDiff / 2))
        } else {
            $("#gallery-wrap #content").css("padding-top", 0)
        }
    }
}