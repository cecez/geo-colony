App = window.App

App.View.TrailOption = Backbone.View.extend
	tagName: 'option'
	initialize: ->
		_.bindAll @, 'render'
	render: (event) ->
		@$el.attr('value', @model.get('id')).html(@model.get('name'))
		return this


App.View.TrailsSelect = Backbone.View.extend
	initialize: ->
		_.bindAll @, 'addOne', 'addAll'
		@collection?.bind 'reset', @addAll
	addOne: (model) ->
		view = new App.View.TrailOption model: model
		@views.push view
		@$el.append view.render().el
	addAll: ->
		_.each(@views, (view) -> view.remove())
		@views = []
		@collection.each @addOne

		disabled = @$el.children().length <= 1
		@$el.attr('disabled', disabled)
		@$el.trigger('disabled', disabled)


App.View.TrailsSearch = Backbone.View.extend
	events:
		'disabled': 'disabled'

	initialize: ->
		_.bindAll @, 'changeSelectedColony'

		@colony = @$('#trails-search-colony')
		@trail  = @$('#trails-search-trail')
		@button = @$('#trails-search-button')

		@trailsSelectView = new App.View.TrailsSelect 
			el: @trail
			collection: new App.Collection.Trails

		@colony.on 'change', @changeSelectedColony

	changeSelectedColony: ->
		colony_id = @colony.val()
		if colony_id is '-1'
			@trailsSelectView.collection.reset()
		else
			@trailsSelectView.collection.url = 'api/colonies/' + colony_id + '/trails'
			@trailsSelectView.collection.fetch()

	disabled: (event, data) ->
		@button.attr('disabled', data)

