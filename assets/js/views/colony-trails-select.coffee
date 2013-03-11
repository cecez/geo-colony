App = window.App

class App.View.ColonyTrailsSelect extends Backbone.View
    initialize: ->
        @collection?.bind 'sync', @onSync
    
    onSyncModel: (model) =>
        view = new App.View.ColonyTrailOption model: model
        @views.push view
        @$el.append view.render().el
    
    onSync: =>
        _.each(@views, (view) -> view.remove())
        @views = []
        @collection.each @onSyncModel

        disabled = @$el.children().length <= 1
        @$el.attr('disabled', disabled)
        @$el.trigger('disabled', disabled)