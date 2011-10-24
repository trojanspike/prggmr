(function(){
    'use strict'
    
    if (!$.prggmr) {
        $.prggmr = {}
    }
    
    // prggmr pages to index
    $.prggmr.pages = [
        'engine.html',
        'event.html'
    ];
    
    // the index
    $.prggmr.indexed = []
    
    // simply word shortener
    function shorten(text) {
        if (text.length <= 60) return text;
        var returnText = '';
        for (var i=0;i!=60;i++) {
            returnText += text[i];
        }
        return returnText+'...';
    }
    
    // the search
    $.prggmr.initSearch = function(){
     
        // index the content
        for (var i = 2; i != 4; i++) {
            $('h'+i).each(function(){
                
                var heading = $(this).text().replace("\n","").replace("\n","");
                var text = $(this).next().text().replace("\n","").replace("\n","");
                var id = $(this).attr('id');
                
                $.prggmr.indexed.push({
                    'heading': heading,
                    'text': text,
                    'id': id,
                    'type': 'h'+i
                });
            });
        }
        
        // make the search
        $('body').append($('<div/>',{
            'id': 'prggmr-search',
            'html': '<div class="icon"><img src="js/toolbar_find.png" /></div>'+
                    '<div class="finder"><div class="input"><input class="search-input" type="text" />'+
                    '</div><div class="results"></div></div>'
        }));
        
        // search DOM objects
        var search = $('#prggmr-search');
        var finder = $('#prggmr-search .finder');
        var icon = $('#prggmr-search .icon');
        var input = $('#prggmr-search input');
        var results = $('#prggmr-search .results');
        
        // show the search
        icon.click(function(){
            if (finder.is(':hidden')) {
                icon.addClass('active');
                finder.fadeIn(150);
                input.focus();
                $('div.document').one('click', function(){
                    icon.click();
                });
            } else {
                icon.removeClass('active');
                finder.fadeOut(150);
            }
        });
        
         // search
        $.prggmr.search = function(value) {

            var top = []
            var rel = []
            var regx = new RegExp(value.replace(/^\s\s*/, '').replace(/\s\s*$/, ''));
            var tmp = {};
            
            // two arrays are kept
            // one for top results 
            // the other for relevant
            
            $.each($.prggmr.indexed, function(_i, _array) { 
                if (regx.test(_array['heading'].toLowerCase()) !== false && !tmp[_i]) {
                    tmp[_i] = true;
                    top.push($.prggmr.indexed[_i]);
                }
            });

            $.each($.prggmr.indexed, function(_i, _array) { 
                if (regx.test(_array['text'].toLowerCase()) !== false && !tmp[_i]) {
                    tmp[_i] = true;
                    rel.push($.prggmr.indexed[_i]);
                }
            });

            if (top.length === 0) {
                top.push({
                    'id': 'null',
                    'heading': 'No results found',
                    'text': null
                });
            } 
            
            if (rel.length === 0) {
                rel.push({
                    'id': 'null',
                    'heading': 'No results found',
                    'text': null
                });
            }
            
            return {
                'rel' : rel,
                'top' : top
            }
        }
            
        input.keyup(function(){

            var result = $.prggmr.search($(this).val())
            var top = result['top'];
            var rel = result['rel'];

            results.html('');
            results.append($('<ul/>', {
                'id': 'result-list'
            }));
            var result_list = $('#result-list');
            
            result_list.append($('<li/>', {
                'class': 'heading',
                'html': 'Top Results'
            }));
            
            $.each(top, function(_i, _item){
                result_list.append($('<li/>',{
                    'class': 'result',
                    'html': sprintf(
                        '<a href="%s">%s</a><div class="copy">%s</div>',
                        '#'+_item.id,
                        shorten(_item.heading),
                        _item.text
                    )
                }));
            });
            
            result_list.append($('<li/>', {
                'class': 'heading',
                'html': 'Relevant'
            }));
            
            $.each(rel, function(_i, _item){
                result_list.append($('<li/>',{
                    'class': 'result',
                    'html': sprintf(
                        '<a href="%s">%s</a><div class="copy">%s</div>',
                        '#'+_item.id,
                        shorten(_item.heading),
                        _item.text
                    )
                }));
            });
        });
        
        /**
         * Passes through next and prev search result items.
         *
         * true = next, false = prev
         */
        $.prggmr.keyResults = function(dir) {
            if (finder.is(':hidden')) return true;
            var found = false;
            $('ul li', results).each(function(){
                console.log($(this));
                if ($(this).hasClass('active')) {
                    found = true;
                    var item = function(a){
                        var t = (dir) ? a.next() : a.prev();
                        if (t.hasClass('heading')) this.call(t);
                        return t;
                    }($(this));
                    console.log(item);
                    if (!found) {
                        // reverse
                        if (item.length === 0) {
                            var item = (dir) ? $('ul li.result:first', results) : $('ul li.result:last', results);
                        }
                        // how?
                        //if (item.length === 0) return false;
                        found = true;
                        $(this).trigger('mouseleave');
                        item.trigger('mouseenter');
                        item.focus();
                    }
                }
                return true;
            });
            
            if (!found) {
                $('ul li:first', results).trigger('mouseenter');
            }
            return false;
        }
        
        
        // Key combinations
        
        // key comb 'alt+space' shows search
        $(document).bind('keydown', 'alt+space', function(){
            icon.click();
            return false;
        });
        
        // alt+down/alt+up moves through results
        $(document).bind('keydown', 'alt+down', function(){
            $.prggmr.keyResults(true);
            // disable anything else
            return false;
        });
        
        // alt+down/alt+up moves through results
        $(document).bind('keydown', 'alt+up', function(){
            $.prggmr.keyResults(false);
            // disable anything else
            return false;
        });
        
        // alt+enter goes to current result
        $(document).bind('keydown', 'alt+enter', function(){
            console.log('year');
            $('ul li', results).each(function(){
                if ($(this).hasClass('active')) {
                    $(this).click();
                }
            });
            return false;
        });
        
        $('ul li', results).live('mouseenter', function(){
            $(this).addClass('active');
            $('div.copy', this).fadeIn(100);
        }).live('mouseleave', function(){
            $(this).removeClass('active');
            $('div.copy', this).fadeOut(100);
        });
        
        // SEARCH USING ?search=SEARCH
        var PARAMS = getUrlVars();
        
        if (PARAMS['search']) {
            console.log($.prggmr.search(PARAMS['search']));
            $('html').html(JSON.stringify($.prggmr.search(PARAMS['search'])));
        }
    }
    
})(jQuery);