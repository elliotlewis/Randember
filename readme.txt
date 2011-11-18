Created by Benjamin Bixby
benjaminbixby.com
bixby1@mail.lcc.edu

This plugin uses the PHP function array_rand() on an array containing every single member_id inside your EE database.  Unfortunately, array_rand() is not very "random"... so the functunality is there for now... it's just not as good as it should be (due to PHP's native random function not being that great).  As I get better at PHP, I'll be able to update this and make it so that each template call truly produces a random result.

** Use this carefully as it queries ALL MEMBER GROUPS, including admins!!**

To use the plugin, follow this syntax:
--------------------------------------

{exp:random_member parse="inward"} {!-- start plugin --}

	{!-- ee member tag --}
	{exp:member:custom_profile_data member_id="{random_member}"} {!-- the "random_member" tag must be used here for the plugin to work correctly --}

        <p>{username}</p>

	{/exp:member:custom_profile_data}


{/exp:random_member} {!-- finish plugin --}

--------------------------------------

EE2 Member Documentation:
http://expressionengine.com/user_guide/modules/member/index.html#custom-profile-data-tag