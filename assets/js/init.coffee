App = window.App

new App.View.Main el: $('body')


# TODO: refactor

sidebar = $('#sidebar')

sidebar.on 'click', '.panel .header', ->
	$(this).closest('.panel').toggleClass('open')