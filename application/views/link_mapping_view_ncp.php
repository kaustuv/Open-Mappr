
<script type="text/javascript">
$(document).ready(function() {
	//vars
	var curFromNode;
	var isAnimatingFrom = false;
	var $vimeoContent = $('#vimeo-content').detach();
	var $instructionVideo = $('#instruction-video').detach();
	var issueSubmission = $('#issue-submission').detach();
	var linksJSON = <?php echo json_encode($user_links);?>;
	var totFroms = <?php echo $total_froms?>;
	var curFromId;
	var isCirclesShrunk = false;
	var $curCircle;
	var isOldBrowser = false;

	//reindex from nodes so they correspond to to nodes
	$('.to-holder').each(function() {
		var name = $(this).find('.name').text();
		var ind = $(this).find('.to-index').text();
		//loop through froms and add new index
		$('.from-holder').each(function() {
			if($(this).find('.name').text() == name)
			{
				$(this).find('.from-index').text(ind);
				return;
			}
		})

	})

	//check for older ie and ask to upgrade or use other browser
	if($.browser.msie && Number($.browser.version) < 9)
	{
		isOldBrowser = true;
		$.fancybox.open("The browser that you are using is not supported by Vibrant Data's Mappr Application. Please upgrade or choose a different browser.",{
			maxWidth:600
		})
	}

	$(window).resize(function() {
		$('.from-instructions').css({
			'top':388,
			'right':$(window).width()/2-370
		})
	})

	$(window).trigger('resize');

	$(document).scroll(function() {
		if(curFromNode == undefined)
		{
			return;
		}
		if($(this).scrollTop() > 330 && isAnimatingFrom == false)
		{
			curFromNode.css({
				'position':'fixed',
				'left':$(window).width()/2-$('#link-mapping').width()/2,
				'top':30,
				'z-index':200
			})

			$('#arrow-filters').css({
				'position':'fixed',
				'right':$(window).width()/2-$('#link-mapping').width()/2,
				'top':-4,
				'z-index':100
			})
		} else
		{
			curFromNode.css({
				'position':'relative',
				'left':0,
				'top':0,
				'z-index':0
			})

			$('#arrow-filters').css({
				'position': 'absolute',
				'right': 0,
				'top': 24
			})
		}
	})

	//see if already chosen froms
	$('.from-holder').each(function() {
		if($(this).hasClass('chosen'))
		{
			$(this).fadeTo(300,1);
			$(this).data('isChosen',true);
		}
	})

	//see is wants to edit or has not yet chosen froms
	if($('.from-holder.chosen').length == 0)
	{
		setInstructions();

		$('#circle-title').show();
		$('.below-circles').fadeTo(1,1);

		if($vimeoContent.length == 1 && isOldBrowser == false)
		{

			var $vid = $vimeoContent.clone();
			var $iframe = $vid.find('iframe');
			$iframe.attr('id','vimeo_video');
			$iframe.attr('src',$iframe.attr('src')+"&api=1&player_id=vimeo_video");
			//show instructional video if one
			$.fancybox.open($vid,{
				afterClose:function() {
					//make sure video shuts the fuck up
					$('.fancybox-wrap #vimeo-content iframe').detach();
				},
				afterShow:function() {
					initVideo();
				}
			});
		}
	} else
	{
		goToLinkMapping();
	}

	function goToLinkMapping() 
	{

		//set circles in corner
		if($.browser.msie)
		{
		$('#circle-tags').css({
			scale:.25
		});
		} else
		{
			$('#circle-tags').css({
				scale:.25,
				left:-300,
				top:-232
			});	
		}
		isCirclesShrunk = true;
		$('#circle-section-heading').hide();
		$('#circle-title').fadeTo(1,0);
		$('.below-circles').fadeTo(1,0);
		$('#link-mapping').show();
		//show loading text for to nodes
		//show that is loading
		$('.from-init-heading').hide();
		$('.from-instruct-begin').show();
		$('.from-instruct-finished').hide();
		$('.from-instruct-begin h1.after').show();
		$('.from-instruct-begin h1.init').hide();
		$(this).hide();
		$('.from-instruct-arrow').hide();
		showToNodes();

		curFromNode = $('.from-holder.chosen').eq(0);
	}

	//show link mappinig
	//$('#link-mapping').show();


	//rollovers for initially choosing froms
	$('.from-holder').mouseenter(function() {
			$(this).stop().fadeTo(300,1);	
	})

	$('.from-holder').mouseleave(function() {
		if($(this).data('isChosen') == undefined || $(this).data('isChosen') == false)
		{
			$(this).stop().fadeTo(300,0.4);
		}
	})

	$('.random-froms-but').click(function() {
		//clear any previously selected froms
		$('.from-holder.chosen').removeClass('chosen');
		//get random assortment of froms
		var maxNum = $('.from-holder').length-1;
		var numsAR = new Array();
		for(var i=0;i<totFroms;i++)
		{
			var newN = getRandomInt(0,maxNum);
			if($.inArray(newN,numsAR) != -1)
			{
				i--;
			} else
			{
				numsAR.push(newN);
			}
		}
		for(var i=0;i<numsAR.length;i++)
		{
			$('.from-holder').eq(numsAR[i]).trigger('click');
		}
		curFromNode = $('.from-holder.chosen').eq(0);
		saveFromChosen();
		goToLinkMapping();
		curFromNode.fadeTo(1,1);
	})

	function showInstructionVideo()
	{
		//singleton (issue with random selection calling this (and showToNodes) twice)
		if($('.fancybox-wrap #instruction-video').length == 1)
		{
			return;
		}
		var $vid = $instructionVideo.clone();
		var $iframe = $vid.find('iframe');
		$iframe.attr('id','vimeo_video');
		$iframe.attr('src',$iframe.attr('src')+"&api=1&player_id=vimeo_video");
		$.fancybox.open($vid,{
			afterClose:function() {
				//make sure video shuts the fuck up
				$('.fancybox-wrap #instruction-video iframe').detach();
			},
			afterShow:function() {
				initVideo();
			}
		});

	}

	function initVideo()
	{

		$('#vimeo_video').each(function(){
		  Froogaloop(this).addEvent('ready', ready);
		});

		function ready(playerID){
		  // Add event listerns
		  // http://vimeo.com/api/docs/player-js#events
		  Froogaloop(playerID).addEvent('finish', function() {
		  	$.fancybox.close();
		  });
		  
		}
	}

  /**
   * Utility function for adding an event. Handles the inconsistencies
   * between the W3C method for adding events (addEventListener) and
   * IE's (attachEvent).
   */
  function addEvent(element, eventName, callback) {
      if (element.addEventListener) {
          element.addEventListener(eventName, callback, false);
      }
      else {
          element.attachEvent(eventName, callback, false);
      }
  }

	function getRandomInt (min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	$('.from-holder').click(function() {
		//hide tooltip
		$('#tiptip_holder').hide();
		if(curFromNode && curFromNode.html() == $(this).html())
		{
			return;
		}
		if($(this).data('isChosen') == true)
		{
			$(this).data('isChosen',false);
			$(this).stop(true,true).fadeTo(1,0.4);
		} else
		{
			$(this).data('isChosen',true);	
		}
		if(curFromNode == undefined)
		{
			if($(this).data('isChosen'))
			{
				$(this).addClass('chosen');
			} else
			{
				$(this).removeClass('chosen');
			}
			setInstructions();	
		} else
		{
			curFromNode.data('isChosen',false);
			curFromNode.stop(true,true).fadeTo(300,0.4);
			$('#to-node-set-'+curFromNode.find('.from-id').val()).hide();
			$('.from-nodes-cover').hide();
			//if static, move from node to list
			curFromNode.css({
				'position':'relative',
				'left':0,
				'top':0,
				'z-index':0
			})
			//reassign from node
			curFromNode = $(this);

			var fId = $(this).find('.from-id').val();
			//reset links for this node
			resetLinks(fId);


			var ind = $('.from-holder').filter(':visible').index(curFromNode);
			//scroll to top if need to
			if($(document).scrollTop() > 200)
			{
				$.scrollTo(200,ind*200);
			}

			//show loading text
			$('.from-instructions').show();

			//slide node to top
			animateFromNode(ind);
		}
	})

	function animateFromNode(ind)
	{
		//hide tooltip
		$('#tiptip_holder').hide();
		isAnimatingFrom = true;
		if(ind == 0)
		{
			return;
		}
		var fNode = $('.from-holder').filter(':visible').filter(':first');
		fNode.animate({
			'margin-top':-65,
			'opacity':0
		},200,'linear',function() {
			//move first node to bottom
			fNode.detach().appendTo($('.from-nodes'));
			fNode.fadeTo(1,.4);
			fNode.css('margin-top',0);
			ind = $('.from-holder').filter(':visible').index(curFromNode);
			if(ind != 0)
			{
				animateFromNode(ind);
			} else
			{
				//show loading text
				$('.from-instructions').hide();
				//set links for this node
				var fId = $('.from-holder').filter(':visible').filter(':first').find('.from-id').val();
				setLinks(fId);
				showNewToNodes();

			}
		})

	}

	function showNewToNodes()
	{
		//no longer moving from node to top
		isAnimatingFrom = false;
		//show gradient
		$('.from-nodes-cover').fadeIn(800);

		//show to nodes
		$('#to-node-set-'+curFromNode.find('.from-id').val()).fadeIn(800,function() {
			setIssueTipTips();
		});

	}

	function setIssueTipTips()
	{
		//tiptip tooltips
		$('.from-holder .name,.to-node .name').tipTip({
			defaultPosition:'top',
			enter:function() {
				$('#tiptip_holder').css('max-width',500);
			}
		});

		$('.phrasing-over,.to-close,.another-issue-but,.from-check').tipTip({
			defaultPosition:'top',
			enter:function() {
				$('#tiptip_holder').css('max-width',300);
			}
		})
	}


	function setInstructions()
	{
		//get number of from issues chosen
		var numIss = 0;
		$('.from-holder').each(function() {
			if($(this).data('isChosen') == true)
			{
				numIss++;
			}
		})
		if(numIss == 0)
		{
			$('.from-instructions .from-init-heading').show();
			$('.from-instructions .from-instruct-begin').hide();
		} else
		{
			$('.from-instructions .from-init-heading').hide();
			$('.from-instructions .from-instruct-begin').show();
			$('.from-instructions .from-instruct-begin span').text(numIss);	
		}
	}

	$('.from-instruct-finished').click(function() {

		//also hide top buttons
		$('.set1-buttons').show();
		$('.set2-buttons').hide();

		$('.circle').css({
			backgroundColor:'#d4d4d4'
		})
		$curCircle = undefined;

		//show that is loading
		$('.from-instruct-begin h1.after').show();
		$('.from-instruct-begin h1.init').hide();
		
		$(this).hide();
		$('.from-instruct-arrow').hide();

		saveFromChosen();
	})

	function saveFromChosen()
	{
		var formData = '';
		var i = 0;
		//loop through from nodes and see which ones are chosen and add to data
		$('.from-holder').each(function() {
			if($(this).hasClass('chosen'))
			{
				if(i != 0)
				{
					formData += '&';
				}
				i++;
				var val = $(this).find('.from-id').val();
				formData += 'fromNode'+i+'='+val;
			}
		})

		//save from nodes to db
		$.ajax({
			type: 'POST',
		  url: baseURL+"links/set_from_nodes",
		  data: formData,
		  success: function(ret) {
		  	if(ret == 'true')
		  	{
		  		//show to nodes
		  		showToNodes();

		  		
		  	}
		  },error: function(er) {
		  	alert("I'm sorry, there was an issue connecting to the database. Please contact sundev@brainvise.com");
		  }
		})
	}

	function showToNodes()
	{
		$('#circle-tags').tipTip({
			defaultPosition:'top',
			enter:function() {
				setTimeout(function() {
					var mL = $('#tiptip_holder').css('margin-left').replace("px", "")-300;
					$('#tiptip_holder').css({
						'margin-left':mL
					});
				},10)
			},
			content:"Click here to add to your focal subset"
		})

		//show finished checkboxes
		$('.from-check').show();

		//if no links, then show instruction video
		if(linksJSON.length == 0)
		{
			//show instructional video
			showInstructionVideo();
		}

		//clear webkit transform so fixed from node works
		$('.from-nodes-holder').css({
			'-webkit-transform':'none'
		})
		//hide any from nodes you arent using
		var i = 1;
		$('.from-holder').each(function() {
			if($(this).hasClass('chosen') == false)
			{
				$(this).hide();
			} else
			{
				$(this).show();
				//reset so that rollovers work correctly
				$(this).data('isChosen',false);
				//renumber from nodes
				//$(this).find('.from-index').text(i);
				if(i != 1)
				{
					$(this).fadeTo(1,0).fadeTo(300,.4);
				} else
				{
					curFromNode = $(this);
					$(this).data('isChosen',true);
				}
				i++;

				//duplicate to node set for each from node
				var $tN = $('.to-node-set').filter(':first').clone(true,true);
				var fId = $(this).find('.from-id').val();

				$tN.attr('id','to-node-set-'+fId);
				$tN.find('.from-id').val(fId);

				//remove the to node that has the same from node id
				var $fN = $(this);
				$tN.find('.to-node').each(function() {
					var tId = $(this).find('.to-id').val();
					//hide node corresponding to from node
					if(tId == fId)
					{
						$(this).parent().hide();
					}
				})
				$('.to-nodes').append($tN);

			}
		})

		//get first from node id
		var fId = $('.from-holder.chosen').filter(':first').find('.from-id').val();
		setLinks(fId);

		//set again because of new to-node sets
		setIssueTipTips();


		$('.from-nodes-cover').fadeIn();

		$('#arrow-titles').fadeTo(1,1);
		//hide subset title
		$('#circle-section-heading').hide();
		$('#arrow-filters').show();
		$('.top-link-buttons').show();
		//hide all instructions
		$('.from-instructions').hide();
		//change instruction position so not on filters
		$('.from-instructions').css({
			'top':410
		})

		$('#to-node-set-'+curFromNode.find('.from-id').val()).fadeIn();
	}

	function hideToNodes()
	{
		//kill tiptip on circle-tags
		$('#circle-tags').unbind('hover');
		//clear webkit transform so fixed from node works
		$('.from-nodes-holder').css({
			'-webkit-transform':'translate3d(0, 0, 0)'
		})

		$('.from-instruct-begin h1.after').hide();
		$('.from-instruct-begin h1.init').show();
		$('.from-instruct-finished').show();
		$('.from-instruct-arrow').hide();

		//hide any from nodes you arent using
		var i = 1;
		$('.from-holder').each(function() {
			if($(this).hasClass('chosen'))
			{
				$(this).data('isChosen',true);
				$(this).fadeTo(1,1);
			} else
			{
				$(this).fadeTo(1,.4);
			}
			$(this).show();
			i++;
		})
		setInstructions();

		//curFromNode = undefined;

		//remove all to node sets
		$('.to-node-set').not(":eq(0)").remove();



		$('.from-nodes-cover').hide();

		$('#arrow-titles').fadeTo(1,0);
		//hide subset title
		//$('#circle-section-heading').show();
		$('#arrow-filters').hide();
		//$('.top-link-buttons').hide();
		//hide all instructions
		$('.from-instructions').show();
		//change instruction position so not on filters
		$('.from-instructions').css({
			'top':388
		})

	}


	function resetLinks(fId)
	{
		//see if any links in set of to nodes corr to this 
		//from id
		$('#to-node-set-'+fId+' .link-holder').each(function() {
			var $lFB = $(this).find('.link-but-filled-background');
			var $lBS = $(this).find('.link-but-set');
			var $lN = $lFB.parent();

			//remove comments
			$lN.find('.link-comment').val('');
			var $com2 = $lN.find('.link-comment2');
			if($com2.length != 0)
			{
				$com2.val('');
			}
			//show link buts
			$(this).find('.create-links-buts').show();
			if($lBS.is('.incoming,.outgoing,.both,.both2,.both3'))			
			{
				$lBS.hide();
				$lBS.attr('class','link-but-set');
				//remove any divs that have a number on end
				$lBS.find('div').each(function() {
					var cl = $(this).attr('class');
					var sub = cl.substr(cl.length-1);
					if(sub == 2 || sub == 3)
					{
						$(this).remove();
					}
					$(this).removeAttr('style');
					$(this).unbind();
				})
			}
			if($lFB.is('.incoming,.outgoing,.both'))
			{
				$lFB.hide();
				$lFB.attr('class','link-but-filled-background');
				//remove any divs that have a number on end
				$lFB.find('div').each(function() {
					var cl = $(this).attr('class');
					var sub = cl.substr(cl.length-1);
					if(sub == 2 || sub == 3)
					{
						$(this).remove();
					}
					$(this).removeAttr('style');
					$(this).unbind();
				})
			}
		})
	}

	function setLinks(fromId)
	{
		curFromId = fromId;
		fromLoop:
		for (var fId in linksJSON)
		{
			toLoop:
			for(var tId in linksJSON[fId])
			{
				//first check that tId or fId does not equal current from
				if(tId != curFromId && fId != curFromId)
				{
					continue toLoop;
				}
				if(linksJSON[tId])
				{
					if(linksJSON[tId][fId] && linksJSON[tId][fId] != '' && linksJSON[fId][tId] && linksJSON[fId][tId] != '')
					{
						//is both link
						setBothLink(fId,tId);
						continue toLoop;
					}	
				}
				if(linksJSON[fId][tId] != '')
				{
					setSingleLink(fId,tId);		
				}
			}
		}
	}

	function setBothLink(fId,tId)
	{
		//know both links, so find nodes and draw links
		var $fN = $('.from-holder.chosen').filter(function() {
			if($(this).find('.from-id').val() == fId)
			{
				return true;
			}
			return false;
		})
		//find to node
		var $tN = $('#to-node-set-'+fId+' .to-holder').filter(function() {
			if($(this).find('.to-id').val() == tId)
			{
				return true;
			}
			return false;
		})

		//see if found a from node
		if($fN.length == 1 && $tN.find('.create-links-buts').is(':hidden'))
		{
			//hide link buts
			$tN.find('.create-links-buts').hide();
			//draw a link
			var json = linksJSON[fId][tId];
			var json2 = linksJSON[tId][fId];
			//set link id
			$tN.find('.link-id').val(json['id']);
			$tN.find('.link-id2').val(json2['id']);
			//see if top link (outgoing) is set
			var isTopLinkSet = false;
			if(json['modified'] == '0000-00-00 00:00:00')
			{

				//fade in link create button
				$tN.find('.create-links-buts').hide();
				var $lBFB = $tN.find('.link-but-filled-background').addClass('both');
				//add extra comment to incoming link arrow
				addCommentBox($lBFB);
				$lBFB.append('<div class="link-positive-corr2"></div>');
				$lBFB.append('<div class="link-negative-corr2"></div>');
				$lBFB.fadeIn();
				setLinkCorrRollovers($lBFB);
				setLinkCorrClicks($lBFB);

			} else
			{
				isTopLinkSet = true;
				//actual link set
				//fade in link create button
				var $lBFB = $tN.find('.link-but-filled-background').addClass('both');
				//add extra comment to incoming link arrow
				addCommentBox($lBFB);
				$lBFB.append('<div class="link-positive-corr2"></div>');
				$lBFB.append('<div class="link-negative-corr2"></div>');
				//hide top arrow link components
				$lBFB.find('.link-close').hide();
				$lBFB.find('.link-comment').hide();
				$lBFB.find('.link-negative-corr').hide();
				$lBFB.find('.link-positive-corr').hide();
				$lBFB.show();
				setLinkCorrRollovers($lBFB);
				setLinkCorrClicks($lBFB);

				var $setLink = $tN.find('.link-but-set');
				$setLink.addClass('both');

				//see if pos or neg
				if(json['sign'] == -1)
				{
					//fade in link set
					$setLink.find('.link-negative-corr-set').show();
				} else
				{
					//is positive
					$setLink.find('.link-positive-corr-set').show();
				}
				$setLink.show();
			}
			if(json2['modified'] != '0000-00-00 00:00:00')
			{
				//fade in link set
				var $setLink = $tN.find('.link-but-set');
				addCommentBox($setLink);
				if(isTopLinkSet)
				{
					$setLink.addClass('both3');
					$lBFB.find('.link-close').hide();
				} else
				{
					$setLink.find('.link-comment').hide();
					$setLink.find('.link-close').hide();
					$setLink.addClass('both2');
				}
				//see if pos or neg
				if(json2['sign'] == -1)
				{
					//duplicate comment box and pos and neg set arrows
					$setLink.append('<div class="link-negative-corr-set2"></div>');
					$setLink.find('.link-negative-corr-set2').show();
				} else
				{
					//is positive
					//fade in link set
					$setLink.append('<div class="link-positive-corr-set2"></div>');
					$setLink.find('.link-positive-corr-set2').show();
				}
				//hide link-but-filled below
				$lBFB.find('.link-comment2').hide();
				$lBFB.find('.link-negative-corr2').hide();
				$lBFB.find('.link-positive-corr2').hide();
				$setLink.fadeIn();

				setCommentClicks($setLink);
			} 

			//set comments
			$tN.find('.link-comment .comment-textfield').val(json['comment']);
			if(json['comment'] != '')
			{
				$tN.find('.link-comment').addClass('comment-filled');
			} 
			//set comments
			$tN.find('.link-comment2 .comment-textfield').val(json2['comment']);
			if(json2['comment'] != '')
			{
				$tN.find('.link-comment2').addClass('comment-filled');
			} 


		}

	}

	function setSingleLink(fId,tId)
	{
		if(curFromId == fId)
		{
			setOutgoingLink(fId,tId);		
		}
		if(curFromId == tId)
		{
			setIncomingLink(fId,tId);	
		}
		
	}

	function setIncomingLink(fId,tId)
	{//set incoming link if opposites included
		//find from node
		var $fN2 = $('.from-holder.chosen').filter(function() {
			if($(this).find('.from-id').val() == tId)
			{
				return true;
			}
			return false;
		})
		//find to node
		var $tN2 = $('#to-node-set-'+tId+' .to-holder').filter(function() {
			if($(this).find('.to-id').val() == fId)
			{
				return true;
			}
			return false;
		})
		//see if found a from node
		if($fN2.length == 1)
		{
			//draw a link
			var json = linksJSON[fId][tId];
			//set link id
			$tN2.find('.link-id').val(json['id']);
			//set comments
			$tN2.find('.link-comment .comment-textfield').val(json['comment']);
			//
			if(json['comment'] != '')
			{
				$tN2.find('.link-comment').addClass('comment-filled');
			} 
			if(json['modified'] == '0000-00-00 00:00:00')
			{
				//fade in link create button
				$tN2.find('.create-links-buts').hide();
				var $lBFB2 = $tN2.find('.link-but-filled-background').addClass('incoming');
				$lBFB2.fadeIn();
				setLinkCorrRollovers($lBFB2);
				setLinkCorrClicks($lBFB2);

			} else
			{
				//actual link set
				//fade in link create button
				$tN2.find('.create-links-buts').hide();
				var $lBFB2 = $tN2.find('.link-but-filled-background').addClass('incoming');
				//$lBFB2.fadeIn();
				setLinkCorrRollovers($lBFB2);
				setLinkCorrClicks($lBFB2);

				//see if pos or neg
				if(json['sign'] == -1)
				{
					//fade in link set
					var $setLink2 = $tN2.find('.link-but-set');
					$setLink2.addClass('incoming');
					$setLink2.find('.link-negative-corr-set').show();
					$setLink2.fadeIn();
				} else
				{
					//is positive
					//fade in link set
					var $setLink2 = $tN2.find('.link-but-set');
					$setLink2.addClass('incoming');
					$setLink2.find('.link-positive-corr-set').show();
					$setLink2.fadeIn();
				}
			}
		}
	}

	function setOutgoingLink(fId,tId)
	{
		//set outgoing link if froms are set for it
		//find from node
		var $fN = $('.from-holder.chosen').filter(function() {
			if($(this).find('.from-id').val() == fId)
			{
				return true;
			}
			return false;
		})
		//find to node
		var $tN = $('#to-node-set-'+fId+' .to-holder').filter(function() {
			if($(this).find('.to-id').val() == tId)
			{
				return true;
			}
			return false;
		})
		//see if found a from node
		if($fN.length == 1)
		{
			//draw a link
			var json = linksJSON[fId][tId];
			//set link id
			$tN.find('.link-id').val(json['id']);
			//set comments
			$tN.find('.link-comment .comment-textfield').val(json['comment']);
			if(json['comment'] != '')
			{
				$tN.find('.link-comment').addClass('comment-filled');
			}
			if(json['modified'] == '0000-00-00 00:00:00')
			{
				//fade in link create button
				$tN.find('.create-links-buts').hide();
				var $lBFB = $tN.find('.link-but-filled-background').addClass('outgoing');
				$lBFB.fadeIn();
				setLinkCorrRollovers($lBFB);
				setLinkCorrClicks($lBFB);

			} else
			{
				//actual link set
				//fade in link create button
				$tN.find('.create-links-buts').hide();
				var $lBFB = $tN.find('.link-but-filled-background').addClass('outgoing');
				//$lBFB.fadeIn();
				setLinkCorrRollovers($lBFB);
				setLinkCorrClicks($lBFB);

				//see if pos or neg
				if(json['sign'] == -1)
				{
					//fade in link set
					var $setLink = $tN.find('.link-but-set');
					$setLink.addClass('outgoing');
					$setLink.find('.link-negative-corr-set').show();
					$setLink.fadeIn();
				} else
				{
					//is positive
					//fade in link set
					var $setLink = $tN.find('.link-but-set');
					$setLink.addClass('outgoing');
					$setLink.find('.link-positive-corr-set').show();
					$setLink.fadeIn();
				}
			}
		}
	}



	//tiptip for instructions
	$('.instruction-info-but').tipTip({
  	content:"Click to view instructions again."
	});

	$('.instruction-info-but').click(function() {
		$.fancybox.open(<?php echo json_encode($link_mapping_instructions)?>,{
			maxWidth:900
		});
	})


	$('.instruction-video-but').tipTip({
		enter:function() {
			$('#tiptip_holder').css('max-width',500);
		}});

	$('.instruction-video-but').click(function() {
		showInstructionVideo();
	})

	function checkForNoLinks(fAR)
	{
		var undoneText = '';
		//loop through linksJSON to see if each from node has links

		//check for any json of from node that has to nodes and link
		for(var i=0;i<fAR.length;i++)
		{
			//also check to see if set in to node set
			if($('#to-node-set-'+fAR[i]).find('.incoming,.outgoing,.both').length == 0)
			{
				var $fN = $('.from-holder-'+fAR[i]);
				//get the from id corresponding and list issue as one
				//that might need to be worked on
				var fId = $fN.find('.from-id').val();
				var fName = $('.from-holder-'+fId+' .name').text();
				var fInd = $('.from-holder-'+fId+' .from-index').text();
				undoneText += "<li>"+fInd+": "+fName+"</li>";
			}
		}

		var retStr = "";
		if(undoneText != '')
		{
			retStr = "<h1 class='zero'>Are You Sure You're Finished?</h1>";
			retStr += "<p>The following from your subset do not have any links assigned:</p>";
			retStr += "<ul style='margin:10px;'>"+undoneText+"</ul>";

		}
		return retStr;
	}

	function checkCompletedLinks()
	{
		var incompleteText = "";
		$('.link-but-filled-background').each(function() {
			var isIncomplete = false;
			if($(this).is('.incoming,.outgoing,.both'))
			{
				if($(this).next().is('.incoming,.outgoing,.both3') == false)
				{
					isIncomplete = true;
				} else
				{
					//also check for both done halfway
				}
			}

			if(isIncomplete)
			{
				//get from id and to id for this link
				var fId = $(this).parent().parent().parent().find('.from-id').val();
				var fInd = $('.from-holder-'+fId).find('.from-index').text();
				var tInd = $(this).parent().parent().find('.to-index').text();
				incompleteText += "<li>Focal Subset Issue: "+fInd+" to Issue: "+tInd+"</li>";
			}
		})

		var retStr = "";
		if(incompleteText != "")
		{
			retStr = "<h1 class='zero'>You have incomplete links!</h1>";
			retStr += "<p>Please address the following incomplete links:</p>";
			retStr += "<ul style='margin:10px;'>"+incompleteText+"</ul>";
		}
		return retStr;
	}



	$('.finished-links-but').click(function() {

		//for resetting from id once set all links needed
		var oldFromId = curFromId;

		//get all from node ids
		var fAR = new Array();
		$('.from-holder.chosen').each(function() {
			var fId = $(this).find('.from-id').val();
			fAR.push(fId);
		})

		//make sure links visually set if any sets have no links
		for(var i=0;i<fAR.length;i++)
		{
			//set links for from if none set
			if($('#to-node-set-'+fAR[i]).find('.incoming,.outgoing,.both').length == 0)
			{
				setLinks(fAR[i]);
			}
		}

		//check to see if at least one link in each from

		var finishedContent = checkCompletedLinks(fAR);
		
		if(finishedContent == "")
		{
			finishedContent = checkForNoLinks(fAR);
		}
		if(finishedContent == "")
		{
			finishedContent = $('#finished-content').html();
		}
		$.fancybox.open(finishedContent,{
			maxWidth:600
		});

		//reset current from id once set links for all froms
		curFromId = oldFromId;
	})

	$('.another-issue-but').click(function() {
		$.fancybox.open(issueSubmission.clone(),{
			beforeShow:function() {
				//submitting issue to eric via email
				$('#issue-submit-but').click(function() {
					//make sure all needed inputs are filled out
					if($('.issue-name').val() == "" || $('.issue-description').val() == "")
					{
						alert("Please fill out at least a name and description for the issue.");
						return;
					}
					//show saving text
					$('.saving-anim').show();
					var formData = $('#issue-form').serialize();
					$.ajax({
						type: 'POST',
					  url: baseURL+"links/create_new_issue",
					  data: formData,
					  success: function(ret) {
					  	if(ret == 'true')
					  	{
					  		//show success
					  		$('.saving-anim').text('Issue Saved!').delay(1000).fadeOut(300,function() {
					  			//close fancybox
					  			$.fancybox.close();
					  		})
					  	} else
					  	{
					  		alert("Error saving issue, please try again later.")
					  	}
					  }
					})
				})
			},
			afterClose:function() {
				$('.fancybox-wrap #issue-submission').detach();
			},
			afterShow:function() {
				//make overflow visible for tag box
				$('.fancybox-inner').css('overflow','visible');
			}
		})
		//set up autocomplete of tags
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

	})



	//create link buttons
	$('.incoming-link-but').click(function() {

		//save link to db
		var $lL = $(this).parent().parent().find('.loading-link');
		var $lBFB = $(this).parent().parent().find('.link-but-filled-background').addClass('incoming');
		//fade out all create links buts
		$(this).parent().fadeOut(300,function() {
			//show loading text
			$lL.show();
			var $tN = $(this).parent().parent();
			//get to and from id
			var tId = curFromNode.find('.from-id').val();
			var fId = $(this).parent().parent().find('.to-id').val();
			var data = "from_id="+fId+"&to_id="+tId+"&direction=incoming";
			$.ajax({
				type:"POST",
				url:baseURL+'links/create_user_link',
				data:data,
				cache:false,
				success:function(ret) {
					var retJSON = $.parseJSON(ret);
					$tN.find('.link-id').val(retJSON.link_id);
					//show incoming link arrow
					$lL.stop().hide();
					$lBFB.fadeIn();
					setLinkCorrRollovers($lBFB);
					setLinkCorrClicks($lBFB);

					//add class to from node letting know that user has begun
					curFromNode.find('.from-node-background').addClass('is-begun');

					//add link to current json
					if(linksJSON[fId] == undefined || linksJSON[fId] == '')
					{
						linksJSON[fId] = new Object();	
					}
					linksJSON[fId][tId] = new Object();	
					linksJSON[fId][tId]['issueFromId'] = fId;
					linksJSON[fId][tId]['issueToId'] = tId;
					linksJSON[fId][tId]['id'] = retJSON.link_id;
					linksJSON[fId][tId]['comment'] = '';
					linksJSON[fId][tId]['sign'] = '-9';
					linksJSON[fId][tId]['modified'] = '0000-00-00 00:00:00';
					

				}
			})
		})
		


	})

	$('.outgoing-link-but').click(function() {

		var $lL = $(this).parent().parent().find('.loading-link');
		var $lBFB = $(this).parent().parent().find('.link-but-filled-background').addClass('outgoing');

		//fade out all create links buts
		$(this).parent().fadeOut(300,function() {
			//show loading text
			$lL.show();
			var $tN = $(this).parent().parent();
			//get to and from id
			var fId = curFromNode.find('.from-id').val();
			var tId = $(this).parent().parent().find('.to-id').val();
			var data = "from_id="+fId+"&to_id="+tId+"&direction=outgoing";
			$.ajax({
				type:"POST",
				url:baseURL+'links/create_user_link',
				data:data,
				cache:false,
				success:function(ret) {
					var retJSON = $.parseJSON(ret);
					$tN.find('.link-id').val(retJSON.link_id);
					//show incoming link arrow
					$lL.stop().hide();
					$lBFB.fadeIn();
					setLinkCorrRollovers($lBFB);
					setLinkCorrClicks($lBFB);

					//add class to from node letting know that user has begun
					curFromNode.find('.from-node-background').addClass('is-begun');

					//update json
					if(linksJSON[fId] == undefined || linksJSON[fId] == '')
					{
						linksJSON[fId] = new Object();	
					}
					linksJSON[fId][tId] = new Object();	
					linksJSON[fId][tId]['issueFromId'] = fId;
					linksJSON[fId][tId]['issueToId'] = tId;
					linksJSON[fId][tId]['id'] = retJSON.link_id;
					linksJSON[fId][tId]['comment'] = '';
					linksJSON[fId][tId]['sign'] = '-9';
					linksJSON[fId][tId]['modified'] = '0000-00-00 00:00:00';
					

				}
			})
		});

	})

	$('.both-link-but').click(function() {

		var $lL = $(this).parent().parent().find('.loading-link');
		var $lBFB = $(this).parent().parent().find('.link-but-filled-background').addClass('both');
		//fade out all create links buts
		$(this).parent().fadeOut(300,function() {
			//show loading text
			$lL.show();
			var $tN = $(this).parent().parent();
			//to and from id
			var fId = curFromNode.find('.from-id').val();
			var tId = $(this).parent().parent().find('.to-id').val();
			var data = "from_id="+fId+"&to_id="+tId+"&both=true&direction=both";
			$.ajax({
				type:"POST",
				url:baseURL+'links/create_user_link',
				data:data,
				cache:false,
				success:function(ret) {
					var retJSON = $.parseJSON(ret);
					$tN.find('.link-id').val(retJSON.link_id);
					if(retJSON.link_id2)
					{
						$tN.find('.link-id2').val(retJSON.link_id2);
					}
					//show incoming link arrow
					$lL.stop().hide();
					//add extra comment to incoming link arrow
					addCommentBox($lBFB);
					$lBFB.append('<div class="link-positive-corr2"></div>');
					$lBFB.append('<div class="link-negative-corr2"></div>');
					$lBFB.fadeIn();
					setLinkCorrRollovers($lBFB);
					setLinkCorrClicks($lBFB);
					
					//add class to from node letting know that user has begun
					curFromNode.find('.from-node-background').addClass('is-begun');

					//update json
					if(linksJSON[fId] == undefined || linksJSON[fId] == '')
					{
						linksJSON[fId] = new Object();	
					}
					linksJSON[fId][tId] = new Object();	
					linksJSON[fId][tId]['issueFromId'] = fId;
					linksJSON[fId][tId]['issueToId'] = tId;
					linksJSON[fId][tId]['id'] = retJSON.link_id;
					linksJSON[fId][tId]['comment'] = '';
					linksJSON[fId][tId]['sign'] = '-9';
					linksJSON[fId][tId]['modified'] = '0000-00-00 00:00:00';

					if(linksJSON[tId] == undefined || linksJSON[tId] == '')
					{
						linksJSON[tId] = new Object();	
					}
					linksJSON[tId][fId] = new Object();	
					linksJSON[tId][fId]['issueToId'] = fId;
					linksJSON[tId][fId]['issueFromId'] = tId;
					linksJSON[tId][fId]['id'] = retJSON.link_id2;
					linksJSON[tId][fId]['comment'] = '';
					linksJSON[tId][fId]['sign'] = '-9';
					linksJSON[tId][fId]['modified'] = '0000-00-00 00:00:00';

				}
			})
		});

	})

	function addCommentBox($div)
	{
		//see if is link set and there's a comment from before
		$div.append('<div class="link-comment2"><div class="link-comment-box"><h3>Link Comment</h3><div class="close-but"></div><textarea class="comment-textfield"></textarea><div class="saving-link-comment">Please wait, saving comment...</div><div class="save-but submit-but">Save</div></div></div>');
		//set comment from link-but-filled-background if has one
		if($div.hasClass('link-but-set'))
		{
			//filter first just in case
			var com2 = $div.parent().find('.link-comment2 textarea').filter(':first').val();
			$div.find('.link-comment2 textarea').val(com2);
		}

	}

	//remove link button
	function setLinkClose($div)
	{
		$div.parent().find('.link-close').unbind('click');
		$div.parent().find('.link-close').click(function() {
			//link filled background
			var $lFB;
			//link set
			var $lS;
			//loading text
			var $lL = $(this).parent().parent().find('.loading-link');
			if($(this).parent().hasClass('link-but-filled-background'))
			{
				$lFB = $(this).parent();
				$lS = $(this).parent().next();
			} else
			{
				$lFB = $(this).parent().prev();
				$lS = $(this).parent();
			}
			$lFB.fadeOut(300,function() {
				$lL.show();
				//remove any divs that have a number on end
				$(this).find('div').each(function() {
					var cl = $(this).attr('class');
					var sub = cl.substr(cl.length-1);
					if(sub == 2 || sub == 3)
					{
						$(this).remove();
					}
					$(this).removeAttr('style');
					$(this).unbind();
				})
				//get id of link to delete
				var lId = $(this).parent().find('.link-id').val();
				var lId2 = $(this).parent().find('.link-id2').val();
				var $lN = $(this).parent();
				var data = "link_id="+lId+"&link_id2="+lId2;
				//delete link on server
				$.ajax({
					type:"POST",
					url:baseURL+'links/delete_user_link',
					data:data,
					cache:false,
					success:function() {
						$lL.stop().hide();
						$lN.parent().find('.create-links-buts').fadeIn();
						//remove values from link-ids
						$lN.find('.link-id').val('');
						$lN.find('.link-id2').val('');

						//remove comments
						$lN.find('.link-comment textarea').val('');
						//remove link filled class if has it
						if($lN.find('.link-comment').hasClass('comment-filled'))
						{
							$lN.find('.link-comment').removeClass('comment-filled');
						}
						var $com2 = $lN.find('.link-comment2 textarea');
						if($com2.length != 0)
						{
							$com2.val('');
							if($lN.find('.link-comment2').hasClass('comment-filled'))
							{
								$lN.find('.link-comment2').removeClass('comment-filled');
							}
						}
						
						//remove link from json
						//find from id and to id
						var fId = curFromId;
						var tId = $lN.parent().find('.to-id').val();
						//see if outgoing or incoming
						if($lN.find('.incoming').length != 0)
						{
							if(linksJSON[tId][fId])
							{
								linksJSON[tId][fId] = '';		
							}
						} else
						{
							if(linksJSON[fId][tId])
							{
								linksJSON[fId][tId] = '';
							}
						}
						if(lId2 != '')
						{
							if(linksJSON[tId][fId])
							{
								linksJSON[tId][fId] = '';
							}
						}
						//reset class
						$lFB.attr('class','link-but-filled-background');
					}
				})
			})

			$lS.fadeOut(300,function() {
				$(this).attr('class','link-but-set');
				//remove any divs that have a number on end
				$(this).find('div').each(function() {
					var cl = $(this).attr('class');
					var sub = cl.substr(cl.length-1);
					if(sub == 2 || sub == 3)
					{
						$(this).remove();
					}
					$(this).removeAttr('style');
					$(this).unbind();
				})
			})
		})
	}

	function setCommentClicks($div)
	{
		$div.parent().find('.link-comment,.link-comment2').click(function() {
			//only do if box is currently hidden
			if($(this).find('.link-comment-box').is(':hidden'))
			{
				$(this).css({'z-index':100})
				$(this).find('.link-comment-box').fadeIn();	
			}
		})
		var $comBox = $div.parent().find('.link-comment,.link-comment2');
		//set events for this box
		$comBox.find('.close-but').click(function() {
			$(this).parent().parent().css({'z-index':1})
			$(this).parent().fadeOut();
		})

		$comBox.find('.save-but').click(function() {

			var $cB = $(this);
			var $tN = $(this).parent().parent().parent().parent().parent();
			//save to db
			//hide textfield and show loading text
			var $tA = $(this).parent().find('textarea');
			$tA.fadeTo(1,0);

			//get link id of link
			var lId;
			var isOutgoing = false;
			if($(this).parent().parent().hasClass('link-comment'))
			{
				isOutgoing = true;
				lId = $(this).parent().parent().parent().parent().find('.link-id').val();
			} else
			{
				lId = $(this).parent().parent().parent().parent().find('.link-id2').val();
			}
			var com = $tA.val();
			var data = "link_id="+lId+"&comment="+escape(com);
			$.ajax({
				type:'POST',
				url:baseURL+'links/save_comment',
				data:data,
				success:function() {
					$tA.parent().fadeOut(300,function() {
						//make sure to show text area again and hide loading text
						$tA.fadeTo(1,1);
						$(this).parent().css({'z-index':1});

						//update comment in other part of link
						//only for outgoing because link-comment2 is 
						//done in addCommentBox function
						if(isOutgoing)
						{
							$tN.find('.link-comment textarea').val(com);	
						}

						if(com != '')
						{
							$cB.parent().parent().addClass('comment-filled');
						} 

						//update json
						var tId = $tN.find('.to-id').val();
						var fId = curFromId;
						if(isOutgoing)
						{
							linksJSON[fId][tId]['comment'] = com;
						} else
						{
							linksJSON[tId][fId]['comment'] = com;	
						}
					})
				}
			})
		})
	}
	


	function setLinkCorrClicks($div)
	{
		setLinkClose($div);
		setCommentClicks($div);
		if($div.hasClass('outgoing'))
		{
			setOutgoingClicks($div);
		} else if($div.hasClass('incoming'))
		{
			setIncomingClicks($div);
		} else
		{
			setBothClicks($div);
		}
	}

	function setOutgoingClicks($div)
	{
		$div.find('.link-positive-corr').click(function() {
				var $setLink = $(this).parent().next();
				$setLink.addClass('outgoing');
				$setLink.find('.link-positive-corr-set').show();
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//$lFB.fadeIn();

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[fId][tId]['sign'] = 1;
							//just set to something other than 0 date
							linksJSON[fId][tId]['modified'] = '';
						}
					})
				})
				
		})

		$div.find('.link-negative-corr').click(function() {
				var $setLink = $(this).parent().next();
				$setLink.addClass('outgoing');
				$setLink.find('.link-negative-corr-set').show();
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=-1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//$lFB.fadeIn();

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[fId][tId]['sign'] = -1;
							//just set to something other than 0 date
							linksJSON[fId][tId]['modified'] = '';
						}
					})
				})
		})
	}

	function setIncomingClicks($div) 
	{
		$div.find('.link-positive-corr').click(function() {
				var $setLink = $(this).parent().next();
				$setLink.addClass('incoming');
				$setLink.find('.link-positive-corr-set').show();
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//$lFB.fadeIn();

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[tId][fId]['sign'] = 1;
							//just set to something other than 0 date
							linksJSON[tId][fId]['modified'] = '';
						}
					})
				})
		})

		$div.find('.link-negative-corr').click(function() {
				var $setLink = $(this).parent().next();
				$setLink.addClass('incoming');
				$setLink.find('.link-negative-corr-set').show();
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=-1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//$lFB.fadeIn();

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[tId][fId]['sign'] = -1;
							//just set to something other than 0 date
							linksJSON[tId][fId]['modified'] = '';
						}
					})
				})
		})
	}

	function setBothClicks($div)
	{
		$div.find('.link-positive-corr').click(function() {
				var $setLink = $(this).parent().next();
				if($setLink.hasClass('both2'))
				{
					$setLink.addClass('both3');
					$setLink.find('.link-comment').show();
					hasTwo = true;
				} else
				{
					$setLink.addClass('both');
				}
				//show close
				$setLink.find('.link-close').show();	
				$setLink.fadeOut(300,function() {
					$setLink.find('.link-positive-corr-set').show();
				});
				setCommentClicks($setLink);
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//hide parts not seen in lfb
							if($setLink.hasClass('both'))
							{
								$lFB.find('.link-close').hide();
								$lFB.find('.link-comment').hide();
								$lFB.find('.link-negative-corr').hide();
								$lFB.find('.link-positive-corr').hide();
								$lFB.fadeIn();
							}

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[fId][tId]['sign'] = 1;
							//just set to something other than 0 date
							linksJSON[fId][tId]['modified'] = '';
						}
					})
				})
		})

		$div.find('.link-negative-corr').click(function() {
				var $setLink = $(this).parent().next();
				if($setLink.hasClass('both2'))
				{
					$setLink.addClass('both3');
					$setLink.find('.link-comment').show();
				} else
				{
					$setLink.addClass('both');
				}
				$setLink.find('.link-close').show();
				$setLink.fadeOut(300,function() {
					$setLink.find('.link-negative-corr-set').show();
				});
				setCommentClicks($setLink);
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id').val();
				var data = 'sign=-1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							//hide parts not seen in lfb
							if($setLink.hasClass('both'))
							{
								$lFB.find('.link-close').hide();
								$lFB.find('.link-comment').hide();
								$lFB.find('.link-negative-corr').hide();
								$lFB.find('.link-positive-corr').hide();
								$lFB.fadeIn();
							}

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[fId][tId]['sign'] = -1;
							//just set to something other than 0 date
							linksJSON[fId][tId]['modified'] = '';
						}
					})
				})
		})

		$div.find('.link-positive-corr2').click(function() {
				var $setLink = $(this).parent().next();
				if($setLink.hasClass('both'))
				{
					$setLink.addClass('both3');
					//show close
					$setLink.find('.link-close').show();
					//duplicate comment box and pos and neg set arrows
					addCommentBox($setLink);
					$setLink.append('<div class="link-positive-corr-set2"></div>');
					$setLink.fadeOut(300,function() {
						$setLink.find('.link-positive-corr-set2').show();
					});
				} else
				{
					$setLink.addClass('both2');	
					//show close
					$setLink.find('.link-close').hide();
					$setLink.find('.link-comment').hide();
					addCommentBox($setLink);
					$setLink.append('<div class="link-positive-corr-set2"></div>');
					$setLink.find('.link-positive-corr-set2').show();
				}
				setCommentClicks($setLink);
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id2').val();
				var data = 'sign=1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.fadeIn();
							if($setLink.hasClass('both2'))
							{
								$lFB.find('.link-comment2').hide();
								$lFB.find('.link-negative-corr2').hide();
								$lFB.find('.link-positive-corr2').hide();	
								$lFB.fadeIn();
							}

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[tId][fId]['sign'] = 1;
							//just set to something other than 0 date
							linksJSON[tId][fId]['modified'] = '';
						}
					})
				})
		})

		$div.find('.link-negative-corr2').click(function() {
				var $setLink = $(this).parent().next();
				if($setLink.hasClass('both'))
				{
					$setLink.addClass('both3');
					//show close
					$setLink.find('.link-close').show();
					//duplicate comment box and pos and neg set arrows
					addCommentBox($setLink);
					$setLink.append('<div class="link-negative-corr-set2"></div>');
					$setLink.fadeOut(300,function() {
						$(this).find('.link-negative-corr-set2').show();
					});
				} else
				{
					$setLink.addClass('both2');	
					//show close
					$setLink.find('.link-close').hide();
					$setLink.find('.link-comment').hide();
					addCommentBox($setLink);
					$setLink.append('<div class="link-negative-corr-set2"></div>');
					$setLink.find('.link-negative-corr-set2').show();
				}
				setCommentClicks($setLink);
				var $lL = $(this).parent().parent().find('.loading-link');
				var $lFB = $setLink.parent().find('.link-but-filled-background');
				var lId = $setLink.parent().find('.link-id2').val();
				var data = 'sign=-1&link_id='+lId;
				$lFB.fadeOut(300,function() {
					$lL.show();
					//save to db
					$.ajax({
						type:'POST',
						url:baseURL+'links/save_user_link',
						data:data,
						cache:false,
						success:function() {
							$lL.stop().hide();
							$setLink.stop(true,true).fadeIn();
							if($setLink.hasClass('both2'))
							{
								$lFB.find('.link-comment2').hide();
								$lFB.find('.link-negative-corr2').hide();
								$lFB.find('.link-positive-corr2').hide();	
								$lFB.fadeIn();
							}
							$setLink.fadeIn();

							//update json
							var tId = $lFB.parent().parent().find('.to-id').val();
							var fId = curFromId;
							linksJSON[tId][fId]['sign'] = -1;
							//just set to something other than 0 date
							linksJSON[tId][fId]['modified'] = '';
						}
					})
				})
		})
	}


	//link button rollovers
	function setLinkCorrRollovers($div) 
	{
		if($div.hasClass('incoming'))
		{
			setIncomingOvers($div);
		} else if($div.hasClass('outgoing'))
		{
			setOutgoingOvers($div);
		} else
		{
			setIncomingOvers($div);
			setOutgoingOvers($div);
		}
	}

	function setIncomingOvers($div)
	{
		//incoming arrow correlations
		$div.find('.link-positive-corr,.link-positive-corr2').mouseenter(function() {
			curFromNode.stop(false,false).animate({
				scale:1.1
			})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({
				scale:1.1
			})
		})
		$div.find('.link-positive-corr,.link-positive-corr2').bind('mouseleave click',function() {
			curFromNode.stop(false,false).animate({'scale':1});
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({'scale':1});
		})


		$div.find('.link-negative-corr,.link-negative-corr2').mouseenter(function() {
			curFromNode.stop(false,false).animate({
				scale:.9
			})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({
				scale:1.1
			})
		})
		$div.find('.link-negative-corr,.link-negative-corr2').bind('mouseleave click',function() {
			curFromNode.stop(false,false).animate({'scale':1})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({'scale':1})
		})
	}

	function setOutgoingOvers($div)
	{
		//outgoing arrow correlations
		$div.find('.link-positive-corr').mouseenter(function() {
			curFromNode.stop(false,false).animate({
				scale:1.1
			})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({
				scale:1.1
			})
		})
		$div.find('.link-positive-corr').bind('mouseleave click',function() {
			curFromNode.stop(false,false).animate({'scale':1});
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({'scale':1});
		})


		$div.find('.link-negative-corr').mouseenter(function() {
			curFromNode.stop(false,false).animate({
				scale:1.1
			})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({
				scale:.9
			})
		})
		$div.find('.link-negative-corr').bind('mouseleave click',function() {
			curFromNode.stop(false,false).animate({'scale':1})
			$(this).parent().parent().parent().find('.to-node').stop(false,false).animate({'scale':1})
		})	
	}

	//close for to's
	$('.to-close').click(function() {
		//bring up confirm dialog
		if(confirm("Are you sure you no longer want to draw any links to this node? Please only do this if you have no knowledge of this issue."))
		{
			//to id
			var id = $(this).parent().find('.to-id').val();
			var data = 'to_id='+id;
			//add this to-id to db of nodes the user has removed
			$.ajax({
				type:'POST',
				url:baseURL+'links/remove_to_node',
				data:data,
				success:function() {
					//hide this issue from all to node lists
					$('.to-nodes .to-holder-'+id).each(function() {
						if($(this).is(':visible'))
						{
							$(this).fadeOut();
						} else
						{
							$(this).hide();
						}
					})
				}
			})
			
		}
	})

	//SORTING

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

	function resetIssueSort()
	{
		$('#issue-sort').val($('options:first', $('#issue-sort')).val());		
	}

	function resetLinkSort()
	{
		$('#link-sort').val($('options:first', $('#link-sort')).val());
	}
	

	$('#issue-sort').change(function() {
		removeFilterLine();
		resetSearchFilter();
		resetTagFilter();
		resetLinkSort();
		var $curToNodes = $('#to-node-set-'+curFromId);
		if($(this).val() == 'alpha')
		{
			$curToNodes.find('.to-holder').sort(sortAlpha).appendTo($curToNodes);
		} else if($(this).val() == 'numeric')
		{
			$curToNodes.find('.to-holder').sort(sortNumeric).appendTo($curToNodes);
		} else if($(this).val() == 'constraint')
		{
			$curToNodes.find('.to-holder').sort(sortConstraint).appendTo($curToNodes);
		} else if($(this).val() == 'enable')
		{
			$curToNodes.find('.to-holder').sort(sortEnablers).appendTo($curToNodes);
		} else if($(this).val() == 'goal')
		{
			$curToNodes.find('.to-holder').sort(sortGoal).appendTo($curToNodes);
		}
		$.scrollTo(250);
	})

	function sortNumeric(a,b)
	{
		return $(a).find('.to-index').text() - $(b).find('.to-index').text();	
	}

	function sortAlpha(a,b){
    return $(a).find('.name').text().toLowerCase() > $(b).find('.name').text().toLowerCase() ? 1 : -1;
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
		$('.to-nodes .filter-line').remove();
		hasLine = false;	
	}

	var curTag;
	var curFilter;
	var hasLine = false;
	function sortTag(a,b)
	{
		var ret;
		var t1 = $(a).find('.issue-tag-list').val().toLowerCase();
		var t2 = $(b).find('.issue-tag-list').val().toLowerCase();
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
		resetIssueSort();
		resetLinkSort();
		curTag = $(this).val();
		var $curToNodes = $('#to-node-set-'+curFromId);
		$curToNodes.find('.to-holder').sort(sortTag).appendTo($curToNodes);
		//draw line between ones that have tag and ones that dont
		$curToNodes.find('.to-holder').each(function() {
			if($(this).find('.issue-tag-list').val().toLowerCase().indexOf(curTag.toLowerCase()) == -1 && hasLine == false)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>')
				return false;
			}
		})
		$.scrollTo(250);
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
    	resetLinkSort();
    	doFilter();
	    e.preventDefault();
			$.scrollTo(250);
    }
	})

	function doFilter()
	{
		removeFilterLine()
		curFilter = $('#issue-text-filter').val();
		var $curToNodes = $('#to-node-set-'+curFromId);
		$curToNodes.find('.to-holder').sort(sortFilter).appendTo($curToNodes);

		//draw line between ones that have tag and ones that dont
		$curToNodes.find('.to-holder').each(function() {
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
		resetIssueSort();
		resetTagFilter();
		resetSearchFilter();
		var $curToNodes = $('#to-node-set-'+curFromId);
		if($(this).val() == 'incoming')
		{
			$curToNodes.find('.to-holder').sort(sortIncoming).appendTo($curToNodes);
			drawLinkLine($curToNodes,'incoming');
		} else if($(this).val() == 'outgoing')
		{
			$curToNodes.find('.to-holder').sort(sortOutgoing).appendTo($curToNodes);
			drawLinkLine($curToNodes,'outgoing');
		} else if($(this).val() == 'both')
		{
			$curToNodes.find('.to-holder').sort(sortBoth).appendTo($curToNodes);
			drawLinkLine($curToNodes,'both');
		}
		$.scrollTo(250);
	})

	function drawLinkLine($curToNodes,cl)
	{
		//draw line between ones that have class and ones that dont
		$curToNodes.find('.to-holder').each(function() {
			if($(this).find('.'+cl).length == 0)
			{
				hasLine = true;
				$(this).before('<div class="filter-line"></div>');
				return false;
			}
		})
	}

	function sortIncoming(a,b)
	{
		var val1;
		var val2;

		if($(a).find('.incoming').length >= 1)
		{
			val1 = 2;
		} else if($(a).find('.both').length >= 1)
		{
			val1 = 1;
		} else if($(a).find('.outgoing').length >= 1)
		{
			val1 = 0;
		} else
		{
			val1 = -1;
		}

		if($(b).find('.incoming').length >= 1)
		{
			val2 = 2;
		} else if($(b).find('.both').length >= 1)
		{
			val2 = 1;
		} else if($(b).find('.outgoing').length >= 1)
		{
			val2 = 0;
		} else
		{
			val2 = -1;
		}
		return val2-val1;
	}

	function sortOutgoing(a,b)
	{
		var val1;
		var val2;

		if($(a).find('.outgoing').length >= 1)
		{
			val1 = 2;
		} else if($(a).find('.both').length >= 1)
		{
			val1 = 1;
		} else if($(a).find('.incoming').length >= 1)
		{
			val1 = 0;
		} else
		{
			val1 = -1;
		}

		if($(b).find('.outgoing').length >= 1)
		{
			val2 = 2;
		} else if($(b).find('.both').length >= 1)
		{
			val2 = 1;
		} else if($(b).find('.incoming').length >= 1)
		{
			val2 = 0;
		} else
		{
			val2 = -1;
		}
		return val2-val1;
	}

	function sortBoth(a,b)
	{
		var val1;
		var val2;

		if($(a).find('.both').length >= 1)
		{
			val1 = 2;
		} else if($(a).find('.outgoing').length >= 1)
		{
			val1 = 1;
		} else if($(a).find('.incoming').length >= 1)
		{
			val1 = 0;
		} else
		{
			val1 = -1;
		}

		if($(b).find('.both').length >= 1)
		{
			val2 = 2;
		} else if($(b).find('.outgoing').length >= 1)
		{
			val2 = 1;
		} else if($(b).find('.incoming').length >= 1)
		{
			val2 = 0;
		} else
		{
			val2 = -1;
		}
		return val2-val1;
	}

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

	//CIRCLEs

	//make main container big enought to hold (since circles absolutely positioned)
	$('#main-container').css({
		'min-height':960
	})

	//show tiptips immediately
	setIssueTipTips();

	//get total number of issues to use to get multiplier
	var totCircIssues = 0;
	$('.circle').each(function() {
		var n = $(this).find('h1').text();
		totCircIssues+=n;
	})

	//circle content positioning
	$('.circle').each(function() {
		var $c = $(this);
		var $d = $(this).find('.holder');
		//create size of circle from h
		var size = $c.find('h1').text();
		var mult = totCircIssues/10;
		size = Math.min(Math.max(size*10,70),400);

		$c.css({
			'width':size,
			'height':size,
			borderRadius:size/2
		})

		//resize d if bigger than c
		if($d.width() > $c.width() || $d.height() > $c.height())
		{
			$d.css({
				scale:.7
			})
		}

		$d.css({
			'left':$c.outerWidth()/2-$d.outerWidth()/2,
			'top':$c.outerHeight()/2-$d.outerHeight()/2
		})
	})


	$('.circle').mouseenter(function() {
		var $c = $(this);
		if(isCirclesShrunk)
		{
			$c = $('.circle');
		}
		$c.stop().animate({
			backgroundColor:'#bff9fa'
		})
	})

	$('.circle').mouseleave(function() {
		var $c = $(this);
		if(isCirclesShrunk)
		{
			$c = $('.circle').filter(function() {
				if($curCircle == undefined)
				{
					return true;
				}
				if($(this).html() == $curCircle.html())
				{
					return false;
				} else
				{
					return true;
				}
			});
		}
		$c.stop().animate({
			backgroundColor:'#d4d4d4'
		})
	})

	$('.back-to-mapping').click(function() {

		$curCircle = undefined;
		//also hide top buttons
		$('.set1-buttons').show();
		$('.set2-buttons').hide();

		$('#circle-section-heading').hide();
		isCirclesShrunk = true;
		$('#circle-title').fadeTo(1,0);
		$('.below-circles').fadeTo(1,0);


		//show loading text for to nodes
		//show that is loading
		$('.from-init-heading').hide();
		$('.from-instruct-begin').show();
		$('.from-instruct-finished').hide();
		$('.from-instruct-begin h1.after').show();
		$('.from-instruct-begin h1.init').hide();
		$('.from-instruct-arrow').hide();

		if($.browser.msie)
		{
			var cssObj = {
				scale:.25
			}
		} else
		{
			var cssObj = {
				scale:.25,
				left:-300,
				top:-232
			}
		}
		//set circles in corner
		$('#circle-tags').animate(cssObj,
			function() {
			$('#link-mapping').show();
			if(curFromNode == undefined)
			{
				saveFromChosen();
			}
		});

	})

	$('.circle').click(function() {
		$('#tiptip_holder').hide();
		$curCircle = $(this);
		if(isCirclesShrunk)
		{
			//curFromNode = undefined;

			if(curFromNode != undefined)
			{
				$('#circle-title').width(700);
				$('.below-circles').remove();
			}
			$('.set2-buttons').show();
			$('.set1-buttons').hide();

			isCirclesShrunk = false;
			$('#tiptip_holder').hide();
			$('#circle-tags').unbind('hover');
			$('#circle-section-heading').fadeOut();
			$('#link-mapping').fadeOut();
			$('.circle').css({
				backgroundColor:'#d4d4d4'
			})
			$('#circle-title').fadeTo(300,1);
			//only fade in random option if not gone to links yet
			if(curFromNode == undefined)
			{
				//$('.below-circles').fadeIn();
			}
			$('#circle-tags').animate({
				scale:1,
				left:15,
				top:90
			},function() {
				$('.circle').css({
					backgroundColor:'#d4d4d4'
				})
				//to clear ie
				$('#circle-tags').css('filter','none');
			})

			//hide
		} else
		{

			$('.set2-buttons').hide();
			$('.set1-buttons').hide();

			hideToNodes();

			var cT = $curCircle.find('.tit').text();
			$('#circle-title').fadeTo(300,0);
			if(curFromNode != undefined)
			{
				$('.below-circles').fadeTo(1,0);
			}
			$('#circle-section-heading').text(cT);

			//set circles in corner
			if($.browser.msie)
			{
				$('#circle-tags').animate({
					scale:.25
				},function() {
					//hide issues that don't have this tag (and show others)
					hideIssuesFromCircle(cT);
					$('#circle-section-heading').fadeIn();
					$('#link-mapping').fadeIn();
					isCirclesShrunk = true;
				});
			} else
			{
				$('#circle-tags').animate({
					scale:.25,
					left:-300,
					top:-232
				},function() {
					//hide issues that don't have this tag (and show others)
					hideIssuesFromCircle(cT);
					$('#circle-section-heading').fadeIn();
					$('#link-mapping').fadeIn();
					isCirclesShrunk = true;
				});	
			}	

		}
	})

	function hideIssuesFromCircle(cT)
	{
		$('.from-holder').each(function() {
			if($(this).find('.subset-tags').val().indexOf(cT) != -1)
			{
				$(this).show();
			} else
			{
				$(this).hide();
			}
		})
	}

	//check box for completion of from node
	$('.from-node .from-check').click(function() {
		var isChecked = !$(this).hasClass('checked');
		var fId = $(this).parent().parent().find('.from-id').val();
		var data = 'is_checked='+isChecked+"&from_id="+fId;
		var $check = $(this);
		//send value to ajax
		$.ajax({
			type:'POST',
			url:baseURL+"links/save_done_from",
			data:data,
			success:function(d) {
				if($check.hasClass('checked'))
				{
					$check.removeClass('checked');
				} else
				{
					$check.addClass('checked');
				}
			}
		})
	})
	



})
</script>

