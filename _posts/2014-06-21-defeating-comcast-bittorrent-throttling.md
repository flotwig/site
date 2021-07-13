---
layout: post
title:  "Defeating Comcast BitTorrent Throttling: The Easy Way"
date:   2014-06-21 00:07:10
tags: netsec comcast
---

![Example settings in Transmission](/images/transmission.png)

If you torrent a lot, eventually Comcast/xfinity will [throttle your torrent speeds to 20kbps or below](/images/throttling.png). Luckily, there is a simple fix which works without installing any external applications. Simply configure the listening port in your BitTorrent client to be 443, and ensure that the port is open on your router. This works because Comcast's deep-packet inspection ignores packets on common Internet ports to save processing power, and 443 is the HTTPS port. This fix will not disrupt your HTTPS traffic. This also works on port 80 (HTTP) and port 53 (DNS).