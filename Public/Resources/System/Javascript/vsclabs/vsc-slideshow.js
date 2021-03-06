// Written by Marcus Gilroy-Ware
// (c) VSC Creative Ltd. 2014

VSC.Slideshow = Class.create({
    
    initialize: function(holderId, options){
        
        // console.log($$('#'+holderId+' .slides .slide'));
        // console.log('#'+holderId);
        
        this.options = {};
        this.name = 'slideshow';
        this.options.holderId = holderId;
        this.options.frequency = (options && options.hasOwnProperty('frequency')) ? options.frequency : 4;
        this.options.transitionDuration = (options && options.hasOwnProperty('duration')) ? options.duration : 0.75;
        // console.log('#'+holderId);
        this.goToSlide($$('#'+holderId+' .slides .slide').first().id);
        this.currentPosition = 0;
        var IDs = [];
        
        $$('#'+holderId+' .slides .slide').each(function(s){
            IDs.push(s.id);
        });
        
        this.IDs = IDs;
        
        if(options && options.hasOwnProperty('autostart')){
            this.options.autostart = options.autostart;
        }else{
            this.options.autostart = false;
        }
        
        if(this.options.autostart){
            this.startAutoAdvance();
        }
        
        if(this.hasNav()){
            this.setupNavEvents();
        }
        
    },
    
    goToSlide: function(slideId){
        if(this.currentSlideId != slideId){
            if($$('#'+this.options.holderId+' .slides #'+slideId).size() > 0){
                
                if(this.currentSlideId){
                    
                    // $$('#'+this.options.holderId+' .slides #'+this.currentSlideId)[0].fade({duration:0.7, transition: Effect.Transitions.sinoidal});
                    if(this.hasOwnProperty('fadeEffect')){
                        // this.fadeEffect.cancel();
                    }
                    
                    this.fadeEffect = new Effect.Fade($$('#'+this.options.holderId+' .slides #'+this.currentSlideId)[0], {duration:this.options.transitionDuration, transition: Effect.Transitions.sinoidal, to:0});
                    
                    // $$('#'+this.options.holderId+' .slides #'+slideId)[0].appear({duration:0.7, transition: Effect.Transitions.sinoidal});
                    if(this.hasOwnProperty('appearEffect')){
                        this.appearEffect.cancel();
                    }
                    
                    this.appearEffect = new Effect.Appear($$('#'+this.options.holderId+' .slides #'+slideId)[0], {duration:this.options.transitionDuration, transition: Effect.Transitions.sinoidal, to:1});
                    this.updateNav(slideId);
                    
                }else{
                    $$('#'+this.options.holderId+' .slides #'+slideId)[0].appear({duration:this.options.transitionDuration});
                }
                
                this.currentSlideId = slideId;
            }
        }   
    },
    
    goToSlideByPosition: function(pos){
        if(this.IDs[pos]){
            this.goToSlide(this.IDs[pos]);
            this.currentPosition = pos;
        }
    },
    
    hasNav: function(){
        return $$('#'+this.options.holderId+' ul.slides-nav').length ? true : false;
    },
    
    updateNav: function(slideId){
        if($('#'+this.options.holderId+' .slides-nav')){
            $$('#'+this.options.holderId+' .slides-nav li').each(function(b){
                b.removeClassName('current');
            });
            $$('#'+this.options.holderId+' .slides-nav li')[this.getSlideIndexFromId(slideId)].addClassName('current');
        }
    },
    
    setupNavEvents: function(){
        $$('#'+this.options.holderId+' ul.slides-nav li').each(function(b, key){
            b.observe('click', this.goToSlideFromClickByPositionFromEvent.bindAsEventListener(this, key));
        }, this);
    },
    
    getSlideIndexFromId: function(slideId){
        var rightIndex = -1;
        
        $A(this.IDs).each(function(sid, index){
            if(sid == slideId){
                rightIndex = index;
            }
        });
        
        return rightIndex;
    },
    
    nextSlide: function(){
        var nextSlidePos = this.currentPosition +1;
        if(nextSlidePos >= this.IDs.length){
            nextSlidePos = 0;
        }
        this.goToSlideByPosition(nextSlidePos);
    },
    
    getAutoAdvanceFrequency: function(){
        return this.options.frequency;
    },
    
    startAutoAdvance: function(){
        // periodicalexecuter on nextslide
        var ns = this.nextSlide.bind(this);
        var freq = this.getAutoAdvanceFrequency.bind(this);
        this.heartbeat = new PeriodicalExecuter(ns, freq());
    },
    
    goToSlideFromClick: function(slideId){
        // stop periodical executer if it is running
        this.pause();
        // go to the requested slide
        this.goToSlide(slideId);
        // set a new timeout on starting the periodicalexecuter
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    goToSlideFromClickByPosition: function(pos){
        this.pause();
        this.goToSlideByPosition(pos);
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    goToSlideFromClickByPositionFromEvent: function(event, pos){
        this.pause();
        this.goToSlideByPosition(pos);
        if(this.options.autostart){
            this.startAutoAdvance();
        }
        event.stop();
    },
    
    goToNextSlideFromClick: function(){
        // stop periodical executer if it is running
        this.pause();
        // go to the requested slide
        this.nextSlide();
        // set a new timeout on starting the periodicalexecuter
        if(this.options.autostart){
            this.startAutoAdvance();
        }
    },
    
    pause: function(){
        if(this.hasOwnProperty('heartbeat')){
            this.heartbeat.stop();
        }
    }
    
});