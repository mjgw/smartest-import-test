Smartest.HelpViewer = Class.create({
    
    isVisible: false,
    history: [],
    current: null,
    
    load: function(help_id){
        
        var url = sm_domain+'ajax:smartest/help/ajax/view/'+help_id;
        
        if(!this.isVisible){
            this.showNew();
        }
        
        this.updateTo(url);
        // this.history
        this.current = help_id;
        
    },
    
    showNew: function(){
        if(!this.isVisible){
            // $('help-updater').update("<p>Loading...</p>");
            $('help').appear({duration: 0.4, to: 0.9});
            this.isVisible = true;
            if(MODALS.isVisible){
                MODALS.hideViewer();
            }
            document.observe('keyup', this.hideOnEscKey.bindAsEventListener(this));
        }
    },
    
    updateTo: function(url){
        
        $('help-updater').fade({duration: 0.25});
        
        setTimeout(function(){
            
            $('help-updater').update('');
            $('help-updater').show();
            
            // var loading = new Element('img', {src: sm_domain+'Resources/System/Images/ajax-loader.gif', id: 'help-loader'});
            var loading = $('primary-ajax-loader-element').clone(true);
            loading.addClassName('left');
            $('help-updater').appendChild(loading);
            
            new Ajax.Updater($('help-updater'), url, {evalScripts: true, onComplete: function(){
                var helpScrollbar = new Control.ScrollBar('help-updater', 'help-scrollbar-track');
                $('modal-title').update(title);
            }});

            $('help-updater').appear({duration: 0.4});
            
        }, 252);
        
    },
    
    hideViewer: function(){
        if(this.isVisible){
            $('help').fade({duration: 0.3});
            setTimeout(function(){$('help-updater').update('');}, 302);
            this.isVisible = false;
            document.stopObserving('keyup');
        }
    },
    
    hideOnEscKey: function(evt){
      
        evt = evt || window.event;
        if (evt.keyCode == 27) {
          this.hideViewer();
        }
      
    },
    
    back: function(){
        
    }

});