<div id='top-space'>
</div>
<!--VIDEO EMBED-->
<?php if(trim($video_embed) != ''):?>
<div style="display:none;">
	<div id="vimeo-content">
		<?php echo $video_embed?>
	</div>
</div>
<?php endif;?>

<!--INSTRUCTION VIDEO-->
<div style="display:none;">
	<div id="instruction-video">
		<iframe id="vimeo_video" src="http://player.vimeo.com/video/40046718?title=0&byline=0&portrait=0&color=588A9E&autoplay=1" width="800" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
</div>


<!--CIRCLES FOR CHOOSING LINKS BASED ON SUBSETS-->
<h1 id='circle-title' class='zero'>Select <?php echo $total_froms?> ISSUES RELEVANT TO YOUR EXPERTISE FROM THE CATEGORIES BELOW <span class='below-circles'>or click <a href='#random' class='random-froms-but'>here</a> and we'll choose <?php echo $total_froms?> for you.</span></h1>
<div id='circle-tags'>
	<?php $i = 1;?>
	<?php foreach($subset_tags as $key=>$value):?>
	<div class='circle' id='circle<?php echo $i?>'><div class='holder'><h1><?php echo $value?></h1><div class='tit'><?php echo $key?></div></div></div>
	<?php $i++;?>
	<?php endforeach;?>
