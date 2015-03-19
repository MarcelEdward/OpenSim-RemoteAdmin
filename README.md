# OpenSim-RemoteAdmin
OpenSim RemoteAdmin php script

Description:

A php script to easy access the remote admin functionality in opensim.

Install:

Read the documentation on http://opensimulator.org/wiki/RemoteAdmin on how to enable the remote admin.

Edit the config.php file, fill out the [RemoteAdmin] info and add the upload location, this should be a location opensim can read from and to who you webserver can write to.

Edit the .htaccesss file, point the AuthUserFile line to the the .htpassword file 
Edit the .htpasswd file, http://www.htaccesstools.com/htpasswd-generator/

Usage:

The script generates a list with remote admin methods from the RemoteAdmin.json file. After selecting a method it generates a form wich posts the method to the opensim server and shows the results of that post.

In the RemoteAdmin.json file a default can be set in the "default": "" lines, the "type":  "" can be string, hidden, readonly, boolean, file, int or uuuid. (the int and uuid default to string)

Disclaimer:

Be carefull with the remoteadmin, there a methods in there wich allow to do permanent changes to your regions and usersdata. Use the enabled_methods in the [RemoteAdmin] section of opensim.ini to limit the remoteadmin to the functions you need.
