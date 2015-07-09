#17acklen Popular Content
======================

A simple Bolt CMS plugin that lets you track views of content and display a list of your most popular content.

##USAGE
_____

Place the following twig snippet near the beginning of any theme single content view template file that you want to track - 
`{{ popcon_recordContentView(record) }}`
Where record is the content being viewed, replace this with whatever your template file uses for the record object.
Ex: To track page views of your site's pages contenttype, assuming single pages are displayed by the template file single.twig,
	place {{ popcon_recordContentView(record) }} near the top of single.twig.  If you use setcontent to use another variable other than
	record, put the code after your setcontent statement replacing record with the variable you use.

To display a list of your 10 most popular pages in a sidebar that uses the template file _sidebar.twig for instance, 
	in _sidebar.twig you can do something like this:

```html
{% set popular = popcon_getPopularContent(10, 'Pages') %}
	<div>
		<h3>Popular Pages</h3>
		{% for pc in popular %}
			{% setcontent poppage = 'pages/' ~ pc.content_id %}
		<a href="{{ poppage.link }}">{{ poppage.title }}</a>
		<br>
		{% endfor %}
	</div>
```

To get something like:
###Popular Pages
[Page 1](#)

[Page 2](#)

[Page 3](#)

[Page 4](#)

[Page 5](#)

[Page 6](#)

[Page 7](#)

[Page 8](#)

[Page 9](#)

[Page 10](#)

##Documentation

`popcon_recordContentView(record)`
Records the view of the record when a user visits that record on your site, if your template uses another variable other than record, page for example, replace
	record with page.

`popcon_getPopularContent(numberResults, 'contenttype')`
returns an array of results, where each result is an array of the form 
```
array(
	"viewCnt" => (integer) [number of recorded views],
	"contenttype" => (string) '[contenttype of result]',
	"content_id" => (integer) [record id of the result]
)
```

Example of `{{ set popular = popcon_getPopularContent(3, 'Pages') }}`:
```
popular = array(
	[0] => array("viewCnt"=>152, "contenttype"=>'Pages', "content_id"=>16),
	[1] => array("viewCnt"=>118, "contenttype"=>'Pages', "content_id"=>3),
	[3] => array("viewCnt"=>97, "contenttype"=>'Pages', "content_id"=>9),
)
```

`numberResults` is an optional integer for the number of results you want to return, e.g. 10 for top 10 results, 5 for top 5 results, etc.  Default is 10, if the number of results found is less than numberResults, that number of results is returned

`contenttype` is an optional string of the contenttype you want limit the results to, e.g. 'Pages', 'Entries', etc.  Default is none and will return all contenttypes that have been recorded by popcon_recordContentView()

`popcon_getPopularContent()` can be called with 0, 1, or 2 arguments, numberResults is required if you want to specify contenttype:

`popcon_getPopularContent()` will return an array of 10 most viewed records out of all of your recorded contenttypes

`popcon_getPopularContent(5)` will return an array of 5 most viewed records out of all of your recorded contenttypes

`popcon_getPopularContent(3, 'Entries')` will return an array of 3 most viewd records that are contenttype 'Entries'

`popcon_getPopularContent('Entries')` will error


