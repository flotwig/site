---
layout: post
title:  "RamNode: One week impressions"
date:   2014-06-15 13:55:17
categories: ramnode vps
---

I switched my VPS from [Hostigation](https://hostigation.com) to [RamNode](https://clientarea.ramnode.com/aff.php?aff=1585) recently. Hostigation was a great value when I signed up in 2011 - $30/year for a 128mb KVM box? I mean, come on! However, as the years have gone by, Hostigation has started to fade out of the low end box scene, which concerned me. So I switched to RamNode, which offered me a few advantages:

 * Better geographical location (Atlanta vs. Rock Hill)  
 * Better value (twice the resources for 1.25x the price)
 * Better support (IRC presence along with a responsive, fully-staffed support desk)
 
Today marks one week since I initially signed up with RamNode. Uptime has been 100%, and benchmarks are very positive:

Disk performance:
{% highlight console %}
zach@overflod:~$ dd if=/dev/zero of=test bs=64k count=16k conv=fdatasync; unlink test
16384+0 records in
16384+0 records out
1073741824 bytes (1.1 GB) copied, 2.54237 s, 422 MB/s
{% endhighlight %}

I have only encountered one small issue, and that is because I originally signed up for the 128mb OpenVZ package at $14.88/yr. Processes would randomly die and get moved into/out of swap, even though there was plentiful RAM free. So if you're going to go RamNode, stay away from their OpenVZ offerings.

There are a few fantastic coupon codes right now. **WOWNUM1** will get you 38% off any VPS purchase (recurring), and **TWOYEAR** will get you 42% off, making [RamNode](https://clientarea.ramnode.com/aff.php?aff=1585) a stable and cheap VPS hosting option.