
var isTableClosed = true;

$(document).ready(function() {


	//ARROW ROLLOVERS 

	//setup tiptip tooltips
	$('.node-name').tipTip();
	$('.from').tipTip({defaultPosition:'top'});
	$('.to').tipTip({defaultPosition:'top'});
	


	//TABLE SORTING AND FILTERING

	//for table sorting
	var sortL;
	if($.cookie('sortList'))
	{
		sortL = $.evalJSON($.cookie('sortList'));
	}
	$('#links-table').tablesorter({
		sortList:sortL
	});
	//assign the sortStart event
	$("#links-table").bind("sortEnd",function() {
		reformatColors();
		//set cookies for remembering current state of table
		var sAR = [];
		var i = 0;
		$('#links-table th').each(function() {
			//see if has header sort down or header sort up
			if($(this).hasClass('headerSortDown'))
			{
				sAR.push([i,0]);
			} else if($(this).hasClass('headerSortUp'))
			{
				sAR.push([i,1]);
			}
			i++;
		});
		//now add array to cookies to retrieve on next load
		$.cookie('sortList',$.toJSON(sAR));
	});
	//set correct scrolling for table
	var sc = 0;
	var lTS = $.cookie('linkTableScroll');
	if(lTS)
	{
		sc = Number(lTS);
	}
	$('#links-table-holder').scrollTop(sc);

	//for table filtering
	var options = {
		filteredRows:function() {
			//reformat colors
			reformatColors();
			//display number of rows
			showTotalRows();
			//get mean strength of shown rows
			showMeanStrength();
		}
	};
	$('#links-table').tableFilter(options);
	$('#links-table thead tr td').filter(':odd').addClass('dark');
	var i = 0;
	$('#links-table thead tr td input').each(function() {
		var cl = $('#links-table thead tr th div').eq(i).attr('class');
		$(this).wrap('<div class="'+cl+'"/>');
		i++;
	});

	showTotalRows();
	showMeanStrength();


	//TABLE OPENING AND CLOSING

	//for closing and opening table
	$('#table-close').click(function() {
		isTableClosed = true;
		$.cookie('isTableClosed',true);
		$('#table-container').animate({
			'left':-1*$('#table-container').width()-2
			},
			'slow');
	});

	$('#table-tab').click(function() {
		if(isTableClosed)
		{
			isTableClosed = false;
			$.cookie('isTableClosed',false);
			$('#table-container').animate({
				'left':-1
				},
				'slow');
		} else
		{
			isTableClosed = true;
			$.cookie('isTableClosed',true);
			$('#table-container').animate({
				'left':-1*$('#table-container').width()-2
				},
				'slow');
		}
	});


	//TABLE ROW MOUSE EVENTS

	//over row
	$('#links-table tbody tr').mouseover(function() {
		$(this).addClass('over');
	});
	//out row
	$('#links-table tbody tr').mouseout(function() {
		$(this).removeClass('over');
	});
	//click row
	$('#links-table tbody tr').click(function() {
		var id = $(this).find('td').filter(':first').text();
		document.location.href = baseURL+'links/verification/'+id;
		//save scroll position for next page
		$.cookie('linkTableScroll',$('#links-table-holder').scrollTop());
	});



	//RESIZING

	//get initial variables for table row sizes
	var idWidth = $('#links-table .id').width();
	var modifiedWidth = $('#links-table .modified').width();
	var fromWidth = $('#links-table .from').width();
	var signWidth = $('#links-table .sign').width();
	var strengthShortWidth = $('#links-table .strength-short').width();
	var strengthLongWidth = $('#links-table .strength-long').width();
	var certaintyWidth = $('#links-table .certainty').width();
	var toWidth = $('#links-table .to').width();

	//resize everything
	resizing();
	$(window).resize(function() {
		resizing();
	});


	//FUNCTIONS

	//resizing window
	function resizing()
	{
		var $tC = $('#table-container');
		var $lT = $('#links-table');

		var wRat = $(window).width()/1072;
		//width
		var totW = Math.max($(window).width()/2+465,950);

		//ratio for table sizing
		var tRat = totW/1001;
		var buf = Math.max((tRat-1)*35,1);
		//row divs
		$lT.find('.id').css('width',idWidth*tRat+buf);
		$lT.find('.modified').css('width',modifiedWidth*tRat+buf);
		$lT.find('.from').css('width',fromWidth*tRat+buf);
		$lT.find('.sign').css('width',signWidth*tRat+buf);
		$lT.find('.strength-short').css('width',strengthShortWidth*tRat+buf);
		$lT.find('.strength-long').css('width',strengthLongWidth*tRat+buf);
		$lT.find('.certainty').css('width',certaintyWidth*tRat+buf);
		$lT.find('.to').css('width',toWidth*tRat+buf);

		$tC.width(totW);

		if(isTableClosed)
		{
			$tC.css('left',-1*$tC.width()-2);	
		} else
		{
			$tC.css('left',-1);
		}

		//height
		$tC.height(Math.max($('body').height(),$(window).height())-375);
		$('#links-table-holder').height($tC.height()-65);
		
	}

	function showTotalRows()
	{
		var tR = $('#links-table tbody tr').filter(':visible').length;
		$('#total-rows .total').text(tR);
	}

	function showMeanStrength()
	{
		var $rs = $('#links-table tbody tr').filter(':visible');
		var tR = $rs.length;
		var aS = 0;
		$rs.each(function() {
			aS += (Number($(this).find('.strength-short').text())+Number($(this).find('.strength-long').text()))/2;
		});
		var mS = aS/tR;
		if(aS === 0)
		{
			mS = 0;
		}
		$('#mean-strength .mean').text(Number(mS.toFixed(2)));
	}

	//changing table row colors after filtering or sorting
	function reformatColors()
	{
		var $lT = $("#links-table tbody tr");
		var i = 0;
		$lT.filter(':visible').each(function() {
			if(i%2 === 0)
			{
				$(this).removeClass('even').addClass('odd');
			} else
			{
				$(this).removeClass('odd').addClass('even');
			}
			i++;
		});
	}

});
