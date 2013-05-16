(function( $ ) {

	var $this;


	var settings;

  var methods = {
  	init:function (options) {

  		settings = $.extend( {
  			closedHeight:21
		  }, options);

	    //initialize each issue box
	    return this.each(function() {
	    	$this = $(this);

	  		methods.setInputsCurVal($this.find('form'));
	  		if($this.find('.arrow-up').length == 0)
				{
					//if issue name static and issue name aren't same, save
					if($this.find('.issue-name-static').text() != $this.find('.issue-name').val())
					{
						methods.closeIssue($this.find('.input'));
					} else
					{
						methods.closeIssue($this.find('.input'),true);	
					}
					//set to gray if not all vars filled out
					//see if added all issue data and if not, color grey
					var form = $this.find('form');
			    if(form.find('.issue-name').val() != '' && (form.find('.issue-description').val() == '' || form.find('.as-values').val() == ''))
			    {
			    	form.parent().parent().addClass('na');	
			    } else
			    {
			    	//color white
			    	form.parent().parent().removeClass('na');
			    }
				}


				//opening and closing boxes
				$this.find('.arrow').click(function(e) {

					e.stopPropagation();
					var $inp = $(this).parent().find('.input');
					//hasClass not working??
					if($(this).attr('class').indexOf('arrow-up') != -1)
					{
						methods.openIssue();
					} else
					{
						methods.closeIssue();
					}
				})

				//title click and edit button
				$this.find('.issue-name-static').click(function() {
					$(this).hide();
					var $in = $(this).parent().parent().parent().find('.issue-name');
					$in.show();
					$in.focus();
					//show quick save but
					$(this).parent().parent().parent().find('.quick-save-but').show();
					goEditMode($(this).parent().parent().parent().parent());
				})

				$this.find('.issue-name').focusout(function() {
					//see if should rehide input since didn't chagne
					if($(this).val() == $(this).parent().find('.issue-name-static').text() && $(this).val() != "" && $(this).parent().parent().find('.quick-save-but').is(':visible'))
					{
						$(this).hide();
						$(this).parent().find('.issue-name-static').show();
						$(this).parent().find('.quick-save-but').hide();
					}
				})

				$this.find('.issue-name').keypress(function() {
					if($(this).parent().find('.fading-inputs').is(':hidden'))
					{
						$(this).parent().find('.quick-save-but').show();
					}
				})
				//AJAX FOR SAVING ISSUES
				$this.find('.save-button,.quick-save-but').click(function() {

			  	var isQuickVis = false;

					if($(this).hasClass('save-button'))
					{
						var but = $(this);
						var form = $(this).parent().parent().parent();
					} else
					{
			    	isQuickVis = true;
						var but = $(this).parent().find('.save-button');
						var form = $(this).parent();
					}

			    var $qsb = form.find('.quick-save-but');
			  	var $isN = form.find('.issue-name');
			  	var $isNS = form.find('.issue-name-static');
					var $delT = form.parent().parent().find('.delete-text');

					//check to make sure all fields filled out
					if($.trim(form.find('.issue-name').val()) == '')
					{
						if(isQuickVis == false || $qsb.is(':visible'))
						{
							alert('Please fill out the issue name before saving');	
						}
						return;
					}


					//show saving text at top
					$delT.text('Please wait, saving issue...');
					$delT.show();
			    if(isQuickVis)
			    {
				    $qsb.hide();
				  }

				  //add tags into form
				  var $isTgs = form.find('.issue-tags');
				  //trigger keypress of comma if issue-tags not empty
				  if($isTgs.val() != '')
				  {
				  	var e = jQuery.Event("keydown");
						e.which = 188; // # Some key code value
						e.keyCode = 188;
						$isTgs.trigger(e);
				  }

				  var tags = form.find('.as-values').val();
				  $isTgs.val(tags);

					//send issue to db
					var formData = form.serialize();

					//reset tag input because not used for actual data, just input
					form.find('.issue-tags').val('');
					$.ajax({
						type: 'POST',
					  url: baseURL+"issues/save_issue",
					  data: formData,
					  success: function(id) {
					  	isNotSaved = false;
					  	form.find('.issue-id').val(id);

					  	//always show saving in delete text area at top
					    $delT.text('Issue Saved!');
					    $delT.delay(1000).fadeOut(300,function() {
				    		$delT.text('');
					    });	
			    		$isNS.text($isN.val());
					    if(isQuickVis)
					    {
						    $isN.hide();
						    $isNS.show();
								form.find('.issue-name-holder .input-info').removeAttr('style');
					    } else
					    {		    	
					    	//add new issue if last issue has a name 
								var $div = $('.issue-box').filter(':visible').filter(':last');
					    	if($div.find('.issue-name').val() != "")
					    	{
					    		$('.add-new-button').trigger('click');
					    	}
					    }
					    //see if added all issue data and if not, color grey
					    if(form.find('.issue-name').val() == '' || form.find('.issue-description').val() == '' || form.find('.as-values').val() == '')
					    {
					    	form.parent().parent().addClass('na');	
					    } else
					    {
					    	//color white
					    	form.parent().parent().removeClass('na');
					    }


				    	//close issue
				    	setInputsCurVal(form);
				    	closeIssue(form.parent(),true);

				    	//trigger final issue if saving all
				    	$(document).trigger('isFinishingLater');

					  },
					  error: function() {
							but.prev().text('Error Saving, Please check your internet connection');
					    but.prev().delay(1000).fadeOut();
					  }
					});
					
				})

				$this.find('.close-but').click(function(e) {

					e.stopPropagation();
					//show dialog for 
					if(confirm('Are you sure you want to delete this issue?'))
					{
						$this.deleteIssue();
					}
				})


				//move title attr to larger box
				$this.find('.issue-tags').each(function() {
					var t = $(this).attr('title');
					$(this).removeAttr('title');
					$(this).data('title',t);
				});


				//tagging autocomplete
				$this.find('.issue-tags').each(function() {
					var pfAR = $(this).val().split(',');
					var pf = [];
					pf.items = [];
					for(var i=0;i<pfAR.length;i++)
					{
						if(pfAR[i] != "")
						{
							pf.items.push({value:pfAR[i],name:pfAR[i]});	
						}
					}
					$(this).autoSuggest(baseURL+'issues/get_issue_tags', {
							selectedItemProp: "name", 
							searchObjProps: "name",
							startText: "",
							preFill:pf.items,
							emptyText:""
					});
				})

				$this.find('.issue-tags').each(function() {
					var $p = $(this).parent().parent();
					$p.attr('title',$(this).data('title'));
					$(this).unbind('hover');
				})
	    })
  	},
  	goViewMode:function() {
			$this.find('.shadowy').each(function() {
				methods.setInputTipTip(false);			
			})

			$this.find('.shadowy').removeClass('shadowy');

			$this.find('.parenth').hide();
			//set focus events for inputs
			methods.setIssueFocusEvents();
			methods.setInputHovers();
  	},
  	goEditMode:function() {

			$this.find('.issue-input,.as-selections').addClass('shadowy');

			$this.find('.shadowy').each(function() {
				methods.setInputTipTip(true);			
			})

			$this.find('.parenth').show();
			methods.removeInputHovers();
  	},
  	setInputHovers:function() {
			methods.removeInputHovers();
			$this.find('.issue-input,.as-selections').mouseenter(function(e) {
				$(this).css('color',hoverColor);
				var $aSI = $(this).find('.as-selection-item');
				if($aSI.length != 0)
				{
					$aSI.css('color',hoverColor);
				}
			});
			$this.find('.issue-input,.as-selections').mouseleave(function(e) {
				$(this).css('color','#000');
				var $aSI = $(this).find('.as-selection-item');
				if($aSI.length != 0)
				{
					$aSI.css('color','#000');
				}
			});
  	},
  	removeInputHovers:function() {
			$this.find('.issue-input,.as-selections').unbind('mouseenter mouseleave');
			$this.find('.issue-input,.as-selections').css('color','');
			$this.find('.as-selections .as-selection-item').css('color','');
  	},
  	setIssueFocusEvent:function() {

			var $form = $this.find('form');
			$this.data('oldForm',$form.serialize());
			var $iB = this;
			$this.find('.issue-input,.as-selections').each(function() {
				//add input ref to this data
				$(this).data('input',$iB);
				//unbind any previous events
				$(this).unbind('focus,focusout');
				$(this).focus(function() {
					goEditMode($(this).data('input'));
				})
				$(this).focusout(function() {
					//see if current data is same as old data
					if($(this).data('input').data('oldForm') == $(this).data('input').find('form').serialize())
					{
						methods.goViewMode();
					}
				})
				$iB.mousedown(function(e) {
					$(this).find('*:focus').blur();
				})
			})
  	},
  	setInputsCurVal:function(form) {
			form.find('textarea,.issue-input').each(function() {
				$(this).data('curVal',$(this).val());
			})
			methods.setTagsCurVal(form);
  	},
  	setInputTipTip:function(isAdded) {
			//save title to data because tiptip strips
			if($this.data('title') == undefined)
			{
				$this.data('title',$this.attr('title'));
			}
			if(isAdded && $this.hasClass('issue-tags') == false)
			{
				$this.attr('title',$this.data('title'));	
				if($this.hasClass('as-selections'))
				{
					var tit = $this.find('.issue-tags').attr('rel');
					this = $this.parent().parent().find('.as-selections');
					$this.attr('title',tit);
				} 
				$this.tipTip({
					defaultPosition:"top",
					enter:function() {
						$('#tiptip_holder').css('max-width',500);
					}
				});
			} else
			{
				$this.attr('title','');
				$this.unbind('hover');
			}

  	},
  	slideInIssue:function(ind) {

  	},
  	closeIssue:function(isNS,skipAnim) {

			var $st = $this.find('.issue-name-holder');
			var $nin = $this.find('.issue-name');
			var $qsb = $this.find('.quick-save-but');

			if($.trim($nin.val()) != '')
			{
				$nin.hide();
				$st.css('display','inline-block');
				$qsb.hide();
			} 

			
			if($this.height() > settings.closedHeight)
			{
				//get height for this input
				$this.css('height','auto');
				$this.data('openHeight',$this.height());
			}
			$this.parent().find('.arrow').addClass('arrow-up');


			$this.find('.title-label').fadeOut();
			$this.find('.fading-inputs').fadeOut();
			var $iB = this;
			$this.animate({
				'height':settings.closedHeight
			},function() {
				if($st.find('.issue-name-static').text() != $nin.val())
				{
					$iB.find('.quick-save-but').show();
				}
				//save this issue
				if(isNS == undefined)
				{
					$iB.find('.quick-save-but').trigger('click');	
				}

				//reset titptips for when closed
				methods.setClosedTipTips();
			})

	    //unfocus any inputs
			$this.find('textarea,.issue-input').blur();

  	},
  	setClosedTipTips:function() {

			$this.find('.issue-name-holder').tipTip({
				content:function() {
					var form = $this.find('form');
					var tgs = form.find('.as-values').val();
					if(tgs.charAt(0) == ',')
					{
						tgs = tgs.substr(1);
					}
					if(tgs.charAt(tgs.length-1) == ',')
					{
						tgs = tgs.substr(0,tgs.length-1);
					}
					var ret = "DESCRIPTION: "+form.find('.issue-description').val()+"<br/><br/>"+
										"UNITS: "+form.find('.issue-units').val()+"<br/><br/>"+
										"TAGS: "+tgs;
					return ret;
				},
				defaultPosition:"bottom",
				enter:function() {
					$('#tiptip_holder').css('max-width',900);
				}
			});
  	},
  	openIssue:function(skipAnim) {

			$this.parent().find('.arrow').removeClass('arrow-up');
			$this.find('.title-label').fadeIn();
			$this.find('.fading-inputs').fadeIn();
			var $st = $this.find('.issue-name-holder');
			var $nin = $this.find('.issue-name');
			var $isInfo = $this.find('.issue-name-holder .input-info');
			var $ta = $this.find('textarea');
			$ta.width($nin.width());
			$ta.trigger('blur');
			//hide quick save button
			$this.find('.quick-save-but').hide();
			$st.hide();
			$nin.show();
			$nin.blur();
			$isInfo.hide();
			if(skipAnim)
			{
				$this.height(Math.max($this.data('openHeight'),291));
				$this.find('.issue-description').elastic();
				$this.removeAttr('style');
				$isInfo.hide();
			} else
			{
				$this.stop(true,true);
				var $iB = this;
				$this.animate({
				  	'height':Math.max($iB.data('openHeight'),291)
				},500,function() {
					$this.removeAttr('style');
					$isInfo.hide();
					$this.find('.issue-description').elastic();
				})		
			}
			methods.setShadowFromOpen();
  	},
  	setShadowFromOpen:function() {

			var isEdit = false;
			$this.find('.issue-input,.as-values').each(function() {
				//categories isn't always complete
				if($(this).val() == '' && $(this).hasClass('issue-tags') == false)
				{
					isEdit = true;
					return false;
				}
			})
			if(isEdit)
			{
				methods.goEditMode();
			} else
			{
				methods.goViewMode();
			}
  	},
  	deleteIssue:function() {

			$this.find('.delete-text').show();
			$this.find('.delete-text').text('Deleting issue...');
			var id = $this.find('.issue-id').val();
			$.ajax({
				type: 'POST',
			  url: baseURL+"issues/delete_issue",
			  data: 'id='+id,
			  success: function(d) {
			    $this.find('.delete-text').delay(100).fadeOut(300,function() {
						//remove and move div to end of file, then renumber
						methods.removeAnIssue();
						
			    });
			  }
			});
  	},
  	removeAnIssue:function() {
			$this.data('isRemoving',true);
			$this.fadeOut(300,function() {
				//make sure add issue button is shown
				$('.add-new-button').show();
				$(this).remove();
				
				//renumber issues
				//change to event dispatch!!
				var ind = 1;
				$('#issue-list .number').each(function() {
					$(this).find('span').text(ind);
					ind++;
				})

				$('#issue-number span.issue-num').text($('.issue-box').filter(':visible').length);
			});
  	},
  	setTagsCurVal:function(form) {

			form.find('.as-values').each(function() {
				$(this).data('curVal',$(this).val());
			})
  	}

  }
  //end of methods object


  $.fn.issueBox = function(method) {


		// Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
    }
  

  };
})( jQuery );