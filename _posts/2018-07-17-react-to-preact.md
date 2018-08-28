---
layout: post
title:  "Moving From React to Preact: A Developer’s Story"
date:   2018-07-17 00:00:00
categories: react
---

![](https://cdn-images-1.medium.com/max/1600/1*mThPH_B2Ms7qG_nVySy3qA.gif)

For the past few months, I’ve been working on a single-page application built using Facebook’s [React](https://reactjs.org) framework.

Those who know me know that I’m obsessed with performance. Getting me on the React bandwagon took years simply because I didn’t like the bloat it introduces. Even though I’ve grown to love React for the deftness it affords when designing front-ends, I still have an issue with all the bloat it introduces.

Recently, I heard about the [Preact](https://preactjs.com/) project — a drop-in, API-compatible replacement for the React framework that’s only **3kb** in size compared to React’s **135kb**. It’s faster and easier to understand than React. When I heard about this framework, I knew I had to try it. I’ve documented my efforts here (including some troubleshooting tips) to guide others who would follow this path.

* * *

### The React App

Before transitioning to Preact, I wanted to examine my existing app so I could be aware of any potential pain points I’d encounter while transitioning.

My application was created with `create-react-app`. This means a lot of the complexity of the build environment is hidden away in packages like react-scripts. This is good for a new user, but potentially bad when trying to switch away from React.

I make heavy use of React’s [createContext](https://reactjs.org/docs/context.html#reactcreatecontext) API to enable authentication and back-end use throughout the app. Preact only supports the [legacy context API](https://reactjs.org/docs/legacy-context.html), so I needed to use Georgios Valotasios’s [preact-context](https://github.com/valotas/preact-context) library to provide those APIs.

My app uses `react-router` for routing and passing state between linked components. I had some concerns about this working.

* * *

### s/React/Preact/g

Preact has a [helpful guide for switching](https://preactjs.com/guide/switching-to-preact). They provide a shim, preact-compat, that allows you to swap your codebase over with minimal code changes. There’s also a longer guide to migrating your codebase to use preact without the compat libraries.

I chose to do the full migration in order to take advantage of the full performance benefits — the `preact-compat` library adds 2kb to Preact’s 3kb size. I wouldn’t have blinked at 2kb before, but now the bloat of React is no longer in the way, 2kb seems like a whole lot more.

First, install the preact package and get rid of react:

<pre name="5af4" id="5af4" class="graf graf--pre graf-after--p">yarn add preact
yarn remove react react-dom</pre>

This will make all of your React plugins begin to throw warnings about unmet peer dependencies, this is fine. Currently there is no way to tell these plugins about Preact.

Then, use the following command to replace all the `import X from 'react'` statements in my package with `import X from 'preact'` (assumes all your code is in `src`):

<pre name="195f" id="195f" class="graf graf--pre graf-after--p">find ./src/ -type f -print0 | xargs -0 sed -Ei "s/(['\"])react(-dom)?(['\"])/'preact'/g"</pre>

The import in `index.js` must be corrected manually. Replace the `React` and `ReactDOM` imports with

<pre name="8685" id="8685" class="graf graf--pre graf-after--p">import React, { render } from 'preact';</pre>

Preact moves the `render` method out of `ReactDOM` and into its own export, so change the `ReactDOM.render` call to just a call to `render` .

To fix React Contexts, install `preact-context` with `yarn add preact-context`. Then, add the import statement to all your Contexts:

<pre name="b94a" id="b94a" class="graf graf--pre graf-after--p">import { createContext } from 'preact-context';</pre>

After changing `React.createContext` calls to `createContext` calls, all that’s left to do is run `yarn start` and pray.

* * *

### You’re done!

(In a perfect world,) your app should now be working with Preact instead of React. Kudos to the developers of Preact for creating a drop-in replacement for the temperamental React framework. Enjoy your faster load times, easier-to-debug code base, and lower bandwidth usage!

If you did encounter issues, continue on to Troubleshooting, where I’ve outlined a few of the issues I ran into when doing this on my app.

* * *

### Troubleshooting

Of course, my migration did not go flawlessly — things never seem to, do they? I hope yours does, but if it does not, here are the issues I encountered and how I overcame them.

#### Context changes

My first issue was that changes being broadcast from a Context were no longer being sent to child components. I had the Context set up like this:

<pre name="b64f" id="b64f" class="graf graf--pre graf-after--p"><Provider value={this.state}> ... </Provider></pre>

Context Consumers are supposed to re-render whenever the Provider’s `value` changes. In React, the above line would cause all dependent components to re-render when the Provider’s `state` changed.

In Preact, this stopped working. I’m assuming this is because Preact does not copy `state` on `setState`; instead, it modifies `state` in place. Because JS object comparisons are done by reference, no change appears to happen and so the Consumer components are never re-rendered.

To get around this, I’m copying `state` to a new object whenever it changes:

<pre name="1eea" id="1eea" class="graf graf--pre graf-after--p"><Provider value={Object.assign({}, this.state)}> ... </Provider></pre>

If anyone knows a cleaner way, please let me know.

#### React Developer Tools

I discovered that my React developer tools were no longer working. This is because Preact does not include the developer tools in the main bundle to cut down on unnecessary load time.

To fix this, I just added the following line to `index.js` to load the debug module if this module is being hot-reloaded by Webpack:

<pre name="111d" id="111d" class="graf graf--pre graf-after--p">if(module.hot) require('preact/debug')</pre>

#### React Router errors

The last issue I encountered was a problem with `react-router-dom`. The `BrowserRouter` component was throwing the following error during render:

<pre name="7e89" id="7e89" class="graf graf--pre graf-after--p">Warning: Failed prop type: Invalid prop `children` supplied to `Router`, expected a ReactNode.</pre>

For some reason, the `<div>` element that was the immediate child of my BrowserRouter was not being recognized as a valid component. I tracked down this error to the definition of `isValidElement` in `prop-types`:

<pre name="f951" id="f951" class="graf graf--pre graf-after--p">var REACT_ELEMENT_TYPE = (typeof Symbol === 'function' &&
  Symbol.for &&
  Symbol.for('react.element')) ||
  0xeac7;</pre>

<pre name="4d08" id="4d08" class="graf graf--pre graf-after--pre">var isValidElement = function(object) {
  return typeof object === 'object' &&
  object !== null &&
  object.$typeof === REACT_ELEMENT_TYPE;
};</pre>

The `<div>` fails the `isValidElement` test because it has no `$$typeof` property. The only thing I can seem to figure out about `$$typeof` is that it’s a special property React uses in order to distinguish React-created DOM objects from native DOM objects.

I tried adding a hook to `preact` to automatically populate the `$$typeof` on new `VNode` (virtual nodes) in the DOM, but that just uncovered another host of issues related to differences between the React virtual DOM and Preact’s VDOM.

So, with a heavy heart (it’s 2 kilobytes of code!!), I decided to install `preact-compat` (2kb!!!) and patch it into my app.

Doing this was fairly simple, just `yarn add preact-compat` and add 2 dependencies to your `resolve.alias` configuration in your webpack config:

<pre name="8d73" id="8d73" class="graf graf--pre graf-after--p">alias: {
  'react': 'react-compat',
  'react-dom': 'react-compat'
},</pre>

Note: Because I created my app with `create-react-app`, I had to run `yarn eject` so that I could edit my webpack config instead of using one from `node_modules`. You may or may not have to do this first.

After doing this, `react-router-dom` worked like a charm.

I also went through my project and replaced all my `preact` imports with `preact-compat` just to match the documented way of doing it:

<pre name="ce78" id="ce78" class="graf graf--pre graf-after--p graf--trailing">find ./src/ -type f -print0 | xargs -0 sed -Ei "s/'preact'/'preact-compat'/g"</pre>