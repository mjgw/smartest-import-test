// Fires a custom event every time the document is scrolled. It does so by 
// checking for a change at a set interval (0.05 seconds by default).
// Also passes the current scrollTop and delta (change) in the memo object to make things
// easier for event handlers.

// Thanks to Mike Ross for the getScrollTop function http://www.themikeross.com/scrolltop-javascript-for-all-browsers

(function() {
    
    var ScrollWatcher = {
      
        CURRENT: 0,
        INTERVAL: 0.05,
    
        checkScrollTop: function(pe) {
            var st = ScrollWatcher.getScrollTop();
            if (ScrollWatcher.CURRENT != st) {
                document.fire('scrolled:vertically', { currentScrollTop: st, delta: (st-ScrollWatcher.CURRENT), totalHeight: ScrollWatcher.getDocumentHeight(), windowHeight: ScrollWatcher.getWindowHeight() });
                ScrollWatcher.CURRENT = st;
            }
        },
    
        getScrollTop: function() {
            return (document.documentElement.scrollTop + 
                document.body.scrollTop
            == document.documentElement.scrollTop) ?
            document.documentElement.scrollTop : 
                document.body.scrollTop;
        },
        
        getDocumentHeight: function(){
            
            var body = document.body,
                html = document.documentElement;

            var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
                                   
            return height;
            
        },
        
        getWindowHeight: function(){
            
            var winH = 600;
            
            if (window.innerHeight) {
                winH = window.innerHeight;
            }else if (document.body && document.body.offsetWidth) {
                winH = document.body.offsetHeight;
            }else if (document.compatMode=='CSS1Compat' &&
                document.documentElement &&
                document.documentElement.offsetHeight ) {
                winH = document.documentElement.offsetHeight;
            }

            return winH;
            
        }
    
    };
  
    new PeriodicalExecuter(ScrollWatcher.checkScrollTop, ScrollWatcher.INTERVAL);
  
})();