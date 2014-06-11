---
layout: post
title:  "Tabs vs. Spaces: A Compare and Contrast Narrative"
date:   2012-08-10 00:00:00
categories: bitching
---

Once upon a time there were three developers: Ned, Jed, and Fred. Ned, Jed, and Fred all got together one day to create some excellent open-source software. They set up a git repo to put all of their code in.

Now, Ned, Jed, and Fred get along on most things. They like the same music, have the same design processes, and all vote for Ron Paul regardless of nominations. They're inseparable - the only way you can tell them apart is through the way their text editors are configured.

* Ned likes his editor to use 4 columns for each indention in the code.
* Jed likes his editor to use 5 columns for each indention in the code.
* Fred likes his editor to use 7 columns for each indention in the code. That's his lucky number.

This story will end in one of two ways. It can end in tears when frustration, incompetence, and lack of standards have ripped the project apart; or it can end in success, when their open-source project capitulates them into the FOSS spotlight.
Sad Ending
-

If you'll remember, Ned uses 4 columns per indention, Jed uses 5, and Fred uses 7. Let's say Ned, Jed, and Fred have been collaborating on a file named swankyCode.c. They've been having some issues though. You know why? It's because they're using spaces to indent.

Ned pushes up the meatiest commit of his life. It fixes all the bugs in the code and makes it run 500x faster. Ned then goes on vacation. Jed looks at the code and realizes that it could be done better - that Ned's code could make swankyCode.c run 2000x faster. So he pushes up that change - with 5-space indents. Fred realizes that Jed's optimizations introduces bugs, so he fixes those with an elegant commit, using 7-space indents.

Ned comes back to the project after his nice vacation to find that the codebase looks like a game of Jenga gone horribly wrong. He can't read any of the code. It's a jumbled mess of LOCs strewn about. He yells at Fred and Jed, tells them to use 4-space indents like any red-blooded American would. Fred and Jed reluctantly agree, because they care about the project and want it to succeed.

Fred and Jed are uncomfortable with 4 spaces. Their screens are different sizes and their minds read code differently. Unfortunately, the project has to adhere to a standard.

End Result:

* Ned is happy. He uses 4 spaces and sees 4 columns, and he is comfortable with 4 columns.
* Jed is sad. He uses 4 spaces and sees 4 spaces, but he is comfortable with 5 columns.
* Fred is sad. He uses 4 spaces and sees 4 spaces, but he is comfortable with 7 columns.
* Ned is as efficient and as good a coder as ever.
* Jed and Fred become more prone to mistakes and are uncomfortable with the codebase's aesthetics, causing them to make sporadic commits of lower quality than usual.

Happy Ending
-

If you'll remember, Ned uses 4 columns per indention, Jed uses 5, and Fred uses 7. Let's say Ned, Jed, and Fred have been collaborating on a file named swankyCode.c. They're using tabs to indent their code. Ned has his editor set to display \t as 4 columns, Jed has it set to display \t as 5, and Fred has it set to display as 7.

Ned pushes up the meatiest commit of his life. It fixes all the bugs in the code and makes it run 500x faster. Ned then goes on vacation. Jed looks at the code and realizes that it could be done better - that Ned's code could make swankyCode.c run 2000x faster. So he pushes up that change using \t. Fred realizes that Jed's optimizations introduces bugs, so he fixes those with an elegant commit, using \t as well.

Ned comes back to the project after his nice vacation and pulls down the latest changes so he can keep working. Because his editor is configured to display \t as 4 columns, he can read the code comfortably and understand what is happening with struggling. Jed and Fred are also contributing to the same codebase using \t. They're happy. Jed gets his 5 columns and Fred gets his 7, just how they like it.

Fred and Jed are comfortable working on the codebase. Their screens are different sizes and their minds read code differently, so they use different columns to represent \t. Of course, this is irrelevant to the project as a whole - because they're using tabs, any contributor can view the code however they are comfortable.

End Result:

* Ned is happy. He uses \t and sees 4 columns, and he is comfortable with 4 columns.
* Jed is happy. He uses \t and sees 5 columns, and he is comfortable with 5 columns.
* Fred is happy. He uses \t spaces and sees 7 columns, and he is comfortable with 7 columns.
* Ned is as efficient and as good a coder as ever.
* So are Jed and Fred.
* They succeed and are happy.

