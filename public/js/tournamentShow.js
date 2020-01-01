(function ($) {
    $(function () {
        setInterval(function () {
            $.ajax({
                url: $('#data-update-link').text(),
                method: 'GET',
                success: function (data) {
                    //M.toast({html: 'Les données ont été mises à jour', classes: "green"});
                    console.log(data);
                    for (id in data['matchs']) {
                        if (data['matchs'].hasOwnProperty(id)) {
                            let match = data['matchs'][id],
                                matchElement = $("#match-" + match.id).children();
                            matchElement.eq(0).html(match.host);
                            matchElement.eq(1).html(match.away);
                            matchElement.eq(2).html(match.time);
                            matchElement.eq(3).html(match.type);
                            matchElement.eq(4).html(match.name);
                            matchElement.eq(5).html(match.score);
                            matchElement.eq(6).html(match.state);
                        }
                    }

                    $('.rankings tbody').empty();
                    for (ct in data['ranking']['all']) {
                        let team = data['ranking']['all'][ct];
                        if ($('#globalRanking').length) {
                            $("#globalRanking tbody").append("<tr>"
                                + "<td>" + team['rank'] + "</td>"
                                + "<td>" + team['name'] + "</td>"
                                + "<td>" + team['points'] + "</td>"
                                + "<td>" + team['pool'] + "</td>"
                                + "</tr>");
                            if (team['points'] === undefined){ // Changed to final ranking
                                location.reload()
                            }
                        } else {
                            $("#finalRanking tbody").append("<tr>"
                                + "<td>" + team['rank'] + "</td>"
                                + "<td>" + team['name'] + "</td>"
                                + "</tr>");
                            if (team['points'] !== undefined){ // Changed to pool ranking : Never knows
                                location.reload()
                            }
                        }
                    }


                    for (pool in data['ranking']) {
                        if (pool !== "all") {
                            for (ct in data['ranking'][pool]) {
                                let team = data['ranking'][pool][ct];
                                console.log("#rankingPool-" + team['pool_id'])
                                $("#rankingPool-" + team['pool_id'] + " tbody").append("<tr>"
                                    + "<td>" + (parseInt(ct) + 1) + "</td>"
                                    + "<td>" + team['name'] + "</td>"
                                    + "<td>" + team['points'] + "</td>"
                                    + "</tr>");

                            }
                        }
                    }

                    $('#tournament-state').html(data['state'])
                },
                error: function (response) {
                    console.log(response);
                    M.toast({html: 'Une erreur est survenue', classes: "red"});
                }
            });
        }, 1000 * 5);
    }); // end of document ready
})(jQuery); // end of jQuery name space
