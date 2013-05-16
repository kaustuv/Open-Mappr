
$(document).ready(function() {

	var goalBorderColor = '#999';
	var flagBorderColor = '#999';
	var completeBorderColor = '#999';
	var normalBorderColor = '#999';
	var posColor = '#ffffff';
	var negColor = '#ffffff';
	var ambigColor = '#ffffff';
	var hoverColor = '#db5bdb';
	var minInputHeight = 339;
	var isBegin = false;
	var isNotSaved = true;
	var savingForLater = -1;
	var curSaving = 0;
	var isFinishingLater = false;
	var numIssues = 0;
	var closedHeight = 21;
	var inputWidth = $('.issue-name').width();
	//width of input box when quick save but shown
	var shortIssueNameWidth = 460;

	var $bodyEle;
	if($.browser.safari || $.browser.chrome)
	{
		$bodyEle = $('body');
	} else
	{
		$bodyEle = $('html');
	}

	//show info background
	//to vertically center
	$('#question-info-holder').height($('#question').height()+20);
	$('#question-background-info').fadeIn();
	$('#question').width(800);
	$('.issue-box').show();



	//tagging autocomplete
	function tagIssue($iT)
	{
		//see if already created
		if($iT.parent().hasClass('as-original'))
		{
			return;
		}
		var pfAR = $iT.val().split(',');
		var pf = [];
		pf.items = [];
		for(var i=0;i<pfAR.length;i++)
		{
			if(pfAR[i] != "")
			{
				pf.items.push({value:pfAR[i],name:pfAR[i]});	
			}
		}
		var tag_id = $iT.parent().parent().parent().find('.issue-id').val();
		if(tag_id == '')
		{
			tag_id = new Date().getTime();
		}
		var $nIT = $iT.autoSuggest(baseURL+'issues/get_node_tags', {
				selectedItemProp: "name", 
				searchObjProps: "name",
				startText: "",
				preFill:pf.items,
				asHtmlID:tag_id,
				emptyText:"",
				selectionAdded:function(elem) {
					var tg = elem.text().substr(1);
					//see if tag is in tag list dd and if not, then add
					if($('#tag-filter option[value="'+tg+'"]').length == 0)
					{
						$('#tag-filter').append('<option value="'+tg+'">'+tg+'</option>');
					}
				}
		});
	}
	


	//first set initial height and title width
	//also hovers and focus events
	$('.input').each(function() {
		$(this).data('openHeight',$(this).height());
		setInputsCurVal($(this).find('form'));
		var $nin = $(this).find('.issue-name');
		positionNameInput($nin);
	})

	$('.arrow').addClass('arrow-up');
	$('.title-label').hide();
	$('.fading-inputs').hide();
	$('.input').height(closedHeight);
	$('.issue-name').hide();
	var $tit = $('.issue-name-static');
	//fix this
	$tit.each(function() {
		$(this).text($(this).parent().find('.issue-name').val());
	});
	$tit.show();

	//set colors
	$('.issue-dds').each(function() {
		setIssueColor($(this));
	})

	$('.issue-box .number span').each(function() {
		if(Number($(this).text()) > numIssues)
		{
			numIssues = Number($(this).text());
		}
	})

	$( "#issue-list" ).disableSelection();

	function setDragAndDropMerge($div)
	{
		$div.draggable({
			delay:100,
			zIndex:1000,
			revert:true,
			containment:'parent',
			start:function() {
				$('#tiptip_holder').hide();
			},
	  	drag: function(event, ui) {
	  		$('.ui-draggable-dragging').offset({ top: event.pageX, left: event.pageY})
	  	}
		});

		$div.droppable({
			over:function() {
				$(this).css('background-color','#666');
			},
			out:function() {
				setIssueColor($(this).find('.issue-dds'));
			},
			drop: function(event, ui) {
				setIssueColor($(this).find('.issue-dds'));
				//hide dropped item
				var $dg = ui.draggable;
				//merge issue texts
				mergeIssues($(this),$dg);

				
			}
		})
	}

	$('.issue-box').each(function() {
		setDragAndDropMerge($(this));
	})


	function mergeIssues($iss1,$iss2)
	{
		$iss2.hide();

		//get id of issue to remove
		var remId = $iss2.find('.issue-id').val();
		//show that merged in delete text
		var $delT = $iss1.find('.delete-text');
		positionDeleteText($delT);
		$delT.text('Merging Issues...');
		$delT.delay(1000).fadeOut();
		
		var $inp = $iss1.find('.input');

		//scroll to the open issue
		setTimeout(function() {
			$.scrollTo($iss1.offset().top+$('#question').height()-220,500);
		},300);
		//open this issue
		openIssue($inp);
		$iss1.find('.cancel-merge').fadeIn();
		//get all values and add to values in current issue
		
		var $remInp = $iss2.find('.input');

		var inp = $inp.find('.issue-name');
		var remInp = $remInp.find('.issue-name');
		var remInd = $iss2.find('.number span').text();
		//set its beforeMerge var to prev value
		inp.data('beforeMerge',inp.val());
		if(remInp.val() != ''){
			inp.val(inp.val()+" | "+remInd+": "+remInp.val());
		}
		var inp = $inp.find('.issue-description');
		var remInp = $remInp.find('.issue-description');
		//set its beforeMerge var to prev value
		inp.data('beforeMerge',inp.val());
		if(remInp.val() != ''){
			inp.val(inp.val()+" \n\nFrom Issue "+remInd+": "+remInp.val());
		}
		//reset elastic
		inp.elastic();

		var inp = $inp.find('.issue-units');
		var remInp = $remInp.find('.issue-units');
		//set its beforeMerge var to prev value
		inp.data('beforeMerge',inp.val());
		if(remInp.val() != ''){
			inp.val(inp.val()+" | "+remInd+": "+remInp.val());
		}

		var inp = $inp.find('.votes');
		var remInp = $remInp.find('.votes');
		inp.data('beforeMerge',inp.val());
		inp.val(Number(inp.val())+Number(remInp.val()));
		//also set value for text
		$inp.find('.votes-text').text(inp.val());
		



		//TAGGING
		var $isTgs = $remInp.find('.as-values');
		if($isTgs.length == 0)
		{
			$isTgs = $remInp.find('.issue-tags');
		}
	  //trigger keypress of comma if issue-tags not empty
	  if($isTgs.val() != '')
	  {
	  	var e = jQuery.Event("keydown");
			e.which = 188; // # Some key code value
			e.keyCode = 188;
			$isTgs.trigger(e);
	  }

	  var tags = $isTgs.val();

	  //now get tags from shown iss(2)
		$isTgs2 = $inp.find('.as-values');
		if($isTgs2.length == 0)
		{
			$isTgs2 = $inp.find('.issue-tags');
		}
	  //trigger keypress of comma if issue-tags not empty
	  if($isTgs2.val() != '')
	  {
	  	var e = jQuery.Event("keydown");
			e.which = 188; // # Some key code value
			e.keyCode = 188;
			$isTgs2.trigger(e);
	  }

		//set its beforeMerge var to prev value
		$inp.data('beforeMergeTags',$isTgs2.val());

	  tags = $isTgs2.val() + tags;
	  

	  //used to get distinct array in javascript
		$.extend({
		    distinct : function(anArray) {
		       var result = [];
		       $.each(anArray, function(i,v){
		           if ($.inArray(v, result) == -1) result.push(v);
		       });
		       return result;
		    }
		});

	  //now split up tags into array and use fro autosuggest
		var pfAR = tags.split(',');
		pfAR = $.distinct(pfAR);
		var pf = [];
		pf.items = [];
		for(var i=0;i<pfAR.length;i++)
		{
			if(pfAR[i] != "")
			{
				pf.items.push({value:pfAR[i],name:pfAR[i]});	
			}
		}
		$inp.find('.as-selections').after("<input name='categories' type='text' class='issue-input issue-tags' value=''/>")
		$inp.find('.as-selections').remove();
		$inp.find('.as-results').remove();
		$inp.find('.issue-tags').autoSuggest(baseURL+'issues/get_node_tags', {
				selectedItemProp: "name", 
				searchObjProps: "name",
				startText: "",
				preFill:pf.items,
				emptyText:""
		});
	  


		var inp = $inp.find('.issue-notes');
		var remInp = $remInp.find('.issue-notes');
		//set its beforeMerge var to prev value
		inp.data('beforeMerge',inp.val());
		if(remInp.val() != ''){
			inp.val(inp.val()+" \n\nFrom Issue "+remInd+": "+remInp.val());
		}
		inp.elastic();
		
		//add to remove hidden value
		$inp.find('.remove-ind').val(remId);
		
	}
	//end of mergeIssues()


	//
	//TIPTIPS

	//rollovers
	$('#question-background-info').tipTip({
		keepAlive:true,
		forceCenter:true,
		enter:function() {
			$('#tiptip_holder').css('max-width',800);
		}
	});

	function setIssueTipTip($isNH)
	{
		var $issNameHolder;
		if($isNH)
		{
			$issNameHolder = $isNH;
		} else
		{
			$issNameHolder = $('.issue-name-holder');
		}
		$issNameHolder.off('mouseover');
		var curTip;
		$issNameHolder.mouseover(function() {
			curTip = $(this);
		})

		$issNameHolder.tipTip({
			content:function() {
				var form = curTip.parent();
				var $asVals = form.find('.as-values');
				if($asVals.length == 0)
				{
					$asVals = form.find('.issue-tags');
				}
				var tgs = $asVals.val();
				if(tgs.charAt(0) == ',')
				{
					tgs = tgs.substr(1);
				}
				if(tgs.charAt(tgs.length-1) == ',')
				{
					tgs = tgs.substr(0,tgs.length-1);
				}
				tgs = tgs.replace(/,/g,', ');
				var ret = "DESCRIPTION: "+form.find('.issue-description').val()+"<br/><br/>"+
									"UNITS: "+form.find('.issue-units').val()+"<br/><br/>"+
									"TAGS: "+tgs;
				return ret;
			},
			defaultPosition:"top",
			enter:function() {
				$('#tiptip_holder').css('max-width',500);
			}
		});
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

		//reset name size
		positionNameInput($nin);


		$inp.parent().find('.arrow').addClass('arrow-up');
		if(skipAnim)
		{
			$inp.find('.title-label').hide();
			$inp.find('.fading-inputs').hide();
			$inp.css({
				'height':closedHeight
			})
			if($st.find('.issue-name-static').text() != $nin.val())
			{
				$inp.find('.quick-save-but').show();
			}
			//save this issue
			if(isNS == undefined)
			{
				$inp.find('.quick-save-but').trigger('click');	
			}
		} else
		{
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
			})
		}
		

    //unfocus any inputs
		$inp.find('textarea,.issue-input').blur();

		//reset tiptip
		setIssueTipTip($st);
	}

	function openIssue($inp,skipAnim)
	{
		$inp.parent().find('.arrow').removeClass('arrow-up');
		if(skipAnim)
		{
			$inp.find('.title-label').show();
			$inp.find('.fading-inputs').show();
		} else
		{
			$inp.find('.title-label').fadeIn();
			$inp.find('.fading-inputs').fadeIn();	
		}
		var $st = $inp.find('.issue-name-holder');
		var $nin = $inp.find('.issue-name');
		var $isInfo = $inp.find('.issue-name-holder .input-info');
		var $ta = $inp.find('.issue-description');
		$nin.width(inputWidth);
		$ta.width(inputWidth);
		//text area reszing
		$ta.height($ta.height+$nin.height()*$ta.prop('scrollHeight'));
		
		//make notes same size as textarea
		$inp.find('.issue-notes').height($ta.height());
		//make notes same height as description (better looking)

		//hide quick save button
		$inp.find('.quick-save-but').hide();
		$st.hide();
		$nin.show();
		$nin.blur();
		$isInfo.hide();

		//add tagging
		tagIssue($inp.find('.issue-tags'));

		if(skipAnim)
		{
			$inp.css({
		  	'height':Math.max($inp.data('openHeight'),minInputHeight)
			})
			$inp.removeAttr('style');
			$isInfo.hide();
			setShadowFromOpen($inp);
			setIssueFocusEvents($inp);
		} else
		{
			$inp.animate({
			  	'height':Math.max($inp.data('openHeight'),minInputHeight)
			},500,function() {
				$inp.removeAttr('style');
				$isInfo.hide();
				setShadowFromOpen($inp);
				setIssueFocusEvents($inp);
			})	
		}	


	}

	function setShadowFromOpen($inp)
	{
		var isEdit = false;
		$inp.find('.issue-input,.as-values').each(function() {
			//categories isn't always complete
			if($(this).val() == '' && $(this).hasClass('issue-tags') == false && $(this).hasClass('issue-notes') == false)
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

	function goViewMode($inp)
	{
		if($inp.find('.shadowy').length == 0)
		{
			return;
		}
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
		$inp.find('.issue-input,.as-selections').off('mouseenter mouseleave');
		$inp.find('.issue-input,.as-selections').css('color','#000');
		$inp.find('.as-selections .as-selection-item').css('color','#000');
	}

	function removeIssueFocusEvents($inp)
	{
		$inp.find('.issue-input,.as-selections').each(function() {
			$(this).off('mousedown blur');
		})

		$inp.closest('.issue-box').off('mousedown');
	}

	function setIssueFocusEvents($inp)
	{
		//removeIssueFocusEvents($inp);
		var $form = $inp.find('form');
		$inp.data('oldForm',$form.serialize());
		$inp.find('.issue-input,.as-selections').each(function() {
			//add input ref to this data
			$(this).data('input',$inp);
			//on click, go into edit mode
			$(this).mousedown(function(e) {
				e.stopPropagation();
				goEditMode($(this).data('input'));
			})
			$(this).blur(function() {
				//see if current data is same as old data
				if($(this).data('input').data('oldForm') == $(this).data('input').find('form').serialize())
				{
					goViewMode($(this).data('input'));
				}
			})
		})
		$inp.find('textarea').elastic();
		$inp.closest('.issue-box').mousedown(function(e) {
			$(this).find('*:focus').blur();
		})
	}

	function goEditMode($inp)
	{
		$inp.find('.issue-input,.as-selections').addClass('shadowy');
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

	function slideInIssue()
	{
		numIssues++;
		var ind = numIssues;
		//make sure all but last are closed
		//$
		$('.issue-box').each(function() {
			if($('.issue-box').index($(this))< ind && $(this).find('.arrow-up').length == 0)
			{
				//close
				$(this).show();
				closeIssue($(this).find('.input'));
			}
		})
		//now fade in first box and slide in issue list
		//clone and empty first issue box
		var $iss1 = $('.issue-box').eq(0);
		$iss1.draggable('destroy');
		$iss1.droppable('destroy');
		$cl = $iss1.clone(true,true);
		setDragAndDropMerge($iss1);
		$cl.find('.number span').text(numIssues);
		$cl.find('.issue-ind').val(numIssues);
		$cl.find('.issue-name').val('');
		$cl.find('.issue-name-static').text('');
		$cl.find('.issue-id').val('');
		$cl.find('.issue-description').val('');
		$cl.find('.issue-notes').val('');
		$cl.find('.issue-units').val('');
		$cl.find('.delete-dd').val($('options:first', $cl.find('.delete-dd')).val());
		$cl.find('.delete-dd').hide();
		$cl.find('.flag-dd').val($('options:first', $cl.find('.flag-dd')).val());
		$cl.find('.type-dd').val($('options:first', $cl.find('.type-dd')).val());
		$cl.find('.issue-type').val('');
		$cl.find('.remove-ind').val('');
		$cl.find('.is-goal').removeAttr('checked');
		$cl.find('.votes').val(1);
		$cl.find('.votes-text').text(1);

		setIssueColor($cl.find('.issue-dds'));
		
		$('#issue-list-bottom').before($cl);

		//also remove tags
		if($cl.find('.as-selections').length != 0)
		{
			$cl.find('.as-selections').off();
			$cl.find('.as-selections').after("<input name='categories' type='text' class='issue-input issue-tags' value=''/>")
			$cl.find('.as-selections').remove();
			$cl.find('.as-results').remove();
		} else
		{
			$cl.find('.issue-tags').val('');
		}
		tagIssue($cl.find('.issue-tags'))

		//add events back in
		setDragAndDropMerge($cl);

		$cl.fadeIn(500,function() {
			//scroll page to bottom
			$.scrollTo($('.add-new-button'),300);
		});
		$('.issue-list').animate({
			'margin-top':0
		})
		var $inp = $cl.find('.input');
		setIssueTipTip();
		openIssue($inp);
		goEditMode($inp);

	}


	//title click and edit button
	$('.issue-name-static').click(function(e) {
		e.stopPropagation();
		$(this).parent().parent().hide();
		var $in = $(this).parent().parent().parent().find('.issue-name');
		positionNameInput($in);
		//shorten so save button fits
		$in.show();
		$in.focus();
		goEditMode($(this).parent().parent().parent().parent());
		//show quick save but
		$(this).parent().parent().parent().find('.quick-save-but').show();
	})

	$('.issue-name').focusout(function() {
		//see if should rehide input since didn't chagne
		if($(this).val() == $(this).parent().find('.issue-name-static').text() && $(this).val() != "" && $(this).parent().parent().find('.quick-save-but').is(':visible'))
		{
			$(this).hide();
			$(this).parent().find('.issue-name-holder').show();
			$(this).parent().find('.quick-save-but').hide();
		}
	})


	$('.issue-name').keypress(function() {
		if($(this).parent().find('.fading-inputs').is(':hidden'))
		{
			$(this).parent().find('.quick-save-but').show();
		}
	})


	$(document).scroll(function() {
		checkQuestionPosition();
		$('#tiptip_holder').hide();
	})


	$('#question-space').height($('#question-box').height()+60);


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


	//for hiding instructions
	var instructionsScrollPos = 0;
	var isInstructionsHidden = false;
	$('#hide-instructions').click(function() {
		if(isInstructionsHidden)
		{
			isInstructionsHidden = false;
			instructionsScrollPos = 0;
			$(this).text('Hide Instructions');
		} else
		{
			isInstructionsHidden = true;
			instructionsScrollPos = -1*$(this).parent().parent().height()+15;
			$(this).text('Show Instructions');
		}
		//animate to scroll pos
		$(this).parent().parent().animate({
			'top':instructionsScrollPos
		})
	})




	function setTagsCurVal(form)
	{
		form.find('.issue-tags').each(function() {
			$(this).data('curVal',$(this).val());
		})
	}


	//adding an issue
	$('.add-new-button').click(function() {
		slideInIssue();
	})

	//AJAX FOR SAVING ISSUES
	$('.save-button,.quick-save-but').click(function(e) {
		e.stopPropagation();

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
		positionDeleteText($delT);
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
		//send issue to db
		var formData = form.serialize();

		var tagId;
		if(formData.indexOf('as_values') != -1)
		{
			tagId = formData.split('as_values_')[1].split('=')[0];	
		} else
		{
			//if no tag setup yet
			tagId = new Date().getTime();
			formData += "&as_values_"+tagId+"="+form.find('.issue-tags').val();
		}
		formData += "&tag_id="+tagId;

		$.ajax({
			type: 'POST',
		  url: baseURL+"issues/save_node",
		  data: formData,
		  success: function(id) {
		  	isNotSaved = false;

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
					form.find('.issue-name-holder').show();
		    }


		    //see if added all issue data and if not, color grey
		    if(form.find('.issue-name').val() == '' || form.find('.issue-description').val() == '' || form.find('.issue-units').val() == '' || form.find('.issue-tags').val() == '')
		    {
		    	form.parent().parent().addClass('na');	
		    } else
		    {
		    	//color white
		    	form.parent().parent().removeClass('na');
		    }

				if(form.find('.issue-id').val() == '')
				{
					form.find('.issue-id').val(id);
				}

		    //change color back to waht should be
		    setIssueColor(form.find('.issue-dds'));



	    	if(isQuickVis == false)
	    	{
		    	setInputsCurVal(form);
		    	closeIssue(form.parent(),true);	
	    	}

	    	//trigger final issue if saving all
	    	$(document).trigger('isFinishingLater');

		    //hide merge dd
		    //reset flag if set to merge
		    if(form.find('.remove-ind').val() != '')
		    {
					form.find('.flag-dd').val($('options:first', form.find('.flag-dd')).val());
					form.find('.delete-dd').val($('options:first', form.find('.delete-dd')).val());
			    form.find('.delete-dd').hide();
			    //hide cancel merge-but
			    form.find('.cancel-merge').hide();

					//remove merged div
					var remInd = form.find('.remove-ind').val();
					//remove dd option from all delete-dds
					$('.delete-dd').each(function() {
						$(this).find('option[value="'+remInd+'"]').remove();
					})
					//remove div associated
					var $remInp = $('.input').filter(function() {
						if($(this).find('.issue-id').val() == remInd)
						{
							return true;
						}
					})

					$remInp.parent().remove();

		    }
		  },
		  error: function() {
				alert('Error Saving, Please check your internet connection');
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
		var $delT = $iss.find('.delete-text');
		$delT.show();
		positionDeleteText($delT);
		$delT.text('Please wait, deleting issue...');
		var id = $iss.find('.issue-id').val();
		$.ajax({
			type: 'POST',
		  url: baseURL+"issues/delete_node",
		  data: 'id='+id,
		  success: function(d) {
		    $iss.find('.delete-text').delay(1000).fadeOut(500,function() {
					//remove and move div to end of file, then renumber
					$iss.hide();
					$iss.remove();
					
		    });
		  }
		});
	}

	function positionDeleteText($dT)
	{
		var r = $dT.parent().find('.issue-dds').width()+46;
		$dT.css('right',r);
	}

	//$nI = $('.issue-name')
	function positionNameInput($nI)
	{
		var $div = $nI.parent().parent().parent();
		var ddW = $div.find('.issue-dds').width();
		$nI.width(Math.min(650-ddW,600));
		$div.find('.quick-save-but').css('left',$nI.width()+73);

		//for easier dragging
		var $iNS = $div.find('.issue-name-static');
		if($iNS.width() > $nI.width())
		{
			$iNS.width($nI.width()+73);	
		} else 
		{
			$iNS.css('width','auto');
		}
	}


	function setIssueColor($div)
	{
		var val = $div.find('.type-dd').val();
		var val2 = $div.find('.flag-dd').val();
		var goal = $div.find('.is-goal').is(':checked');
		
		if(val2 == 'revisit')
		{
			$div.parent().parent().parent().css('border-color',flagBorderColor);	
		} else if(goal)
		{
			$div.parent().parent().parent().css('border-color',goalBorderColor);
		} else if(val2 == 'complete')
		{
			$div.parent().parent().parent().css('border-color',completeBorderColor);	
		} else
		{
			$div.parent().parent().parent().css('border-color',normalBorderColor);
		}
		if(val == -1)
		{
			//negative
			$div.parent().parent().parent().animate({
			   	backgroundColor: negColor
			});	
		} else if(val == 1)
		{
			//positive
			$div.parent().parent().parent().animate({
			   	backgroundColor: posColor
			});	
			
		} else if(val == -2)
		{
			//positive
			$div.parent().parent().parent().animate({
			   	backgroundColor: ambigColor
			});	
		} else
		{
			//neither
			$div.parent().parent().parent().animate({
			   	backgroundColor: ambigColor
			});	
		}
	
	}

	//goal checkbox 
	$('.is-goal').click(function(e) {
		e.stopPropagation();
		setIssueColor($(this).parent().parent());
		ddSave($(this).parent());
	})

	//dd functionality
	$('.flag-dd').change(function(e) {
		e.stopPropagation();
		var val = $(this).val();
		if(val == 'delete')
		{
			$(this).parent().find('.delete-dd').val($('options:first', $(this).parent().find('.delete-dd')).val());
			$(this).parent().find('.delete-dd').show();
		} else
		{
			//make sure delete is hidden
			$(this).parent().find('.delete-dd').hide();
			$(this).parent().find('.delete-dd').val($('options:first', $(this).parent().find('.delete-dd')).val());
			//save via ajax
			ddSave($(this));
		}
	})

	$('.type-dd').change(function(e) {
		e.stopPropagation();
		var ddVal = $(this).val();
		var dd = $(this);
		//set hidden value in form and save to db
		$(this).parent().parent().find('.issue-type').val(ddVal);
		//save to db via ajax
		ddSave($(this));
	});

	function ddSave(dd)
	{
		//save issue if closed
		if(dd.parent().parent().height() < minInputHeight)
		{
			dd.parent().parent().find('.quick-save-but').trigger('click');	
		}
		setIssueColor(dd.parent());
		
	}

	$('.delete-dd').change(function(e) {
		e.stopPropagation();
		var remId = $(this).val();
		if(confirm("Are you sure you want to delete this issue and add a vote to issue #"+remId))
		{
			var $addInp = $('.issue-box').filter(function() {
				if($(this).find('.issue-id').val() == remId)
				{
					return true;
				}
			})
			var $div = $(this).parent().parent().parent().parent();
			var $vInp = $addInp.find('.votes');
			var vts = Number($vInp.val())+Number($div.find('.votes').val());
			$vInp.val(vts);
			$addInp.find('.votes-text').text(vts);
			//save issue
			$addInp.find('.quick-save-but').trigger('click');

			//now remove current issue
			deleteIssue($div);

		} else
		{
			//reset dd
			$(this).val($('options:first', $(this)).val());
			
		}


	})


	$('.cancel-merge').click(function(e) {
		e.stopPropagation();
		var $inp = $(this).parent().parent().parent().parent();
		var inp = $inp.find('.issue-name');
		inp.val(inp.data('beforeMerge'));
		var inp = $inp.find('.issue-description');
		inp.val(inp.data('beforeMerge'));
		var inp = $inp.find('.issue-units');
		inp.val(inp.data('beforeMerge'));
		var inp = $inp.find('.votes');
		inp.val(inp.data('beforeMerge'));
		$inp.find('.votes-text').text(inp.val());


		//tagging
		var tags = $inp.data('beforeMergeTags');
		var pfAR = tags.split(',');
		var pf = [];
		pf.items = [];
		for(var i=0;i<pfAR.length;i++)
		{
			if(pfAR[i] != "")
			{
				pf.items.push({value:pfAR[i],name:pfAR[i]});	
			}
		}
		$inp.find('.as-selections').after("<input name='categories' type='text' class='issue-input issue-tags' value=''/>")
		$inp.find('.as-selections').remove();
		$inp.find('.as-results').remove();
		$inp.find('.issue-tags').autoSuggest(baseURL+'issues/get_node_tags', {
				selectedItemProp: "name", 
				searchObjProps: "name",
				startText: "",
				preFill:pf.items,
				emptyText:""
		});


		var inp = $inp.find('.issue-notes');
		inp.val(inp.data('beforeMerge'));

		var $sv = $(this).parent().find('.saving-anim');
	  $sv.text('Merged Canceled!');
	  $sv.delay(1000).fadeOut();
	  setIssueColor($inp.find('.issue-dds'));

	  //reset merge dd
		$inp.find('.delete-dd').val($('options:first', $inp.find('.delete-dd')).val());
		$inp.find('.delete-dd').hide();
		$inp.find('.flag-dd').val($('options:first', $inp.find('.flag-dd')).val());

		//hide this button
		$(this).hide();

		//show hidden previously removed div
		var remId = $inp.find('.remove-ind').val();
		$inp.find('.remove-ind').val('');
		var $isId = $('#issue-list .issue-id').filter(function() {
			if($(this).val() == remId)
			{
				return true;
			}
		})

		$isId.parent().parent().parent().show();
		
		
	})




	//
	//FILTERING AND SORTING

	//filtering and sorting
	$('#issue-text-filter').focus(function() {
		if($(this).val() == 'Filter issues...')
		{
			$(this).val('');	
		}
	})


	function sortAlpha(a,b){
    return $(a).find('.issue-name').val().toLowerCase() > $(b).find('.issue-name').val().toLowerCase() ? 1 : -1;
	};

	function sortNegative(a,b)
	{
		var val1 = $(a).find('.type-dd').val();
		var val2 = $(b).find('.type-dd').val();
		if(val1 == -2)
		{
			val1 = 0.5;
		}
		if(val2 == -2)
		{
			val2 = 0.5;
		}
		return val1 - val2;
	}

	function sortPositives(a,b)
	{
		var val1 = $(a).find('.type-dd').val();
		var val2 = $(b).find('.type-dd').val();
		if(val1 == -2)
		{
			val1 = 0.5;
		}
		if(val2 == -2)
		{
			val2 = 0.5;
		}
		return val2 - val1;
	}

	function sortFlag(a,b){
    if($(a).find('.flag-dd').val() == 'revisit' && $(b).find('.flag-dd').val() != 'revisit')
    {
			return -1;
    } else if($(a).find('.flag-dd').val() != 'revisit' && $(b).find('.flag-dd').val() == 'revisit')
    {
			return 1;
    } else
    {
			return 0;
    }
	};

	function sortComplete(a,b){
    if($(a).find('.flag-dd').val() == 'complete' && $(b).find('.flag-dd').val() != 'complete')
    {
			return -1;
    } else if($(a).find('.flag-dd').val() != 'complete' && $(b).find('.flag-dd').val() == 'complete')
    {
			return 1;
    } else
    {
			return 0;
    }
	};

	function sortNumeric(a,b)
	{
		return $(a).find('.number span').text() - $(b).find('.number span').text();
	}

	$('#issue-sort').change(function() {

		//reset others
		$('#issue-text-filter').val('Filter issues...');
    //reset tag filter
    $('#tag-filter').val($('options:first', $('#tag-filter')).val());	


		//scroll to top
		$.scrollTo($('#question-space'),300);

		removeFilterLine();
		//close all rows
		//$('.close-issues-button').trigger('click');

		if($(this).val() == 'alpha')
		{
			$('.issue-box').sort(sortAlpha).appendTo('#issue-list');
		} else if($(this).val() == 'negative')
		{
			$('.issue-box').sort(sortNegative).appendTo('#issue-list');
		} else if($(this).val() == 'enable')
		{
			$('.issue-box').sort(sortPositives).appendTo('#issue-list');
		} else if($(this).val() == 'flag')
		{
			$('.issue-box').sort(sortFlag).appendTo('#issue-list');
		} else if($(this).val() == 'complete')
		{
			$('.issue-box').sort(sortComplete).appendTo('#issue-list');
		} else if($(this).val() == 'number')
		{
			$('.issue-box').sort(sortNumeric).appendTo('#issue-list');
		}
	})

	function removeFilterLine()
	{
		$('#issue-list .filter-line').remove();
		hasLine = false;	
	}


	var curTag;
	var curFilter;
	var hasLine = false;
	function sortTag(a,b)
	{
		var ret;
		var $tgs = $(a).find('.as-values');
		if($tgs.length == 0)
		{
			$tgs = $(a).find('.issue-tags');
		}
		var $tgs2 = $(b).find('.as-values');
		if($tgs2.length == 0)
		{
			$tgs2 = $(b).find('.issue-tags');
		}
		var t1 = $tgs.val().toLowerCase();
		var t2 = $tgs2.val().toLowerCase();

		curTag = curTag.toLowerCase();
		
		if(t1.indexOf(curTag) != -1)
		{
			if(t2.indexOf(curTag) != -1)
			{
				ret = 0;	
			} else
			{
				ret = -1;
			}
		} else if(t2.indexOf(curTag) != -1)
		{
			ret = 1;
		} else
		{
			ret = 0;
		}
		return ret;
	}

	function sortFilter(a,b)
	{
		var t1 = $(a).find('form').serialize().toLowerCase();
		var t2 = $(b).find('form').serialize().toLowerCase();
		curFilter = curFilter.toLowerCase();
		if(t1.indexOf(curFilter) != -1)
		{
			if(t2.indexOf(curFilter) != -1)
			{
				ret = 0;	
			} else
			{
				ret = -1;
			}
		} else if(t2.indexOf(curFilter) != -1)
		{
			ret = 1;
		} else
		{
			ret = 0;
		}
		return ret;
	}

	$('#tag-filter').change(function() {
		//scroll to top
		$.scrollTo($('#question-space'),300);
		//remove filter line if there
		removeFilterLine();
		//close all rows
		//$('.close-issues-button').trigger('click');

		curTag = $(this).val();
		$('.issue-box').sort(sortTag).appendTo('#issue-list');

		//clear tag text filter
		$('#issue-text-filter').val('Filter issues...');

		//draw line between ones that have tag and ones that dont
		$('.issue-box').each(function() {
			var $tgs = $(this).find('.as-values');
			if($tgs.length == 0)
			{
				$tgs = $(this).find('.issue-tags');
			}
			if($tgs.val().toLowerCase().indexOf(curTag.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>');
				return false;
			}
		})
	})



	$('#issue-text-filter').keypress(function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13)
    {
			//scroll to top
			$.scrollTo($('#question-space'));
    	doFilter();
	    e.preventDefault();
	    //reset tag filter
	    $('#tag-filter').val($('options:first', $('#tag-filter')).val());	
    }
	})

	$('#issue-text-filter').focusout(function() {
		if($(this).val() == "")
		{
			$(this).val('Filter issues...');
		}
	})

	function doFilter()
	{
		removeFilterLine();
		//close all rows
		//$('.close-issues-button').trigger('click');

		curFilter = $('#issue-text-filter').val();
		$('.issue-box').sort(sortFilter).appendTo('#issue-list');

		//draw line between ones that have tag and ones that dont
		$('.issue-box').each(function() {
			if($(this).find('form').serialize().toLowerCase().indexOf(curFilter.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>')
			}
		})
	}


	//opening and closing rows

	function showOpeningRows() {
			hideRowStatus();
		$('.opening-closing-rows-status p').eq(0).show();
		$('.opening-closing-rows-status').fadeIn();
	}
	function showClosingRows() {
			hideRowStatus();
		$('.opening-closing-rows-status p').eq(1).show();
		$('.opening-closing-rows-status').fadeIn();
	}
	function hideRowStatus() {
		$('.opening-closing-rows-status p').hide();
		$('.opening-closing-rows-status').hide();
	}

	$('.close-issues-button').click(function() {
		showClosingRows();
		var ind = $('.issue-box').length-1;
		var closeInt = setInterval(function() {
			var $iss = $('.issue-box').eq(ind);
			if(ind < 0)
			{
				clearInterval(closeInt);
				hideRowStatus();
			}
			if($iss.find('.arrow-up').length == 0)
			{
				closeIssue($iss.find('.input'),true,true);
			}
			ind--;
		},200);
	})

	$('.open-issues-button').click(function() {
		showOpeningRows();
		var ind = 0;
		var openInt = setInterval(function() {
			var $iss = $('.issue-box').eq(ind);
			if($iss.length == 0)
			{
				clearInterval(openInt)
				hideRowStatus();
			}
			if($iss.find('.arrow-up').length == 1)
			{
				openIssue($iss.find('.input'),true);
			}
			ind++;
		},200);
	})



	//for saving and finishing later
	$('.save-all-button').click(function() {
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
		//alert(savingForLater+" : "+curSaving);
		if(savingForLater == curSaving)
		{
			showFinishedText();
			
		}
	})

	function beginFinishedSave()
	{
		$('#finished-text').text('Please wait, saving all issues...');
		$('#finished-text').fadeIn();

		savingForLater = 0;
		curSaving
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
		//and reset value
		savingForLater = -1;
		curSaving = 0;
		
	}


})