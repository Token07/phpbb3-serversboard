phpbb.addAjaxCallback('token07.serversboard.players', function(response) {
    var player_list;
	
	player_list = $('<ul style="list-style: none" /></ul>')
	$.each(response.PLAYER_LIST, function(i, v) {
		$('<li/>').text(v.Name).appendTo(player_list)
	})
	phpbb.alert(response.TITLE, player_list)
});