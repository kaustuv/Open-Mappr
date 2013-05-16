
$(document).ready(function() {
	//vars 
	var goalBorderColor = '#999';
	var flagBorderColor = '#999';
	var completeBorderColor = '#999';
	var normalBorderColor = '#999';
	var posColor = '#ffffff';
	var negColor = '#ffffff';
	var ambigColor = '#ffffff';
	var hoverColor = '#db5bdb';
	var isBegin = false;
	var closedHeight = 21;
	var inputWidth = $('.issue-name').width();
	var minInputHeight = 283;
	var isFirst = true;

	var $bodyEle;
	if($.browser.safari || $.browser.chrome)
	{
		$bodyEle = $('body');
	} else
	{
		$bodyEle = $('html');
	}


	//disable all issue list 
	$('#issue-list input,#issue-list textarea').attr('readonly', 'readonly');

	//show info background
	//to vertically center
	$('#question-info-holder').height($('#question').height()+20);
	$('#question-background-info').fadeIn();
	$('#question').width(800);
	$('.issue-box').show();

	//rollovers
	$('#question-background-info').tipTip({
		keepAlive:true,
		forceCenter:true,
		enter:function() {
			$('#tiptip_holder').css('max-width',800);
		}
	});

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


	//close all issues in list
	//first set initial height



	function closeIssue($inp,skipAnim)
	{

		var $st = $inp.find('.issue-name-holder');
		var $nin = $inp.find('.issue-name');
		var $qsb = $inp.find('.quick-save-but');

		$nin.hide();

		if($.trim($nin.val()) != '')
		{
			$st.css('display','inline-block');
		} 
		
		if($inp.height() > closedHeight)
		{
			//get height for this input
			$inp.css('height','auto');
			$inp.data('openHeight',$inp.height());
		}
		$inp.parent().find('.arrow').addClass('arrow-up');
		if(skipAnim)
		{
			$inp.find('.title-label').hide();
			$inp.find('.fading-inputs').hide();
			$inp.height(closedHeight);
		} else 
		{
			$inp.find('.title-label').fadeOut();
			$inp.find('.fading-inputs').fadeOut();
			$inp.animate({
				'height':closedHeight
			})	
		}

    //unfocus any inputs
		$inp.find('textarea,.issue-input').blur();
	}

	function openIssue($inp,skipAnim)
	{
		var time = 500;
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
		$ta.height($ta.prop('scrollHeight'));
		//make notes elastic
		var $issN = $inp.find('.issue-notes');
		$issN.height($issN.prop('scrollHeight'));
		console.log($inp.find('.issue-tags').prop('scrollHeight'));
		$inp.find('.issue-tags').height($inp.find('.issue-tags').prop('scrollHeight'));

		//hide quick save button
		$inp.find('.quick-save-but').hide();
		$st.hide();
		$nin.show();
		//$nin.blur();
		$isInfo.hide();
		if(skipAnim)
		{
			$inp.css({
		  	'height':Math.max($inp.data('openHeight'),minInputHeight)
			})
			$inp.removeAttr('style');
			$isInfo.hide();

		} else
		{
			$inp.animate({
			  	'height':Math.max($inp.data('openHeight'),minInputHeight)
			},time,function() {
				$inp.removeAttr('style');
				$isInfo.hide();
			})	
		}	
	}


	//
	//get issue list and update
	function reloadIssues()
	{
		$.ajax({
			type: 'POST',
		  url: baseURL+"issues/get_leaderboard_nodes",
		  success: function(jData) {
		  	var json = $.parseJSON(jData);
		  	populateFilters(json.tags);

		  	populateIssues(json.issues);
		  	//fade out loading text
				$('#loading-issues').delay(1000).fadeOut();
		  },
		  error: function() {
		  	//alert('Error loading issues, please check with the administrator.')
		  }
		});
	}

	function reloadEvents()
	{
		//unbind all arrows
		$('.arrow').unbind();
		//opening and closing boxes
		$('.arrow').click(function() {
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

		$('.input').each(function() {
			$(this).data('openHeight',$(this).height());
			var $nin = $(this).find('.issue-name');
			positionNameInput($nin);
		})



	}


	var curTip;
	function setIssueTipTip()
	{
		$('.issue-name-holder').unbind('mouseover');
		$('.issue-name-holder').mouseenter(function() {
			curTip = $(this);
		})

		$('.issue-name-holder').tipTip({
			content:function() {
				if(curTip.length == 0)
				{
					return;
				}
				var form = curTip.parent();
				var tgs = form.find('.issue-tags').val();
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

	//colors
	function setIssueColor($div,issue)
	{
		var val = issue.issueType;
		var val2 = issue.isRevisit;
		var goal = issue.isGoal;
		var $boxDiv = $div.parent().parent().parent();
		if(val2 == 1)
		{
			$boxDiv.css('border-color',flagBorderColor);

		} else if(goal == 1)
		{
			$boxDiv.css('border-color',goalBorderColor);
			$boxDiv.find('.goal-text').text('');
		} else if(val2 == 2)
		{
			$boxDiv.find('.complete-text').text('Flagged');
			$boxDiv.css('border-color',completeBorderColor);	
		} else
		{
			$boxDiv.css('border-color',normalBorderColor);
		}

		if(goal == 1)
		{
			$boxDiv.find('.goal-text').text('Goal');	
		} else
		{
			$boxDiv.find('.goal-text').text('');	
		}

		if(val2 == 1)
		{
			$boxDiv.find('.complete-text').text('Flagged');	
		} else if(val2 == 2)
		{
			$boxDiv.find('.complete-text').text('Complete');	
		} else
		{
			$boxDiv.find('.complete-text').text('');	
		}

		if(val == -1)
		{
			//negative
			$boxDiv.animate({
			   	backgroundColor: negColor
			});	
			$boxDiv.find('.type-text').text('Negative');
		} else if(val == 1)
		{
			//positive
			$boxDiv.animate({
			   	backgroundColor: posColor
			});	
			$boxDiv.find('.type-text').text('Positive');
			
		} else if(val == -2)
		{
			//positive
			$boxDiv.animate({
			   	backgroundColor: ambigColor
			});	
			$boxDiv.find('.type-text').text('Ambiguous');
		} else
		{
			//neither
			$boxDiv.animate({
			   	backgroundColor: "#ffffff"
			});	
			$boxDiv.find('.type-text').text('');
		}

		//show text as to whether neg,pos,ambig and goal,flag
	
	}


	function positionNameInput($nI)
	{
		var $div = $nI.parent().parent().parent();
		$nI.width(600);
		$div.find('.issue-name-static').width(600);
	}

	function populateIssues(issues)
	{
		//for(var i=0;i<issues.length;i++)
		//{
			var totIssues = issues.length;
			var curIssue = 0;
			var issueUpdateInt = setInterval(function() {
				//if done
				if(totIssues == curIssue)
				{
					//tiptips for issue rollover when up
			  	setIssueTipTip();
			  	reloadEvents();
					clearInterval(issueUpdateInt);
					return;
				}

				var issue = issues[curIssue];
				//see if issue is already populated, if not append to list
				var isListed = false;
				$('.issue-box').each(function() {
					if($(this).find('.issue-id').val() == issue.id)
					{
						isListed = true;
						$(this).data('isUpdated',true);
						updateIssue(issue,$(this));
						setIssueColor($(this).find('.issue-dds'),issue);
						return false;
					}
				})
				if(isListed == false)
				{
					createIssue(issue);
				}

				curIssue++;
			},40);

		

		//after updating and creating, see if any need to be deleted
		$('.issue-box').each(function() {
			//alert($(this).data('isUpdated'));	
			if($(this).data('isUpdated'))
			{
				$(this).data('isUpdated',false);
			} else if($(this).hasClass('template-box') == false)
			{
				$(this).remove();
			}

		});

	}

	function updateIssue(issue,$issDiv)
	{
		$issDiv.find('.issue-name').val(issue.name);
		$issDiv.find('.issue-description').val(issue.description);
		$issDiv.find('.issue-units').val(issue.units);
		$issDiv.find('.issue-name-static').val(issue.name);
		$issDiv.find('.issue-notes').val(issue.notes);
		$issDiv.find('.issue-id').val(issue.id);
		$issDiv.find('.number span').text(issue.issueInd);

		//tags
		//remove extra commas
		if(issue.categories.charAt(0) == ',')
		{
			issue.categories = issue.categories.substr(1);
		}
		if(issue.categories.charAt(issue.categories.length-1) == ',')
		{
			issue.categories = issue.categories.substr(0,issue.categories.length-1);
		}
		$issDiv.find('.issue-tags').val(issue.categories.replace(/,/g,', '));
		$issDiv.find('.votes-text').text(issue.votes);

		//$issDiv.find('.issue-description').elastic();
		//$issDiv.find('.issue-notes').elastic();


		return $issDiv;
		
	}

	function createIssue(issue)
	{
		var $issDiv = $('.template-box').clone();
		updateIssue(issue,$issDiv);
		$issDiv.data('isUpdated',true);
		$issDiv.removeClass('template-box');
		$('#issue-list').append($issDiv);
		$issDiv.hide();
		setIssueColor($issDiv.find('.issue-dds'),issue);
		$issDiv.show();
		$issDiv.find('.issue-name').hide();
		$issDiv.find('.issue-name-static').text($issDiv.find('.issue-name').val());
		$issDiv.find('.issue-name-static').show();

		closeIssue($issDiv.find('.input'),true,true);
	}

	function populateFilters(tags)
	{
		var curTagsAR = new Array();
		for(var t in tags)
		{
			var tag = tags[t];
			curTagsAR.push(tag);
			//see if tag already in list
			if($('#tag-filter option[value="'+tag+'"]').length == 0)
			{
				$('#tag-filter').append('<option value="' + tag + '">' + tag + '</option>');
			}
		}

		$('#tag-filter option').each(function() {
			//see if value needs to be removed
			if($.inArray($(this).val(),curTagsAR) == -1 && $(this).val() != "")
			{
				$(this).remove();
			}
		})
		
	}

	//set reload issues on timer
	self.setInterval(function() {
		$('#loading-issues').fadeIn();
		reloadIssues();
	},60000)

	reloadIssues();


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
		var val1 = $(a).find('.type-text').text();
		var val2 = $(b).find('.type-text').text();
		var val1Ind;
		var val2Ind;
		if(val1 == 'Positive')
		{
			val1Ind = 1;
		} else if(val1 == 'Ambiguous')
		{
			val1Ind = .5;
		} else if(val1 == '')
		{
			val1Ind = 0;
		} else if(val1 == 'Negative')
		{
			val1Ind = -1;
		}

		if(val2 == 'Positive')
		{
			val2Ind = 1;
		} else if(val2 == 'Ambiguous')
		{
			val2Ind = .5;
		} else if(val2 == '')
		{
			val2Ind = 0;
		} else if(val2 == 'Negative')
		{
			val2Ind = -1;
		}

		return val1Ind - val2Ind;
	}

	function sortPositives(a,b)
	{
		var val1 = $(a).find('.type-text').text();
		var val2 = $(b).find('.type-text').text();
		var val1Ind;
		var val2Ind;
		if(val1 == 'Positive')
		{
			val1Ind = 1;
		} else if(val1 == 'Ambiguous')
		{
			val1Ind = .5;
		} else if(val1 == '')
		{
			val1Ind = 0;
		} else if(val1 == 'Negative')
		{
			val1Ind = -1;
		}

		if(val2 == 'Positive')
		{
			val2Ind = 1;
		} else if(val2 == 'Ambiguous')
		{
			val2Ind = .5;
		} else if(val2 == '')
		{
			val2Ind = 0;
		} else if(val2 == 'Negative')
		{
			val2Ind = -1;
		}

		return val2Ind - val1Ind;
	}

	function sortFlag(a,b){
		var val1 = $(a).find('.complete-text').text() == 'Flagged';
		var val2 = $(b).find('.complete-text').text() == 'Flagged';
		return val2-val1;
	};

	function sortComplete(a,b){
    
		var val1 = $(a).find('.complete-text').text() == 'Complete';
		var val2 = $(b).find('.complete-text').text() == 'Complete';
		return val2-val1;
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
		$('.close-issues-button').trigger('click');

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
		var t1 = $(a).find('.issue-tags').val().toLowerCase();
		var t2 = $(b).find('.issue-tags').val().toLowerCase();

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
		$('.close-issues-button').trigger('click');

		curTag = $(this).val();
		$('.issue-box').sort(sortTag).appendTo('#issue-list');

		//clear tag text filter
		$('#issue-text-filter').val('Filter issues...');

		//draw line between ones that have tag and ones that dont
		$('.issue-box').each(function() {
			if($(this).find('.issue-tags').val().toLowerCase().indexOf(curTag.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>')
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
		$('.close-issues-button').trigger('click');

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
	$('.close-issues-button').click(function() {
		var ind = 0;
		var closeInt = setInterval(function() {
			var $iss = $('.issue-box').eq(ind);
			if($iss.length == 0)
			{
				clearInterval(closeInt)
			}
			if($iss.find('.arrow-up').length == 0)
			{
				closeIssue($iss.find('.input'),true);
			}
			ind++;
		},10);
	})

	$('.open-issues-button').click(function() {
		var ind = 0;
		var openInt = setInterval(function() {
			var $iss = $('.issue-box').eq(ind);
			if($iss.length == 0)
			{
				clearInterval(openInt)
			}
			if($iss.find('.arrow-up').length == 1)
			{
				openIssue($iss.find('.input'),true);
			}
			ind++;
		},10);
	})


});