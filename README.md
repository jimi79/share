# share

A php script that allow to share files easily, like sprunge.

There is also some sort of gui, to do copy paste or drag&drop. And an url shortener.

install: 
* have a database
* copy conf/config.php.exemple to conf/config.php
* update the password access, and set a random password for the ciphering
* go to the url /share.php to get help in a cli (the output is in text)
* go to the url /gui.php to have a gui

run with cli:

Syntax :

To upload somethg
`somethg | curl -F data=@- https://mysite.com/share.php`
`something` can be `date`, `fortune`, etc.

Will return the url to reach the posted data

To upload somethg with a limited duration, in minutes
somethg | curl -F data=@- -F 'duration=1' https://mysite.com/share.php

Will return the url to reach the posted data. After the duration written, the posted element won't be available

To upload somethg with a password
somethg | curl -F data=@- -F 'password=foobar' https://mysite.com/share.php

Will return the url to reach the posted data, and the parameter password ready to be filled.

Notes :
- you can have duration and password

To upload somethg with the gui
go to https://mysite.com/gui.php

To download somethg

curl [url given when uploading]

If the url has a password parameter, you have to fill it with the password used to upload the element.

Notes :
- If the password is wrong, the message 'incorrect password' will be returned.
- If the element doesn't exists anymore, or never existed, the message 'not found' will be returned.

The url shortener is just at the end of the gui, and nothing is special about it.
