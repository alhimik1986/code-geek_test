/*
* jQuery tagit
*
* Copyright 2011, Nico Rehwaldt
* Released under the MIT license
* 
* 
* Inspired by jQuery UI tagit (http://aehlke.github.com/tag-it/) but with 
* cleaner syntax, less styles and bootstrap (http://twitter.github.com/bootstrap) 
* support.
* 
* @version v1.0 (06/2011)
* @author nico.rehwaldt
* 
* Starting with a simple ul element
* 
*     <ul id="tags"></ul>
* 
* Make it a tagit element
* 
*     $("#tags").tagit();
* 
* yields the following markup:
* 
*     <ul id="tags" class="fake-input tagit" tabindex="1">
*        <li class="tag"><span>Hello World!</span><input type="hidden" name="tag" value="Hello World!"><a class="close">x</a></li>
*        <li class="tag"><span>Bar</span><input type="hidden" name="tag" value="Bar"><a class="close">x</a></li>
*        <li class="tag"><span>Foo</span><input type="hidden" name="tag" value="Foo"><a class="close">x</a></li>
*        <li class="tagit-edit-handle">
*            <input type="text" class="no-style"><ul></ul>
*        </li>
*     </ul>
* 
* Feel free to style it (as you wish).
* 
* Methods
* =======
* 
* addTag(name): Adds a tag with the given name
* 
* Options
* =======
* var options = { 
*     // Field to be sent in form for each selected tag
*     field: "tags", 
*     
*     // Source to autocomplete from
*     tags: []       
*     
*     // Function to provide the tags
*     tags: function(input) {
*         // return tags based on input variations
*     }
* };
*/
(function($) {
    var tagit = {
        "addTag": function(tag) {
			
			// Фильтрую почтовые адреса
			if (typeof tag === "string") {
				// достаю email-адрес из скобок < >
				var str = tag.replace(/^[\w\W]*\</g, "");
				str = str.replace(/\>[\w\W]*$/g, "");
				// достаю email-адрес из скобок [ ]
				str = str.replace(/^[\w\W]*\]/g, "");
				str = str.replace(/\[[\w\W]*$/g, "");
				
				// Проверяю валидность email-адреса
				var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
				if (str.match(r) == null) {
					if (str.length != 0 && typeof(notyMessage) === 'function') notyMessage('info', '"' + str + '"' + ' - не адрес почты!');
					return;
				}
			}
			
		
            var element;
            var self = $(this);

            if (typeof tag === "string") {
                
                var selection = $(this).find("input[type=hidden]").filter(function() {
                    return $(this).val() == tag;
                });
                
                // Tag already added
                if (selection.length) {
                    return;
                }
                
                element = $('<li></li>');
            } else {
                element = $(tag);
                tag = element.text();
            }

            var data = self.data("tagit");
            
            var hiddenInput = $('<input type="hidden"/>')
                                    .attr("name", data.field)
                                    .val(tag);

            element
                .empty()
                .append($("<span></span>").text(tag))
                .append(hiddenInput);

            var close = $('<a class="close"></a>');
            close
                .text(unescape("%D7"))
                .click(function() {
                    $(this).parent().remove();
                });
            
            element
                .addClass("tag")
                .append(close);

            if (!$(element).parent().length) {
               element.insertBefore($(".tagit-edit-handle", self));
            }

            self.trigger("tagit-tag-added", [tag]);
			
			return true;
        }, 
        
        removeTag: function(tag) {
            var self = $(this);
            
            var selection = self.find("input[type=hidden]").filter(function() {
                return $(this).val() == tag;
            });
            
            if (selection.length) {
                selection.parent().remove();
                self.trigger("tagit-tag-removed", [tag]);
            }
        },
        
        getTags: function() {
            return $.map($(this).find("input[type=hidden]"), function(e) {
                return $(e).val();
            });
        }
    };

    $.extend($.fn, {
        tagit: function() {
            var args = $.makeArray(arguments);

            var arg0 = args.shift();
            if (tagit[arg0]) {
                return tagit[arg0].apply(this, args);
            }

            return this.each(function() {
                var e = $(this);

                var options = $.extend({}, $.fn.tagit.defaults);
                if ($.isPlainObject(arg0)) {
                    options = $.extend(options, arg0);
                }

                if (e.is(".tagit")) {

                } else {
                    e.data("tagit", options);

                    var input = $('<input type="text" class="no-style" />');
                    var autocomplete = $("<ul></ul>");

                    e.bind("tagit-tag-added", function() {
                        autocomplete.removeClass("open");
                    });

                    /*e.bind("focusin", function(event) {
                        $(this)
                            .addClass("focused")
                            .find("input[type=text]")
                                .focus();
                    }).bind("focusout", function(event) {
                        $(this).removeClass("focused");
                        input.val("");
                    });*/
					// https://github.com/Nikku/jquery-tagit/issues/2
					e.bind("focusin", function(event) {

						$(this)
							.addClass("focused")
							.find("input[type=text]")
								.focus(function (event) {
										event.stopPropagation();
										}).focus();
					})
					.bind("focusout", function(event) {
						$(this).removeClass("focused");
					});
                    
                    input.keydown(function(event) {
                        var self = $(this);
                        var tag = self.val();
						var searchText = tag.toLowerCase();

                        var keyCode = event.which;


                        // tab key pressed
                        if (keyCode == 9) {
                            if (tag && autocomplete.is(".open") && $("li.selected", autocomplete).length) {
								e.tagit("addTag", $("li.selected", autocomplete).text());
								self.val("");
								event.preventDefault();
							} else 
							if (tag) {
                                e.tagit("addTag", self.val());
                                self.val("");
                                
                                event.preventDefault();
                            }
                        } else
                        // enter key pressed
                        if (keyCode == 13) {
                            if (autocomplete.is(".open")) {
                                var selection = $("li.selected", autocomplete);
                                if (selection.length) {
                                    e.tagit("addTag", selection.text());
                                    self.val("");
                                }
                            }

                            event.preventDefault();
                        } else 
                        // up / down arrows pressed
                        if (keyCode == 38 || keyCode == 40) {                                    
                            if (autocomplete.is(".open")) {
                                var elements = $("li", autocomplete);
                                var selection = $(elements).filter(".selected");
                                if (selection.length == 0 && elements.length > 0) {
                                    elements.eq(keyCode == 38 ? elements.length - 1 : 0)
                                            .addClass("selected");
                                } else {
                                    var selector = keyCode == 38 ? "prev" : "next";
                                    var newSelection = selection
                                        [selector]()
                                        .addClass("selected");

                                    if (newSelection.length) {
                                        selection.removeClass("selected");
                                    }
                                }
                                
                                event.preventDefault();
                            }
                        } else
                        // delete key pressed
                        if (keyCode == 8 && !tag) {
							var delElem = self.parent().prev();
							if (delElem.hasClass('tagit-mark-to-delete')) {
								delElem.remove();
							} else {
								delElem.addClass('tagit-mark-to-delete')
							}
                            event.preventDefault();
						} else
                        // Esc key pressed
                        if (keyCode == 27) {
							autocomplete.removeClass("open");
							// Очищаю помеченные на удаление теги
							e.find('.tagit-mark-to-delete').each(function(){
								$(this).removeClass('tagit-mark-to-delete');
							});
                        } else {
                            tag = (tag + String.fromCharCode(keyCode)).toLowerCase();
                            if (tag) {
                                var tagitBase = $(this).parents(".tagit")
                                var tags = tagitBase.data("tagit").tags;
                                var currentTags = tagitBase.tagit("getTags");
                                
                                if ($.isFunction(tags)) {
                                    tags = tags(tag);
                                }
                                
                                autocomplete.empty();
                                
                                var availableTags = $.grep(tags, function(e) {
                                    return $.inArray(e, currentTags) == -1;
                                });
								
								
								var selectedTags = [];
								e.find("input[type=hidden]").each(function(i, e){
									selectedTags.push($(e).val());
								});
								availableTags = availableTags.sort();
                                var count = 0;
                                $.each(availableTags, function(i, e) {
                                    if (e.toLowerCase().indexOf(searchText) != -1) {
										if ($.inArray(e, selectedTags) == -1) {
											autocomplete.append($("<li></li>").text(e));
											count++;
										}
                                    }
									if (count > options.maxCount) return false;
                                });
                                
                                if (count > 0) {
                                    autocomplete.addClass("open");
                                } else {
                                    autocomplete.removeClass("open");
                                }
                            }
                        }
                    });
					
					/*input.blur(function(event){
						if (e.tagit("addTag", input.val())) {
							$(this).val("");
						}
						event.preventDefault();
					});*/
					
					input.click(function(event){
						var tagitBase = $(this).parents(".tagit");
						var availableTags = tagitBase.data("tagit").tags;
						var count = 0;
						autocomplete.empty();
						
						var selectedTags = [];
						e.find("input[type=hidden]").each(function(i, e){
							selectedTags.push($(e).val());
						});
						availableTags = availableTags.sort();
						var count = 0;
						$.each(availableTags, function(i, e) {
							if (e.toLowerCase().indexOf(input.val()) != -1) {
								if ($.inArray(e, selectedTags) == -1) {
									autocomplete.append($("<li></li>").text(e));
									count++;
								}
							}
							if (count > options.maxCount) return false;
						});
						
						if (count > 0 && ! autocomplete.hasClass('open')) {
							autocomplete.addClass("open");
						} else {
							autocomplete.removeClass("open");
						}
						event.stopPropagation();
					});
					
					input.blur(function(event){
						var target = $(event.target);
						// Такая хитрость, чтобы не добавляла тег из input во время клика по autocomplete
						setTimeout(function(){
							if ( ! $(target).is('.no-class') && ! $(target).is('ul') && ! $(target).hasClass('open')) {
								if (e.tagit("addTag", input.val())) {
									input.val("");
								}
							}
						}, 500);
					});
					
					
                    autocomplete.click(function(event) {
                        var target = $(event.target);
                        if (target.is("li")) {
                            $(e).tagit("addTag", target.text());
							// https://github.com/Nikku/jquery-tagit/issues/2
							$(e).find("input[type=text]").val("");
							event.preventDefault();
                        }
                    });
					
					// blur focus
					$(document).click(function(event) {
						var target = $(event.target);
						
						if ($(target).is('ul') && $(target).hasClass('open')) return;
						autocomplete.removeClass("open");
						
						// Очищаю помеченные на удаление теги
						e.find('.tagit-mark-to-delete').each(function(){
							$(this).removeClass('tagit-mark-to-delete');
						});
					});
					
                    e.append($('<li class="tagit-edit-handle"></li>').append(input).append(autocomplete))
                     .addClass("tagit");

                    $("li:not(.tagit-edit-handle)", e).each(function() {
                        $(e).tagit("addTag", this);
                    });
                }
            });
        }
    });

    $.fn.tagit.defaults = {
        field: "tag",
        tags: [],
		maxCount: 10
    };
})(jQuery);