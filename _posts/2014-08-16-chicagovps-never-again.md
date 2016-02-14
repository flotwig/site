---
layout: post
title:  "ChicagoVPS: Never again"
date: 2014-08-16 12:22:58
categories: chicagovps vps
---

I needed a VPN for me and my roommates, and ChicagoVPS seemed a perfect fit - cheap prices for decent specs in good locations. I ordered my service. Normally, when you buy a VPS, it is immediately provisioned automatically. This did not happen with ChicagoVPS. I did not get an e-mail confirming my order or anything. I assumed that they needed to verify my order, so I waited a few hours. My WHMCS control panel showed an IP address which was down. When I clicked the boot button, it said "system booted successfully". Upon refreshing the page, the system was down again. After a number of hours, I filed a ticket, the contents of which speak for itself.

#### The Ticket

<ul>
{% for reply in site.data.cvps-ticket %}
	<li>
		<strong class="text-primary">{{ reply.from }}</strong>
		<small class="text-muted pull-right">{{ reply.time }}</small>
		<br/>
		{{ reply.content }}
		{% if reply.note %}
		<br/>
		<span class="text-muted">Comment: {{ reply.note }}</span>
		{% endif %}
		<br/>
	</li>
{% endfor %}
</ul>

Saying a server is up doesn't make it so. I'm still waiting for a functioning server. 

Also, during this whole ticket exchange, I never received a single e-mail notification. Apparently their entire IP range is blacklisted because they are a haven for spammers. So I have to manually refresh the ticket to check for responses.