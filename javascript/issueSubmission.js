$(document).ready(function() {
	var isBegin = true;
	var isNotSaved = true;
	var savingForLater = -1;
	var curSaving = 0;
	var isFinishingLater = false;
	//var numIssues = "<?php echo $numIssues?>";
	var closedHeight = 21;
	var $issueTemplate;
	var hoverColor = '#db5bdb';


	$('#begin-but').click(function() {
		//for static question positioning
		isBegin = false;
		$('#question').width(800);

		$('#question-background').fadeTo(100,0);

		//get next issue index
		var ind = $('#issue-list .issue-box').length+1;
		//show arrow pointing to issues
		$('#question-arrow').show();
		$('#question-box').animate({
			'height':$('#question').height()
			},
			function() {
				//to vertically center
				$('#question-info-holder').height($('#question').height());
				//show info background
				$('#question-background-info').fadeIn();
				$('#question-background').hide();
				$('#add-new').fadeIn();
				$('#question-space').height($('#question-box').height()+60);

				//remove first dummy issue to create template
				var $div = $('#issue-list .issue-box').eq(0);
				$issueTemplate = $div.clone(true,true);
				if($('#issue-list .issue-box').eq(0).find('.issue-id').val() == '')
				{
					$div.remove();
					ind = 1;
				} 
				//add in first issue
				slideInIssue(ind);

				
				//set initial value for inputs in curVal for each issue
				//and events for showing shadow if focusing
				$('#issue-list form').each(function() {
					setInputsCurVal($(this));
					
					setIssueFocusEvents($(this).parent());
				})

				//make issues sortasble
				setIssueListSortable();

			}
		)

		if(ind != 0)
		{
			isNotSaved = false;
		}
		//begin checking question scroll for hugging top
		$(document).scroll(function() {
			checkQuestionPosition();
			$('#tiptip_holder').hide();
		})
	})

	//move directly to adding the first issue
	$('#begin-but').trigger('click');

	//initial extra space at beginning
	$('#question-space').height($('#question-box').height()+20);

	//make list items sortable (drag and drop)
	function setIssueListSortable()
	{
		$( "#issue-list" ).sortable({
			delay:100,
			containment:'parent',
			placeholder:'sortable-placeholder',
			revert:true,
			update:function(event,ui) {
				var i = 1;
				$('#issue-list .issue-box').each(function() {
					$(this).find('.number span').text(i);
					$(this).find('.issue-order-num').val(i);
					i++;
				})
				$('#issue-list').css('height','auto');
			},
			start:function(event,ui) {
				$('.sortable-placeholder').height(ui.item.height()+20);
			},
			sort:function() {
				$('#tiptip_holder').hide();
			}
		});
		$( "#issue-list" ).disableSelection();
	}


	function goViewMode($inp)
	{
		var $in = $inp.find('.shadowy');
		if($in.length == 0)
		{
			return;
		}
		$in.each(function() {
			setInputTipTip($(this),false);			
		})

		$in.removeClass('shadowy');

		$inp.find('.parenth').hide();
		//set focus events for inputs
		setIssueFocusEvents($inp);
		setInputHovers($inp);
	}

	function setInputHovers($inp)
	{
		removeInputHovers($inp);
		console.log('setting hovers');
		$inp.find('.issue-input,.as-selections').mouseenter(function(e) {
			$(this).css('color',hoverColor);
			var $aSI = $(this).find('.as-selection-item');
			if($aSI.length != 0)
			{
				$aSI.css('color',hoverColor);
			}
		});
		$inp.find('.issue-input,.as-selections').mouseleave(function(e) {
			$(this).css('color','#000');
			var $aSI = $(this).find('.as-selection-item');
			if($aSI.length != 0)
			{
				$aSI.css('color','#000');
			}
		});
	}

	function removeInputHovers($inp)
	{
		console.log('removing hovers');
		$inp.find('.issue-input,.as-selections').off('mouseenter mouseleave');
		$inp.find('.issue-input,.as-selections').css('color','');
		$inp.find('.as-selections .as-selection-item').css('color','');
	}

	function removeIssueFocusEvents($inp)
	{
		$inp.find('.issue-input,.as-selections').each(function() {
			$(this).off('focus blur');
		})

		$inp.closest('.issue-box').off('mousedown');
	}

	function setIssueFocusEvents($inp)
	{
		removeIssueFocusEvents($inp);
		var $form = $inp.find('form');
		$inp.data('oldForm',$form.serialize());
		$inp.find('.issue-input,.as-selections').each(function() {
			//add input ref to this data
			$(this).data('input',$inp);
			//on click, go into edit mode
			$(this).focus(function() {
				goEditMode($(this).data('input'));
			})
			$(this).blur(function(e) {
				//see if current data is same as old data
				if($(this).data('input').data('oldForm') == $(this).data('input').find('form').serialize())
				{
					goViewMode($(this).data('input'));
				}
			})
		})
		$inp.closest('.issue-box').mousedown(function(e) {

			$(this).find('*:focus').blur();
		})
	}

	function goEditMode($inp)
	{
		$inp.find('.issue-input,.as-selections').addClass('shadowy');

		$inp.find('.shadowy').each(function() {
			setInputTipTip($(this),true);			
		})

		$inp.find('.parenth').show();
		removeInputHovers($inp);
	}



	function setInputsCurVal(form)
	{
		form.find('textarea,.issue-input').each(function() {
			$(this).data('curVal',$(this).val());
		})
		setTagsCurVal(form);
	}



	function setInputTipTip($in,isAdded)
	{
		//save title to data because tiptip strips
		if($in.data('title') == undefined)
		{
			$in.data('title',$in.attr('title'));
		}
		if(isAdded && $in.hasClass('issue-tags') == false)
		{
			$in.attr('title',$in.data('title'));	
			if($in.hasClass('as-selections'))
			{
				var tit = $in.find('.issue-tags').attr('rel');
				$in = $in.parent().parent().find('.as-selections');
				$in.attr('title',tit);
			} 
			$in.tipTip({
				defaultPosition:"top",
				enter:function() {
					$('#tiptip_holder').css('max-width',500);
				}
			});
		} else
		{
			$in.attr('title','');
			$in.off('hover');
		}
	}


	//text area reszing
	$('#issue-list textarea').elastic();

	var $bodyEle;
	if($.browser.safari || $.browser.chrome)
	{
		$bodyEle = $('body');
	} else
	{
		$bodyEle = $('html');
	}

	function slideInIssue(ind)
	{
		var $div = $issueTemplate.clone(true,true);

		
		//make sure all but last are closed
		//$
		$('.issue-box').each(function() {
			if($(this).find('.arrow-up').length == 0)
			{
				//close
				$(this).show();
				//if issue name static and issue name aren't same, save
				if($(this).find('.issue-name-static').text() != $(this).find('.issue-name').val())
				{
					closeIssue($(this).find('.input'));
				} else
				{
					closeIssue($(this).find('.input'),true);	
				}
				//set to gray if not all vars filled out
				//see if added all issue data and if not, color grey
				var form = $(this).find('form');
		    if(form.find('.issue-name').val() != '' && (form.find('.issue-description').val() == '' || form.find('.as-values').val() == ''))
		    {
		    	form.parent().parent().addClass('na');	
		    } else
		    {
		    	//color white
		    	form.parent().parent().removeClass('na');
		    }
			}
		})

		//disallow more than number of issues possible
		$('#issue-number .issue-num').text(ind);
		//$('#issue-number .issue-num').text(Math.min(ind,numIssues));
		/*if(ind > numIssues)
		{
			$('.add-new-button').fadeOut();
			return;
		}*/
		//cleaer all inputs
		$div.find('.issue-name-static').text('');
		$div.find('.issue-name').val('');
		$div.find('.issue-description').val('');
		$div.find('.issue-units').val('');
		$div.find('.issue-tags').val('');
		$div.find('.number span').text(ind);
		$div.find('.issue-order-num').val(ind);
		$div.find('.delete-text').hide();
		$div.find('.issue-id').val('');
		$div.removeClass('na');
		var $iss = $div.insertBefore('#issue-list-bottom');
		openIssue($iss.find('.input'));
		$iss.hide();

		//also remove tags
		$iss.find('.as-selections').after("<input name='categories' type='text' class='issue-input issue-tags' title='What broader tags or keywords, if any, help define this issue or place it in context? If not applicable or you are uncertain, just enter &lquo;N/A&rquo;' value=''/>");


		$iss.find('.as-selections').remove();

		//move title attr to larger box
		var $iT = $iss.find('.issue-tags')
		var t = $iT.attr('title');
		$iT.removeAttr('title');

		$iss.find('.issue-tags').autoSuggest(baseURL+'issues/get_issue_tags', {
			selectedItemProp: "name", 
			searchObjProps: "name",
				asHtmlID:$iT.parent().parent().parent().find('.issue-id').val(),
			startText: "",
			emptyText:""
		});
		var $p = $iT.parent().parent();
		$p.attr('title',t);




		$div.find('.issue-name-holder').hide();
		$div.fadeIn(500,function() {
			//scroll page to bottom
			$.scrollTo($('.finished-button'),300);
			//set textarea size
			$(this).find('textarea').width($(this).find('.issue-name').width());
		});

		//show shadow
		goEditMode($iss.find('.input'));
	}





	var instructionsScrollPos = 0;
	function checkQuestionPosition()
	{
		var whenToFix = 199;
		if($('#hide-instructions').text() == 'Show Instructions')
		{
			whenToFix = 310;
		}
		if(isBegin)
		{
			return;
		}
		if($bodyEle.scrollTop()>whenToFix)
		{
			$('#question-box').css({
				'position':'fixed',
				'top':instructionsScrollPos
			})
			$('#hide-instructions').show();
		} else if($bodyEle.scrollTop()<whenToFix)
		{
			var h = $('#question-box').height();
			$('#question-box').removeAttr('style');
			$('#question-box').height(h);
			$('#question-box').css({
				'position':'absolute'

			})
			$('#hide-instructions').hide();
		}
	}


	//opening and closing boxes
	$('.arrow').click(function(e) {

		e.stopPropagation();
		var $inp = $(this).parent().find('.input');
		//hasClass not working??
		if($(this).attr('class').indexOf('arrow-up') != -1)
		{
			openIssue($inp);
		} else
		{
			closeIssue($inp);
		}
	})



	function closeIssue($inp,isNS,skipAnim)
	{

		var $st = $inp.find('.issue-name-holder');
		var $nin = $inp.find('.issue-name');
		var $qsb = $inp.find('.quick-save-but');

		if($.trim($nin.val()) != '')
		{
			$nin.hide();
			$st.css('display','inline-block');
			$qsb.hide();
		} 

		
		if($inp.height() > closedHeight)
		{
			//get height for this input
			$inp.css('height','auto');
			$inp.data('openHeight',$inp.height());
		}
		$inp.parent().find('.arrow').addClass('arrow-up');


		$inp.find('.title-label').fadeOut();
		$inp.find('.fading-inputs').fadeOut();
		$inp.animate({
			'height':closedHeight
		},function() {
			if($st.find('.issue-name-static').text() != $nin.val())
			{
				$inp.find('.quick-save-but').show();
			}
			//save this issue
			if(isNS == undefined)
			{
				$inp.find('.quick-save-but').trigger('click');	
			}

			//reset titptips for when closed
			setClosedTipTips();
		})

    //unfocus any inputs
		$inp.find('textarea,.issue-input').blur();
	}

	function openIssue($inp,skipAnim)
	{
		$inp.parent().find('.arrow').removeClass('arrow-up');
		$inp.find('.title-label').fadeIn();
		$inp.find('.fading-inputs').fadeIn();
		var $st = $inp.find('.issue-name-holder');
		var $nin = $inp.find('.issue-name');
		var $isInfo = $inp.find('.issue-name-holder .input-info');
		var $ta = $inp.find('textarea');
		$ta.width($nin.width());
		$ta.trigger('blur');
		//hide quick save button
		$inp.find('.quick-save-but').hide();
		$st.hide();
		$nin.show();
		$nin.blur();
		$isInfo.hide();
		if(skipAnim)
		{
			$inp.height(Math.max($inp.data('openHeight'),291));
			$inp.find('.issue-description').elastic();
			$inp.removeAttr('style');
			$isInfo.hide();
		} else
		{
			$inp.stop(true,true);
			$inp.animate({
			  	'height':Math.max($inp.data('openHeight'),291)
			},500,function() {
				$inp.removeAttr('style');
				$isInfo.hide();
				$inp.find('.issue-description').elastic();
			})		
		}
		setShadowFromOpen($inp);
	}

	function setShadowFromOpen($inp)
	{
		var isEdit = false;
		$inp.find('.issue-input,.as-values').each(function() {
			//categories isn't always complete
			if($(this).val() == '' && $(this).hasClass('issue-tags') == false)
			{
				isEdit = true;
				return false;
			}
		})
		if(isEdit)
		{
			goEditMode($inp);
		} else
		{
			goViewMode($inp);
			setInputHovers($inp);
		}
	}

	//title click and edit button
	$('.issue-name-static').click(function() {
		$(this).hide();
		var $in = $(this).parent().parent().parent().find('.issue-name');
		$in.show();
		$in.focus();
		//show quick save but
		$(this).parent().parent().parent().find('.quick-save-but').show();
		goEditMode($(this).parent().parent().parent().parent());
	})

	$('.issue-name').focusout(function() {
		//see if should rehide input since didn't chagne
		if($(this).val() == $(this).parent().find('.issue-name-static').text() && $(this).val() != "" && $(this).parent().parent().find('.quick-save-but').is(':visible'))
		{
			$(this).hide();
			$(this).parent().find('.issue-name-static').show();
			$(this).parent().find('.quick-save-but').hide();
		}
	})


	$('.issue-name').keypress(function() {
		if($(this).parent().find('.fading-inputs').is(':hidden'))
		{
			$(this).parent().find('.quick-save-but').show();
		}
	})



	//adding an issue
	$('.add-new-button').click(function() {
		var ind = $('.issue-box:visible').length+1;
		/*if(ind == numIssues)
		{
			$('.add-new-button').fadeOut();
		}*/
		slideInIssue(ind);
	})


	//AJAX FOR SAVING ISSUES
	$('.save-button,.quick-save-but').click(function() {

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

	$('.close-but').click(function(e) {

		e.stopPropagation();
		//show dialog for 
		if(confirm('Are you sure you want to delete this issue?'))
		{
			deleteIssue($(this).parent());
		}
	})

	function deleteIssue($iss)
	{
		$iss.find('.delete-text').show();
		$iss.find('.delete-text').text('Deleting issue...');
		var id = $iss.find('.issue-id').val();
		$.ajax({
			type: 'POST',
		  url: baseURL+"issues/delete_issue",
		  data: 'id='+id,
		  success: function(d) {
		    $iss.find('.delete-text').delay(100).fadeOut(300,function() {
					//remove and move div to end of file, then renumber
					removeAnIssue($iss);
					
		    });
		  }
		});
	}

	function removeAnIssue($iss)
	{
		$iss.data('isRemoving',true);
		$iss.fadeOut(300,function() {
			//make sure add issue button is shown
			$('.add-new-button').show();
			$(this).remove();
			
			//renumber issues
			var ind = 1;
			$('#issue-list .number').each(function() {
				$(this).find('span').text(ind);
				ind++;
			})

			$('#issue-number span.issue-num').text($('.issue-box').filter(':visible').length);
		});
	}

	$('.finished-button').click(function() {
		//check to see if any divs have na class (are grey)
		var isDone = true;
		$('.issue-box').each(function() {
			if($(this).hasClass('na'))
			{
				alert('Please finish filling out all fields for all the issues marked in grey.');
				isDone = false;
				return false;
			}
		})

		var $div = $('.issue-box').filter(':visible').filter(':last');


		if(isDone)
		{
			//make sure at least 1 issue is entered and saved
			if(isNotSaved)
			{
				alert('Please enter in at least One issue.');
				return;
			}

			removeEmptyIssues();

			isFinishingLater = false;
			beginFinishedSave();
		}
	
		$('.issue-box').each(function() {
			var $inp = $(this).find('.input');
			if($(this).hasClass('na'))
			{
				openIssue($inp);
			} else
			{
				closeIssue($inp);
			}
		})

		//scroll to first na
		if($('.na').filter(':first').length == 1)
		{
			setTimeout(function() {
				$.scrollTo($('.na').filter(':first').offset().top-$('#question-space').height());	
			},300)
		}
	})


	//for saving and finishing later
	$('.finish-later-button').click(function() {
		isFinishingLater = true;
		//loop through all issues and save any that are open or that show a quick-save-but
		$('#finished-text').text('Please wait, saving all issues...');
		removeEmptyIssues();
		beginFinishedSave();
	})


	function removeEmptyIssues()
	{
		//hide issues if no fields added
		$('.issue-box').each(function() {
			if($(this).find('.issue-name').val() == "" && $(this).find('.issue-description').val() == "" && $(this).find('.issue-units').val() == "" && $(this).find('.issue-tags').val() == "")
			{
				removeAnIssue($(this));
			}
		})
	}

	//custom listener for when saving is done
	$(document).bind('isFinishingLater',function() {
		curSaving++;
		if(savingForLater == curSaving)
		{
			showFinishedText();
			
		}
	})

	function beginFinishedSave()
	{
		$('#finished-text').text('Please wait, saving all issues...');
		$('#finished-text').fadeIn();

		//and reset value
		savingForLater = 0;
		curSaving = 0;
		$('.issue-box').each(function() {
			if($(this).find('.quick-save-but').is(':visible') || $(this).find('.fading-inputs').is(':visible') || $(this).find('.issue-order-num').val() != 0)
			{
				//dont count ones that are empty and being removed
				if($(this).data('isRemoving') == undefined)
				{
					closeIssue($(this).find('.input'));	
					savingForLater++;	
				} else
				{
					$(this).data('isRemoving',undefined);
				}
			}
		})
		if(savingForLater == 0)
		{
			showFinishedText();
		}
		
	}

	function showFinishedText()
	{
		//tell user saved all issues and now free to close page
		$('#finished-text').hide();
		if(isFinishingLater)
		{
			$('#finished-text').text('All your issues have been saved. You are free to leave this page and come back later to where you left off.');	
		} else
		{
			$('#finished-text').text('Thank you for completing the Issue Submission process. ');	
		}
		//show text thanking for completing
		$('#finished-text').delay(300).fadeIn(1000);

		//scroll page to bottom
		$.scrollTo($('#page-bottom'),300);
		
	}



	//rollovers
	$('#question-background-info').tipTip({
		defaultPosition:'left',
		forceCenter:true,
		keepAlive:true,
		enter:function() {
			$('#tiptip_holder').css('max-width',800);
		}
	});

	var curTip;
	function setClosedTipTips()
	{
		$('.issue-name-holder').mouseover(function() {
			curTip = $(this);
		})

		$('.issue-name-holder').tipTip({
			content:function() {
				var form = curTip.parent();
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
	}
	setClosedTipTips();
	


	//move title attr to larger box
	$('.issue-tags').each(function() {
		var t = $(this).attr('title');
		$(this).removeAttr('title');
		$(this).data('title',t);
	});


	//tagging autocomplete
	$('.issue-tags').each(function() {
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

	$('.issue-tags').each(function() {
		var $p = $(this).parent().parent();
		$p.attr('title',$(this).data('title'));
		$(this).off('hover');
	})


	function setTagsCurVal(form)
	{
		form.find('.as-values').each(function() {
			$(this).data('curVal',$(this).val());
		})
	}





})