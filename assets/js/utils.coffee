App = window.App
App.utils = {}

App.utils.coordsToLatLng = (array) ->
	_.map array, (item) ->
		new google.maps.LatLng item.latitude, item.longitude

App.utils.mapInitialConfigs = ->
	center: new google.maps.LatLng(-30.391830328088137, -52.767333984375)
	zoom: 7
	mapTypeId: google.maps.MapTypeId.ROADMAP

App.utils.getMapZoomByBounds = (map, bounds) ->
	max_zoom = map.mapTypes.get(map.getMapTypeId()).maxZoom or 21
	min_zoom = map.mapTypes.get(map.getMapTypeId()).minZoom or 0
	double_pad = 80

	ne = map.getProjection().fromLatLngToPoint(bounds.getNorthEast())
	sw = map.getProjection().fromLatLngToPoint(bounds.getSouthWest())

	worldCoordWidth = Math.abs(ne.x - sw.x)
	worldCoordHeight = Math.abs(ne.y - sw.y)

	width = $(map.getDiv()).width()
	height = $(map.getDiv()).height()

	for zoom in [max_zoom..min_zoom]
		return zoom if worldCoordWidth * (1 << zoom) + double_pad < width and 
					   worldCoordHeight * (1 << zoom) + double_pad < height
	return 0

class App.utils.Colors
	constructor: -> 
		@current = 0
	
	get: ->
		@current = 0 if @current is @colors.length
		@colors[@current++]
	
	reset: ->
		@current = 0

	colors: [
		'#FF0000','#00CC00','#FF7400','#009999','#FFAA00','#1240AB','#FFD300','#CD0074','#A9F16C','#FF7373','#61D7A4','#FFB273','#66A3D2','#FFD073','#7373D9','#FFE773','#996AD6','#FFFF73','#FFFF00','#530FAD','#E40045','#9FEE00','#FFE800','#008500','#A60000','#006363','#A64B00','#06266F','#A67C00','#200772','#A69700','#48036F','#85A000','#85004B','#439400','#A600A6','#007241','#A62F00','#FF9B73','#5CCCCC','#FFC373','#6C8CD5','#FFDC73','#876ED7','#FFF273','#AD66D5','#E3FB71','#E667AF','#3914AF','#CCF600','#7109AA','#FF4900','#67E300','#00AF64','#FF9200','#0B61A4','#FFBF00','#1B1BB3','#033E6B','#A66F00','#090974','#A68900','#330570','#A6A600','#6C006C','#679B00','#94002D','#67E667','#D25FD2','#C9F76F','#F16D95'
	]