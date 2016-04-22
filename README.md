# share

*A php script that allow to share files easily, like sprunge.*

I just checked the sprunge website, and couldn't find source code, so i rewrote approximately a php page that seems to do the same thing

**mongodb is required**

###Install
put the both php file at some place

####Usage in cli
#####Post a file

somme command|curl [-F "password=foo"] [-F "duration=1"] -F data=@- http://localhost/share/share.php
  password is facultative
  duration is facultative, the document will not be available after a given number of minutes.
Return the url.
Example, without a passwrod :
<pre>http://localhost/share/share.php?id=571a1bfc3d78591376a8d86d</pre>
with a password
<pre>http://localhost/share/share.php?id=571a1bfc3d78591376a8d86d&password=</pre>
_password isn't shown, but the url mentions it to confirm the file is password protected_

#####Get a file
without a password
<pre>http://localhost/share/share.php?id=571a1bfc3d78591376a8d86d</pre>
with a password
<pre>curl http://localhost/share/share.php?id=571a1bfc3d78591376a8d86d&password=password</pre>


