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
                for (id in data) {
                    if (data.hasOwnProperty(id)) {
                        let match = data[id],
                            matchElement = $("#match-" + match.id).children();
                        console.log(match);
                        matchElement.eq(1).html(match.host);
                        matchElement.eq(2).html(match.away);
                        matchElement.eq(3).html(match.time);
                        matchElement.eq(4).html(match.type);
                        matchElement.eq(6).html(match.score);
                        matchElement.eq(7).html(match.state);
                    }
                }
            },
            error: function (data) {
                M.toast({html: data.responseText, classes: "red"});
                console.log(data.responseText)
            }
        };
        $('#matchEditForm').ajaxForm(options);


        $('#recalculateLink').click(function (event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                method: 'POST',
                success: function (response) {
                    M.toast({html: 'Les points ont été recalculés', classes: "green"});
                    $('.rankings tbody').empty();
                    for ($ct in response['all']) {
                        let $team = response['all'][$ct];
                        $("#globalRanking tbody").append("<tr>"
                            + "<td>" + (parseInt($ct) + 1) + "</td>"
                            + "<td>" + $team['name'] + "</td>"
                            + "<td>" + $team['points'] + "</td>"
                            + "<td>" + $team['pool'] + "</td>"
                            + "</tr>");
                    }

                    for ($pool in response) {
                        if ($pool !== "all") {
                            for ($ct in response[$pool]) {
                                let $team = response[$pool][$ct];
                                $("#rankingPool-" + $pool + " tbody").append("<tr>"
                                    + "<td>" + $team['id'] + "</td>"
                                    + "<td>" + $team['name'] + "</td>"
                                    + "<td>" + $team['points'] + "</td>"
                                    + "<td>" + (parseInt($ct) + 1) + "</td>"
                                    + "</tr>");

                            }
                        }
                    }

                },
                error: function (response) {
                    console.log(response);
                    M.toast({html: 'Une erreur est survenue', classes: "red"});
                }
            });
            return false; // for good measure
        });
    }); // end of document ready
})(jQuery); // end of jQuery name space
