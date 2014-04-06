App = window.App

class App.View.SearchTrail extends Backbone.View
	events:
		'disabled': 'onDisabled'
		'submit':   'onSubmit'

	initialize: ->
		@fieldColony = @$('#search-trail-colony')
		@fieldTrail  = @$('#search-trail-trail')
		@button = @$('#search-trail-button')

		@trailsView = new App.View.SearchTrailsSelect
			el: @fieldTrail
			collection: new App.Collection.SearchTrails

		@fieldColony.on 'change', @onChangeSelectedColony

	onChangeSelectedColony: =>
		colony_id = @fieldColony.val()
		if colony_id is '-'
			@trailsView.collection.reset()
		else
			@trailsView.collection.url = 'api/colonies/' + colony_id + '/search/trails'
			@trailsView.collection.fetch()

	onSubmit: (event) =>
		event.preventDefault()
		trail_id = @fieldTrail.val()
		unless trail_id is '-' or @button.attr('disabled')
			App.trigger 'addLand', 'T' + trail_id

	onDisabled: (event, data) =>
		@button.attr('disabled', data)

