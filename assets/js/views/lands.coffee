App = window.App

class App.View.Lands extends Backbone.View
	template: _.template($('#lands-template').html())
	events:
		'click .land':               'zoomLand'
		'click .remove-link':        'removeLand'
		'click .reset-lands-button': 'resetLands'

	initialize: ->
		@collection.bind 'add',    @onAddLand
		@collection.bind 'change', @onChangeLand
		@collection.bind 'reset',  @onResetLands
		@collection.bind 'remove', @onRemoveLand
		@collection.bind 'all',    @onAllLands

		@list = @$('.lands-list')

	checkEmpty: =>
		@$el.toggleClass('empty', !@collection.size())

	# Land
	getLand: (model) =>
		@list.find("[data-land_id=#{model.id}]")
	
	renderLand: (model) ->
		@template data: model.toJSON()

	# Land events
	zoomLand: (event) =>
		el = $(event.currentTarget)
		App.trigger 'zoomLand', el.data('land_id')

	removeLand: (event) =>
		link = $(event.currentTarget)
		el = link.closest('.land')
		App.trigger 'removeLand', el.data('land_id')

	resetLands: =>
		App.trigger 'resetLands'

	# Collection
	onAddLand: (model) =>
		@list.append @renderLand(model)
		@list.scrollTop(9000)

	onChangeLand: (model) =>
		@getLand(model).replaceWith @renderLand(model)

	onResetLands: =>
		@list.empty()

	onRemoveLand: (model) =>
		@getLand(model).remove()

	onAllLands: =>
		@checkEmpty()