---
layout: post
title: "Replacing my phone's battery with a cheap AliExpress knock-off"
date: 2020-11-09 17:06:48
tags: devices
---

This is a story of one man's quest for power.

I purchased my current phone, a OnePlus 5T, in 2017. This summer, after about two and a half years of ownership, I noticed that it was no longer holding a charge all day. Frequently, the phone would reach 0% and shut off, right in the middle of tracking an evening bike ride or watching Netflix while cooking dinner. Although cell phone battery wear is a well-known issue, I got tired of it pretty quickly.

I used the [AccuBattery](https://play.google.com/store/apps/details?id=com.digibites.accubattery&hl=en_US&gl=US) app for about two months to try and get a handle on my battery health. It measured my phone's amperage draw during the day and used that to estimate that of the 3300 milliamp-hour (mAh) capacity that my battery originally offered, only about 2400 mAh of capacity remainied - only about 75% of the battery's original health:

![Screenshot of AccuBattery app for OEM battery capacity](/assets/battery/oem-capacity.png)

This answered the question of "why does it feel like my phone is shutting off so quickly?" pretty clearly. Now, it was up to me to get a replacement battery.

### Attempting to get a genuine battery

My first thought was that I could simply order the OEM OnePlus 5T battery somewhere online. Why not? I found a page on the OnePlus website where prices are listed for replacement parts. The USA page was down at the time of this writing, but the [India support page](https://www.oneplus.in/support/pricing/detail?code=7) lists a OnePlus 5T OEM battery replacement as being about $15.

This seemed acceptable to me. My first thought was to email OnePlus support asking how to purchase the battery. Unfortunately, according to the service rep, they do not ship or sell OEM batteries without service:

> We would like to inform you that we do not ship or sell the accessories in any parts of the world, and all the repairs are carried out by our Authorized service centers only. So if you wish to get the device repaired, you can send it to the OnePlus authorized service center and get the same repaired.

This is in line with what other OnePlus customers have reported - nobody, as far as I can tell, has ever been able to source OEM batteries from OnePlus, leaving DIY customers like myself to try and find knock-offs elsewhere.

I would've sent my phone in for repairs, but after I received the above email, I had such a long and terrible customer support experience trying to arrange the repair that by the end of it, I no longer trusted OnePlus to reliably service and return my phone. This lack of trust was reinforced by horror stories from other OnePlus customers - one customer's phone was [lost by the Fort Worth, TX service center](https://www.reddit.com/r/oneplus/comments/eleckw/sent_my_oneplus_5_to_fort_worth_tx_for_repair_no/), another's was [lost and took 4 weeks before being returned](https://www.reddit.com/r/oneplus/comments/depgkg/oneplus_lost_my_coworkers_phone_during_repair_at/), and yet another customer had [their phone held hostage unless they agreed to repairing EVERYTHING instead of just getting the battery replaced](https://www.reddit.com/r/oneplus/comments/jke2kd/sent_my_op3t_for_a_battery_replacement_oneplus/). These stories, combined with my awful customer support experience, convinced me that sending my phone in would be a truly bad idea.

### Buying an aftermarket battery

Many people on the /r/oneplus5t subreddit have recommended purchasing a [replacement battery from iFixit](https://www.ifixit.com/Store/Android/OnePlus-5-5T-Replacement-Battery/IF330-018?o=2), but I felt like iFixit was simply selling cheap Chinese batteries with a nice label on them. I mean, if OnePlus can fix it for $15, why does the iFixit battery cost $30, if not for marketing?

So, I hit up eBay and AliExpress, and eventually found the [sic] "Specail Mobilephone Parts Store", where they offer a ["4650 mAh" "Perfect business battery"](https://web.archive.org/web/20201109223630/https://www.aliexpress.com/item/4000438352423.html) for the OnePlus 5T. With slogans like ["Giant energy; huge capacity"](/assets/battery/giant-energy-huge-capacity.webp), ["Safety does not explode"](/assets/battery/safety-does-not-explode.webp), and ["Ensure qualified and safe to use"](/assets/battery/ensure-qualified-and-safe-to-use.webp), I felt confident that my $11.87 was going to a good place. I placed the order and, about 3 weeks later, I received the battery in my mailbox.

### Battery Physics 101

![Photo of the OEM battery and the aftermarket battery side-by-side](/assets/battery/oem-and-aftermarket.jpg)
<small>The OEM battery (left) and the aftermarket battery installed (right).</small>

The first thing I noticed about the replacement battery was that the capacity was even HIGHER than what I ordered. The OnePlus 5T OEM battery is rated at 3300 mAh capacity, the AliExpress product page advertised a battery with 4650 mAh capacity, and the label on the battery I received claimed an astounding *5350 mAh* capacity - 162% of the OEM capacity. Clearly, I had gotten a great deal!

The second thing I noticed was that the aftermarket battery was significantly lighter than the OEM battery. So much lighter that I weighed the batteries out of curiosity. The OEM OnePlus 5T battery weighed 47.0g. The aftermarket OnePlus 5T battery weighed 38.7g, or about 17% less.

It's amazing that Da Da Xiong was able to achieve 162% capacity with 17% less weight. Too amazing to be true, in fact.

Via Wikipedia, I learned that the [specific energy](https://en.wikipedia.org/wiki/Specific_energy) of a lithium-ion polymer battery can be up to [265 watt-hours per kilogram (Wh/kg)](https://en.wikipedia.org/wiki/Lithium-ion_battery). The nominal voltage of the lithium-ion polymer batteries here is about 3.8V. We can use [Ohm's law](https://en.wikipedia.org/wiki/Ohm%27s_law) to calculate the maximum possible capacity of each battery based on weight, assuming that each battery is always supplying the nominal 3.8V.

Let's start by calculating the maximum possible Amp-hours (Ah) per kilogram (kg) for a Li-ion poly battery at 3.8V, using Ohm's law:

```
265 Wh/kg / 3.8 V = 69 Ah/kg
```

Now, we can calculate the maximum physically possible capacity for each battery by multiplying this number by the weights of each battery:

```
OEM battery:          .047 kg * 69 Ah/kg = 3.2 Ah = 3200 mAh
Aftermarket battery: .0387 kg * 69 Ah/kg = 2.6 Ah = 2600 mAh
```

The astute reader might be wondering why this estimate for the maximum capacity of the OEM battery (3200 mAh) is less than the capacity OnePlus advertises (3300 mAh). Why is this? Well, it's because the assumption we made - that each battery is always supplying the nominal 3.8V - is false. The voltage output of a Li-ion poly battery [drops over time](https://learn.adafruit.com/li-ion-and-lipoly-batteries/voltages), so the calculation shown is only a lower bound approximation of each battery's maximum capacity.

I don't have information about the exact chemical composition of these batteries, nor the voltage charts, nor do I know what the upper and lower voltage limits are on the OnePlus 5T charging circuit. However, if we estimate that the voltage drops from 3.8V to 3.0V in a linear fashion (`V = 3.8 - .8t, 0 <= t <= 1`), we can use integration to arrive at approximately 3600 mAh maximum capacity for the OEM battery and 2900 mAh maximum capacity for the aftermarket battery.

Even without exact numbers, these calculations demonstrate that *something* is fishy about the Da Da Xiong battery's mAh claims.

### Real-world usage

Anyways, I didn't buy this shady AliExpress battery just so that I could do a bunch of math. I purchased it to restore my phone's ability to last all day, and it has definitely succeeded at that. From a qualitative perspective, I now have enough juice to keep my phone's battery fueled all day until I can recharge it at night.

From a quantitative perspective, AccuBattery reports that the aftermarket battery has an estimated 3360 mAh capacity, which about matches the capacity of the OEM battery:

![Screenshot of AccuBattery app for aftermarket battery capacity](/assets/battery/oem-capacity.png)

However, what AccuBattery fails to account for is the fact that once the aftermarket battery reaches 15%, the battery percentage begins to free-fall until it reaches 0% and shuts off. It seems like 15% on the aftermarket battery is equivalent to 1% on the OEM battery. I think this is because the Android OS cannot correctly estimate the battery's remaining charge because it has different voltage characteristics than the OEM battery, but it doesn't really bother me, I just have to make sure that to charge the phone at 15% instead of 1%. This seems to be an extremely common experience with DIY battery replacements - even folks using the iFixit battery run in to this issue.

If we take 15% off of AccuBattery's estimated capacity, we get 2856 mAh, which is really really close to what a brand new OnePlus 5T reports - AccuBattery estimates the OEM battery as having ~3000 mAh capacity when it is brand new. That about matches my experience - with the Da Da Xiong battery, the phone is staying alive longer, almost like when it was new.

### Conclusions

* Random Chinese batteries do not work as advertised - they will not magically double your phone's battery capacity.
* However, random Chinese batteries work *almost as well* as brand new OEM batteries, but your battery percentage will forever be miscalibrated.
* Never trust OnePlus customer service.
