App = window.App

class App.Storage
	constructor: (name) ->
		@name = name
		@load()

	load: ->
		data = window.localStorage.getItem(@name)
		@itens = data?.split(',') or []

	save: ->
		window.localStorage.setItem @name, @itens.join(',')

	add: (item) ->
		@itens.push(item) unless _.contains(@itens, item)
		@save()

	remove: (item) ->
		index = @itens.indexOf(item)
		@itens.splice(index, 1) if index isnt -1
		@save()

	reset: ->
		@itens = []
		@save()

