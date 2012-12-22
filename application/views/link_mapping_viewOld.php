<script type="text/javascript">
$(document).ready(function() {

	var $bodyEle;
	var $curDiv;
	if($.browser.safari || $.browser.chrome)
	{
		$bodyEle = $('body');
	} else
	{
		$bodyEle = $('html');
	}

	//initially show first from link map
	var ind = 0;
	if(window.location.hash != '')
	{
		ind = window.location.hash.substr(1);
	}
	$curDiv = $('.from-link-map').eq(ind);
	$curDiv.show();

	$(window).scroll(function() {
		checkQuestionPosition();
		checkFromArrowPosition();
	})

	$('#question-space').height($('#question-box').height());

	function checkQuestionPosition()
	{
		if($bodyEle.scrollTop()>190)
		{
			$('#question-box').css({
				'position':'fixed',
				'top':instructionsScrollPos
			})
			$('#hide-instructions').show();
		} else if($bodyEle.scrollTop()<190)
		{
			$('#question-box').removeAttr('style');
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
			instructionsScrollPos = -1*$(this).parent().height();
			$(this).text('Show Instructions');
		}
		//animate to scroll pos
		$(this).parent().animate({
			'top':instructionsScrollPos
		})

		var arScPos;
		arScPos = 450;
		if($bodyEle.scrollTop()>arScPos)
		{
			$('.from-holder').animate({
				'top':$('#question-space').height()+50+instructionsScrollPos
			})
		}
	})



	$(document).resize(function() {
		checkFromArrowPosition();
	})

	function checkFromArrowPosition()
	{
		var arScPos;
		if(instructionsScrollPos != 0)
		{
			arScPos = 450;
		} else {
			arScPos = 290;
		}
		if($bodyEle.scrollTop()>arScPos)
		{
			$('.from-holder').css({
				'position':'fixed',
				'top':$('#question-space').height()+50+instructionsScrollPos,
				'left':$bodyEle.width()/2-$('#page').width()/2-7
			})
		} else if($bodyEle.scrollTop()<arScPos)
		{
			$('.from-holder').removeAttr('style');
			$('.from-holder').css({
				'position':'absolute'

			})
		}
	}


	
	//tiptip tooltips
	$('.input-info').tipTip({
		enter:function() {
			$('#tiptip_holder').css('max-width',500);
		}
	});
	$('.input-info-wide').tipTip({
		enter:function() {
			$('#tiptip_holder').css('max-width',500);
		}
	});


	//link arrow coloring
	$('.link-but-filled-background').each(function() {
		var sign = $(this).parent().find('.link-sign').val();
		var strength = $(this).parent().find('.link-strength').val();
		var col = getLinkArrowColor(sign,strength);
		$(this).css('background',col);
	})


	//next prev from issue linking 
	$('.next-but').click(function() {
		$curDiv = $(this).parent().parent().parent().parent().parent();
		var canMove = true;
		$curDiv.find('.link-but').each(function() {
			if($(this).is(':visible'))
			{
				canMove = false;
				return false;
			}
		})
		if(canMove == false)
		{
			alert("Please Enter in Attributes for all issues by clicking the 'Add Attributes' button beside all issues with links before moving on to the next issue.");
			return;
		}
		//reset filters
		resetFilters();
		var ind = $('.from-link-map').index($curDiv);
		ind++;
		window.location.hash = ind;
		var $newDiv = $('.from-link-map').eq(ind);
		$curDiv.fadeOut(500,function() {
			$newDiv.fadeIn();
			$curDiv = $newDiv;
			var $curToNodes = $curDiv.find('.to-nodes');
			$curDiv.find('.to-holder').sort(sortNumeric).appendTo($curToNodes);
		})
	})

	$('.prev-but').click(function() {
		$curDiv = $(this).parent().parent().parent().parent().parent();
		var canMove = true;
		$curDiv.find('.link-but').each(function() {
			if($(this).is(':visible'))
			{
				canMove = false;
				return false;
			}
		})
		if(canMove == false)
		{
			alert("Please Enter in Attributes for all issues by clicking the 'Add Attributes' button beside all issues with links before moving on to the next issue.");
			return;
		}
		//reset filters
		resetFilters();
		var ind = $('.from-link-map').index($curDiv);
		ind--;
		window.location.hash = ind;
		var $newDiv = $('.from-link-map').eq(ind);
		$curDiv.fadeOut(500,function() {
			$newDiv.fadeIn();
			$curDiv = $newDiv;
			var $curToNodes = $curDiv.find('.to-nodes');
			$curDiv.find('.to-holder').sort(sortNumeric).appendTo($curToNodes);
		})
	})

	function resetFilters()
	{
		var $cl = $('#arrow-filters');
		$cl.find('#link-sort').val($('options:first', $cl.find('#link-sort')).val());	
		$cl.find('#tag-filter').val($('options:first', $cl.find('#tag-filter')).val());	
		$cl.find('#issue-sort').val($('options:first', $cl.find('#issue-sort')).val());	
		$('#issue-text-filter').val('Search');
	}



	$('.to-node').hover(function() {
		$(this).find('.to-node-over').show();
	},
	function() {
		$(this).find('.to-node-over').hide();
	})

	$('.to-node').click(function() {
		//show loading text for link
		var $lnk = $(this).parent().find('.loading-link');
		//make sure not already a link
		if($lnk.parent().find('.link-but').is(':visible') || $lnk.parent().find('.link-but-filled').is(':visible'))
		{
			return;
		}
		$lnk.text('Please Wait, Creating Link...');
		$lnk.fadeIn();
		//create link and send to db
		var fromId = $(this).parent().parent().parent().find('.from-hidden').val();
		var toId = $(this).parent().find('.to-hidden').val();
		$.ajax({
			type:'POST',
			url:baseURL+'links/create_user_link',
			data:'from_id='+fromId+'&to_id='+toId,
			success: function(d) {
				$lnk.hide();
				//insert link id into link
				$lnk.parent().find('.link-id').val(d);
				//also set default values
				$lnk.parent().find('.link-sign').val(-9);
				$lnk.parent().find('.link-strength').val(-9);
				$lnk.parent().find('.link-certainty').val(-9);
				$lnk.parent().find('.link-but').show();
			}
		});
	})


	//for adding attributes
	var $curLink;
	$('.link-but,.link-but-filled').mousedown(function() {
		$curLink = $(this);
	})
	$('.link-but,.link-but-filled').fancybox({
		autoSize:false,
		width:977,
		height:460,
		beforeLoad:function() {
			var fIt = $curLink.parent().parent().parent().parent().find('.from-issue-type').val();
			var fId = $curLink.parent().parent().parent().parent().find('.from-hidden').val();
			var fDesc = $curLink.parent().parent().parent().parent().find('.from-node .input-info-hidden').val();
			var fName = $curLink.parent().parent().parent().parent().find('.from-node .name').text();
			var tIt = $curLink.parent().parent().find('.to-issue-type').val();
			var tId = $curLink.parent().parent().find('.to-hidden').val();
			var tDesc = $curLink.parent().parent().find('.to-node .input-info-hidden').val();
			var tName = $curLink.parent().parent().find('.to-node .name').text();
			//link vals
			var lId = $curLink.parent().find('.link-id').val();
			var lSign = $curLink.parent().find('.link-sign').val();
			var lStrength = $curLink.parent().find('.link-strength').val();
			var lComment = $curLink.parent().find('.link-comment').val();
			var lCertainty = $curLink.parent().find('.link-certainty').val();
			var lModified = $curLink.parent().find('.link-modified').val();


			//now set form values
			//first arrows text
			var $linkForm = $('#user-link-form');

			//claer form bind events if any from other times
			$linkForm.find('.link-remove-but,.link-form-save-but,.link-sign,.link-strength,.link-certainty').unbind();
			$linkForm.find('.link-save-text').text('');

			$linkForm.find('.from-arrow-name').text(fName);
			$linkForm.find('.to-arrow-name').text(tName);
			$linkForm.find('.from-arrow-content .input-info-wide').attr('title',fDesc);
			$linkForm.find('.from-arrow-content .input-info-wide').tipTip({
				enter:function() {
					$('#tiptip_holder').css('max-width',500);
				}
			})
			$linkForm.find('.to-arrow-content .input-info-wide').attr('title',tDesc);
			$linkForm.find('.to-arrow-content .input-info-wide').tipTip({
				enter:function() {
					$('#tiptip_holder').css('max-width',500);
				}
			})
			var signTxt = '';
			//now link arrow text
			if(lModified == '0000-00-00 00:00:00')
			{
				$linkForm.find('.link-arrow-content .link-values').html('<br/><br/><br/>');	
				//reset values in form before setting
				$linkForm.find('.link-comment').val('');
				$linkForm.find('.link-sign').val(-9);
				$linkForm.find('.link-strength').val(-9);
				$linkForm.find('.link-certainty').val(-9);
			} else
			{

				var signTxt = '';
				if(lSign == -1)
				{
					signTxt = '-';
				} else if(lSign == 1)
				{
					signTxt = '+';
				} else if(lSign == '0')
				{
					signTxt = '0';
				}


				if(lStrength == -9)
				{
					strengthTxt = ''
				} else
				{
					strengthTxt = lStrength;
				}
				if(lCertainty == -9)
				{
					certTxt = ''
				} else
				{
					certTxt = lCertainty;
				}
				$linkForm.find('.link-arrow-content .link-values').html(signTxt+'<br/>'+strengthTxt+'<br/>'+certTxt);	

				//now form elements
				$linkForm.find('.link-comment').val(lComment);
				$linkForm.find('.link-sign').val(lSign);
				$linkForm.find('.link-strength').val(lStrength);
				$linkForm.find('.link-certainty').val(lCertainty);
			}

			$linkForm.find('.link-id').val(lId);

			//delete link
			$linkForm.find('.link-remove-but').click(function() {
				//delete link
				if(confirm('Are you sure you want to delete this link?'))
				{
					$linkForm.find('.link-save-text').text('Please Wait, Deleting Link...');
					$linkForm.find('.link-save-text').fadeIn();
					//delete link and send to db
					var lId = $linkForm.find('.link-id').val();
					data = 'link_id='+lId;
					$.ajax({
						type:'POST',
						url:baseURL+'links/delete_user_link',
						data:data,
						success: function(d) {
							$.fancybox.close();
							//delay and then fade out any link buttons
							$curLink.parent().find('.link-but-filled-background,.link-but-filled,.link-but').delay(1000).fadeOut();
							//clear link values
							$curLink.parent().find('.link-sign').val('');
							$curLink.parent().find('.link-strength').val('');
							$curLink.parent().find('.link-certainty').val('');
						}
					});	
				}
			})
			//save in form
			$linkForm.find('.link-form-save-but').click(function() {


				//save data to specific link arrow

				var strength = $linkForm.find('.link-strength').val();
				var certainty = $linkForm.find('.link-certainty').val();
				var sign = $linkForm.find('.link-sign').val();
				var signTxt = 0;
				if(sign == -1)
				{
					signTxt = '-';
				} else if(sign == 1)
				{
					signTxt = '+';
				} 
				//check to see if filled out all values
				if(strength == -9 || sign == -9 || certainty == -9)
				{
					alert('Please select a Sign, Strength and Certainty for this link before saving.');
					return;
				}


				$linkForm.find('.link-save-text').text('Please Wait, Saving Link...');
				$linkForm.find('.link-save-text').fadeIn();



				var data = $linkForm.find('form').serialize();
				$.ajax({
					type:'POST',
					url:baseURL+'links/save_user_link',
					data:data,
					success: function(d) {
						$linkForm.find('.link-save-text').text('Link Saved!');
						$linkForm.find('.link-save-text').delay(1000).fadeOut();
						
						$curLink.parent().find('.link-sign').val(sign);
						$curLink.parent().find('.link-strength').val(strength);
						$curLink.parent().find('.link-certainty').val(certainty);
						var col = getLinkArrowColor(sign,strength);
						var $bk = $curLink.parent().find('.link-but-filled-background');
						$bk.css('background',col);
						$bk.find('.sign').html('SI<br/>'+signTxt);
						$bk.find('.strength').html('ST<br/>'+strength);
						$bk.find('.certainty').html('C<br/>'+certainty);
						$curLink.parent().find('.link-but').delay(1000).fadeOut(500,function() {
							$bk.fadeIn();
							$curLink.parent().find('.link-but-filled').fadeIn();
						});
						//close fancybox
						$.fancybox.close();
					}
				});	
			})

			//dd listeners
			$linkForm.find('.link-sign').change(function() {
				setLinkArrow();
			})

			$linkForm.find('.link-strength').change(function() {
				setLinkArrow();
			})

			$linkForm.find('.link-certainty').change(function() {
				setLinkArrow();
			})

			function setLinkArrow()
			{
				var sign = $linkForm.find('.link-sign').val();
				var signTxt = '';
				if(sign == -1)
				{
					signTxt = '-';
				} else if(sign == 1)
				{
					signTxt = '+';
				} else if(sign == 0)
				{
					signTxt = '0';
				}
				var strength = $linkForm.find('.link-strength').val();
				if(strength == -9)
				{
					strength = '';
				}
				var certainty = $linkForm.find('.link-certainty').val();
				if(certainty == -9)
				{
					certainty = '';
				}
				$linkForm.find('.link-values').html(signTxt+'<br/>'+strength+'<br/>'+certainty);
				var col = getLinkArrowColor(sign,strength);
				$('#user-link-form .link-arrow-background').css('background',col);
			}


			//set colors for arrows in form
			var fColor;
			if(fIt == -2)
			{
				fColor = '#e1dee1';
			} else if(fIt == -1)
			{
				fColor = '#fcd7d0';
			} else if(fIt == 0)
			{
				fColor = '#ffffff';
			} else if(fIt == 1)
			{
				fColor = '#d1fadb';
			} else if(fIt == 2)
			{
				fColor = '#f4d5fa';
			} 
			var tColor;
			if(tIt == -2)
			{
				tColor = '#e1dee1';
			} else if(tIt == -1)
			{
				tColor = '#fcd7d0';
			} else if(tIt == 0)
			{
				tColor = '#ffffff';
			} else if(tIt == 1)
			{
				tColor = '#d1fadb';
			} else if(tIt == 2)
			{
				tColor = '#f4d5fa';
			} 

			var lColor = getLinkArrowColor(lSign,lStrength);
			

			//change arrow colors
			$('#user-link-form .from-arrow-background').css('background',fColor);
			$('#user-link-form .to-arrow-background').css('background',tColor);
			$('#user-link-form .link-arrow-background').css('background',lColor);



			

		}
	});
	$('.link-but-filled').fancybox({
		autoSize:false,
		width:977,
		height:460
	});

	function getLinkArrowColor(lSign,lStrength)
	{
		var lColor = '#ffffff';
		if(lSign == 1)
		{
			if(lStrength < 2)
			{
				lColor = '#f0f9fb';
			} else if(lStrength < 4)
			{
				lColor = '#dbf8fd';
			} else
			{
				lColor = '#beeef7';
			}
		} else if(lSign == -1)
		{
			if(lStrength < 2)
			{
				lColor = '#fffcf3';
			} else if(lStrength < 4)
			{
				lColor = '#fcf6e1';
			} else
			{
				lColor = '#fdf2cb';
			}
		} else if(lStrength == 0)
		{	
			if(lStrength < 2)
			{
				lColor = '#f9f9f9';
			} else if(lStrength < 4)
			{
				lColor = '#eeeeee';
			} else
			{
				lColor = '#e4e4e4';
			}
		}
		return lColor;
	}


	//FILTERING
	//
	//
	
	function resetSearchFilter()
	{
		
		$('#issue-text-filter').val('Search');
	}

	function resetTagFilter()
	{
		
		$('#tag-filter').val($('options:first', $('#tag-filter')).val());	
	}
	

	$('#issue-sort').change(function() {
		removeFilterLine();
		resetSearchFilter();
		resetTagFilter();
		var $curToNodes = $curDiv.find('.to-nodes');
		if($(this).val() == 'alpha')
		{
			$curDiv.find('.to-holder').sort(sortAlpha).appendTo($curToNodes);
		} else if($(this).val() == 'numeric')
		{
			$curDiv.find('.to-holder').sort(sortNumeric).appendTo($curToNodes);
		} else if($(this).val() == 'constraint')
		{
			$curDiv.find('.to-holder').sort(sortConstraint).appendTo($curToNodes);
		} else if($(this).val() == 'enable')
		{
			$curDiv.find('.to-holder').sort(sortEnablers).appendTo($curToNodes);
		} else if($(this).val() == 'goal')
		{
			$curDiv.find('.to-holder').sort(sortGoal).appendTo($curToNodes);
		}
	})

	function sortNumeric(a,b)
	{
		return $(a).find('.to-index').text() - $(b).find('.to-index').text();	
	}

	function sortAlpha(a,b){
    return $(a).find('.name').text() > $(b).find('.name').text() ? 1 : -1;
	};

	function sortConstraint(a,b)
	{
		var val1 = $(a).find('.issue-type').val()
		var val2 = $(b).find('.issue-type').val()
		if(val1 == -1 && val2 != -1)
		{
			return -3;
		} else if((val1 == -1 && val2 == -1))
		{
			return 0;
		} else if((val1 != -1 && val2 == -1))
		{
			return 3;
		} else {
			return val2-val1;
		}
	}

	function sortEnablers(a,b)
	{
		var val1 = $(a).find('.issue-type').val()
		var val2 = $(b).find('.issue-type').val()
		if(val1 == -2 && val2 != -2)
		{
			return 2;
		} else if(val1 == -2 && val2 == -2)
		{
			return 0;
		} else if(val1 == -1 && val2 != -1)
		{
			return 3;
		} else if((val1 == -1 && val2 == -1))
		{
			return 0;
		} else if((val1 != -1 && val2 == -1))
		{
			return -3;
		} else {
			return val1-val2;
		}
	}

	function sortGoal(a,b)
	{
		var val1 = $(a).find('.issue-type').val()
		var val2 = $(b).find('.issue-type').val()
		return val2-val1;
		
	}


	function removeFilterLine()
	{
		$curDiv.find('.to-nodes .filter-line').remove();
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

	$('#tag-filter').change(function() {
		removeFilterLine()
		resetSearchFilter();
		curTag = $(this).val();
		var $curToNodes = $curDiv.find('.to-nodes');
		$curDiv.find('.to-holder').sort(sortTag).appendTo($curToNodes);
		//draw line between ones that have tag and ones that dont
		$curDiv.find('.to-holder').each(function() {
			if($(this).find('.issue-tags').val().toLowerCase().indexOf(curTag.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>')
				return false;
			}
		})
	})

	function sortFilter(a,b)
	{
		var t1 = $(a).find('.name').text();
		$(a).find('input').each(function() {
			t1+=$(this).val();
		})
		var t2 = $(b).find('.name').text();
		$(b).find('input').each(function() {
			t2+=$(this).val()
		})
		t1 = t1.toLowerCase();
		t2 = t2.toLowerCase();

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


	//filtering and sorting
	$('#issue-text-filter').focus(function() {
		if($(this).val() == 'Search')
		{
			$(this).val('');	
		}
	})

	$('#issue-text-filter').keypress(function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13)
    {
    	//reset tag filter
    	resetTagFilter();
    	doFilter();
	    e.preventDefault();
    }
	})

	function doFilter()
	{
		removeFilterLine()
		curFilter = $('#issue-text-filter').val();
		var $curToNodes = $curDiv.find('.to-nodes');
		$curDiv.find('.to-holder').sort(sortFilter).appendTo($curToNodes);

		//draw line between ones that have tag and ones that dont
		$curDiv.find('.to-holder').each(function() {
			var t1 = $(this).find('.name').text();
			$(this).find('input').each(function() {
				t1+=$(this).val();
			})
			if(t1.toLowerCase().indexOf(curFilter.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>')
				return false;
			}
		})
	}


	//link filter

	$('#link-sort').change(function() {
		removeFilterLine();
		var $curToNodes = $curDiv.find('.to-nodes');
		if($(this).val() == 'sign')
		{
			$curDiv.find('.to-holder').sort(sortSign).appendTo($curToNodes);
		} else if($(this).val() == 'strength')
		{
			$curDiv.find('.to-holder').sort(sortStrength).appendTo($curToNodes);
		} else if($(this).val() == 'certainty')
		{
			$curDiv.find('.to-holder').sort(sortCertainty).appendTo($curToNodes);
		}
	})

	function sortSign(a,b)
	{
		var val1 = $(a).find('.link-sign').val()
		var val2 = $(b).find('.link-sign').val()
		if(val1 == '')
		{
			val1 = -15;
		}
		if(val2 == '')
		{
			val2 = -15;
		}
		return val2-val1;
	}

	function sortStrength(a,b)
	{
		var val1 = $(a).find('.link-strength').val()
		var val2 = $(b).find('.link-strength').val()
		if(val1 == '')
		{
			val1 = -15;
		}
		if(val2 == '')
		{
			val2 = -15;
		}
		return val2-val1;
	}

	function sortCertainty(a,b)
	{
		var val1 = $(a).find('.link-certainty').val()
		var val2 = $(b).find('.link-certainty').val()
		if(val1 == '')
		{
			val1 = -15;
		}
		if(val2 == '')
		{
			val2 = -15;
		}
		return val2-val1;
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

<!--FEEDBACK-->
<!--<div class='user-feedback-holder'>
	<div class='feedback-but submit-but2'>Feedback</div>
	<div class='user-feedback'>
	<h3>Please send us feedback about this Software.</h3>
	<p>Feedback can help us fix any issues you might find with the software, or simply as a way for you to give us advice on how to better the software.</p>
		<form>
			<textarea name='user_feedback'></textarea>
			<div class='submit-but user-feedback-submit'>Send</div>
			<div class='loading-text'></div>
		</form>
	</div>
</div>-->

<!--HIDDEN DIV FOR OVERLAY OF LINK FORM-->
<div id='user-link-form'>
	<h4>Does the issue on the left have a direct influence on the issue on the right?</h4>
	<div class='arrow'>
		<div class='from-arrow-content'>
			<div class='from-arrow-name'>
			</div>
			<div class='input-info-wide' title=''></div>
		</div>

		<div class='link-arrow-content'>
			<div class='values-holder'>
				<div class='link-values'>

				</div>
				<div class='link-labels'>
					Sign<br/>Strength<br/>Certainty
				</div>
			</div>
		</div>

		<div class='to-arrow-content'>
			<div class='to-arrow-name'>
			</div>
			<div class='input-info-wide' title=''></div>
		</div>

		<div class='arrow-outline'></div>
		<div class='from-arrow-background'></div>
		<div class='link-arrow-background'></div>
		<div class='to-arrow-background'></div>
	</div>
	<form>
		<input type='hidden' class='link-id' name='link_id' value=''/>
		<div class='form-div form-left'>
			<br/>
			<select class='link-sign' name='sign'>
				<option value='-9'></option>
				<option value='-1'>-</option>
				<option value='0' selected='selected'>0</option>
				<option value='1'>+</option>
			</select>
			<br/>
			<label>Sign</label>
			<label>-&nbsp;&nbsp;&nbsp;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;+</label>
		</div>
		<div class='form-div form-middle'>
			<br/>
			<select class='link-strength' name='strength'>
				<option value='-9'></option>
				<option value='1'>1</option>
				<option value='2'>2</option>
				<option value='3'>3</option>
				<option value='4'>4</option>
				<option value='5'>5</option>
			</select>
			<br/>
			<label>Strength</label>
			<div id='strength-bell'>
				<div>Very Weak</div>
				<div class='right'>Very Strong</div>
			</div>
		</div>
		<div class='form-div form-middle-right'>
			<br/>
			<select class='link-certainty' name='certainty'>
				<option value='-9'></option>
				<option value='0'>0</option>
				<option value='1'>1</option>
				<option value='2'>2</option>
			</select>
			<br/>
			<label>Certainty</label>
			<div id='certainty-bell'>
				<div>Uncertain</div>
				<div class='right'>Certain</div>
			</div>
		</div>
		<div class='form-div form-right'>
			<textarea class='link-comment' name='comment'></textarea>
			<label>Comment</label>
		</div>
		<div class='submit-but link-form-save-but'>Save</div>
		<div class='submit-but link-remove-but'>Remove Link</div>
		<div class='link-save-text'></div>
	</form>
</div>

<!--QUESTION BOX-->
<div id='question-box' class='above-box'>
	<div id='question' class='mapping-question'>
	<h2 class='no-margin'>Instructions</h2>
		<ol id='mapping-instructions'>
			<li>Draw a Link from A to B if a change in A directly causes a meaningful change in B by clicking on B.</li>
			<li>Assign Link Attributes by clicking on the &lsquo;Add Attributes&rsquo; Button:<br/>
			<span>Link Sign</span><span title="Positive = an increase in A causes an increase in B<br/><br/>Negative = an increase in A causes a decrease in B<br/><br/>Zero = the sign is uncertain, or 'it depends' (feel free to add a comment!)" class='input-info'></span>
			<span>Link Strength</span><span title="Link Strength is defined on a 5 point scale (where 5 is 'extremely strong' and 1 is 'extremely weak'). The time scale here is 1-3 years." class='input-info'></span>
			<span>Link Certainty</span><span title="The Certainty of link strength is defined on a 3 point scale (2 is 'highly certain' and 0 is 'highly uncertain'). <br/><br/>* If the strength is highly uncertain, assign the maximum possible Strength to the link and note the uncertainty (and feel free to add a comment!)" class='input-info'></span>
			<br/>
			</li>
			<li class='last'>Move relatively quickly. Go with your gut. Don't think too hard!</li>
		</ol>
		<br/>
	</div>
	<div id='mapping-instruction-image'></div>
	<div id='hide-instructions' style='position:absolute; bottom:10px; right:10px;'>Hide Instructions</div>
</div>
<div id='question-space'></div>
<!--LINK MAPPING-->
<div id='link-mapping'>
	<div id='arrow-titles'>
		<h1>From</h1><h1 class='right'>To</h1>
	</div>
	<div id='arrow-filters'>
		<div>
			<select id='link-sort'>
				<option value=''>Sort Links...</option>
				<option value='sign'>Sign</option>
				<option value='strength'>Strength</option>
				<option value='certainty'>Certainty</option>
			</select>
		</div>
		<div id='to-filter'>
			<select id='tag-filter'>
				<option value=''>Filter by Tag...</option>
				<?php foreach($tags as $tag):?>
				<option value='<?php echo $tag ?>'><?php echo $tag ?></option>
				<?php endforeach;?>
			</select>
			<select id='issue-sort'>
				<option value=''>Sort By...</option>
				<option value='numeric'>Numerical</option>
				<option value='alpha'>Alphabetical</option>
				<option value='constraint'>Constraints</option>
				<option value='enable'>Enablers</option>
				<option value='goal'>Goals</option>
			</select>
			<input id='issue-text-filter' value='Search'/>
		</div>
		<br/>
	</div>
	<?php $i = 0;?>
	<?php $tot_from = count($user_from_nodes);?>
	<?php foreach($user_from_nodes as $from_node):?>
	<div class='from-link-map'>
	<form>
		<div class='from-holder'>
			<input type='hidden' class='from-hidden' name='from_id_<?php echo $i?>' value='<?php echo $from_node["id"]?>'/>
			<input type='hidden' class='from-issue-type' value='<?php echo $from_node['issueType']?>'/>
			<div class='from-node'>
				<div class='from-node-content'>
					<div class='name'><?php echo $from_node['name']?></div>
					<?php
						switch ($from_node['issueType']) {
							case -2:
								$isType = '';
								break;
							case -1:
								$isType = '<br/><br/>NOTE: Negative Phrasing, Increase is not Desirable';
								break;
							case 0:
								$isType = '';
								break;
							case 1:
								$isType = '<br/><br/>NOTE: Positive Phrasing, Increase is Desirable';
								break;
							case 2:
								$isType = '<br/><br/>NOTE: One of the High Level Goals';
								break;
							
							default:
								$isType = '';
								break;
						}
					?>
					<?php $tit = htmlspecialchars($from_node["description"],ENT_QUOTES).'<br/><br/>UNITS: '.htmlspecialchars($from_node["units"],ENT_QUOTES).'<br/><br/>ISSUE TYPE: '.$isType?>
					<div class='input-info-wide' title='<?php echo $tit?>'></div>
					<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
				</div>
				<?php if($from_node['issueType'] == 1):?>
				<div class='from-node-background enabler'></div>
				<?php elseif($from_node['issueType'] == -1):?>
				<div class='from-node-background constraint'></div>
				<?php elseif($from_node['issueType'] == 2):?>
				<div class='from-node-background goal'></div>
				<?php elseif($from_node['issueType'] == -2):?>
				<div class='from-node-background na'></div>
				<?php endif;?>

			</div>
			<div class='status'>
				<div class='loader'>
					<?php 
					$rPer = 11+floor(10*$i/$tot_from)*37;
					$bWidth = 380*$i/$tot_from;
					$rNum = round($i/$tot_from*100);
					?>
					<div class='load-percentage' style='left:<?php echo $rPer;?>px'><?php echo $rNum?>%</div>
					<div class='load-cover'></div>
					<div class='load-bar' style='width:<?php echo $bWidth?>px'></div>
				</div>
				<label>I&rsquo;m done making links for this issue. I am ready for the</label>
				<div class='but-holder'>
					<?php if($i != 0):?>
					<div class='submit-but prev-but'>Previous Issue</div>
					<?php endif;?>
					<?php if($i+1 != $tot_from):?>
					<div class='submit-but next-but'>Next Issue</div>
					<?php endif;?>
				</div>
			</div>
		</div>
		<div class='to-nodes'>
		<?php $j=0;?>
		<?php foreach($to_nodes as $to_node):?>
		<?php if($to_node['id'] != $from_node['id']):?>
		<div class='to-holder'>
			<input type='hidden' class='to-hidden' name='to_id_<?php echo $j?>' value='<?php echo $to_node["id"]?>'/>
			<input type='hidden' class='to-issue-type' value='<?php echo $to_node['issueType']?>'/>
			<div class='link-holder'>
				<div class='loading-link'>Please Wait, Creating Link...</div>
				<?php
				$linkAR = '';
				if(isset($user_links[$from_node['id']][$to_node['id']]))
				{
					$linkAR = $user_links[$from_node['id']][$to_node['id']];
				}
				?>
				<?php if($linkAR != ''):?>
					<input type='hidden' class='link-id' value='<?php echo $linkAR->id?>'/>
					<input type='hidden' class='link-comment' value='<?php echo $linkAR->comment?>'/>
					<input type='hidden' class='link-sign' value='<?php echo $linkAR->sign?>'/>
					<input type='hidden' class='link-strength' value='<?php echo $linkAR->strength?>'/>
					<input type='hidden' class='link-certainty' value='<?php echo $linkAR->certainty?>'/>
					<input type='hidden' class='link-modified' value='<?php echo $linkAR->modified?>'/>
					<?php if($linkAR->modified == '0000-00-00 00:00:00'):?>
					<a href='#user-link-form' class='link-but' style='display:block;'>Add Attributes</a>
					<a href='#user-link-form' class='link-but-filled'></a>
					<div class='link-but-filled-background'>
						<div class='sign'></div>
						<div class='strength'></div>
						<div class='certainty'></div>
					</div>
					<?php else:?>
					<a href='#user-link-form' class='link-but'>Add Attributes</a>
					<a href='#user-link-form' class='link-but-filled' style='display:block;'></a>
					<div class='link-but-filled-background' style='display:block;'>
					<?php 
					$signTxt = 0;
					if($linkAR->sign == 1)
					{
						$signTxt = '+';
					} else if($linkAR->sign == -1)
					{
						$signTxt = '-';
					}
					?>
						<div class='sign'>SI<br/><?php echo $signTxt?></div>
						<div class='strength'>ST<br/><?php echo $linkAR->strength?></div>
						<div class='certainty'>C<br/><?php echo $linkAR->certainty?></div>
					</div>
					<?php endif;?>
				<?php else:?>
					<input type='hidden' class='link-id' value=''/>
					<input type='hidden' class='link-comment' value=''/>
					<input type='hidden' class='link-sign' value=''/>
					<input type='hidden' class='link-strength' value=''/>
					<input type='hidden' class='link-certainty' value=''/>
					<a href='#user-link-form' class='link-but'>Add Attributes</a>
					<a href='#user-link-form' class='link-but-filled'></a>
					<div class='link-but-filled-background'>
						<div class='sign'></div>
						<div class='strength'></div>
						<div class='certainty'></div>
					</div>
					<?php endif;?>
			</div>
			<div class='to-node'>
				<div class='to-node-content'>
					<div class='to-index'><?php echo $j+1?></div>
					<div class='name'><?php echo trim($to_node['name'])?></div>
					<?php
						switch ($to_node['issueType']) {
							case -2:
								$isType = '';
								break;
							case -1:
								$isType = '<br/><br/>NOTE: Negative Phrasing, Increase is not Desirable';
								break;
							case 0:
								$isType = '';
								break;
							case 1:
								$isType = '<br/><br/>NOTE: Positive Phrasing, Increase is Desirable';
								break;
							case 2:
								$isType = '<br/><br/>NOTE: One of the High Level Goals';
								break;
							
							default:
								$isType = '';
								break;
						}
					?>
					<?php $tit = htmlspecialchars($to_node["description"],ENT_QUOTES).'<br/><br/>UNITS: '.htmlspecialchars($to_node["units"],ENT_QUOTES).$isType?>
					<div class='input-info-wide' title='<?php echo $tit?>'></div>
					<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
					<input type='hidden' class='issue-tags' value='<?php echo $to_node["categories"]?>'/>
				</div>
				<input type='hidden' class='issue-type' value='<?php echo $to_node["issueType"]?>'/>
				<div class='to-node-over'></div>
				<?php if($to_node['issueType'] == 1):?>
				<div class='to-node-background enabler'></div>
				<?php elseif($to_node['issueType'] == -1):?>
				<div class='to-node-background constraint'></div>
				<?php elseif($to_node['issueType'] == 2):?>
				<div class='to-node-background goal'></div>
				<?php elseif($to_node['issueType'] == -2):?>
				<div class='to-node-background na'></div>
				<?php endif;?>
			</div>
			<div class='clearer'></div>
		</div>
		<?php endif;?>
		<?php $j++;?>
		<?php endforeach;?>
		</div>
		<div class='clearer'></div>
		<br/>
		</form>
	</div>
	<?php $i++;?>
	<?php endforeach;?>
</div>