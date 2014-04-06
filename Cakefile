flour = require 'flour'

# Dev
# ===========

task 'dev', ->
	# disable minifiers and compressions
	flour.minifiers.js = null
	flour.minifiers.css = null
	flour.compilers.styl.compress = false

# Build
# ===========

task 'build', ->
	invoke 'build:css'
	invoke 'build:js'

task 'build:css', ->
	bundle [
		'assets/css/reset.css'
		'assets/css/main.styl'
	], 'public/assets/css/main.css'

task 'build:js', ->
	bundle [
		'assets/js/vendor/underscore.js'
		'assets/js/vendor/backbone.js'
		'assets/js/vendor/backbone-autocomplete.js'
		'assets/js/vendor/infobox.js'

		'assets/js/app.coffee'
		'assets/js/storage.coffee'
		'assets/js/utils.coffee'
		'assets/js/models.coffee'

		'assets/js/views/search/*.coffee'
		'assets/js/views/*.coffee'

		'assets/js/init.coffee'
	], 'public/assets/js/main.js'


# Watch
# ===========

task 'watch', ->
	invoke 'build'

	watch 'assets/css/', -> invoke 'build:css'
	watch 'assets/js/', -> invoke 'build:js'
