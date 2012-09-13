$(function() {
    var position = 0;
    var layer = $("#video .layers");
    var h1 = $("#video h1");
    var like = $("#video .like span");
    var likenot = $("#video .likenot span");

    function updateTitle() {
        var title = video_list[position]["title"];
        TweenLite.to(h1, .1, {css:{opacity:0}, onComplete: function() { h1.html(title); } });
        TweenLite.to(h1, .5, {css:{opacity:1}, delay: 0.1});
    }

    function updateNumbers() {
        var like_value = video_list[position]["plus"];
        var likenot_value = video_list[position]["minus"];

        like.html(like_value);
        likenot.html(likenot_value);
    }

    function loadMore(step) {
        var next_in_path = position + step;

        next_in_path = letInRange(next_in_path);

        var layers = $("#layers .layer");
        var iframe = $(layers[next_in_path]).find("iframe");
        var src = iframe.attr("src");

        if (src == "") {
            iframe.attr("src", video_list[next_in_path]["src"]);
        }
    }

    function letInRange(position) {
        if (position >= video_list.length) {
            position = 0;
        } else if (position < 0) {
            position = video_list.length - 1;
        }

        return position;
    }

    function advance(step) {
        position = position + step;

        position = letInRange(position);


        loadMore(step);


        updateTitle();
        updateNumbers()
        TweenLite.to(layer,.6, {css:{left:position*640*-1}});

        return false;
    }

    function vote(value) {
        var id = video_list[position]["id"];
        $.post("vote.php", {vote: value, video_id: id }, function(data) {
            if (data["error"] == "already voted") {
                $('.alreadyvoted').css('opacity', 1);
                $('.alreadyvoted').show();
                TweenLite.to($('.alreadyvoted'), .5, {css:{opacity:0}, delay: 2});
            } else {
                video_list[position]["plus"] = data["plus"];
                video_list[position]["minus"] = data["minus"];
                updateNumbers();
            }
        }, "json");
        return false;
    }

    $('#video .left').click(function() {
        return advance(-1);
    });

    $('#video .right').click(function() {
        return advance(1);
    });

    $("#video .like").click(function() {
        return vote(1);
    });

    $("#video .likenot").click(function() {
        return vote(-1);
    });

    function getPlayer(video, visible) {
        switch (video["type"]) {
            case "youtube":
                return $("<iframe>", {
                    width: "640",
                    height: "360",
                    src: visible ? video["src"] : "",
                    frameborder: "0",
                    allowfullscreen: "allowfullscreen"
                });
                break;
        }
    }

    for (var i = 0; i < video_list.length; i++) {
        $("#layers").append(
            $("<div>", {
                "class": "layer"
            }).append(
                getPlayer(video_list[i], (i < 2) || i == (video_list.length - 1))
            )
        );
    }

    updateTitle();
    updateNumbers();
});



