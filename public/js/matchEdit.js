(function ($) {
    $(function () {
        $(".matchEditModal").click(function (e) {
            let childList = $(this).parent().parent().children(),
                scores = childList.eq(6).html().split(' : '),
                date = childList.eq(3).html().split(" ");

            $("#match-id").val(childList.eq(0).html());

            $('#host-team option[value="' + childList.eq(1).attr('team-id') + '"]').prop('selected', true);
            $('#away-team option[value="' + childList.eq(2).attr('team-id') + '"]').prop('selected', true);
            $("#host-score").val(scores[0]);
            $("#away-score").val(scores[1]);

            $("#match-time").val(date[1]);
            $("#match-day").val(date[0]);
            $('#match-state option[value="' + childList.eq(7).attr('match-state') + '"]').prop('selected', true);
            $('#match-type option[value="' + childList.eq(4).attr('match-type') + '"]').prop('selected', true);
            $("#match-name").val(childList.eq(5).html());

            $("#matchEditForm").attr('action', $(this).attr("form-link"));

            M.updateTextFields();

            $('select').formSelect();

        });

        let options = {
            success: function (data) {
                M.toast({html: 'Match mis à jour', classes: "green"});
                let matchElement = $("#match-" + data.id).children();

                matchElement.eq(1).html(data.host);
                matchElement.eq(2).html(data.away);
                matchElement.eq(3).html(data.time);
                matchElement.eq(4).html(data.type);
                matchElement.eq(6).html(data.score);
                matchElement.eq(7).html(data.state);
            },
            error: function (data) {
                M.toast({html: data.responseText, classes: "red"});
            }
        };
        $('#matchEditForm').ajaxForm(options);
    }); // end of document ready
})(jQuery); // end of jQuery name space
