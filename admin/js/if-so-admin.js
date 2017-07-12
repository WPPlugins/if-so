(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	// Global Vars
	var datetime_index = 0; // Used to re-enable datetime when adding new item
	var removed_items = 0; // number of removed items
	var isScrolled = false; // Indication for first scroll (new trigger)

	// Enable tooltip

	function activeTooltip(items) {	
		$(document).tooltip({
			items: items,
			track: true,
			show: null, // show immediately
			open: function(event, ui)
			{
			    if (typeof(event.originalEvent) === 'undefined')
			    {
			        return false;
			    }

			    var $id = $(ui.tooltip).attr('id');

			    // close any lingering tooltips
			    $('div.ui-tooltip').not('#' + $id).remove();

			    // ajax function to pull in data and add it to the tooltip goes here
			},
			close: function(event, ui)
			{
			    ui.tooltip.hover(function()
			    {
			        $(this).stop(true).fadeTo(400, 1); 
			    },
			    function()
			    {
			        $(this).fadeOut('400', function()
			        {
			            $(this).remove();
			        });
			    });
			}
		});
	}

	activeTooltip(".ifso_tooltip");

	 
	$(document).ready(function () {
		 // Enable Time/Day Schedule
		 $(".date-time-schedule").dayScheduleSelector({
	        
	        // days: [1, 2, 3, 5, 6], 	
	        startTime: '0:00',
	        endTime: '24:00',
	        interval: 60
	        
	      });

		// Enable DateTimePicker
		$('.datetimepicker').datetimepicker();
		
		$(".date-time-schedule").on('selected.artsy.dayScheduleSelector', function (e, selected) {
		  /* selected is an array of time slots selected this time. */
			//alert("HEY");
			//console.log(selected);
			console.log($(this).data('artsy.dayScheduleSelector').serialize());
		});

		// create repeater
		$(document).on( 'click', '#reapeater-add', function() {
			var repeaterItemTemplate = $('#repeater-template').html();
			var index = $('.reapeater-item').length - 1 - removed_items;

			var versionInstructions = "";

			if (index == 0) {
				versionInstructions = "Select a condition, the content will be displayed only if it&apos;s met";
			} else if (index == 1) {
				versionInstructions = "Appears only if option A is not realized";
			} else {
				versionInstructions = "Appears only if option A-"+String.fromCharCode(64+index)+" are not realized";
			}

			datetime_index += 1;
			
			repeaterItemTemplate = repeaterItemTemplate.replace('{version_number}', index+1);
			repeaterItemTemplate = repeaterItemTemplate.replace(/{datetime_number}/g, datetime_index);
			repeaterItemTemplate = repeaterItemTemplate.replace('{version_char}', String.fromCharCode(65+index));
			repeaterItemTemplate = repeaterItemTemplate.replace(/index_placeholder/g, (index));
			repeaterItemTemplate = repeaterItemTemplate.replace('{version_instructions}', versionInstructions);
			// repeaterItemTemplate = repeaterItemTemplate.replace('cloned-index', 'cloned'+(index));
			
			$('.reapeater-item').last().after(repeaterItemTemplate);
			var clonedElement = $('.reapeater-item-cloned').last();
			clonedElement.find('textarea').addClass('textarea'+index);

			var data = {
				'action': 'load_tinymce_repeater',
				'nonce': nonce,
				'editor_id': (index)
			};
				
			jQuery.post(ajaxurl, data, function(response) {
				
				clonedElement.find('.repeater-editor-wrap').append(response);
				
				var editors = ['repeatable_editor_content'+(index)];
				tinyMCE_bulk_init(editors);
				
				clonedElement.slideDown();
				$('.post-type-ifso_triggers #post').validator('update');

				 $(".date-time-schedule").dayScheduleSelector({
			        
			        // days: [1, 2, 3, 5, 6], 	
			        startTime: '0:00',
			        endTime: '24:00',
			        interval: 60
			        
			      });

				$(".date-time-schedule").on('selected.artsy.dayScheduleSelector', function (e, selected) {
				  /* selected is an array of time slots selected this time. */
					//console.log(selected);
					//console.log
				});

				// Re-Enable DateTimePicker
				$('.datetimepickercustom-' + datetime_index).datetimepicker();

				if (!isScrolled) {
					isScrolled = true;
					setTimeout(function() {
						scrollToElement(clonedElement);
					}, 300);
				} else {
					scrollToElement(clonedElement);
				}


			});
		});

		// handle repeater item delete
		$(document).on( 'click', '.repeater-delete', function() {
			// Check if trying to remove testing-mode item
			var $repeaterParent = $(this).closest(".reapeater-item");

			if($repeaterParent.find(".circle-active").length)
				alert("You can't remove an item that is marked in Testing Mode, \n \
						you must disable the Testing Mode mark beforehand.");
			else if(confirm('Are you sure you want to delete this rule?')) {
				removed_items++;
				var itemWrap = $(this).closest('.reapeater-item');
				itemWrap.slideUp( "slow", function() {
					// itemWrap.html("");
					// itemWrap.removeClass("reapeater-item");
					// itemWrap.removeClass("reapeater-item-cloned");
					itemWrap.find(".rule-toolbar-wrap").removeClass("rule-toolbar-wrap");
					itemWrap.find('select').remove();
					itemWrap.find('input').remove();
					// itemWrap.removeClass("reapeater-item-cloned");
					// itemWrap.remove();
					$('.rule-toolbar-wrap').each(function(index){
						var newIndex = index - 1;
						var versionNumber = newIndex+1;
						var templateTitle = "Personalized Content – "+jsTranslations['Version']+" {version_char}";
						var versionInstructions;
						var switchWrap = $(this).closest('.rule-wrap');

						if (newIndex == 0) {
							versionInstructions = "Select a condition, the content will be displayed only if it's met:";
						} else if (newIndex == 1) {
							versionInstructions = "Appears only if option A is not realized:";
						} else {
							versionInstructions = "Appears only if option A-"+String.fromCharCode(65+newIndex)+" are not realized:";
						}

						if ($(this).find('.version-alpha').text() != templateTitle) {
							switchWrap.find('.versioninstructions').text(versionInstructions);
							$(this).find('.version-count').text(versionNumber);
							$(this).find('.version-alpha').text("Personalized Content – "+jsTranslations['Version']+' '+String.fromCharCode(65+newIndex));
						}
					});	
				});
			}
		});
		
		// toggle PHP code
		$(document).on( 'click', '.php-shortcode-toggle-link', function() {
			$('.php-shortcode-toggle-wrap').slideToggle( "slow", function() {
				
			});
		});
		
		$('.post-type-ifso_triggers #post').validator().on('submit', function (e) {
			// Updating all the schedule data with their correspond hidden input
			$(".date-time-schedule").each(function() {
				var $elem = $(this);
				var $parent = $elem.parent();
				var scheudleInput = $parent.find(".schedule-input");

				scheudleInput.val(JSON.stringify($elem.data('artsy.dayScheduleSelector').serialize()));
			});


			if (e.isDefaultPrevented()) {
				// handle the invalid form...
			} else {
				// was removed in order to allow saving empty content
				/*var isValid = true;
				// everything looks good!
				$(".repeater .reapeater-item").each(function(){
					// check each trigger, if value exists check its equivalent wysiwyg
					var triggerTypeValue = $(this).find('.trigger-type').val();
					var triggerContent = $.trim($(this).find('iframe').contents().find('body').text());
					
					if(triggerContent == '' && triggerTypeValue != '') {
						$(this).find('.wp-editor-wrap').addClass('wysiwyg-not-valid');
						isValid = false;
					}
					else if(triggerContent != '' && triggerTypeValue == '') {
						//$(this).find('.wp-editor-wrap').addClass('wysiwyg-not-valid');
						isValid = false;
						var triggerType = $(this).find('.trigger-type');
						var triggerTypeWrap = triggerType.closest('.form-group');
						triggerTypeWrap.addClass('has-danger').addClass('has-error');
						triggerType.on( 'change', function() {
							triggerTypeWrap.removeClass('has-danger').removeClass('has-error');
						});
					}
				});
				if(!isValid) e.preventDefault();*/
			}
		})

		function platform_symbols($elem) {
			var selectedOptionLabel = $elem.find(':selected')[0].label;
			var switchWrap = $elem.closest('.rule-wrap');
			var platSymbol = switchWrap.find(".platform-symbol");

			if (selectedOptionLabel == "Facebook Ads") {
				platSymbol.html("");
			} else if (selectedOptionLabel == "Google Adwords"){
				platSymbol.html("{lpurl}?");
			}
		}
		
		$(document).on( 'change', '.advertising-platforms-option', function() {
			platform_symbols($(this));
		});

		$(document).on( 'change', '.rule-wrap select', function() {
			var selectedOption = $(this).find(':selected');
			var switchWrap = $(this).closest('.rule-wrap');
			var ruleToolbarWrap = switchWrap.find('.rule-toolbar-wrap');
			var nextFieldAttr = selectedOption.data('next-field');
			var resetFieldsDataAttr = selectedOption.data('reset');
			var closestLeftPanel = $(this).closest('.col-md-3');
			var textarea = switchWrap.find("textarea");

			// reset fields
			if (typeof resetFieldsDataAttr !== 'undefined') {
				var resetFields = resetFieldsDataAttr.split('|');
				$.each( resetFields, function( key, resetAttrValue ) {
					switchWrap.find("[data-field*='" + resetAttrValue + "']").hide();
					switchWrap.find("[data-field*='" + resetAttrValue + "']").val("").prop('selectedIndex', 0);
					switchWrap.find("[data-field*='" + resetAttrValue + "']").prop('required', false);

					// Treat special data-fields
					if (resetAttrValue == "advertising-platforms-selection") {
						// switchWrap.find("[data-field*='" + resetAttrValue + "']").trigger('change');
						var elem = switchWrap.find("[data-field*='" + resetAttrValue + "']");
						platform_symbols(elem);
					}
				});
			}
			
			if (resetFieldsDataAttr.indexOf("locked-box") != -1) {
				ruleToolbarWrap.removeClass("rule-toolbar-wrap-clear");
			}

			if (typeof nextFieldAttr === 'undefined') return;

			var nextFields = nextFieldAttr.split('|');
			$.each( nextFields, function( key, nextAttrValue ) {
				switchWrap.find("[data-field*='" + nextAttrValue + "']").show();
				switchWrap.find("[data-field*='" + nextAttrValue + "']").prop('required', true);
			});

			if (nextFields.indexOf("locked-box") == -1) {
				ruleToolbarWrap.removeClass("rule-toolbar-wrap-clear");
			} else {
				// Locked trigger
				ruleToolbarWrap.addClass("rule-toolbar-wrap-clear");
			}

			var newTextAreaHeight = closestLeftPanel.height() - 60;
			if (newTextAreaHeight < 250) newTextAreaHeight = 250;
			// alert(newTextAreaHeight);
			textarea.css("height", newTextAreaHeight);

		});
		
		// update query string text in the instruction box
		$(document).on( 'keyup', "input[data-field='url-custom']", function() {
			var inputValue = $(this).val();
			
			var isValid = true;
			$("input[data-field='url-custom']").not(this).each(function( index ) {
				if($(this).val() != '') {
					if(inputValue == $(this).val()) {
						// handle duplicated query string trigger
						isValid = false;
					}
				}
			});
			
			if(!isValid) {
				// handle invalid query string
				$(this).closest('.form-group').addClass('has-danger').addClass('has-error');
				$(this).after('<div class="help-block">'+jsTranslations['translatable_dupplicated_query_string_notification_trigger']+'</div>');
				
				$('#publishing-action').append('<div class="query-string-err-notification">'+jsTranslations['translatable_dupplicated_query_string_notification_publish']+'!</div>');
			}
			else {
				// query string is valid
				$(this).closest('.form-group').removeClass('has-danger').removeClass('has-error');
				$(this).closest('.form-group').find('.help-block').remove();
				$('#publishing-action .query-string-err-notification').remove();
			}
			
			var queryStringTyped = ($(this).val() == '') ? 'your-query-string' : $(this).val();
			$(this).closest('.rule-wrap').find('.instructions b').text(queryStringTyped);
		});

		// update query string text in the instruction box
		$(document).on( 'keyup', "input[data-field='advertising-platforms-selection']", function() {
			var inputValue = $(this).val();
			
			var isValid = true;
			$("input[data-field='advertising-platforms-selection']").not(this).each(function( index ) {
				if($(this).val() != '') {
					if(inputValue == $(this).val()) {
						// handle duplicated query string trigger
						isValid = false;
					}
				}
			});
			
			if(!isValid) {
				// handle invalid query string
				$(this).closest('.form-group').addClass('has-danger').addClass('has-error');
				$(this).after('<div class="help-block">'+jsTranslations['translatable_dupplicated_query_string_notification_trigger']+'</div>');
				
				$('#publishing-action').append('<div class="query-string-err-notification">'+jsTranslations['translatable_dupplicated_query_string_notification_publish']+'!</div>');
			}
			else {
				// query string is valid
				$(this).closest('.form-group').removeClass('has-danger').removeClass('has-error');
				$(this).closest('.form-group').find('.help-block').remove();
				$('#publishing-action .query-string-err-notification').remove();
			}
			
			var queryStringTyped = ($(this).val() == '') ? 'the-name-you-choose' : $(this).val();
			$(this).closest('.rule-wrap').find('.instructions b').text(queryStringTyped);
		});
		
		// set custom Add New link active
		if(window.location.href.indexOf("post-new.php?post_type=ifso_triggers") > -1) {
			$('a[href="'+window.location.href+'"]').closest('li').addClass('current');
		}
		
	});



	// define the skeleton of the overlay
	var overlayDivHTML = '<div class="ifso-tm-overlay"><span class="text">Testing Mode</span></div>';
	var overlayFreezeHTML = '<div class="ifso-freeze-overlay ifso_tooltip" title="Inactive mode"></div>';
	var selectedTestingMode = false;

	function disableTestingMode($elem, $repeaterParent, isDefaultRepeater) {
		// before appending, removing all the 'ifso-tm-overlay' present
		// due to prior appending
		$(".ifso-tm-overlay").remove();
		$("#tm-input").attr("value", "");
	}

	function activateTestingMode($elem, $repeaterParent, isDefaultRepeater) {
		var versionIndex = 0;
		var i = 0;

		// append 'overlayDiv' to any version
		$(".reapeater-item").each(function() {
			// iterate over each 'rule-item' class
			// and append 'overlayDiv' at the end
			// * Skipping the current .rule-item
			// * to not overlay the selected testing mode item

			var $elem = $(this);
			i++;

			if (!$elem.is($repeaterParent)) // if not the selected repeater
				$elem.append(overlayDivHTML);
			else
				versionIndex = i;
		});

		// append 'overlayDiv' to the default content
		// if not selected the default content
		if (!isDefaultRepeater)
			$(".default-repeater-item").append(overlayDivHTML);
		else
			versionIndex = 0; // indicating default content

		$("#tm-input").attr("value", versionIndex);
	}

	$(document).on("click", ".ifso-tm", function(e) {		
		var $elem = $(this);
		var $repeaterParent = null;
		var isDefaultRepeater = false;

		// check if active button already exist
		if ($(".circle-active").length)
			selectedTestingMode = true;

		// Check if it's the default repeater
		var defaultRepreaterParent = $(this).closest(".default-repeater-item");

		if (defaultRepreaterParent.length > 0) {
			isDefaultRepeater = true;
			$repeaterParent = defaultRepreaterParent[0];
		}
		else
			$repeaterParent = $(this).closest(".reapeater-item")[0];

		if (selectedTestingMode) {
			selectedTestingMode = false;
			$(".ifso-tm").removeClass("circle-active");
			disableTestingMode($elem, $repeaterParent, isDefaultRepeater);
		} else {
			selectedTestingMode = true;
			$(this).addClass("circle-active");
			activateTestingMode($elem, $repeaterParent, isDefaultRepeater);
		}
	});






	$(document).on("click", ".ifso-freezemode", function(e) {		
		var $elem = $(this);
		var $inptDom = $elem.parent().find(".freeze-mode-val");
		var isActive = ($inptDom.val() == "true") ? true : false;
		var $parent = $elem.parent();
		var $ancParent = $elem.closest('.reapeater-item');

		// Check if trying to freeze testing-mode item
		if($ancParent.find(".circle-active").length) {
			alert("You can't freeze an item that is marked in Testing Mode, \n \
					you must disable the Testing Mode mark beforehand.");
			return;
		}


		// Switch false <-> true
		if (isActive) $inptDom.val("false");
		else $inptDom.val("true");

		if (isActive) {
			// Handle deactive
			$ancParent.find(".ifso-freeze-overlay").remove();
			$parent.removeClass("freeze-overlay-active-container");
			$elem.find(".text").html('<i class="fa fa-play" aria-hidden="true">');
		} else {
			// Handle  active
			$ancParent.append(overlayFreezeHTML);
			$parent.addClass("freeze-overlay-active-container");
			$elem.find(".text").html('<i class="fa fa-pause" aria-hidden="true">');
			activeTooltip(".ifso_tooltip");
		}
	});


	/* Utils Funcs */

	function scrollToElement($elem) {
	    $('html, body').animate({
	        scrollTop: $elem.offset().top - 50
	    }, 1000);
	}


})( jQuery );

