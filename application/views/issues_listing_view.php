<?php $numIssues = $project_info['numberOfIssuesPerParticipant']?>
<script type='text/javascript'>
$(document).ready(function() {
	var isBegin = true;
	var isNotSaved = true;
	var savingForLater = -1;
	var curSaving = 0;
	var isFinishingLater = false;
	var numIssues = "<?php echo $numIssues?>";
	var closedHeight = 21;
	var $issueTemplate;


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

				//exception for if no issues yet
				//remove first dummy issue
				var $div = $('#issue-list .issue-box').eq(0);
				$issueTemplate = $div.clone(true,true);
				if($('#issue-list .issue-box').eq(0).find('.issue-id').val() == '')
				{
					$div.remove();
					ind = 1;
				} 
				slideInIssue(ind);

				
				//set initial value for inputs in curVal for each
				$('#issue-list form').each(function() {
					setInputsCurVal($(this));
					
				})
				setIssueListSortable();

			}
		)

		if(ind != 0)
		{
			isNotSaved = false;
		}
		//begin checking question scroll
		$(document).scroll(function() {
			checkQuestionPosition();
			$('#tiptip_holder').hide();
		})
	})

	//move directly to adding (may change back)
	$('#begin-but').trigger('click');

	//initial extra space at beginning
	$('#question-space').height($('#question-box').height()+20);

	function setIssueListSortable()
	{
		//make list items sortable (drag and drop)
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

		$inp.find('.shadowy').each(function() {
			setInputTipTip($(this),false);			
		})

		$inp.find('.shadowy').removeClass('shadowy');

		$inp.find('.parenth').hide();
		//set focus events for inputs
		setIssueFocusEvents($inp);
		setInputHovers($inp);
	}

	function setInputHovers($inp)
	{
		removeInputHovers($inp);
		$inp.find('.issue-input,.as-selections').mouseenter(function(e) {
			$(this).css('color','#978D22');
			var $aSI = $(this).find('.as-selection-item');
			if($aSI.length != 0)
			{
				$aSI.css('color','#978D22');
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
		$inp.find('.issue-input,.as-selections').unbind('mouseenter mouseleave');
		$inp.find('.issue-input,.as-selections').css('color','#000');
		$inp.find('.as-selections .as-selection-item').css('color','#000');
	}

	function setIssueFocusEvents($inp)
	{
		var $form = $inp.find('form');
		$inp.data('oldForm',$form.serialize());
		$inp.find('.issue-input,.as-selections').each(function() {
			//add input ref to this data
			$(this).data('input',$inp);
			//unbind any previous events
			$(this).unbind('focus,focusout');
			$(this).focus(function() {
				goEditMode($(this).data('input'));
			})
			$(this).focusout(function() {
				//see if current data is same as old data
				if($(this).data('input').data('oldForm') == $(this).data('input').find('form').serialize())
				{
					goViewMode($(this).data('input'));
				}
			})
			$(this).closest('.issue-box').mousedown(function(e) {

				$(this).find('*:focus').blur();
			})
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
			$in.unbind('hover');
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
		$(this).unbind('hover');
	})


	function setTagsCurVal(form)
	{
		form.find('.as-values').each(function() {
			$(this).data('curVal',$(this).val());
		})
	}





})
</script>
<div id='top-space'>
</div>

<!--USER NOTES-->
<div class='user-notes-holder'>
	<div class='notes-but submit-but'>My Notes</div>
	<div class='user-notes'>
		<form>
			<textarea name='user_notes'><?php echo $user_notes;?></textarea>
			<div class='submit-but user-notes-submit'>Save</div>
			<div class='loading-text'></div>
		</form>
	</div>
</div>

<div id='question-box' class='above-box'>
	<div id='question'>
		<h1>Here is our question to you:</h1>
		<p><?php echo $project_info['question'] ?></p>
		<div id='question-arrow'></div>
	</div>
	<div id='question-info-wrap'>
		<div id='question-info-holder'>
			<div id='question-background-info' title='<?php echo nl2br($project_info["description"]) ?>'></div>
		</div>
	</div>
	<div class='clearer'></div>
	<div id='question-background'>
	<br/>
		<h1>CONTEXT AND BACKGROUND INFORMATION</h1>
		<?php echo nl2br($project_info['description']) ?>
		<br/>
		<div class='centered'>
			<span id='begin-but' class='submit-but centered-but'>Begin</span>
		</div>
	</div>
	
</div>
<div id='question-space'></div>
<div id='issue-list' class='issue-list-submission'>
	<div class='clearer' style='height:1px;'></div>
	<?php for($i=0;$i<max(count($issues_list),1);$i++):?>
		<?php 
		if(isset($issues_list[$i]))
		{
			$isOpen = FALSE;
			$issue = $issues_list[$i];
		} else
		{
			$isOpen = FALSE;
			$issue = array('name'=>'',
											'description'=>'',
											'units'=>'',
											'categories'=>'',
											'id'=>'',
											'issueOrderNum'=>0);
		}
		?>
		<div class='dark-box single-box instruction-box issue-box'>
			<div class='arrow'></div>
			<div class='number'><span><?php echo $i+1?></span></div>
			<div class='delete-text'></div>
			<div class='close-but'></div>
			<div class='input'>
				<form>
					<?php if($isOpen):?>
					<div class='is-open'></div>
					<?php endif;?>
					<input name='issue_order_num' type='hidden' class='issue-order-num' value='<?php echo $issue['issueOrderNum']?>'/>
					<input name='issue_id' type='hidden' class='issue-id' value='<?php echo $issue['id']?>'/>
					<label class='title-label'>Short issue title <span class='parenth'>(<50 characters)</span></label>
					<?php if($isOpen):?>
					<input class='issue-input issue-name' name='name' type='text' value="<?php echo $issue["name"]?>" title='Provide a short descriptive title for this issue'/>
					<div class='issue-name-holder'>
						<div class='issue-name-tc'>
							<div class='issue-name-static'><?php echo $issue["name"]?></div>
						</div>
					</div>
					<?php else:?>
					<input class='issue-input issue-name' name='name' type='text' value="<?php echo $issue["name"]?>" style='display:none;' title='Provide a short descriptive title for this issue'/>
					<div class='issue-name-holder'>
						<div class='issue-name-tc'>
							<div class='issue-name-static' style='display:block;'><?php echo $issue["name"]?></div>
						</div>
					</div>
					<?php endif;?>
					<div class='submit-but quick-save-but'>Save</div>
					<div class='fading-inputs'>
						<label>Description <span class='parenth'>(<250 Words)</span></label>
					  <?php echo form_textarea( array( 'name' => 'description', 'class'=>'issue-input issue-description', 'title'=>'Describe and define this issue in more detail. Why did you choose it? What does it influence? Is this a major constraint or an enabler?', 'rows' => '5', 'cols' => '80', 'value' => $issue['description'] ) )?>
					  <br/>
						<label>Units <span class='parenth'>(Optional <25 Words)</span></label>
						<input name='units' type='text' class='issue-input issue-units' title='How would you measure a change in this issue? (optional)' value='<?php echo $issue["units"]?>'/>
					  <br/>
						<label>Tags <span class='parenth'>(up to 5, separated by commas)</span></label>
						<input name='categories' type='text' class='issue-input issue-tags' title='What broader tags or keywords, if any, help define this issue or place it in context? If not applicable or you are uncertain, just enter "N/A"' value='<?php echo $issue["categories"]?>'/>
						<div class='clearer'></div>
						<div class='right'>
							<div class='saving-anim'>Saving Issue...</div>
							<input type='submit' class='submit-but save-button' value='Save' onclick='return false;' />
						</div>
						<div class='clearer'></div>
					</div>
				</form>
			</div>
		</div>
	<?php endfor;?>
	<div id='issue-list-bottom' class='clearer' style='height:1px;'></div>
</div>
<div id='add-new'>
	<div class='submit-but add-new-button'>Add Issue</div>
	<div id='issue-number'>So far, you have added <span class='issue-num'>1</span> of <span class='tot-issues'><?php echo $numIssues?></span> Max Issues</div>
	<div class='clearer'></div>
	<div class='submit-but finished-button'>Finished Adding Issues</div>
	<div class='submit-but finish-later-button'>Save Issues And Continue Later</div>
	<div id='finished-text'>Thank you for completing the Issue Submission process. </div>
</div>
<div class='clearer'></div>
<div id='page-bottom'></div>