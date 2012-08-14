var path = require('path');
var fs = require('fs');
var sys = require('sys')
var exec = require('child_process').exec;

fs.readdir(__dirname + '/dev', function(req, files)
{
	var log = '';

	var buildLog = function(error, stdout, stderr)
	{
		if (stderr)
		{	
			console.log(stderr);
			log += stderr + '\n';
		}
		
		var buildTime = 'Build time: ' + new Date() + '\n\n';
		console.log(buildTime);
		log = buildTime + log;

		fs.writeFile('build.log', log, function(err) {
		  if (err) throw err;
		});
	}

	var compress = function(error, stdout, stderr)
	{
		if (stderr)
		{	
			console.log(stderr);
			log += stderr + '\n';
		}

		if (files.length < 1)
		{
			buildLog();
			return;
		}

		var file = files.shift();

		// Skip to the next file
		if (file.substr(-3,3)!='.js')
			return compress();

		var command = ''
		if (file=='easydiscuss.js')
		{
			command = 'uglifyjs -nc -v -nm -ns --no-dead-code -o '
		} else {
			command = 'uglifyjs -nc -v -o ';
		}

		command += file + ' dev/' + file;

		console.log(command);
		log += command + '\n';

		child = exec(command, compress);
	}

	compress();

});
