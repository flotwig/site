---
layout: post
title:  "A survey of crossdomain.xml vulnerabilities"
date:   2014-08-15 07:15:00
tags: netsec
---

Vulnerable crossdomain.xml files can be used by malicious people to run CSRF attacks if the victim has Flash installed on their computer. In response to a post by chs on [crossdomain.xml proofs of concept](http://www.chs.us/liberal-crossdomain-xml-exploit-example/) and Seth Art's [real-world exploit of Bing using crossdomain.xml](http://sethsec.blogspot.com/2014/07/crossdomain-bing.html), I created an application in Ruby which parses the [Alexa top million site list (CSV, 10MB)](http://s3.amazonaws.com/alexa-static/top-1m.csv.zip) and scans for vulnerable crossdomain.xml files. Vulnerable here is defined as a crossdomain.xml file which permits connections from any domain name (*). It sorts the domains into four categories:

- Unable to connect: Ruby was unable to establish a connection to the website. Interestingly enough, a significant portion of Alexa's top million sites were inaccessible during this survey.
- Invalid or 404: Returned 404 or the returned XML was not valid.
- Secure: The XML returned does not contain a reference to allow-access-from domain="*". This does not necessarily mean that the whole crossdomain.xml file is secure, just that it is not vulnerable to the most basic of CSRF exploits.
- Permissive: The XML returned from a GET to /crossdomain.xml does allow access from any domain.
	
Without further ado, let's get into it.

#### The Code

I chose Ruby for this project because it has good XML processing libraries, is reasonably fast, and because I needed an excuse to practice Ruby.

{% highlight ruby %}

require 'net/http'
require 'rexml/document'
include REXML
require 'csv'

counters = {
	'unconnect'   => 0,
	'invalid-404' => 0,
	'permissive'  => 0,
	'secure'      => 0,
	'total-count' => 0
}

trap 'SIGINT' do
	print counters.inspect
	exit 130
end

permissive = CSV.open('permissive.csv','wb')

CSV.foreach('top-1m.csv') do |row|
	counters['total-count'] += 1
	print "\n"+'Getting '+row[1]+'... '
	begin
		xd = Net::HTTP.get(row[1], '/crossdomain.xml')
	rescue
		counters['unconnect'] += 1
		print 'unable to connect'
		next
	end
	begin
		xd = REXML::Document.new(xd)
	rescue
		counters['invalid-404'] += 1
		print 'invalid xml'
		next
	end
	wildcard_access = false
	XPath.each(xd,'//allow-access-from') do |access|
		next unless access.attributes['domain'] == '*' # <allow-access-from domain="*">
		wildcard_access = true
		counters['permissive'] += 1
		print 'permissive'
		permissive << row
		break
	end
	unless wildcard_access
		counters['secure'] += 1
		print 'secure'
	end
end

print counters.inspect

{% endhighlight%}

#### The Results

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
var data = google.visualization.arrayToDataTable([
['Task', 'Sites'],
['Unable to connect', 3535],
['Permissive',        4653],
['Invalid or 404',    84883],
['Secure',            67097]
]);

var options = {
title: 'crossdomain.xml Breakdown',
slices: { 1: {offset: 0.2} },
pieStartAngle: 90
};

var chart = new google.visualization.PieChart(document.getElementById('piechart'));
chart.draw(data, options);
}
</script>
<div id="piechart" style="width: 100%;"></div>

After 160,169 websites were inspected over the course of a few days, the script hung.

- 3,535 (2.2%) of the websites were down at the time of the scan.
- 84,883 (53%) of the websites had invalid or non-existent XML files at /crossdomain.xml.
- 67,097 (41.9%) of the websites surveyed had a "secure" crossdomain.xml file.
- 4,653 (2.9%) of the websites surveyed had insecure crossdomain.xml files.
	
A wildcard crossdomain.xml file is fine for certain websites, but a quick scan of the results reveals a number of banks, bitcoin websites, and popular entertainment sites (9gag and Vimeo included) with poor crossdomain.xml files. [The results as a CSV with columns corresponding to the Alexa rank and the domain name.](/assets/permissive.csv)

Although a full scan of the Alexa top million was not completed, an alarmingly large number of sites have overly permissive and insecure crossdomain.xml files.