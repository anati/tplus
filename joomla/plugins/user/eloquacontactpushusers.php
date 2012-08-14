<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.plugin.plugin');

/* 
 * Mock a form submit after a user is created.
 */
class eloquaMockSubmit{
    const NEW_USER_CREATED = 'eloqua_mock_submit_new_user_created';
    const NEW_USER_SUBMITTED = 'eloqua_mock_submit_new_user_submitted';
    /*
     * After a user is created, store their contact data.
     * The contact data will be submitted to Eloqua.
     */
    public static function newUserCreated( $contact ) {
        $_SESSION[self::NEW_USER_CREATED] = array('firstname' => $contact['name']['first'], 'lastname' => $contact['name']['last'], 'email' => $contact['email']);
    }
    /*
     * Determine if a new user has been created.
     * If the user has been created, we need to submit their contact data to Eloqua.
     */
    public static function isNewUser() {
        return isset($_SESSION[self::NEW_USER_CREATED]) && is_array($_SESSION[self::NEW_USER_CREATED])? true : false;
    }
    /*
     * Return the new user's contact data.
     */
    public static function newUserData() { 
        return self::isNewUser()? $_SESSION[self::NEW_USER_CREATED] : false;
    }
    /*
     * Generate the script needed to submit the contact data to Eloqua.
     */
    public static function formSubmit($form_name){
        $new_user = self::newUserData();
        if( ! $new_user )
        {
            return;
        }

        // Make sure data for JS is properly escaped.
        foreach( $new_user as $key => &$value )
        {
            $value = addslashes($value);
        }
        $form_name = addslashes($form_name);
        $script = <<<SCRIPT
            <script type="text/javascript">
                        jQuery(document).ready(function(){
                        jQuery('{$form_name}')
                            .find('input[name=C_FirstName]').val('{$new_user['firstname']}').end()
                            .find('input[name=C_LastName]').val('{$new_user['lastname']}').end()
                            .find('input[name=C_EmailAddress]').val('{$new_user['email']}').end()
                            .find('input[type=submit]').trigger('click');
                        });
            </script>
SCRIPT;
        self::formSubmitted();
        return $script;
    }
    /*
     * After the form submit, identify in the session data
     * that the contact data has been sent to Eloqua.
     * In addition, remove the contact data from the session to 
     * prevent the user from being re-submitted.
     */
    private static function formSubmitted() {
        unset($_SESSION[self::NEW_USER_CREATED]);
        $_SESSION[self::NEW_USER_SUBMITTED] = "1";
    }
    /*
     * Determine if the user was submitted to Eloqua.
     */
    public static function newUserSubmitted() {
        return isset($_SESSION[self::NEW_USER_SUBMITTED]) &&  $_SESSION[self::NEW_USER_SUBMITTED] == "1";
    }
    public static function finish() {
        self::reloadThankYouMessage();
        self::cleanup();
    }
    /*
     * Reload the default Joomla "Thank you for creating an account" message.
     * The contact data submit causes the user to miss the initial message.
     */
    private static function reloadThankYouMessage() { 
        // Load the "user" component language file.
        $lang =& JFactory::getLanguage();
        $extension = 'com_user';
        $base_dir = JPATH_SITE;
        $language_tag = 'en-GB';
        $reload = true;
        $lang->load($extension, $base_dir, $language_tag, $reload);

        // Display the "Thank you for registering" message.
        JFactory::getApplication()->enqueueMessage( JText::_( 'REG_COMPLETE_ACTIVATE' ), 'message' );
    }
    /* 
     * After the user has been submitted to Eloqua, clean up the session data.
     */
    private static function cleanup() {
        unset($_SESSION[self::NEW_USER_CREATED]);
        unset($_SESSION[self::NEW_USER_SUBMITTED]);
    }
}

class plgUserEloquaContactPushUsers extends JPlugin {
    private $password, $username, $endpoint;
    /**
     * Constructor
     *
     * For php4 compatibility we must not use the __constructor as a constructor for
     * plugins because func_get_args ( void ) returns a copy of all passed arguments
     * NOT references.  This causes problems with cross-referencing necessary for the
     * observer design pattern.
     */
    function plgUserEloquaContactPushUsers( &$subject, $config )
    {
        parent::__construct( $subject, $config );
        $this->username = (isset($config['username']))? $config['username'] : "";
        $this->password = (isset($config['password']))? $config['password'] : "";
    }

