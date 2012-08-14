Foundry.run(function($)
{
	// http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
	$.isNumeric = function(n)
	{
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	$.Number = {
		rotate: function(n, min, max, offset)
		{
			if (offset===undefined)
				offset = 0;

			n += offset;
			if (n < min) {
				n += max + 1;
			} else if (n > max) {
				n -= max + 1;
			}

			return n;
		}
	}
});
