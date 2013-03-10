App = window.App

class App.View.Main extends Backbone.View

	initialize: ->
		@colors = new App.utils.Colors

		@collection = new App.Collection.Trails
		@collection.bind 'add', @onAddModel

		App.on 'addTrail',    @addTrail
		App.on 'removeTrail', @removeTrail
		App.on 'resetTrails', @resetTrails

		# sub views
		@map          = new App.View.Map         el: @$('#map'),    collection: @collection
		@trails       = new App.View.Trails      el: @$('#trails'), collection: @collection
		@searchTrails = new App.View.SearchTrail el: @$('#search-trail')
		
		# storage
		@storage = new App.Storage('trails')
		@collection.bind 'add',    @addToStorage
		@collection.bind 'remove', @removeFromStorage
		@collection.bind 'reset',  @resetStorage
		@fetchStored()

	onAddModel: (model) =>
		model.set 'color', @colors.get()

	addTrail: (id) =>
		model = new App.Model.Trail id: id
		model.fetch()
		@collection.add model

	removeTrail: (id) =>
		model = @collection.get id
		@collection.remove model

	resetTrails: =>
		@collection.reset()


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

