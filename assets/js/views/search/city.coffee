App = window.App

class App.View.SearchCity extends Backbone.View
	template: _.template($('#search-city-result-template').html())
	events:
		'submit':  'onSubmit'
		'click a': 'onItemClick'

	initialize: ->
		@fieldName = @$('#search-city-name')
		@result = @$('#search-city-result')

		@collection = new App.Collection.SearchTrails
		@collection.url = 'api/cities/search/trails'

		(new AutoCompleteView
			minKeywordLength: 1
			input: @fieldName
			model: new App.Collection.SearchCities
		).render()

	onSubmit: (event) =>
		event.preventDefault()
		@fetch @fieldName.val()
		
	fetch: (query) =>
		@result.empty()
		@collection.fetch
			success: @onFetched
			data: query: query

	onFetched: =>
		if @collection.length
			el = $('<ul>')
			@collection.each (model) =>
				el.append @template data: model.toJSON()
		else
			el = '<span class="warning">Nenhum lote encontrado.</span>'
		@result.empty().append el
		
	onItemClick: (event) =>
		event.preventDefault()
		landholder_id = $(event.currentTarget).data('trail_id')
		App.trigger 'addLand', 'T' + landholder_id

