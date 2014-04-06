App = window.App

class App.View.Main extends Backbone.View

	initialize: ->
		@colors = new App.utils.Colors

		@collection = new App.Collection.Lands
		@collection.bind 'add', @onAddLand

		App.on 'addLand',    @addLand
		App.on 'removeLand', @removeLand
		App.on 'resetLands', @resetLands

		# sub views
		(new App.View.Map el: @$('#map'), collection: @collection).render()
		(new App.View.Lands el: @$('#lands'), collection: @collection).render()
		(new App.View.SearchTrail el: @$('#search-trail')).render()
		(new App.View.SearchCity el: @$('#search-city')).render()
		(new App.View.SearchLandholder el: @$('#search-landholder')).render()
		(new App.View.User el: @$('#sign-up-form')).render()

		# storage
		@storage = new App.Storage('lands')
		@collection.bind 'add',    @addToStorage
		@collection.bind 'remove', @removeFromStorage
		@collection.bind 'reset',  @resetStorage
		@fetchStored()

	# Lands
	onAddLand: (model) =>
		model.set 'color', @colors.get()


	# Global events to lands
	addLand: (id) =>
		model = new App.Model.Land id: id
		model.fetch()
		@collection.add model

	removeLand: (id) =>
		model = @collection.get id
		@collection.remove model

	resetLands: =>
		@collection.reset()

	# Storage
	addToStorage: (model) =>
		@storage.add model.id

	removeFromStorage: (model) =>
		@storage.remove model.id

	resetStorage: (collection) =>
		@storage.reset()
		collection.each @addToStorage

	fetchStored: =>
		if @storage.itens.length
			@collection.fetch
				update: true
				data: id: @storage.itens