</div>
<h1 id='circle-section-heading'></h1>



<!--FINISHED MAPPING CONTENT-->
<div style='display:none;'>
	<div id='finished-content'><h1 class='zero'>You're Done!</h1><p>Thanks for finishing the Link Mapping for <?php echo $project_name?>. Please make sure you have made all the necessary links from your focal subset. Any time you wish to review or add to your current links, just revisit:<br/><br/> <a href='<?php echo site_url()?>'><?php echo site_url()?></a></p></div>
</div>

<!--ISSUE LISTING TO SEND TO EMAIL-->
<div style='display:none;'>
	<div id='issue-submission' class='single-box issue-box link-mapping-issue-box' style='display:block;'>
		<h2 style='margin:-10px 0 10px;'>Add a new issue to be curated and added to Link Mapping</h2>
		<div class='input'>
			<form id='issue-form'>
				<label class='title-label'>Short issue title <span class='parenth'>(<50 characters)</span></label>
				<input class='issue-input issue-name shadowy' name='name' type='text' value="" title='Provide a short descriptive title for this issue'/>
					<label>Description <span class='parenth'>(<250 Words)</span></label>
				  <?php echo form_textarea( array( 'name' => 'description', 'class'=>'issue-input issue-description shadowy', 'title'=>'Describe and define this issue in more detail. Why did you choose it? What does it influence? Is this a major constraint or an enabler?', 'rows' => '5', 'cols' => '80' ) )?>
				  <br/>
					<label>Units <span class='parenth'>(Optional <25 Words)</span></label>
					<input name='units' type='text' class='issue-input issue-units shadowy' title='How would you measure a change in this issue? (optional)' value=''/>
				  <br/>
					<label>Tags <span class='parenth'>(up to 5, separated by commas)</span></label>
					<input name='categories' type='text' class='issue-input issue-tags shadowy' title='What broader tags or keywords, if any, help define this issue or place it in context? If not applicable or you are uncertain, just enter "N/A"' value=''/>
					<div class='clearer'></div>
					<div class='right'>
						<div class='saving-anim'>Saving Issue...</div>
						<input id='issue-submit-but' type='submit' class='submit-but save-button' value='Save' onclick='return false;' />
					</div>
					<div class='clearer'></div>
			</form>
		</div>
	</div>
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
<div class='top-link-buttons'>
	<div class='set1-buttons'>
		<?php if($link_mapping_instructions != ''):?>
		<div class='instruction-info-but'></div>
		<?php endif;?>
		<?php if($video_embed != ''):?>
		<div class='instruction-video-but' title='Click to rewatch instructional video.'></div>
		<?php endif;?>
		<div class='finished-links-but submit-but'>Finished Mapping Links</div>
		<div class='another-issue-but submit-but' title='Submit New Issue for Vibrant Data to Review'>Submit New Issue</div>
	</div>
	<div class='set2-buttons'>
		<div class='back-to-mapping submit-but'>Back to Link Mapping</div>
	</div>
