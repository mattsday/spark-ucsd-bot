<!doctype html>
<html>
<head>
<title>Rollback: Request #{$request}</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<meta http-equiv="refresh" content="60" />
<style>
	div#sr_req {
		border: 1px solid #000080;
		margin-left: 15%;
		margin-right: 15%;
		padding: 1em;
		margin-top: 3em;
		max-height: 30em;
		overflow: scroll;
	}
	div#sr_req h3 {
		margin-top: 0;
		font-size: 1.2em;
	}
	p.completed {
		color: green;
	}
	p.failed {
		color: red;
	}
	p.in_progress {
		color: blue;
		font-weight: bold;
	}
</style>
</head>
<body>
<h1>Rolling back {$request}</h1>
<p>Note: This page updates automatically. You can also <a href="javascript:location.reload();">reload it</a> to track progress.</p>
<div id="sr_req">
{foreach from=$steps item=step}
<h3>Step {$step.Number}: {$step.Name}</h3>
<p class="{$step.Style}">Status: {$step.Status}</p>
{/foreach}
</div>
</body>
</html>
