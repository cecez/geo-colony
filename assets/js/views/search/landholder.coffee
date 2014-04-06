App = window.App

class App.View.SearchLandholder extends Backbone.View
	template: _.template($('#search-landholder-result-template').html())
	events:
		'submit':  'onSubmit'
		'click a': 'onItemClick'

	initialize: ->
		@fieldName = @$('#search-landholder-name')
		@result = @$('#search-landholder-result')

		@collection = new App.Collection.SearchLandholders

	onSubmit: (event) =>
		event.preventDefault()
		@fetch @fieldName.val()
		
	fetch: (query) =>
		@result.empty()
		@collection.reset()
		@collection.fetch
			success: @onFetched
			data: query: query

	onFetched: =>
		if @collection.length
			el = $('<ul>')
			@collection.each (model) =>
				el.append @template data: model.toJSON()
		else
			el = '<span class="warning">Nenhum proprietário encontrado.</span>'
		@result.empty().append el

	onItemClick: (event) =>
		event.preventDefault()
		landholder_id = $(event.currentTarget).data('landholder_id')
		App.trigger 'addLand', 'L' + landholder_id