</div>
<!--LINK MAPPING-->
<div id='link-mapping'>
	<div id='arrow-titles'>
		<h1>Focal Subset</h1><h1 class='links-header'>STRONG INFLUENCES</h1><h1 class='right to-header'>Complete Set</h1>
	</div>
	<div id='arrow-filters'>
		<div>
			<select id='link-sort'>
				<option value=''>Sort Links...</option>
				<option value='incoming'>Incoming</option>
				<option value='outgoing'>Outgoing</option>
				<option value='both'>Both</option>
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
				<!--<option value='constraint'>Constraints</option>
				<option value='enable'>Enablers</option>
				<option value='goal'>Goals</option>-->
			</select>
			<input id='issue-text-filter' value='Search'/>
		</div>
		<br/>
	</div>

	<div class='from-instructions'>
		<?php if($total_froms == 0):?>
		<h1 class='from-init-heading'>SELECT ISSUES FROM THE LEFT THAT YOU KNOW MOST ABOUT. CLICK ON THE CIRCLES TO SELECT MORE ISSUES FROM ANOTHER CATEGORY.</h1>
		<?php else:?>
		<h1 class='from-init-heading'>SELECT <?php echo $total_froms?> ISSUES FROM THE LEFT THAT YOU KNOW MOST ABOUT. CLICK ON THE CIRCLES TO SELECT MORE ISSUES FROM ANOTHER CATEGORY.</h1>
		<?php endif;?>
		<div class='from-instruct-begin'>
		<?php if($total_froms == 0):?>
			<h1 class='init'>You have selected <span>1</span> FOCAL ISSUES TO DRAW INFLUENCE LINKS FROM (AND TO)</h1>
		<?php else:?>
			<h1 class='init'>You have selected <span>1</span> of <?php echo $total_froms?> FOCAL ISSUES TO DRAW INFLUENCE LINKS FROM (AND TO).</h1>
		<?php endif;?>
			<h1 class='after'>Please wait, loading issues...</h1>
			<div class='from-finished-holder'>
				<a href='#' class='submit-but from-instruct-finished'>I'M READY TO MAP LINKS!</a>
			</div>
		</div>
	</div>
	<?php $i = 0;?>
	<?php $tot_from = count($from_nodes);?>
	<div class='from-nodes-holder'>
	<div class='from-nodes'>
		<div class='from-nodes-cover'></div>
		<?php foreach($from_nodes as $from_node):?>
		<?php if(in_array($from_node['id'], $user_from_nodes)):?>
		<div class='from-holder from-holder-<?php echo $from_node["id"]?> chosen'>
		<?php else:?>
		<div class='from-holder from-holder-<?php echo $from_node["id"]?>'>
		<?php endif;?>
		<?php 
			//convert array to string so can search
			//when deciding whether to show when clicking a circle
			$sub_tags = implode('|', $from_node['subset_tags']);
		?>
			<input type='hidden' class='subset-tags' name='subset_tags' value='<?php echo $sub_tags?>'/>
			<input type='hidden' class='from-id' name='from_id_<?php echo $i?>' value='<?php echo $from_node["id"]?>'/>
			<input type='hidden' class='from-issue-type' value='<?php echo $from_node['issueType']?>'/>
			<div class='from-node'>
				<?php if($from_node['isDone']):?>
				<div class='from-check checked' title='Use to Mark Completion'></div>
				<?php else:?>
				<div class='from-check' title='Use to Mark Completion'></div>
				<?php endif;?>
				<div class='from-node-content'>
					<div class='from-index'><?php echo $i+1?></div>
					<?php $tit = htmlspecialchars($from_node["description"],ENT_QUOTES);?>
					<div class='name' title='<?php echo $tit?>'><?php echo trim($from_node['name'])?></div>
					<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
				</div>
				<?php if($from_node['issueType'] == 1):?>
					<!--<div class='phrasing-over' title='Positive Phrasing, Increase is Desirable'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background enabler is-begun'></div>
					<?php else:?>
					<div class='from-node-background enabler'></div>
					<?php endif;?>
				<?php elseif($from_node['issueType'] == -1):?>
					<!--<div class='phrasing-over' title='Negative Phrasing, Increase is Not Desirable'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background constraint is-begun'></div>
					<?php else:?>
					<div class='from-node-background constraint'></div>
					<?php endif;?>
				<?php elseif($from_node['issueType'] == 2):?>
					<!--<div class='phrasing-over' title='One of the High Level Goals'></div>-->
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background goal is-begun'></div>
					<?php else:?>
					<div class='from-node-background goal'></div>
					<?php endif;?>
				<?php else:?>
					<?php if($from_node['isBegun']):?>
					<div class='from-node-background is-begun'></div>
					<?php else:?>
					<div class='from-node-background'></div>
					<?php endif;?>
				<?php endif;?>
			</div>
		</div>
	<?php $i++;?>
	<?php endforeach;?>
	</div>
