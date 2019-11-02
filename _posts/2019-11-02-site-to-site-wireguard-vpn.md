---
layout: post
title:  "Creating a Site-to-Site WireGuard VPN for a home server"
date:   2019-11-02 13:17:40
categories: declouding, wireguard
---

![Image of Bezos gazing at you.](/assets/bezos-gazing.jpg)
<small>This guy is looking at pictures of my wife, probably. [photo source](https://news.sky.com/story/amazon-boss-jeff-bezos-claims-he-was-blackmailed-by-national-enquirer-over-below-the-belt-selfie-11631136)</small>

For the last decade or so, I've been steadily increasing the amount of data I send to the cloud. I sync photos of my friends and family to Amazon Photos, blast my private data off to Microsoft OneDrive, give my passwords to 1password, and trust my web hosting provider not to run away with my data.

I've been growing less satisfied with the privacy options afforded by major cloud providers. Amazon Photos, for example, has started using machine learning to identify the people in my photos. I'm not really keen on the mental image of Jeff Bezos, sitting on a yacht, looking at pictures of my wife.

For that reason, I've decided to start moving my personal data into my personal control. I want the bits and bytes that describe the intimate details of my life to live around under my own roof and only escape to the Internet with my permission.

I'd like to self-host [NextCloud](https://nextcloud.com/) (to replace Amazon Photos and OneDrive), static site hosting (to replace my existing VPS and GitHub Pages), and continuous integration using [GitLab CE](https://about.gitlab.com/install/?version=ce) (to replace Travis CI). It's also a pipe dream to one day host my own email server, so I can move off of Google Apps.

To make all that possible, I'd need a way for Internet traffic to reach my home LAN, behind a router with a dynamic IP. I don't want to use [dynamic DNS](https://en.wikipedia.org/wiki/Dynamic_DNS), since I think it provides a poor user experience due to DNS caching. Also, my Internet service provider does not explicitly allow server hosting, so excess incoming Internet traffic might get me in trouble. 😅

The solution comes in the form of an Internet-facing server with a static IP. That server will receive requests and forward them to the LAN server through an encrypted, performant [WireGuard][wireguard] tunnel:

```text
┌-------------------┐     ┌------------------------┐
| Internet Traffic  | <-> | Internet-Facing Server |
└-------------------┘     └------------------------┘
                                    /\
                                    || WireGuard Site-to-Site VPN
                                    \/
                             ┌-----------------┐
                             | Home LAN Server |
                             └-----------------┘
```

I chose [WireGuard][wireguard] over other VPN candidates because of the simplicity of configuration and low server overhead. Without further ado, let's get into how to set this up.

### Step 1: Internet-Facing Server Setup

When choosing a server provider for your Internet-facing server, make sure to choose one with low latency to your home network, since that latency will be added to every request you make.

If the provider has test servers listed on their website, you can `ping` them from your home network to make an estimate of the round-trip-time that will be added to each request.

I chose [RamNode][ramnode] for my hosting, since I get about 3ms of ping to their [test IP](https://clientarea.ramnode.com/knowledgebase/17/Do-you-have-a-test-IP-I-can-ping.html) in Atlanta:

```shell
~ ping -c 4 107.191.101.180
PING 107.191.101.180 (107.191.101.180) 56(84) bytes of data.
64 bytes from 107.191.101.180: icmp_seq=1 ttl=55 time=3.08 ms
64 bytes from 107.191.101.180: icmp_seq=2 ttl=55 time=3.34 ms
64 bytes from 107.191.101.180: icmp_seq=3 ttl=55 time=3.08 ms
64 bytes from 107.191.101.180: icmp_seq=4 ttl=55 time=3.41 ms

--- 107.191.101.180 ping statistics ---
4 packets transmitted, 4 received, 0% packet loss, time 7ms
rtt min/avg/max/mdev = 3.076/3.226/3.410/0.165 ms
```

Since WireGuard is really efficient, you don't need a beefy, expensive server to run it on. I chose a server with 512MB of RAM, 1 CPU core, and 2 TB of outgoing bandwidth per month for $3/mo. This will be the only real expense of this project.

I installed CentOS on my Internet-facing server, but WireGuard is compatible with [a wide variety of operating systems][wireguard-install].

Once you have your server, SSH in and follow this guide to configuring WireGuard:

1. Install WireGuard by following [the instructions for your server OS][wireguard-install].
2. After installing WireGuard, you will have access to the [`wg`][wg] command, which we will use to generate public/private keypairs for the server and client.
    * Run `wg genkey` to generate a private key. This will be the server's private key. This should be kept a secret, as it can be used to decrypt data sent to the server.
    * Now, pipe that result into `wg pubkey` to generate the server's public key. This will used later to configure the client to send encrypted messages to the server. For example: `echo "server-private-key" | wg pubkey`
    * Repeat the above steps to generate a private & public key for the LAN client.
3. Create a file using your favorite text editor in `/etc/wireguard/wg0.conf`, and fill it out using the below template. If you're curious about the `wg0.conf` file format, check out the [`wg-quick` man page][wg-quick] for more information.
{% gist 5d50cdcb8d1a3548ab3fc607e14f128d internet-wg0.conf %}

<ol start="4">
<li markdown="1">Now that you've configured the server, you can bring up the WireGuard interface by doing `wg-quick up wg0`.
</li>
<li markdown="1">Do `wg show` to see the status of your WireGuard network:

```shell
~ wg show
interface: wg0
public key: your-server-public-key
private key: (hidden)
listening port: 51820

peer: your-client-public-key
allowed ips: 10.222.0.2/32
persistent keepalive: every 25 seconds
```

</li>
<li markdown="1">Now use `systemctl enable wg-quick@wg0` to ensure that this interface is brought up on every boot.
</li>

</ol>

Congrats! Your Internet-facing server is now set up to act as a WireGuard host. Now let's proceed to the client configuration on the LAN server.

### Step 2: LAN Server Setup

Follow these instructions on your home LAN server to set it up as a WireGuard client:

1. Install WireGuard using the [installation instructions for your OS][wireguard-install].
2. Create a file using your favorite text editor in `/etc/wireguard/wg0.conf`, and fill it out using the below template. Again, for more info on the `wg0.conf` file format, check out the [`wg-quick` man page][wg-quick].
{% gist 5d50cdcb8d1a3548ab3fc607e14f128d lan-wg0.conf %}

<ol start="3">
<li markdown="1">Now that you've configured the client, you can bring up the WireGuard interface by doing `wg-quick up wg0`.
</li>
<li markdown="1">Do `wg show` to see the status of your WireGuard network:

```shell
~ wg show
interface: wg0
  public key: your-client-private-key
  private key: (hidden)
  listening port: 55018

peer: your-server-public-key
  endpoint: your-server-domain-name-or-IP-address:51820
  allowed ips: 10.222.0.0/16
  latest handshake: 1 minute, 41 seconds ago
  transfer: 959.23 MiB received, 1.57 GiB sent
  persistent keepalive: every 25 seconds
```

</li>
<li markdown="1">At this point, you should be able to do `ping 10.222.0.1` to reach your WireGuard server through your new VPN.
</li>
<li markdown="1">Now use `systemctl enable wg-quick@wg0` to ensure that this interface is brought up on every boot.
</li>
</ol>

Now your VPN is set up and you are ready to start exposing services on your home server through your VPN.

### Step 3: Start Exposing Services

You'll need a way to proxy traffic that hits your Internet-facing server through the VPN to your home server.

* For **HTTP traffic**, set up a reverse proxy on the Internet-facing server. My tool of choice for this is [nginx](https://nginx.org/), which has a fantastic [reverse proxy module](https://nginx.org/en/docs/http/ngx_http_proxy_module.html). Here's a very basic nginx config to proxy traffic for `example.com` to port 8080 on your LAN server:
  ```conf
  server {
    server_name example.com;
    location / {
      proxy_pass http://10.222.0.2:8080/;
    }
  }
  ```
* For **other TCP/IP traffic**, set up [`rinetd`](https://github.com/boutell/rinetd) on the Internet-facing server. It will tunnel TCP traffic on one port/interface to another port/interface. For example, if you have an IRC server running on port 6667 of your home server, you could put this in `/etc/rinetd.conf` to forward traffic from port 6667 of the Internet-facing server:
  ```text
  # bind to all interfaces on 6667 and pass to LAN server
  0.0.0.0 6667 10.222.0.2 6667
  ```

With both of these methods, keep in mind that the IP of the original client will be obscured by the reverse proxy. You'll need to use other methods (such as an [`X-Proxied-For` header](https://en.wikipedia.org/wiki/X-Forwarded-For) containing the real client's IP address) if you want to receive the client's real IP at your home server.

Now you can start moving all of the services you want to self-host under your own roof! In future articles, I will discuss setting up your own self-hosted photo storage, continuous integration pipelines, web hosting, and others.

### Extra: Securing Your Internet-Facing Server

One of the benefits to this setup is that you no longer need to expose your Internet-facing server's SSH port publicly. You can use the VPN to access it instead.

1. Set up your computer as a WireGuard client using the same method that you used to set up your home LAN server as a client. Or, just use your home LAN server as a [bastion host](https://en.wikipedia.org/wiki/Bastion_host), so you must be SSH'd into it to SSH into your Internet-facing server.
2. Set up `ufw` on your Internet-facing server using these commands:
  ```shell
  # turn on ufw
  ufw enable
  # allow VPN IPs to access SSH on port 22
  ufw allow from 10.222.0.0/24 to any port 22 proto tcp
  # remove default SSH allow rules
  ufw delete allow SSH
  ufw delete allow 22/tcp
  ```

Now you should only be able to access SSH on your Internet-facing server via the VPN IP address, `10.111.0.1`.

### Extra: Alternative WireGuard Distribution

The official [WireGuard][wireguard] distribution comes as a kernel mod. While the official implementation is probably best, there is also an alternative in [`boringtun`](https://github.com/cloudflare/boringtun).
`boringtun` is Cloudflare's own userspace WireGuard client, used in their proprietary [Argo Tunnel](https://developers.cloudflare.com/argo-tunnel/) Site-to-Site VPN. You can use this if you're unwilling to install a kernel mod.

[wireguard]: https://www.wireguard.com/
[ramnode]: https://ramnode.com/
[wireguard-install]: https://www.wireguard.com/install/
[wg-quick]: https://git.zx2c4.com/WireGuard/about/src/tools/man/wg-quick.8
[wg]: https://git.zx2c4.com/WireGuard/about/src/tools/man/wg.8