    /* 
     * After the user is created, add them to the
     * contact list in Eloqua.
     */
    function onAfterStoreUser($user, $isnew, $success, $msg)
    {
        global $mainframe;

        $contact = array();
        $fullname = isset($user['name'])? $user['name'] : "";
        $contact['name'] = $this->parseName($fullname);
        $contact['email'] = isset($user['email'])? $user['email'] : "";

        $this->username = $this->params->get('username');
        $this->password = $this->params->get('password');
        $this->endpoint = $this->params->get('endpoint');

        // If this is a new user, we will add them to Eloqua.
        if ($isnew)
        {
            // Add user as a contact in Eloqua
            try
            {
                define('ELOQUA_LIBRARY_DIR', "eloqua.client.soap1.2.php");
                define('ELOQUA_WSDL', $_SERVER['DOCUMENT_ROOT']."/plugins/user/".ELOQUA_LIBRARY_DIR."/wsdl/EloquaServiceV1.2.wsdl");
                require_once(ELOQUA_LIBRARY_DIR."/EloquaSOAPClient.php");
                $client = new EloquaSoapClient(ELOQUA_WSDL, $this->username, $this->password, $this->endpoint);

                $entity_type = new EntityType(0, "Contact", "Base");

                $dynamic_entity_fields = new DynamicEntityFields();
                $dynamic_entity_fields->setDynamicEntityField('C_EmailAddress', $contact['email']);
                $dynamic_entity_fields->setDynamicEntityField('C_FirstName', $contact['name']['first']);
                $dynamic_entity_fields->setDynamicEntityField('C_LastName', $contact['name']['last']);
                define('ELOQUA_CONTACT_GROUP_ID', 151);
                $dynamic_entity_fields->setDynamicEntityField('C_SFDC_Web_Activity_Type1', "Blogs");

                $entity = new DynamicEntity($entity_type, $dynamic_entity_fields, null);

                // Create the request.
                $param = new Create(array($entity));

                // Initiate the Eloqua mock submit process.  This will submit a form to Eloqua with the new user's information to initiate tracking.
                eloquaMockSubmit::newUserCreated($contact);

                // Invoke SOAP Request.
                $response = $client->Create($param);

                // Fetch the response and the contact's ID.
                $create_result_id = $response->CreateResult->CreateResult->ID;
            }
            catch(Exception $e)
            {
                error_log("Eloqua Contact Integration Plugin - Failed to add contact: ".$e->getMessage());
            }
            try
            {
                // Add them to the website contact group
                $this->addToGroup($client, $create_result_id, ELOQUA_CONTACT_GROUP_ID);
            }
            catch(Exception $e)
            {
                error_log("Eloqua Contact Integration Plugin - Failed to add contact to group ".ELOQUA_CONTACT_GROUP_ID.": ".$e->getMessage());
            }
        }
    }
    function parseName($fullname)
    {
        $fullname = trim($fullname);
        $fullname = preg_split("/ /", $fullname);
        $name = array('first' => "", 'last' => "");
        $name['first'] = array_shift($fullname);
        $name['last'] = join(" ", $fullname);
        return $name;
    }
    function addToGroup($client, $contact_id, $group_id)
    {
        // Retrieve the contact.
        $entityTypeName = "Contact";
        $entityType = "Base";
        $entityType = new EntityType(0, $entityTypeName, $entityType);
        $param = new Retrieve($entityType,array($contact_id),array());
        $response = $client->Retrieve($param);
        $retrieveResult = $response->RetrieveResult;
        reset($retrieveResult);
        $dynamicEntity = current($retrieveResult);

        // Retrieve the "Website Blog Contacts" Group Asset.
        $assetTypeName = "Website Blog Contacts";
        $assetType = "ContactGroup";
        $assetType = new AssetType(0, $assetTypeName, $assetType);
        $param = new RetrieveAsset($assetType,array($group_id),array());

        $response = $client->RetrieveAsset($param);
        $retrieveResult = $response->RetrieveAssetResult;
        reset($retrieveResult);
        $dynamicAsset = current($retrieveResult);

        // Add the user to the group.
        $param = new AddGroupMember($dynamicEntity,$dynamicAsset);
        $response = $client->AddGroupMember($param);
        $createResultID = $response->AddGroupMemberResult->Success;
    }
}
