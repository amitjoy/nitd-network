<?
if( !class_exists('he_contacts') ) {

/**
 * Contacts Module
 *
 * @author Eldar
 * @copyright Hire-Experts LLC
 * @version Contacts Module 1.01
 */
class he_contacts
{
    function he_contacts()
    {
        $this->check_db_structure();
    }

    function check_db_structure()
    {
        global $settings;

        $file_version = $this->get_version();

        //check if db structure is ready
        if( !$settings['he_contacts_version'] )
        {
            if( !he_database::fetch_row("SHOW COLUMNS FROM se_settings LIKE 'he_contacts_version'") )
            {
                he_database::query("ALTER TABLE `se_settings` ADD `he_contacts_version` int(10) NOT NULL DEFAULT $file_version");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698000', '1', 'Choose contacts.', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698001', '1', 'Add your friends by clicking on their pictures below.', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698002', '1', 'No Friends', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698003', '1', 'more', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698004', '1', 'Invite by E-mail Address:', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698005', '1', 'Use commas to separate e-mails', 'contacts')");
                he_database::query("INSERT INTO `se_languagevars` VALUES('690698006', '1', 'Send', 'contacts')");
                $settings['he_contacts_version'] = $file_version;
            }

        }

        //check db and file version
        if( $file_version > $settings['he_contacts_version'] )
        {
            //db version is older than file so we have to upgrade db version
            switch( $settings['he_contacts_version'] )
            {
                case 101:
                break;
            }
        }
    }

    function get_version()
    {
        return 101;
    }
}
}
?>