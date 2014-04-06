App = window.App

class App.View.Map extends Backbone.View

	initialize: ->
		@collection.bind 'change', @onChangeLands
		@collection.bind 'reset',  @onResetLands
		@collection.bind 'remove', @onRemoveLand

		App.on 'zoomLand',         @zoomLand
		App.on 'openInfoWindow',   @openInfoWindow
		App.on 'closeInfoWindow',  @closeInfoWindow

	render: ->
		@map = new google.maps.Map @el, App.utils.mapInitialConfigs()
		google.maps.event.addListener @map, 'click', @onMapClick

	zoomLand: (id) =>
		return unless @mapReady()
		@closeInfoWindow()
		bounds = @landBounds @collection.get(id)
		zoom = App.utils.getMapZoomByBounds @map, bounds
		@map.setCenter(bounds.getCenter())
		@map.setZoom(zoom)		

	landBounds: (model) =>
		unless model.has('bounds')
			bounds = new google.maps.LatLngBounds()
			extend = _.bind(bounds.extend, bounds)
			model.get('plots').each (plot) ->
				_.each plot.get('google_coords'), extend
			model.set('bounds', bounds)
		model.get('bounds')
	
	# Map
	mapReady: =>
		# sometimes map doesnt have all properties
		@map.mapTypes.get(@map.getMapTypeId())

	onMapClick: =>
		@closeInfoWindow()

	# Info Window
	closeInfoWindow: =>
		@infoWindow?.close()

	openInfoWindow: (view, position) =>
		@closeInfoWindow()
		@infoWindow = new InfoBox
			content: view.el
			position: position
			alignBottom: true
			pixelOffset: new google.maps.Size(0, -10)
			infoBoxClearance: new google.maps.Size(0, 50)
			boxClass: 'info-window'
			closeBoxLabel: 'Fechar'
			boxStyle:
				width: '350px'

		@infoWindow.open @map

	# Colection
	onChangeLands: (model) =>
		@closeInfoWindow()
		if _.isArray model.get('plots')
			@addLand(model)

	onRemoveLand: (model) =>
		plots = model.get('plots')
		plots.remove(plots.models)
	
	onResetLands: (c, collection) =>
		@closeInfoWindow()
		_.each collection.previousModels, @onRemoveLand

	addLand: (model) =>
		plots = _.map model.get('plots'), (data) =>
			@addPlot data, model
		model.set 'plots', new App.Collection.Plots(plots)
		@zoomLand model.id

	addPlot: (data, land) =>
		data.google_coords = App.utils.coordsToLatLng(data.plot_coordinates)
		model = new App.Model.Plot(data)
		(new App.View.MapPlot map: @map, model: model, land: land).render()
		model

	
