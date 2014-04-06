App = window.App

class App.View.User extends Backbone.View
	events:
		'submit':  'onSubmit'
	
	initialize: ->
		@result = @$('#sign-up-result')

	onSubmit: (event) =>
		event.preventDefault()

		newUser = new App.Model.User();
		userDetails = 
			name: @$('#sign-up-name').val(),
			email: @$('#sign-up-email').val(),
			password: @$('#sign-up-password').val()

		newUser.save userDetails, success: @onSave

		el = '<span>Chegou aqui! com ' + @$('#sign-up-name').val() + '</span>'
		@result.empty().append el
	
	onSave: (data) =>
		alert data.toJSON().chave;
