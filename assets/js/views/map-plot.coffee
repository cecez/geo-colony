App = window.App

class App.View.MapPlot extends Backbone.View

	initialize: (options) ->
		@map = options.map
		@land = options.land
		@model.bind 'remove', @onRemove

	render: =>
		color = @land.get('color')
		@el = new google.maps.Polygon
			paths: @model.get('google_coords'),
			fillColor: color
			strokeColor: color
			strokeWeight: 1

		google.maps.event.addListener @el, 'click', @onClick
		@el.setMap @map

	remove: =>
		@el.setMap null

	onRemove: =>
		@remove()

	onClick: (event) =>
		@view = new App.View.MapPlotWindow model: @model
		@view.render()
		App.trigger 'openInfoWindow', @view, event.latLng
		@model.fetch()



		