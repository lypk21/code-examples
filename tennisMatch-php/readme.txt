please read the question description on question.txt first.

I resolve the question by PHP using Terminal to input the filename and output the result to Terminal.

php files explain:
index.php : entry file of the Application
Application.php: handle the business logic, including analyzing the input commands from Terminal,analyzing line by line and generating results.
Model/Player.php:  entity of Player, store data and result.
Model/Match.php:  entity of Match, store some data and result.
Model/Tournament.php: entity of Tournament, store Matches and Players


How to run the APP?

cd tennisMatch
php index.php
Enter a source file: full_tournament.txt
Tournament Result, Matches Names:
01
02
Tournament Result, Players Names:
A
B
C
To query the match result, enter 1
To query player score, enter 2
exit application, enter 0
1
To query the match result, please enter the above Match Name (ie:01): 01
Person A defeated Person B
2 sets to 0
To query the match result, enter 1
To query player score, enter 2
exit application, enter 0
1
To query the match result, please enter the above Match Name (ie:01): 02
Person C defeated Person A
2 sets to 1
To query the match result, enter 1
To query player score, enter 2
exit application, enter 0
0
192-168-1-103:tennisMatch liuyingping$ php index.php
Enter a source file: full_tournament.txt
Tournament Result, Matches Names:
01
02
Tournament Result, Players Names:
A
B
C
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
1
To query the match result, please enter the above Match Name (ie:01): 02
Person C defeated Person A
2 sets to 1
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
1
To query the match result, please enter the above Match Name (ie:01): 01
Person A defeated Person B
2 sets to 0
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
2
To query the Games Player Person, please enter the above Player Name (ie:A): A
23  17
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
2
To query the Games Player Person, please enter the above Player Name (ie:A): B
0  12
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
2
To query the Games Player Person, please enter the above Player Name (ie:A): C
17  11
To query the match result, enter 1; To query player score, enter 2; To exit application, enter 0
0