function tinyMCE_bulk_init( editor_ids ) {
    var init, ed, qt, first_init, DOM, el, i, qInit;

    if ( typeof(tinymce) == 'object' ) {

        var editor;
        for ( e in tinyMCEPreInit.mceInit ) {
            editor = e;
            break;
        }
        for ( i in editor_ids ) {
            var ed_id = editor_ids[i];
            tinyMCEPreInit.mceInit[ed_id] = tinyMCEPreInit.mceInit[editor];
            tinyMCEPreInit.mceInit[ed_id]['elements'] = ed_id;
            tinyMCEPreInit.mceInit[ed_id]['body_class'] = ed_id;
            tinyMCEPreInit.mceInit[ed_id]['succesful'] =  false;
			tinyMCEPreInit.mceInit[ed_id]['height'] =  '220';
			
			// init qTags
			function getTemplateWidgetId( id ){
				var form = jQuery( 'textarea[id="' + id + '"]' ).closest( 'form' );
				var id_base = form.find( 'input[name="id_base"]' ).val();
				var widget_id = form.find( 'input[name="widget-id"]' ).val();
				return id.replace( widget_id, id_base + '-__i__' );
			}
			
			var qInit;
			if( typeof tinyMCEPreInit.qtInit[ ed_id ] == 'undefined' ){
				qInit = tinyMCEPreInit.qtInit[ ed_id ] = jQuery.extend( {}, tinyMCEPreInit.qtInit[ getTemplateWidgetId( ed_id ) ] );
				qInit['id'] = ed_id;
			}else{
				qInit = tinyMCEPreInit.qtInit[ ed_id ];
			}
			
			if ( typeof(QTags) == 'function' ) {
				jQuery( '[id="wp-' + ed_id + '-wrap"]' ).unbind( 'onmousedown' );
				jQuery( '[id="wp-' + ed_id + '-wrap"]' ).bind( 'onmousedown', function(){
					wpActiveEditor = ed_id;
				});
				QTags( tinyMCEPreInit.qtInit[ ed_id ] );
				QTags._buttonsInit();
				//switchEditors.go( $( 'textarea[id="' + editor_id + '"]' ).closest( '.widget-mce' ).find( '.wp-switch-editor.switch-' + ( getUserSetting( 'editor' ) == 'html' ? 'html' : 'tmce' ) )[0] );
			}
			// END - init qTags
        }

        for ( ed in tinyMCEPreInit.mceInit ) {
            // check if there is an adjacent span with the class mceEditor
            if ( ! jQuery('#'+ed).next().hasClass('mceEditor') ) {
                init = tinyMCEPreInit.mceInit[ed];
				// jQuery( document ).triggerHandler( 'quicktags-init', [ ed ] );
                try {
                    tinymce.init(init);
                    tinymce.execCommand( 'mceAddEditor', true, ed_id );
                } catch(e){
                    console.log('failed');
                    console.log( e );
                }
            }
        }
    }
}
