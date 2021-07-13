---
layout: post
title: "Reliable, Deliverable, Self-Hosted Email"
date: 2021-07-08 11:56:19
tags: declouding
---

I have been on an [ongoing](/blog/2019/11/site-to-site-wireguard-vpn.html) [quest](/blog/2020/08/pilo-raspberry-pi-lights-out-management.html) to free myself from cloud services for years now. During this time, I have hosted my personal email (`@bloomqu.ist`) on a <strike>Google Apps</strike> <strike>G Suite</strike> <em>Google Workspace</em> account, which, while convenient, also means that my personal emails are at the whims of [one of the world's most privacy-hostile companies](https://en.wikipedia.org/wiki/Privacy_concerns_regarding_Google).

![Man looking at the words "Do Be Evil" in Google font on the wall.](/assets/email/do-be-evil.png)
<small>Google's famous slogan.</small>

Obviously, this cannot stand forever. I wanted to self-host my email, but I know that self-hosting email is fraught with issues. The problems I wanted to avoid are as follows:

1. Open-source email software is complicated to set up if you were not a sysadmin in the 90's.
2. Popular email providers like Google Mail frequently block emails from residential/public cloud IP addresses for anti-spam reasons.
3. If your self-hosted mail server goes down, you can potentially miss out on important email.

I cannot use an email service that does not have *reliable sending and receiving*. Happily, each of these problems has a solution:

1. The [Mailu](https://mailu.io/) project bundles antispam, POP3, IMAP, SMTP, webmail, administrative interface, etc. into a set of Docker containers that can be managed with Docker Compose or your container tool of choice. This gives us a good, well-architected base setup to configure further.
2. Use a trusted SMTP outgoing relay to send email. Yes, this is not self-hosting, but you cannot "self-host" an outgoing email anyways, it is ultimately leaving your network one way or another.
3. Use a backup email server to receive email when your server is down. Set up MX records with a lower priority than your self-hosted mail server to have them act as a fallback. You can either self-host this backup or use a public service. Then, when the primary server is back up, use IMAP to mirror messages from the backup automatically.

In fact, Mailu is so easy to use, that we can configure (2) and (3) out of the box. Here is a step-by-step guide to configuring Mailu with an outbound SMTP gateway and backup MX server:

### Part 1: Install Mailu

Follow the [Mailu setup guide](https://mailu.io/1.7/setup.html) published on their website. Make sure to enable the `fetchmail` service - `fetchmail` will pull email from our backup mail server.

Set up your DNS records appropriately. For most use cases, this means pointing an A record from your mail hostname (in my case, `mail.chary.us`) to your mail server's public IP, and configuring MX records on the domains that will be receiving email:

```shell
~ dig A mail.chary.us
mail.chary.us.      300	IN	A	107.191.100.174
~ dig MX chary.us
chary.us.           300	IN	MX	1 mail.chary.us.
~ dig MX bloomqu.ist
bloomqu.ist.        300	IN	MX	1 mail.chary.us.
```

We do not need to set up SPF/DKIM DNS records at this time, since we will be using an outgoing SMTP relay to send email, not the mail server itself.

### Part 2: Set up an outgoing SMTP relay

To prevent other email servers from discarding our emails because they're from an IP with no reputation for mail sending, we can use an outgoing SMTP relay instead of sending email ourselves.

There are many options available for this, with [Mailgun](https://mailgun.com/), [Mandrill by Mailchimp](https://mandrillapp.com/) and [SendGrid by Twilio](https://sendgrid.com/) being the three heavy-weights of the industry. I chose to go with SendGrid, because their free plan offers sending 100 emails/day forever, which is far more emails than I can see myself sending.

Once you have an account with your SMTP relay provider, you will want to add the domain(s) that you will be sending from to your account. In SendGrid, this is called "Sender Authentication". As part of this, you will configure DNS CNAME records for [DKIM](https://en.wikipedia.org/wiki/DomainKeys_Identified_Mail) and [SPF](https://en.wikipedia.org/wiki/Sender_Policy_Framework):

![Screenshot of SendGrid CNAME instructions](/assets/email/sendgrid-dns.png)

Confused about how SPF can work like this, even though it's being set on `em0000.yourdomain.com`, not `yourdomain.com`? SPF is used to validate the `Return-Path` header, not `From`, and SendGrid uses `em0000.yourdomain.com` as the `Return-Path`. 💥. [Read more on StackOverflow.](https://stackoverflow.com/q/67156334/3474615)

Now that the domains are configured, obtain the credentials for your provider's SMTP gateway. In SendGrid, this is under the "API Keys" settings panel. Your API key is your password.

In your `mailu.env` file, update the following environment variables:

```shell
# Change to your SMTP relay host - port 587 will automatically use SSL
RELAYHOST=smtp.sendgrid.net:587
# Change to your username, on SendGrid this is the literal string "apikey"
RELAYUSER=apikey
# Change to your password, on SendGrid this is the API key you obtained
RELAYPASSWORD=your-api-key-goes-here
```

If you have already started Mailu, run `docker-compose down; docker-compose up -d` to reload the env file.

You should now be able to send an outgoing email (for example, via the Mailu webmail) and by inspecting the `Received` header at the recipient end, see that it was relayed through SendGrid:

```
// more headers
Received: from xtranbvx.outbound-mail.sendgrid.net (xtranbvx.outbound-mail.sendgrid.net. [167.12.12.138])
// more headers
```

If this does not work, you can tail the logs using `docker-compose logs -f --tail=0` and re-send the email to see what the error was.

### Part 3: Set up the backup email server

If, heaven forbid, your power goes out, your server gets knocked off the shelf, or the fiber line to your house is cut, you will not want to miss out on incoming e-mails. Although some mail transfer agents (MTAs) will retry delivery if the recipient SMTP server is unavailable, you really really don't want to be relying on "maybe the sender will retry" when it comes to important email.

Luckily, this is pretty easy to fix, by the nature of how MX (*M*ail E*X*changer) DNS records work. Each record has a priority, with the lowest priority records being considered first. Records with equal priority are "randomly" chosen by the sending MTA. For example, with the following MX records, mail would always be delivered to `mail.foo.net`, unless `mail.foo.net` is down, in which case it will go to `backup-mail.foo.net` (`10 < 20`):

```shell
foo.net.   300  IN  MX  10 mail.foo.net.
foo.net.   300  IN  MX  20 backup-mail.foo.net.
```

You can either set up your own mail server for this purpose, or use a public Internet service as the backup.

If you decide to self-host a backup mail server, make sure the infrastructure is separate from your primary mail server. There is [some discussion on the Mailu repo](https://github.com/Mailu/Mailu/issues/591) about setting up a backup MX server, and if you DuckDuckGo "backup MX server", you can find plenty of information about setting up a `postfix` server in this manner.

I went with the non-self-hosted option - since I already have GSuite set up to receive emails for all my domains, I can use it as the "backup" MX. Here is what my MX records look like:

```shell
➜  ~ dig MX bloomqu.ist
bloomqu.ist.		300	IN	MX	1 mail.chary.us.
bloomqu.ist.		300	IN	MX	3 aspmx.l.google.com.
bloomqu.ist.		300	IN	MX	5 alt1.aspmx.l.google.com.
bloomqu.ist.		300	IN	MX	5 alt2.aspmx.l.google.com.
bloomqu.ist.		300	IN	MX	10 aspmx2.googlemail.com.
bloomqu.ist.		300	IN	MX	10 aspmx3.googlemail.com.
```

As a result, MTAs will first attempt to deliver to `mail.chary.us`. If my server is down, delivery will fall back to Google Mail.

There are also paid services that offer to do specifically this - a cursory search found [this affordable offering](https://www.prolateral.com/email-services/backup-smtp/backup-mx.html).

Once your backup MX records are in place, the last step is to configure Mailu to automatically retrieve email from your backup MX using `fetchmail`. This is available via the web administration interface. Navigate to `/admin` and click on "Fetched accounts". From here, you can configure automatic fetching via IMAP or POP3. My setup for Google Apps looks like this:

![Screenshot of Fetched Account page](/assets/email/fetched-account.png)

Note that Mailu is configured to only pull unread email via "Fetched accounts".

By default, `fetchmail` will run every 60 seconds to pull in email.

### Part 4: Next Steps

Now you are sending and receiving email in a safe, reliable way, while still maintaining control of your data at rest. 🎉🎉🎉!

![Life is happy!](/assets/email/life-is-happy.jpg)
<small>Life is happy!</small>

After setting everything up, you may wish to take some additional steps to enhance and secure your setup:

* Set up a catch-all address for your domains using `Aliases` in the Mailu admin panel.
* Establish a backup strategy - as with anything self-hosted, the data is now your ultimate responsibility. Nobody will be around to help you recover lost emails when your server's hard drive inevitably crashes, so establish a strategy now.
* Increase your mailbox quota - by default, Mailu creates all accounts with a 1GB mail quota, which is pretty small.
* Disable any services you don't need running 24/7 - this could include the admin panel and webmail.
* Test your email setup by sending and receiving emails from your friends. This will help catch any errors with your setup before they manifest into truly embarrassing email problems.