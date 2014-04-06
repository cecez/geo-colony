App = window.App

class App.View.MapPlotWindow extends Backbone.View
	tagName: 'article'
	template: _.template($('#map-plot-window-template').html())
	events:
		'click .nav a': 'nav'

	initialize: ->
		@model.bind 'change', @onChange

	render: =>
		@$el.html @template data: @model.toJSON()

	nav: (event) =>
		event.preventDefault()
		height = @$el.height()
		@$('.content').addClass('hide')
		@$('.nav a').removeClass('active')

		target = $(event.currentTarget)
		target.addClass('active')
		$(target.attr('href')).removeClass('hide').css('min-height', height)

	onChange: =>
		@render()

