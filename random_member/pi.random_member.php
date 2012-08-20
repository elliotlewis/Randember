<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Randember Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Benjamin Bixby
 * @link		http://benjaminbixby.com
 */

/**
 * Version history
 *
 	1.0.4
 	Added by Elliot Lewis 20/08/12
 	Removed PHP array and random. Random member selection in MySQL. This may be an issue with large member tables
 	Added session storing of last random member so same member will not appear twice. Need move than 1 member in table!
 	
 	1.0.3
 	forked version
 	
 */
 
$plugin_info = array(
	'pi_name'		=> 'Randember',
	'pi_version'	=> '1.0.4',
	'pi_author'		=> 'Benjamin Bixby',
	'pi_author_url'	=> 'http://benjaminbixby.com',
	'pi_description'=> 'Returns a random member.',
	'pi_usage'		=> Random_member::usage()
);


class Random_member {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		$sql_where = "";

		$allmembers = array();

		$multiple = array();

		$group = $this->EE->TMPL->fetch_param('groupid');

		if ($group != "")
		{
			if(strrpos($group, "|") === FALSE)
			{
				// single group
				
				$sql_where = "group_id = '$group'";
			}
			else
			{
				// multiple groups
				
				$multiple = explode("|", $group);
				$i = 0;
				foreach ($multiple as $k => $v)
				{
					if ($i++ < count($multiple) - 1)
					{
						$sql_where .= "(group_id = '".$v."') OR ";
					}
					else
					{
						$sql_where .= "(group_id = '".$v."')";
					}
				}
			}
		}

		// Check for returned values
		$random_member = $this->find_member($sql_where);
		if ( !$random_member )
	    {			
			// should maybe return something to indicate a fail
			$random_member = 0;
		}
		
		// Return the member_id and replace the tag
		$tagdata = $this->EE->TMPL->tagdata;
		return $this->return_data = str_replace("{random_member}", $random_member, $tagdata);

	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Find member ID not returned on last call
	 *
	 * Check session for last random
	 * Loop until different
	 * But bail out after 10 tries incase only 1 member!
	 *
	 */
	 private function find_member($sql_where, $try = 0)
	 {
	 	
		 // Find members from 1 or more groups
		if ($sql_where)
		{
			$sql = "SELECT member_id FROM exp_members WHERE $sql_where ORDER BY RAND() LIMIT 0,1";
		}
		else
		{
			// Find all available members
			$sql = "SELECT `member_id` FROM `exp_members` ORDER BY RAND() LIMIT 0,1";
		}

		// Run the query
		$query = $this->EE->db->query($sql);

		// Check for returned values
		if ($query->num_rows() > 0)
	    {
	    
	    	$member_id		= $query->row('member_id');
	    	$last_member_id	= isset($_SESSION['ntts_random_member']['member_id']) ? $_SESSION['ntts_random_member']['member_id'] : FALSE;
	    	
	    	// Get last id
	    	if ( $last_member_id !== FALSE)
	    	{
	    		// Check if unique
	    		if( $last_member_id != $member_id )
	    		{
		    		// Add to cache
		    		$_SESSION['ntts_random_member']['member_id'] = $member_id;
					return $member_id;
	    		}
	    		else
	    		{
	    			if($try >= 9){
		    			return $member_id;
	    			}
	    			else
	    			{
			    		// Same as last so try again
			    		$this->find_member($sql_where, $try++);
			    	}
	    		}
	    	}
	    	else
	    	{
	    		// Add to cache
	    		$_SESSION['ntts_random_member']['member_id'] = $member_id;
				return $member_id;
			}
		}
		else
		{
			return FALSE;
		}
		
		// NB
		// Seemed obvious to use
		// $this->EE->session->cache('ntts_random_member', 'member_id');
		// $this->EE->session->set_cache('ntts_random_member', 'member_id', $member_id);
		// BUT in the docs:
		// 'Note, this is not persistent across requests'

	 }
	 
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

Returns a random member_id.  Make sure you use "parse=inward" to the plugin. Ex:

{exp:random_member parse="inward"} ... {random_member} ... {/exp:random_member}

You can also use it to select a random member based on a member group id (single group or multiple groups... use standard EE syntax and separate group numbers by using the pipe character i.e. "3|4". Ex:

{exp:random_member groupid="3|5" parse="inward"} ... {random_member} ... {/exp:random_member}

*If returned random_member == 0 then an error occoured. Likely no members in that group.

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}




/* End of file pi.random_member.php */
/* Location: /system/expressionengine/third_party/random_member/pi.random_member.php */