</div>

		<div class='to-nodes'>
			<div class='to-node-set'>
				<input type='hidden' class='from-id' value=''/>
			<?php $j=0;?>
				<?php foreach($to_nodes as $to_node):?>
				<?php
				if($to_node['isDeleted'])
				{
					$j++;
					continue;
				}
				?>
				<div class='to-holder to-holder-<?php echo $to_node["id"]?>'>
					<div class='link-holder'>
						<div class='loading-link'>Please Wait, Creating Link...</div>
							<input type='hidden' class='link-id' value=''/>
							<input type='hidden' class='link-id2' value=''/>
							<div class='create-links-buts'>
								<div class='incoming-link-but'></div>
								<div class='both-link-but'></div>
								<div class='outgoing-link-but'></div>
							</div>
							<div class='link-but-filled-background'>
								<div class='link-close'></div>
								<div class='link-comment'>
									<div class='link-comment-box'>
										<h3>Link Comment</h3>
										<div class='close-but'></div>
										<textarea class='comment-textfield'></textarea>
										<div class='saving-link-comment'>Please wait, saving comment...</div>
										<div class='save-but submit-but'>Save</div>
									</div>
								</div>
								<div class='link-positive-corr'></div>
								<div class='link-negative-corr'></div>
							</div>
							<div class='link-but-set'>
								<div class='link-close'></div>
								<div class='link-comment'>
									<div class='link-comment-box'>
										<h3>Link Comment</h3>
										<div class='close-but'></div>
										<textarea class='comment-textfield'></textarea>
										<div class='saving-link-comment'>Please wait, saving comment...</div>
										<div class='save-but submit-but'>Save</div>
									</div>
								</div>
								<div class='link-positive-corr-set'></div>
								<div class='link-negative-corr-set'></div>
							</div>
					</div>
						<div class='to-node'>
						<div class='to-close' title='Remove (Outside My Knowledge Area)'></div>
						<input type='hidden' class='to-id' name='to_id_<?php echo $j?>' value='<?php echo $to_node["id"]?>'/>
						<input type='hidden' class='to-issue-type' value='<?php echo $to_node['issueType']?>'/>
						<div class='to-node-content'>
							<div class='to-index'><?php echo $j+1?></div>
							<?php $tit = htmlspecialchars($to_node["description"],ENT_QUOTES)?>
							<div class='name' title='<?php echo $tit;?>'><?php echo trim($to_node['name'])?></div>
							<input type='hidden' class='input-info-hidden' value='<?php echo $tit?>'/>
							<input type='hidden' class='issue-tag-list' value='<?php echo $to_node["categories"]?>'/>
						</div>
						<input type='hidden' class='issue-type' value='<?php echo $to_node["issueType"]?>'/>
						<div class='to-node-over'></div>
						<?php if($to_node['issueType'] == 1):?>
						<div class='phrasing-over' title='Positive Phrasing, Increase is Desirable'></div>
						<div class='to-node-background enabler'></div>
						<?php elseif($to_node['issueType'] == -1):?>
						<div class='phrasing-over' title='Negative Phrasing, Increase is Not Desirable'></div>
						<div class='to-node-background constraint'></div>
						<?php elseif($to_node['issueType'] == 2):?>
						<div class='phrasing-over' title='One of the High Level Goals'></div>
						<div class='to-node-background goal'></div>
						<?php elseif($to_node['issueType'] == -2):?>
						<div class='to-node-background na'></div>
						<?php else:?>
						<div class='to-node-background'></div>
						<?php endif;?>
					</div>
					<div class='clearer'></div>
				</div>
				<?php $j++;?>
				<?php endforeach;?>
			</div>
		</div>
</div>