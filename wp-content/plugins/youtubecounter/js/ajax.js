jQuery(document).ready(function() {
    jQuery('#fetch').on('click', function() {
        // console.log(jQuery('#title').val());
        // get only video id from string.
        var video_link = jQuery('#title').val(); // get video link.
        var id = video_link.substr(32, 11);
        var url = "https://content.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id=" + id + "&key=AIzaSyAblvIArQ_G37jRqlR8xORi-_w21v8fCn8";
        // ajax request
        jQuery.get(url, function(data, status) {
            // alert("Data: " + data + "\nStatus: " + status);
            jQuery("#video_thumbnail").attr("src", data.items[0].snippet.thumbnails.default.url); // add video thumbnail src
            jQuery("#video_thumbnail_video_thumbnail").attr("value", data.items[0].snippet.thumbnails.default.url); // add video thumbnail src
            jQuery("input#total_views_total_views").val(data.items[0].statistics.viewCount); // add total views
            // alert(data.items[0].snippet.thumbnails.default.url);
            // console.log(data.items[0]);
            // console.log(data);
        });
    });
    // convert checkboxes to radio
    jQuery('form#post').find('.categorychecklist input').each(function() {
        var new_input = jQuery('<input type="radio" />'),
            attrLen = this.attributes.length;
        for (i = 0; i < attrLen; i++) {
            if (this.attributes[i].name != 'type') {
                new_input.attr(this.attributes[i].name.toLowerCase(), this.attributes[i].value);
            }
        }
        jQuery(this).replaceWith(new_input);
    });
});