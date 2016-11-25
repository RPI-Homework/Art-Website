	//returns if niether return blank, return loc1 if no loc2, else loc2
	function toplocation(loc1, loc2)
	{
		if(loc1 == "" && loc2 == "")
		{
			return "";
		}
		else if(loc2 == "")
		{
			return loc1;
		}
		else
		{
			return loc2;
		}
	}
	//returns hash if located, and search if no hash, if neither returns blank
	function toplocation_default()
	{
		return toplocation(window.location.search.substring(1), window.location.hash.substring(1));
	}
	//returns a combination of loc1 and loc2 with the loc2 elements on top
	function truelocation(loc1, loc2)
	{	
		if(loc1 == "" && loc2 == "")
		{
			return "";
		}
		else if(loc2 == "")
		{
			return loc1;
		}
		else if(loc1 == "")
		{
			return loc2;
		}
		else
		{
			var locs = loc1.split("&");
			var loch = loc2.split("&");
			for (var y in locs)
			{
				var temp1 = locs[y].split("=");
				for (var i in loch)
				{
					var temp2 = loch[i].split("=");
					if(temp1[0] == temp2[0])
					{	
						if(temp1[1] == null && temp2[1] == null)
							break;
						if(temp2[1] == null)
							temp1.pop();
						else
							temp1[1] = temp2[1];
						break;
					}
				}
				locs[y] = temp1.join("=");
			}
			for (var i in loch)
			{
				var isin = false;
				var temp2 = loch[i].split("=");
				for (var y in locs)
				{
					var temp1 = locs[y].split("=");
					if(temp1[0] == temp2[0])
					{
						isin = true;
						break;
					}
				}
				if(!isin)
				{
					locs.push(loch[i]);
				}
			}
			return locs.join("&");
		}
	}
	//returns a combination of the hash and search with the hash elements on top
	function truelocation_default()
	{
		return truelocation(window.location.search.substring(1), window.location.hash.substring(1));
	}
	function updatelink(classname)
	{
		$("a." + classname).each(
			function()
			{
				if($(this).attr("link"))
				{
					$(this).attr("href", '#' + truelocation(toplocation_default(), $(this).attr("link").substring(1)));
				}
				else
					$(this).attr("href", '#' + toplocation_default());
			}
		);	
	}
	function updatelinkall()
	{
		$("a").each(
			function()
			{
				if($(this).attr("link"))
				{
					$(this).attr("href", '#' + truelocation(toplocation_default(), $(this).attr("link").substring(1)));
				}
				else
					$(this).attr("href", '#' + toplocation_default());
			}
		);
	}
	function refreshlink()
	{
		return ('?' + toplocation_default());
	}
	function updatecontent()
	{
		if(toplocation_default() == "idx=2")
			$("div.content").load("2.html");
		else if(toplocation_default() == "idx=1")
			$("div.content").load("1.html");
		else
			$("div.content").load("0.html");
	}