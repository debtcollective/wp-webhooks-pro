=== WP Webhooks Pro ===
Author URI: https://wp-webhooks.com/about/
Plugin URI: https://wp-webhooks.com/
Contributors: ironikus
Donate link: https://paypal.me/ironikus
Tags: api, webhooks, zapier, automation, connector, integrations, automator, create user, ironikus
Requires at least: 4.7
Tested up to: 6.0
Stable Tag: 5.2.1
License: GNU Version 3 or Any Later Version

Put your website on autopilot by using webhooks to get rid of manual tasks and focus on what's really important for your business.

== Description ==

Put your website on autopilot by automating manual tasks to leverage your time and ressources for what's really important to your project.
WP Webhooks can be used on two different ways: 

**Trigger (Send Data):**
A trigger sends information from your WordPress website on a specific event (e.g. when a user logs in), to any API or external service of your choice (e.g. Zapier, Integromat, Pabbly, ...).

**Action (Receive Data):**
An action is the reversed way of the trigger. It allows you to receive data on your website from any API or external service (e.g. Zapier, Integromat, Pabbly) which then executes a specific functionality on your WordPress website (e.g. creating a user or post).


To give you some practical examples, here's what you can do via our plugin: 

= Usage examples =
* Create a WordPress user as soon as a new signup happens on Teachable
* Create a WordPress post using Alexa (Voice Control)
* Create WordPress users from an Excel list
* Create a Woocommerce order from some Airtable data
* Send data once a Gravity Form submission was received
* Send data after a user filled out a WPForms form
* Send data to intercom when a user logs into your WordPress website
* Fire your own PHP code based on incoming data

= Features =

**Plugin related**

* Create automated Workflows that execute multiple tasks consecutively after a specific event happened
* All of our integrations are available within a single plugin file. No need for any extra plugins.
* Authenticate every trigger and action. Supported are: API Key, Bearer Token and Basic Auth
* Add multiple Webhooks for each trigger and also for the actions
* Test all of the available triggers with a single click
* Test all of the available actions within the plugin
* Advanced settings for each webhook trigger url
* Manage all of our extensions within one plugin
* Fully translatable and ready for multilingual sites
* Fully WPML compatible
* Advanced Developer Hooks
* Optimized settings page for more control
* Supports XML, JSON, plain text/HTML and form urlencode
* Supports the following request methods: POST (Default), GET, HEAD, PUT, DELETE, TRACE, OPTIONS, PATCH
* A lot of awesome plugin features (Details down below)
* All of our [pro integrations](https://wp-webhooks.com/integrations/)
* All of our [pro features](https://wp-webhooks.com/features/)
* All of our [pro extensions](https://wp-webhooks.com/downloads/)
* Advanced security features
* Completely free premium extensions
* Supports Pabbly, Integromat, automate.io, Zapier and much more
* Data Mapping engine to manipulate your incoming/outgoing data
* Whitelabel feature (see comparison table)
* Log feature for easier debugging
* IP Whitelist feature for enhanced security
* Access token feature for enhanced security
* Webhook URL action whitelist
* In-plugin assistant

**[WordPress](https://wp-webhooks.com/integrations/wordpress/) related integrations**

* **Action**: Create, Delete, Search and Retrieve users via webhooks on your website
* **Action**: Create, Delete, Search and Retrieve posts via webhooks on your website (Custom post types supported)
* **Action**: Create, Update and Delete WordPress comments
* **Action**: Create users with user meta (ACF supported)
* **Action**: Update users and user meta (ACF supported)
* **Action**: Add and/or remove multiple user roles
* **Action**: Send an email via a webhook call
* **Action**: Find and get one or multiple users/posts via a webhook call
* **Action**: Receive data from a custom API or webhooks URL (Do whatever you want with the incoming data)
* **Action**: Create taxonomy terms and term meta (custom taxonomies supported)
* **Action**: Create posts with post meta (ACF supported)
* **Action**: Update posts with post meta (ACF supported)
* **Action**: Bulk webhook action to trigger multiple actions at the same time
* **Action**: Shortcode webhook action that fires once the shortcode is called
* **Action**: WordPress hook webhook action that fires once a specific filter of the WordPress API was called
* **Trigger**: Send data on login, register, update and deletion of a user
* **Trigger**: Send data on new post, update post and delete post
* **Trigger**: Send data on a new, updated, trashed, or deleted comment
* **Trigger**: Send data once an email was sent from your WordPress system
* **Trigger**: Send data on custom WordPress hook calls

**[Contact Form 7](https://wp-webhooks.com/integrations/contact-form-7/) related integrations**

* **Trigger**: Send data once a **Contact Form 7** form was submitted

**[Easy Digital Downloads](https://wp-webhooks.com/integrations/easy-digital-downloads/) related integrations**

* **Action**: Create, update and delete an EDD customer
* **Action**: Create, update and delete an EDD discount
* **Action**: Create, update and delete an EDD download
* **Action**: Create, update and delete an EDD payment
* **Action**: Create, update, delete, and renew an "EDD Software licensing" license
* **Action**: Create, update, and delete an "EDD Recurring" subscription

* **Trigger**: Send data once a customer was updated or created
* **Trigger**: Send data once a file was downloaded
* **Trigger**: Send data once a payment was created or a specific status reached
* **Trigger**: Send data once an "EDD Software licensing" license was activated, deactivated, or the status updated
* **Trigger**: Send data once an "EDD Software licensing" license was created, activated, deactivated, or the status changed
* **Trigger**: Send data once an "EDD Recurring" subscription was created or a specific status changed

**[Gravity Forms](https://wp-webhooks.com/integrations/gravity-forms/) related integrations**

* **Trigger**: Send data once a "Gravity Form" form was submitted

**[WP Reset](https://wp-webhooks.com/integrations/wp-reset/) related integrations**

* **Action**: Clean the WordPress uploads folder
* **Action**: Delete the .htaccess file
* **Action**: Delete all custom tables
* **Action**: Delete plugins
* **Action**: Delete themes
* **Action**: Delete WordPress transients
* **Action**: Reset WordPress
* **Action**: Truncate Custom Tables

**[WP Webhooks](https://wp-webhooks.com/integrations/wp-webhooks/) related integrations**

* **Action**: Fire a webhook trigger

* **Trigger**: Trigger once a webhook action was executed

Our free premium extensions for [WP Webhooks Pro](https://wp-webhooks.com/?utm_source=wordpress&utm_medium=description&utm_campaign=WP%20Webhooks%20Pro)

* [Create Blog Post Via Email](https://wp-webhooks.com/downloads/wpwh-pro-send-blog-post-by-email/): Yes, it will allow you to create WordPress posts via Email
* [Execute PHP Code](https://wp-webhooks.com/downloads/execute-php-code/): It is as massive as it sounds. It allows you to run php scripts through webhooks on your WordPress site
* [Code Trigger](https://wp-webhooks.com/downloads/code-trigger/): This is a next-level extension. You can run code through a webhook everytime WordPress get's called.

= For devs =

Feel free to message us in case you want special features - We love to help!

== Installation ==

1. Activate the plugin
2. Go to Settings > WP Webhooks Pro and include your license.
3. Activate your license and you are ready to automate!
4. You will find the menu item under Settings > WP Webhooks Pro


== Changelog ==

= 5.2.1: May 31, 2022 =

**Fixed issues:**

* Some of the ajax select fields haven't been loaded correctly due to a array mismatch

= 5.2: May 31, 2022 =

**New Features:**

* Added new "KlickTipp" integration
* Added new "Favorites" integration
* Added new "WP All Export" integration
* Added new "WP All Import" integration
* Added new "WooCommerce Subscription" integration
* Added new action "Get user membership" for the WooCommerce Memberships action
* Add a simplified argument for adding/updating post meta with the "Manage WP post meta" and "Manage WP user meta" actions
* Add a simplified argument for adding/updating post meta with the "Create post" and "Update post" actions
* Add a simplified argument for adding/updating post meta with the "Create user" and "Update user" actions
* Provide an easy way to update ACF fields within Flows
* Provide a simplified way to add/update ACF (Advanced Custom Fields) fields via the create_post, update_post, create_user, update_user actions
* Certain fields are now ajax searchable for better visibility and performance
* Add a new tag (%post_permalink%) to the wpwh_link and wpwh_button triggers
* The wpcw_module_completed trigger can now be filtered against courses
* The wpcw_unit_completed trigger can now be filtered against courses and modules

**Little tweaks:**

* Added the user ID to the TutorLMS lesson completed and course completed triggers
* The choices for select field action parameters are now pre-validated to a specific structure
* The send_request webhook action got its reject_unsafe_urls argument changed to a dropdown
* On some installations, the redirect after creating a flow was not working
* Adjust common tag naming for Flows common tags
* Optimized layout and tab descriptions
* Added titles to some tabs

**Fixed issues:**

* The Contact Form 7 file transmission on the "Form submitted" trigger did not save the data due to a new CF7 data structure
* The Newsletter integration was active even without being installed
* Some of the dynamic tags for the wpwh_link and wpwh_button triggers have not been filled correctly
* With certain servers, the getallheaders() function is not available - we added a fallback logic to that
* Make sure the update_user webhook action does not require the user_login argument

**For developers:**

* Added the $integration_slug variable as an argument to WPWHPRO()->integrations->get_integrations_folder( $integration_slug );
* Added a new helper function WPWHPRO()->integrations->get_all_headers(); to get all request headers

= 5.1.1: April 27, 2022 =

**New Features:**

* Added new "wpDiscuz" integration
* Added new "WP-Polls" integration
* New action "Get taxonomy term" for the "WordPress" integration
* New action "Delete taxonomy term" for the "WordPress" integration
* New action "Get taxonomy terms" for the "WordPress" integration


**Little tweaks:**

* Optimized layout based on the latest WP Webhooks standards

**Fixed issues:**

* An issue occured with data mapping templates being not visible in some scenarios within a Flow
* The flow dropdowns caused some encoded values to not be returned in the correct format
* The internal flow actions URL have been visible in certain configurations

**For developers:**

* New function WPWHPRO()->helpers->serve_first( $data ); to always serve a single, scalar value

= 5.1.0: April 15, 2022 =

**New Features:**
* Every action has now a standardized action argument called "wpwh_call_action", which allows you to fire a custom WordPress action call
* Added new "bbPress" integration
* Added new "Tutor LMS" integration
* Added new "WP Webhooks Formatter" integration to manipulate and re-format specific data
* New action "Text default value" for the "WP Webhooks Formatter" integration
* New action "Text extract email" for the "WP Webhooks Formatter" integration
* New action "Text letter case adjustments" for the "WP Webhooks Formatter" integration
* New action "Text extract number" for the "WP Webhooks Formatter" integration
* New action "Text match expression" for the "WP Webhooks Formatter" integration
* New action "Text extract URL" for the "WP Webhooks Formatter" integration
* New action "Text count characters" for the "WP Webhooks Formatter" integration
* New action "Text count words" for the "WP Webhooks Formatter" integration
* New action "Text remove HTML" for the "WP Webhooks Formatter" integration
* New action "Text find and replace" for the "WP Webhooks Formatter" integration
* New action "Text JSON implode" for the "WP Webhooks Formatter" integration
* New action "Text trim character" for the "WP Webhooks Formatter" integration
* New action "Text truncate" for the "WP Webhooks Formatter" integration
* New action "Text URL-encode" for the "WP Webhooks Formatter" integration
* New action "Text URL-decode" for the "WP Webhooks Formatter" integration
* New action "Text explode JSON" for the "WP Webhooks Formatter" integration
* New action "Date change format" for the "WP Webhooks Formatter" integration
* New action "Date add/subtract time" for the "WP Webhooks Formatter" integration
* New action "Number format currency" for the "WP Webhooks Formatter" integration
* New action "Number math operation" for the "WP Webhooks Formatter" integration
* New action "Text JSON construct" for the "WP Webhooks Formatter" integration
* New action "Text unserialize" for the "WP Webhooks Formatter" integration
* New action "Text JSON serialize" for the "WP Webhooks Formatter" integration

**Fixed issues:**

* In some rare occasions, WordPress can return 0 while calling wp_insert_user(). This is not documented, but caused our plugin to show a success message

= 5.0: April 09, 2022 =

**READ BEFORE UPDATE:**
* This is a major version release that comes along with many features, improvements and changes to the overall funcionality.
* While we spent most of our time testing everything against backwards compatibility, we expect no issues, however, please test the update on a testing system first and ALWAYS make backups before.

**New Features:**
* Added new "Zapier" integration
* Added new "Make (Integromat)" integration
* Added new "Pabbly Connect" integration
* Added new "Integrately" integration
* Added new "Automate.io" integration
* Added new "n8n" integration
* Added new "IFTTT" integration
* Added new "Zoho Flow" integration
* New webhook trigger "HTTP request received" for the "Webhooks" integration
* New webhook action "Verify trigger signature" for the "WP Webhooks" integration
* New webhook action "Multisite assign user" for the "WordPress" integration
* Flows: You can now select a whole body or sub parts from the dynamic variable dropdown
* Action URLs are now available on an action layer, reulting in a full switch from global action URLs to action based URLs only
* Newly added webhook trigger requests now contain a header signature (key: x-wp-webhook-signature) that can be used to verify the authenticity of the trigger itself
* The IP whitelist now supports better request data before validating an IP address
* The admin menu got now its own sub menu items for faster access of information
* Added whitelabel support for the new feature tag
* Added new Features tab with quick links
* You can now search triggers and actions by the integration name
* Introduced new Tools page
* New feature to export/import WP Webhooks plugin data via the tools page. You can now transfer data between your sites
* New feature to create system reports within the Tools page
* New feature to restart the Wizard within the Tools page
* Introduced new wizard to help you with the initial setup
* Added link relations to the landing pages at wp-webhooks.com for triggers and actions
* The "Send remote HTTP POST" action of the "WordPress" integration now supports a RAW body field
* The "Send webhook request" action of the "Webhooks" integration now supports a RAW body field

**Little tweaks:**

* The WP Webhooks Pro tabs are now grouped
* Enhanced security for response data generated for unauthenticated requests. We now provide customized filters for unauthenticated requests.
* Greatly enhanced security throughout the plugin
* Creating and updating webhook actions and triggers now also causes a reload for more accurate caching data
* Enhanced the overall performance of our plugin
* Receivable trigger URLs now also validate against the whitelist feature
* Introduced enhanced permission checks on plugin page actions throughout the plugin
* Optimize various plugin texts
* Removed old and unused ajax functionality
* Streamline the plugin name to provide compatibility for the get_page_title() function
* The data mapping feature can now also map tags of array fields
* The Flow triggers now contain an info icon with further details about the specific setting
* Introduced caching for Woocommerce helpers to provide a more speedy data delivery
* The dynamic display of action select parameters has been optimizes so that old fields without labels are still displayed
* The Testing feature for actions within a flow can now also return WP_Error object notices in case something went wrong while testing
* Optimized error handling for WP_Errors during the flow action execution (With the Debug settings active)
* Directly open a flow after it was created
* Make active submenu items clickable
* Optimized error responses
* Remove deprecated variable FILTER_SANITIZE_STRING from our plugin
* The additional footer information hasn't been visible on the WP Webhooks page
* Removed deprecated single action whitelist feature
* Moved the "Request received" trigger from the "Webhooks" integration to "WP Webhooks"
* Initiate a new transient key for each updated, dynamic Home tab

**Fixed issues:**

* The WPWHPRO()->webhook->echo_response_data() function did not work properly with the content type selector on XML responses
* The Broken Link checker "blc_broken_links_detected" trigger was not returning a filter value when no links have been given
* The trigger secret key setup overwrote a value that caused the trigger data to be wrong in new flows
* Some servers caused the current URL to be created as http due to wrong server variable values
* The Flows haven't been deleted on a full plugin reset
* Using a membership plan id on the wcm_remove_user_memberships action caused the membership to be deleted
* In case a webhook action URL was deactivated, the response header was not set to application/json by default
* The get_hooks() function could cause notices of an undefined index for arrays
* In some occasions, the window had an x-scroll on the WP Webhooks page
* Based on the name it was not always clear if a data mapping template is for the request or the response
* Some icons haven't been visible within a single Flow

**For developers:**

* Deprecated our core function WPWHPRO()->helpers->get_response_body() and introduced a dual concept: WPWHPRO()->http->get_current_request() | WPWHPRO()->http->get_response( $custom_data )
* Introduced new filter to validate the deprecated function data of get_response_body(): wpwhpro/helpers/get_deprecated_response_body
* Introduced new HTTP handler class WPWHPRO()->http->function_name() that centralizes the usage of HTTP request data
* New function WPWHPRO()->http->get_response( $payload_data ) to get the validated response of a WP_Http class or custom data
* New function WPWHPRO()->http->validate_response_body( $payload_data ) to validate the data of a custom response or an WP_Http response
* New function WPWHPRO()->http->get_http_origin( $payload_data ) to get the current instance origin
* New function WPWHPRO()->http->get_current_request( $payload_data ) to fetch the current instance request
* New function WPWHPRO()->http->get_current_request_headers( $payload_data ) to fetch the current request headers
* New function WPWHPRO()->http->get_current_request_cookies( $payload_data ) to fetch the current request cookies
* New function WPWHPRO()->http->get_current_request_method( $payload_data ) to fetch the current request method
* New function WPWHPRO()->http->get_current_request_content_type( $payload_data ) to fetch the current request content type
* New function WPWHPRO()->http->get_current_request_code( $payload_data ) to fetch the current request HTTP code
* New function WPWHPRO()->http->get_current_request_query( $payload_data ) to fetch the current request query parameters
* New function WPWHPRO()->http->get_current_request_body( $payload_data ) to fetch the current and validated request body
* New function WPWHPRO()->http->send_http_request( $url, $args = array() ) to send a wrapped HTTP request
* New function WPWHPRO()->http->get_request_content_type( $data ) to get the content type of a current request
* New function WPWHPRO()->http->validate_request_body( $data ) to validate the body of the request
* New helper function WPWHPRO()->helpers->current_user_can() to fetch a filtrable version of our internal permission checks
* New Flows function WPWHPRO()->flows->get_flow_async() to get the asynchronous helper class (You can use it to enqueue your very own Flow executions)
* New function WPWHPRO()->webhook->generate_trigger_secret( $length = 32 ) to create unique secret keys for triggers
* New filter wpwhpro/admin/webhooks/generate_trigger_secret to filter a generated trigger secret key
* New filter wpwhpro/webhooks/compromised_response_response_type for responses to unauthenticated URLs
* New filter wpwhpro/webhooks/compromised_response_json_arguments for responses to unauthenticated URLs
* New filter wpwhpro/webhooks/compromised_response_json_arguments for responses to unauthenticated URLs
* Extended the hook API WPWHPRO()->webhook->create() to also accept predefined values for date_created (triggers and action) and secret (triggers)
* Deprecated ironikus_verify_license_renewal() function as the validation process is now more streamlined
* We relocated the trigger data mapping response templates to their own logic within the post_to_webhook() function itself
* The receivable trigger execution is now handled by the WPWHPRO()->integrations->execute_receivable_triggers() function
* New filter introduced: wpwhpro/integrations/execute_receivable_triggers
* New function argument for WPWHPRO()->integrations->get_actions( $integration_slug, $integration_action ); to filter down to a single action
* New filter wpwhpro/integrations/get_actions/output to filter against the ourput of the WPWHPRO()->integrations->get_actions() function
* Introduced optimized caching for integration actions
* The flow process response now returns the flow_id
* Introduced caching for the WPWHPRO()->http->get_current_request( $cached = true ) function
* The WPWHPRO()->auth->add_template( $name, $auth_type, $args = array() ) now supports templates to be created with a predefined id, time, and template data
* The WPWHPRO()->data_mapping->add_template( $name, $args = array() ) now supports templates to be created with a predefined id, time, and template data
* Flows can now be created with a predefined id using the WPWHPRO()->flows->add_flow( $data ) function
* New function WPWHPRO()->flows->delete_logs_table() to delete Flows logs
* The whitelist function WPWHPRO()->whitelist->add_item( $ip, $args = array() ) can now be used along with a predefined key for an added IP
* The WPWHPRO()->whitelist->delete_item( $key ) function can now be set to 'all' to delete all whitelist items
* Introduced new class WPWHPRO()->tools that allows you to manage current and future tools available within WP Webhooks
* Action URLs can now be created with predefined api keys, status, and groups
* Deprecated function WPWHPRO()->webhook->initialize_default_webhook() due to the change to an action based webhook structure
* The function WPWHPRO()->webhook->get_incoming_action() now prioritizes query and post parameter before any other types

= 4.3.7: March 11, 2022 =

**New Features:**

* Added new "Kadence Blocks" integration
* New webhook trigger "Form Submitted" for the "Kadence Blocks" integration
* Added new "JetEngine" integration
* New webhook trigger "Form Submitted" for the "JetEngine" integration
* New webhook trigger "Quiz failed" for the "LearnDash" integration
* New webhook trigger "Course access expired" for the "LearnDash" integration
* New webhook action "Create group" for the "LearnDash" integration
* New webhook action "Get group leaders" for the "LearnDash" integration
* New webhook action "Delete course progress" for the "LearnDash" integration
* New webhook action "Delete quiz progress" for the "LearnDash" integration
* New webhook trigger "Manage WP post meta" for the "WordPress" integration
* New webhook trigger "Manage WP user meta" for the "WordPress" integration
* New webhook action "Get active post relations" for the "JetEngine" integration
* New webhook trigger "ACF post field updated" for the "Advanced Custom Fields" integration
* New webhook trigger "ACF user field updated" for the "Advanced Custom Fields" integration
* New webhook trigger "ACF comment field updated" for the "Advanced Custom Fields" integration
* New webhook trigger "ACF term field updated" for the "Advanced Custom Fields" integration
* New webhook action "Manage ACF post meta" for the "Advanced Custom Fields" integration
* New webhook action "Manage ACF user meta" for the "Advanced Custom Fields" integration
* New webhook action "Manage ACF term meta" for the "Advanced Custom Fields" integration
* New webhook action "Manage ACF comment meta" for the "Advanced Custom Fields" integration
* New webhook trigger "Product viewed" for the "Woocommerce" integration
* New webhook trigger "Product review approved" for the "Woocommerce" integration
* New webhook trigger "Product added to cart" for the "Woocommerce" integration
* New webhook action "Add coupon emails" for the "Woocommerce" integration
* New webhook action "Add coupon user IDs" for the "Woocommerce" integration
* New webhook action "Create coupon" for the "Woocommerce" integration
* Added new "Woocommerce Memberships" integration
* New webhook trigger "Membership created" for the "Woocommerce Memberships" integration
* New webhook trigger "Membership cancelled" for the "Woocommerce Memberships" integration
* New webhook trigger "Membership expired" for the "Woocommerce Memberships" integration
* New webhook trigger "Membership paused" for the "Woocommerce Memberships" integration
* New webhook trigger "Membership pending cancellation" for the "Woocommerce Memberships" integration
* New webhook action "Add user membership" for the "Woocommerce Memberships" integration
* New webhook action "Remove user memberships" for the "Woocommerce Memberships" integration
* Certain webhook triggers can now have receivable URL's, which can be used to fire a trigger from an external request
* Trigger URLs can now send data with the content type multipart/form-data (customizable within the trigger URL settings) 

**Little tweaks:**

* WP Webhooks is now visible within the main menu by defualt (Can be customized within the settings)
* We changed the "Request received" trigger to "Action request received" within the "Webhooks" Extension
* Optimized multiple texts for various webhook endpoints
* Optimized texts for the ACF integration
* Treat wrong server port variable values more softly by using relative redirects

**Fixed issues:**

* The Flow action testing feature did not work properly due do a clearning of action URL's
* The Flow action to grant access to a Learndash course did not fire correctly due to a undefined variable
* Fix fatal error if Whitelabel was activated
* Once a webhook was updated, the triggers haven't been refreshed, causing other plugins to not have the latest trigger data from the cache

**For developers:**

* Added the copyable argument to the settings API that allows webhook actions and trigger to have copyable text fields
* Added the attributes argument to the settings API that allows extra arguments to be added to the input
* New function WPWHPRO()->webhook->built_trigger_receivable_url( $group, $trigger, $args ); to build a receivable URL for a given trigger
* Added a new argument to the single trigger API: receivable_url - This argument accepts a bool whether the trigger runs on a receivable URL callback or not
* Introduced new function WPWHPRO()->flows->get_flow_trigger_url_name( $flow_id ) to fetch the webhook trigger URL name
* The WPWHPRO()->helpers->get_current_url( $with_args, $relative = false ); got a new $relative argument that returns only the path along with query parameters

= 4.3.6: February 28, 2022 =

**New Features:**

* Added new "Webhooks" integration
* New webhook trigger "Request received" for the "Webhooks" integration
* New webhook action "Send webhook request" for the "Webhooks" integration
* New webhook action "Resolve target URL" for the "Webhooks" integration
* Added new "Divi" integration
* New webhook trigger "Form submitted" for the "Divi" integration
* Added new "Events Manager" integration
* New webhook trigger "Booking Approved" for the "Events Manager" integration
* New webhook trigger "Booking cancelled" for the "Events Manager" integration
* New webhook trigger "Booking rejected" for the "Events Manager" integration
* New webhook trigger "Booking pending" for the "Events Manager" integration
* New webhook action "Approve bookings" for the "Events Manager" integration
* New webhook action "Cancel bookings" for the "Events Manager" integration
* New webhook action "Reject bookings" for the "Events Manager" integration
* Added new "Restrict Content Pro" integration
* New webhook trigger "Paid membership activated" for the "Restrict Content Pro" integration
* New webhook trigger "Free membership activated" for the "Restrict Content Pro" integration
* New webhook trigger "Paid membership Expired" for the "Restrict Content Pro" integration
* New webhook trigger "Free membership Expired" for the "Restrict Content Pro" integration
* New webhook trigger "Paid membership cancelled" for the "Restrict Content Pro" integration
* New webhook trigger "Free membership cancelled" for the "Restrict Content Pro" integration
* New webhook action "Create membership" for the "Restrict Content Pro" integration
* New webhook action "Cancel membership" for the "Restrict Content Pro" integration
* New webhook action "Renew membership" for the "Restrict Content Pro" integration
* New webhook action "Expire membership" for the "Restrict Content Pro" integration
* New webhook action "Enable membership" for the "Restrict Content Pro" integration
* New webhook action "Disable membership" for the "Restrict Content Pro" integration
* The "Create contact" action of the "FluentCRM" integration got new arguments: name_prefix, full_name, address_line_1, address_line_2, city, state, postal_code, country, ip, phone, source, date_of_birth, custom_values
* The "Update contact" action of the "FluentCRM" integration got new arguments: name_prefix, full_name, address_line_1, address_line_2, city, state, postal_code, country, ip, phone, source, date_of_birth, custom_values

**Little tweaks:**

* The argument details within the Receive Data tab now displays all default values that can be used for arguments with predefined content.
* Added the full contact object to the Groundhogg triggers
* Corrected WP Courseware and WP User Manager Logos
* Removed unused arguments from the WPWHPRO()->integrations->execute_actions() call

**Fixed issues:**

* The Flow feature did not properly execute an attached Data Mapping template
* Using the "Test action" within a flow caused undeledet, temporary webhook action URLs

**For developers:**

* The helper function WPWHPRO()->helpers->get_response_body( $data, $cached = true ) supports a new argument ($cached) that will, by default, return a cached version of the current default data. 
* New helper function WPWHPRO()->helpers->force_array( $data ) that always turns a variable into an array, based on its given data
* New helper function WPWHPRO()->helpers->get_formatted_date( $date, $date_format = 'Y-m-d H:i:s' ) that automatically formats a given date to a unified format.

= 4.3.5: February 16, 2022 =

**New Features:**

* Added new "WS Form" integration
* New webhook trigger "Form submitted" for the "WS Form" integration
* Added new "WP User Manager" integration
* New webhook trigger "User registered" for the "WP User Manager" integration
* New webhook trigger "User logged in" for the "WP User Manager" integration
* New webhook trigger "User updated" for the "WP User Manager" integration
* New webhook trigger "User password recovered" for the "WP User Manager" integration
* New webhook trigger "User password changed" for the "WP User Manager" integration
* New webhook trigger "Cover photo updated" for the "WP User Manager" integration
* New webhook trigger "Cover photo removed" for the "WP User Manager" integration
* New webhook trigger "Profile photo updated" for the "WP User Manager" integration
* Added new "Groundhogg" integration
* New webhook trigger "Contact tag added" for the "Groundhogg" integration
* New webhook trigger "Contact tag removed" for the "Groundhogg" integration
* New webhook action "Add user tags" for the "Groundhogg" integration
* New webhook action "Remove user tags" for the "Groundhogg" integration
* Added new "WP Courseware" integration
* New webhook trigger "User enrolled" for the "WP Courseware" integration
* New webhook trigger "Course completed" for the "WP Courseware" integration
* New webhook trigger "Module completed" for the "WP Courseware" integration
* New webhook trigger "Unit completed" for the "WP Courseware" integration
* New webhook action "Course enroll user" for the "WP Courseware" integration
* New webhook action "Course unenroll user" for the "WP Courseware" integration
* Added new FluentCRM actions
* New webhook action "Get contact" for the "FluentCRM" integration
* New webhook action "Create contact" for the "FluentCRM" integration
* New webhook action "Update contact" for the "FluentCRM" integration

**Little tweaks:**

* On the data mapping tab, we showed duplicates within the connected templates column if a template was assigned multiple times
* Make icon height dynamic to not stretch certain logos within the Send Data and Receive Data views

**Fixed issues:**

* PHP 8+ threw an error on pages that used the wpwh_shortcode, wpwh_link, or wpwh_button endpoints
* In case the wpwhpro/webhooks/validate_webhook_action was set to false, the function threw a notice and used the echo_action_data instead of echo_response_data
* The give_create_donor_note action caused a PHP notice due to an undefined argument

**For developers:**

* Flow conditionals are now fetched from the backend (They can now be filtered using the wpwhpro/admin/settings/flow_condition_labels filter)
* New filter wpwhpro/flows/validate_action_conditions to manipulate the Flow conditional validation

= 4.3.4: January 24, 2022 =

**New Features:**

* Flow conditionals - fire actions only if certain conditions are met
* Added new "Fluent Support" integration
* Added new "WP Fusion" integration
* Added new "GiveWP" integration
* New webhook trigger "Ticket closed" for the "Fluent Support" integration
* New webhook trigger "Ticket response added" for the "Fluent Support" integration
* New webhook trigger "Ticket note added" for the "Fluent Support" integration
* New webhook trigger "Ticket reopened" for the "Fluent Support" integration
* New webhook trigger "Ticket created" for the "Fluent Support" integration
* New webhook trigger "Tag added" for the "WP Fusion" integration
* New webhook trigger "Tag removed" for the "WP Fusion" integration
* New webhook trigger "Donation completed" for the "GiveWP" integration
* New webhook trigger "Donation refunded" for the "GiveWP" integration
* New webhook trigger "Donation failed" for the "GiveWP" integration
* New webhook trigger "Donation cancelled" for the "GiveWP" integration
* New webhook trigger "Donation abandoned" for the "GiveWP" integration
* New webhook trigger "Donation preapproved" for the "GiveWP" integration
* New webhook trigger "Donation revoked" for the "GiveWP" integration
* New webhook trigger "Donation pending" for the "GiveWP" integration
* New webhook trigger "Donation processed" for the "GiveWP" integration
* New webhook action "Add tags" for the "WP Fusion" integration
* New webhook action "Remove tags" for the "WP Fusion" integration
* New webhook action "Create donor" for the "GiveWP" integration
* New webhook action "Create donor note" for the "GiveWP" integration
* You can now limit flow executions to one per instance (Via a setting within the trigger settings of the Flow)
* You can now use the get_post action to fetch an attachment (media) id from an URL

**Little tweaks:**

* Flow triggers now accept data for logs as soon as they have been added
* Optimize integration items on single endpoint view for "Send Data" and "Receive Data"
* Optimized webhook descriptions and other text
* Tested plugin with version 5.9

**Fixed issues:**

* The FluentCRM and update_user/delete_user actions could not be used properly within the Flows feature due to a double-requirement
* The FluentCRM triggers contained a wrong key within the demo data
* Shop link on plugin list page was broken
* Linebreaks and tabs within the flow fields caused broken encoding, which resulted in unusable data
* The wpwh_shortcode webhook trigger did not render the shortcode correctly

**For developers:**

* Added new filter wpwhpro/webhooks/validate_webhook_action to prevent webhook actions from firing
* New function WPWHPRO()->flows->validate_flow_values( $config ); that validates the flow data to its real format

= 4.3.3: January 08, 2022 =

**New Features:**

* Setting for automatic cleanup of logs every 30 days
* Added post/user meta to every Woocommerce webhook trigger
* Added posttaxonomies to every Woocommerce webhook trigger that supports posts
* Allow the Woocommerce integration "Order created" trigger to be filtered against the order status
* Allow the Woocommerce integration "Order updated" trigger to be filtered against the order status
* Allow the Woocommerce integration "Order restored" trigger to be filtered against the order status

**Little tweaks:**

* Optimize webhook names and newly added sentences for triggers and actions
* Updated plugin updater to 1.9.1
* Make Woocommerce triggers support the post-delay feature to avoid outdated WC data

**For developers:**

* New scheduled event "wpwh_maintenance" that runs once daily for various checks
* New function WPWHPRO()->sql->prepare( $sql, $values ) as an equivalent to wpdb's prepare

= 4.3.2: December 29, 2021 =

**New Features:**

* New integration: Learndash
* New integration: Amelia
* New integration: Broken Link Checker
* New trigger integration for Woocommerce
* New trigger "Course completed" for the integration "Learndash"
* New trigger "Lesson completed" for the integration "Learndash"
* New trigger "Quiz completed" for the integration "Learndash"
* New trigger "Topic completed" for the integration "Learndash"
* New trigger "Assignment uploaded" for the integration "Learndash"
* New trigger "Course access granted" for the integration "Learndash"
* New trigger "Course access removed" for the integration "Learndash"
* New trigger "Group access granted" for the integration "Learndash"
* New trigger "Group access removed" for the integration "Learndash"
* New action "Grant course access" for the integration "Learndash"
* New action "Grant group access" for the integration "Learndash"
* New action "Adjust group leader" for the integration "Learndash"
* New action "Complete courses" for the integration "Learndash"
* New action "Complete lessons" for the integration "Learndash"
* New action "Complete topics" for the integration "Learndash"
* New action "Mark lesson incomplete" for the integration "Learndash"
* New action "Mark topics incomplete" for the integration "Learndash"
* New action "Remove group access" for the integration "Learndash"
* New action "Remove course access" for the integration "Learndash"
* New trigger "Booking added" for the integration "Amelia"
* New trigger "Booking status updated" for the integration "Amelia"
* New trigger "Appointment rescheduled" for the integration "Amelia"
* New trigger "Broken links detected" for the integration "Broken Link Checker"
* New trigger "Coupon created" for the integration "Woocommerce"
* New trigger "Coupon updated" for the integration "Woocommerce"
* New trigger "Coupon deleted" for the integration "Woocommerce"
* New trigger "Coupon restored" for the integration "Woocommerce"
* New trigger "Customer created" for the integration "Woocommerce"
* New trigger "Customer updated" for the integration "Woocommerce"
* New trigger "Customer deleted" for the integration "Woocommerce"
* New trigger "Order created" for the integration "Woocommerce"
* New trigger "Order updated" for the integration "Woocommerce"
* New trigger "Order deleted" for the integration "Woocommerce"
* New trigger "Order restored" for the integration "Woocommerce"
* New trigger "Product created" for the integration "Woocommerce"
* New trigger "Product updated" for the integration "Woocommerce"
* New trigger "Product deleted" for the integration "Woocommerce"
* New trigger "Product restored" for the integration "Woocommerce"
* Data Mapping can be set for trigger request cookies
* Prevent duplicate trigger calls within a single WordPress instance (new webhook URL setting)

**Little tweaks:**

* Optimize EDD descriptions
* Optimize PHPDocs
* Optimize the single endpoint layouts for triggers and actions
* Included the same Woocommerce webhook response, just with better trigger validation
* Optimize value sanitization for certain image URLs

**Fixed issues:**

* The settings dropdowns of WP Webhooks Pro's Data Mapping feature displayed an object instead of a string
* The code tags and pre tags haven't been displayed properly with the latest version of WordPress
* Default webhook URL settings of newly initialized triggers and actions haven't displayed the correct default value
* The flow feature threw an undefined index log notification if for the given webhook no settings had been saved

= 4.3.1: December 13, 2021 =

**New Features:**
* New action "Custom button clicked" for the integration "WP Webhooks"
* New action "Custom link clicked" for the integration "WP Webhooks"
* New integration: FluentCRM
* New action "Add contact to list" for the integration "FluentCRM"
* New action "Add contact to tag" for the integration "FluentCRM"
* New action "Remove lists from contact" for the integration "FluentCRM"
* New action "Remove tags from contact" for the integration "FluentCRM"
* New trigger "Contact added to list" for the integration "FluentCRM"
* New trigger "Contact added to tag" for the integration "FluentCRM"
* New trigger "Contact deleted to tag" for the integration "FluentCRM"
* New trigger "Contact removed from list" for the integration "FluentCRM"
* New trigger "Contact removed from tag" for the integration "FluentCRM"
* New trigger "Contact status updated" for the integration "FluentCRM"
* New and simplified layout for multi select fields within Flows
* New layout for Flows dropdowns
* New function WPWHPRO()->helpers->generate_signature( $data, $secret ) to generate signatures

**Little tweaks:**

* Optimize "Send data" settings to show individual settings first, followed by default and required settings
* Remove unnecessary PHPDocs
* Optimize small text bugs

**Fixed issues:**

* The get_curret_url() helper returned a wrong host part in some cases, causing creating triggers and actions to not work properly
* The Flows feature could cause a undefined variable notice if a deleted data mapping template was selected
* Prevent undefined variable notice on the "Send Data" tab
* The Paid Memberships Pro "Order Created" and "Order Updated" triggers returned different values depending on the payment method
* Fixed possible undefined variable notice for the Paid Memberships Pro triggers

= 4.3.0: October 14, 2021 =

**New Features:**

* Create fully automated workflows using our new Flow feature
* Added new integration for "WP Webhooks" - This will add triggers for our plugin itself
* New webhook action endpoint to fire a webhook trigger (fire_trigger)
* New webhook action endpoint to trigger a custom HTTP request (send_remote_post)
* New webhook trigger endpoint to send data once an action has been fired (action_fired)

**Little tweaks:**

* Automatically validate action arguments as text fields
* Correct PHPDocs namings 

**Fixed issues:**

* If no content type was given within a request, the custom content type check for get_response_body() failed
* Prevent PHP notice in case the log item did not contain a body key
* Make sure all characters are properly JSON encoded before applying them for dynamic mapping tags
* Properly sanitize log SQLs

**For developers:**

* New function WPWHPRO()->integrations->get_integrations( $integration_slug ) to retrieve a list of all available integrations
* The WPWHPRO()->integrations->get_triggers( $trigger_slug ) can now be used to return a a single trigger only
* The WPWHPRO()->integrations->get_actions( $action_slug ) can now be used to return a a single action only
* Allow custom settings to be saved on creation of a new webhook trigger
* Added WP Background Processing library to WP Webhooks: https://github.com/deliciousbrains/wp-background-processing
* Added new function WPWHPRO()->webhook->reload_webhooks() to reload preloaded webhook settings
* New filter wpwhpro/helpers/get_current_url to customize the dynamically available, current URL
* New action wpwhpro/logs/add_log that fires after a log was added

= 4.2.3: September 02, 2021 =

**New Features:**

* Added the new "AffiliateWP" integration (https://wp-webhooks.com/integrations/affilaitewp/)
* New action endpoint "Add referral" for "AffiliateWP"
* New action endpoint "Add visit" for "AffiliateWP"
* New trigger endpoint "Affiliate status changed" for "AffiliateWP"
* New trigger endpoint "Referral status changed" for "AffiliateWP"
* New trigger endpoint "New Affiliate" for "AffiliateWP"
* New trigger endpoint "New Payout" for "AffiliateWP"
* New trigger endpoint "New Referral" for "AffiliateWP"
* Add "Data Mapping" feature to "Format Value": Strip Slashes
* Add "Data Mapping" feature to "Format Value": Add Slashes
* You can now apply data mapping templates to the headers of triggers
* New action "Update a taxonomy term" to update default and custom taxonomy terms

**Little tweaks:**

* Optimized log data for triggers (better readability and header data is separate)
* Added post permalinks to the response data of create_user, update_user, delete_user
* Added name argument to the create_term webhook action
* Add slashes to dynamic data mapping tags to make them compatible with JSON constructs
* Encode special characters within the Send Data and Receive Data code examples
* Reworked example codes for various triggers and actions
* Apply a clickable link to the active tab
* Tewak: On a full reset of the plugin, we now also delete every related post meta that was set with the "Trigger on initial post status change" of the Post Created trigger (wpwhpro_create_post_temp_status%)
* On multisites, WordPress returns the site id with special characters, which broke the JSON for the user create/update triggers
* Optimize formatting of the do_action parameter descriptions and some other misformatted breaks

**Fixed issues:**

* The copy_folder() helper function for our WordPress integration did not return correct values and did not create sub files correctly
* Undefined $this->page_name variable within the auth templates
* If no content type was given within a request, the custom content type check for get_response_body() failed
* In case a filter was given as a callback for an action in combination with the post delay feature, the argument wasn't returned

**For developers:**

* New filter wpwhpro/integrations/integration/is_active to filter whether an integration should be considered active or not
* New filter wpwhpro/integrations/dependency/is_active to filter whether a dependency for an integration should be considered active or not
* Create a new executable function for whitelabel related WordPress hooks: WPWHPRO()->whitelabel->execute();
* Create a new handler class and a new executable function for authentication related WordPress hooks: WPWHPRO()->extensions->execute();
* Create a new executable function for authentication related WordPress hooks: WPWHPRO()->auth->execute();
* Create a new executable function for data mapping related WordPress hooks: WPWHPRO()->data_mapping->execute();
* Add new executable function for outsourcing feature related WordPress hooks to their class files for a more controlled handling
* Add the definition of the private variable $pre_action_values for the following triggers: deleted_user, post_delete, post_update
* The validate_path() function within our WordPress helper class now validates against absolute paths
* Add new argument $additional_args to the following function: WPWHPRO()->webhook->built_url( $webhook, $api_key, $additional_args = array() );
* The WPWHPRO()->logs->add_log() function now returns the id of the newly created log
* Added asynchronous job feature using WPWHPRO()->async->new_process()
* The WPWHPRO()->sql->run() function now allows you to return a given id for a newly created entry
* New SQL function to check for a column: WPWHPRO()->sql->column_exists( $table_name, $column_name )

= 4.2.2: June 26, 2021 =
* Feature: Add "Paid Memberships Pro" integration (https://wp-webhooks.com/integrations/paid-memberships-pro/)
* Feature: Add "Advanced Custom Fields" integration (https://wp-webhooks.com/integrations/advanced-custom-fields/)
* Feature: Add "Newsletter" integration (https://wp-webhooks.com/integrations/newsletter/)
* Feature: Add "HappyForms" integration (https://wp-webhooks.com/integrations/happyforms/)
* Feature: Add "Fluent Forms" integration (https://wp-webhooks.com/integrations/fluent-forms/)
* Feature: Add "Forminator" integration (https://wp-webhooks.com/integrations/forminator/)
* Feature: Add "Formidable Forms" integration (https://wp-webhooks.com/integrations/formidable-forms/)
* Feature: New trigger "Membership canceled" for "Paid Memberships Pro"
* Feature: New trigger "Membership expired" for "Paid Memberships Pro"
* Feature: New trigger "Order created" for "Paid Memberships Pro"
* Feature: New trigger "Order updated" for "Paid Memberships Pro"
* Feature: New trigger "Order deleted" for "Paid Memberships Pro"
* Feature: New trigger "Form submitted" for "Newsletter"
* Feature: New trigger "Form submitted" for "HappyForms"
* Feature: New trigger "Form submitted" for "Fluent Forms"
* Feature: New trigger "Form submitted" for "Forminator"
* Feature: New trigger "Form submitted" for "Formidable Forms"
* Feature: New action "Add user to membership" for "Paid Memberships Pro"
* Feature: New action "Remove user from membership" for "Paid Memberships Pro"
* Feature: New action "Get user membership" for "Paid Memberships Pro"
* Feature: New action "Update options page" for "Advanced Custom Fields"
* Tweak: New optimized and reworked webhook descriptions
* Tweak: Assign attachment metadata to the create_path_attachment and create_url_attachment action responses
* Fix: Using the manage_term_meta action in combination with a numeric slug caused the slug to be the id instead
* Fix: The Comment Update webhook action caused a redirect when clicking on the description tab
* Fix: Elementor notice was thrown in case the widget type was not defined iwhtin a specific object
* Dev: The function WPWHPRO()->acf->manage_acf_meta( 0, '', option ) now supports custom option pages
* Dev: New function WPWHPRO()->webhook->get_endpoint_description( $type = 'trigger', $data = array() ) to dynamically load descriptions

= 4.2.1: June 23, 2021 =
* Feature: Add "Elementor" integration (https://wp-webhooks.com/integrations/elementor/)
* Feature: Add "Ninja Forms" integration (https://wp-webhooks.com/integrations/ninja-forms/)
* Feature: Add "WP Simple Pay" integration (https://wp-webhooks.com/integrations/wp-simple-pay/)
* Tweak: Display integration icon next to trigger and action title
* Tweak: Add the plugin version within the footer of WP Webhooks
* Tweak: Remove unnecessary headlines within the "Send Data" tab
* Tweak: Apply single array filter for incoming values only to strings
* Fix: Remove duplicated ids notices
* Fix: Prevent EDD Purchase Receipt email from being sent even though "send_receipt" was set to "no"
* Fix: The triggers edd_new_customer and edd_update_customer did not work properly due to a wrong check for a non-existent function
* Dev: New helper function WPWHPRO()->helpers->get_nonce_field( $nonce ) to fetch a nonce with a random id
* Dev: Add two new sql query tags: {posts} and {postmeta}

= 4.2.0: June 09, 2021 =
* Feature: New webhook action create_term to create taxonomy terms
* Feature: New webhook trigger wordpress_hook that can fire on any kind of WordPress hook
* Feature: Add "Gravity Form" integration
* Feature: Add "WPForms" integration
* Feature: Add "WP Reset" integration to core
* Feature: Add "Manage Plugins" integration to core
* Feature: Add "Email integration" integration to core
* Feature: Add "Woocommerce" integration to core
* Feature: Add "Comments" integration to core
* Feature: Add "Manage Taxonomy Terms" integration to core
* Feature: Add "Remote File Control" integration to core
* Feature: Add "Manage Media Files" integration to core
* Feature: Add "Easy Ditigal Downloads" integration to core
* Feature: Switch triggers and actions to an integration-based setup
* Feature: Allow integration-based grouping for actions and triggers
* Feature: Add beautified namings to webhook actions
* Feature: Setting to toggle WP Webhooks between a sub menu item or a menu item in the main admin menu
* Tweak: Show the status of a webhook trigger or action next to the table item (green/red dot)
* Tweak: Display JSON constructs for webhook triggers instead of array notations
* Tweak: Add support for post trigger delay towpwhh_shortcode trigger
* Tweak: Allow pretty-printing for Receive Data responses
* Tweak: Sort triggers and actions by name
* Tweak: Optimize performance for logging feature (duplicated "if exists" statement)
* Tweak: Optimize performance for data mappig feature (duplicated "if exists" statement)
* Tweak: Optimize performance for authentication feature (duplicated "if exists" statement)
* Tweak: Update our core updater class to the newest standard
* Tweak: Optimize performance of settings tab
* Tweak: Optimize descriptions for the manage_meta_data argument
* Tweak: Optimize performance of log feature
* Tweak: Optimize performance of authentication feature
* Tweak: Optimize performance of data mapping feature
* Fix: If a custom URL port is used, the get_current_url() helper function returned the URL without the port
* Fix: Log page caused Fatal error if less than 20 logs have been present
* Dev: Support centralized support for post-delayed triggers
* Dev: Make callback argument for triggers optional to support the integration based notation
* Dev: Make post delay filter wpwhpro/post_delay/post_delay_triggers more accessible - we added the webhook_group name and the webhook object
* Dev: New, public function WPWHPRO()->data_mapping->maybe_setup_data_mapping_table()
* Dev: New, public function WPWHPRO()->auth->maybe_setup_authentication_table()
* Dev: New, public function WPWHPRO()->logs->maybe_setup_logs_table()
* Dev: New helper function WPWHPRO()->helpers->get_folders( $path ) to retrieve a list of folders from a given path
* Dev: New helper function WPWHPRO()->helpers->get_files( $path, $ignore = array() ) to retrieve a list of files from a given path
* Dev: New action wpwhpro/integrations/callbacks_registered which fires after all integration callbacks have been registered

= 4.1.2: May 01, 2021 =
* Tweak: Optimize performance for logs page due to a duplicated query
* Tweak: Update EDD core updater from v1.6.17 to v1.6.19

= 4.1.1: May 01, 2021 =
* Feature: Allow the "Send Data on Post Update" to only trigger on specific post statuses
* Tweak: Correct naming of authentication title and description
* Fix: When no plugin updates have been available, the extension page threw a fatal error
* Fix: In case extensions have been installed, there was no default return available for actions without a valid parameter

= 4.1.0: April 15, 2021 =
* Feature: New webhook triggger wpwh_shortcode which allows you to fire a webhook using a shortcode
* Feature: The logs now track the response data of webhook actions as well
* Feature: Add pagination to logs
* Feature: Allow a custom bearer token scheme for auth templates
* Tweak: Better handling for webhook action responses (centralized through a new filter)
* Tweak: Correct response data for successful delete_post webhook
* Tweak: Add internal slug to triggers for better usability
* Fix: The JSON example within the bulk_webhooks action was not a alid JSON
* Fix: Log count function was not always returning the proper values on cached function calls
* Fix: $response_data for trigger overwrote response if multiple webhooks have been used within webhook trigger groups
* Dev: Switch webhooks to separate classes for a better and more constrolled handling

= 4.0.1: April 04, 2021 =
* Feature: "Send Data On Post Update" can now be triggered only on specific post statuses
* Feature: Full ACF integration for the "Send Data On Register" trigger
* Feature: Full ACF integration for the "Send Data On Login" trigger
* Feature: Full ACF integration for the "Send Data On User Update" trigger
* Feature: Full ACF integration for the "Send Data On User Deletion" trigger
* Feature: Full ACF integration for the "Send Data On New Post" trigger
* Feature: Full ACF integration for the "Send Data On Post Update" trigger
* Feature: Full ACF integration for the "Send Data On Post Deletion" trigger
* Feature: Full ACF integration for the "Send Data On Post Trash" trigger
* Feature: Full ACF integration for the "get_users" action
* Feature: Full ACF integration for the "get_user" action
* Feature: Full ACF integration for the "get_posts" action
* Feature: Full ACF integration for the "get_post" action
* Feature: Add filter for webhook URL on webhook triggers
* Tweak: Optimize texts
* Fix: Data Mapping: If you used a JSON within the mapping field, it did not show up on loading the settings
* Dev: Trigger settings required the default_value to not be prefilled with a 1, now not anymore
* Dev: New filter for the data mapping default return value
* Dev: New filter for the data mapping return value

= 4.0.0: March 12, 2021 =
* Feature: Fully reworked and optimized design
* Feature: Allow updating a user via the user_login argument
* Feature: Secure webhook actions with Basic Auth and API Keys
* Feature: New argument load_taxonomies for the get_posts action to also return the assigned taxonomies of your choice
* Feature: For the whitelabel feature, you can now also hide the Logs, Authentication, IP Whitelist, as well as the Data Mapping
* Feature: New webhook action setting to apply data mapping template to action response
* Feature: Add support for multipart/form-data
* Feature: Add action setting to whitelist only specific Request methods for incoming data
* Feature: Integrate details to each action argument (Popup)
* Feature: Add IP Address wildcard whitelisting
* Feature: Introduce log versioning
* Feature: Add new webhook property called "webhook_url_name" to webhook trigger and action calls and logs
* Feature: Add new key to trigger response data called body_validated, which shows the original body filtered and validated for further use
* Feature: New trigger setting to apply data mapping template to the new trigger response for the body_validated key
* Tweak: Data Mapping is integrated into the core and always active
* Tweak: Authentication is integrated into the core and always active
* Tweak: Optimize menu setup and group related menu items
* Tweak: Integrate Authentication into core, always active, and remove activate/deactivate possibility
* Tweak: Integrate Data Mapping into core, always active, and remove activate/deactivate possibility
* Tweak: On plugin reset, also deactivate the domain on the license
* Tweak: Add New, dynamic news feed
* Tweak: Optimize licensing functionality
* Tweak: Optimize whitelabel settings descriptions
* Tweak: All actions and triggers are active by default
* Tweak: Make trigger response connectable by using the trigger name as an array key
* Tweak: Optimize performance for previously available active_webhooks feature
* Tweak: Optimized deletion of logs (New capabilities are in place to decide who is allowed to delete logs)
* Tweak: Optimize webhook action return values
* Tweak: Optimize webhok action return value description
* Fix: Data Mapping templates using the blacklist setting for incoming action templates caused to not work properly
* Fix: Authentication for API tokens did not work properly if the API token was added only to the header
* Fix: Add manage_meta_data argument description to the update_post webhook action
* Fix: Issue with same id namings within the "Receive Data" tab
* Fix: Optimize backwards compatibility for post status changes to also fire on multiple webhooks
* Fix: Integrated logs into core - they're always active now
* Fix: Notice with undefined identkey variable
* Fix: Not all data of WP Webhooks Pro has been reset on the reset feature
* Fix: Make WP Webhooks Pro name dynamic for whitelabel feature on the Whitelist tab
* Dev: New helper function get_current_request_method()
* Dev: New function do delete single logs: WPWHPRO()->logs->delete_log( $log_id = 'all' );
* Dev: New function WPWHPRO()->webhook->get_current__webhook_action() to fetch the webhook action in case the current instance runs on an incoming webhook call
* Dev: New filter wpwhpro/webhooks/get_current_webhook_action
* Dev: Deprecated WPWHPRO()->settings->get_active_webhooks() and WPWHPRO()->settings->setup_active_webhooks()
* Dev: Add new webhook property called $webhook_url_name to default webhook trigger data
* Dev: Add new function WPWHPRO()->settings->get_whitelabel_settings_option_key() to fetch the whitelabel settings option key
* Dev: Extend wpwhpro/helpers/validate_response_body filter by another variable called $custom_data
* Dev: New helper function to fetch server header: WPWHPRO()->helpers->validate_server_header( 'key' )
* Dev: Place the authentication response on the right position (after the webhook authentication)

= 3.1.1: November 12, 2020 =
* Fix: Data Mapping with old templates contained a compatibility issue causing the mapped value fields to be empty
* Fix: Missing description for user_delete webhook trigger $user object

= 3.1.0: November 06, 2020 =
* Feature: Full ACF support for create_post, update_post, create_user, update_user webhook actions (New argument manage_acf_data)
* Feature: Completely reworked meta functionality for create_post, update_post, create_user, update_user webhook actions (New argument manage_meta_data)
* Feature: Add post meta data to get_posts webbhook action as a separate argument (load_meta)
* Feature: Added the possibility to serialize and JSON encode a value within the data mapping template (Added within the "Format Value" setting)
* Feature: Create multi-level data payloads within your data mapping template for webhook triggers (Send Data) - (Set a JSON as the value and select JSON Decode within the Format value setting)
* Feature: New webhook action (bulk_webhooks) - It allows you to send multiple webhook requests within a single webhook call. It supports internal and external URLs and is fully compatible will all features of WP Webhooks
* Tweak: correct webhook response grammar mistakes
* Tweak: Optimize Data Mapping Key Settings Descriptions
* Tweak: Optimize the data mapping description
* Tweak: Optimize webhook action responses for create_user, update_user, create_post, update_post
* Tweak: Deprecated argument meta_input on create_post and update_post webhook actions (It's backwards compatible)
* Tweak: Deprecated argument user_meta on create_user and update_user webhook actions (It's backwards compatible)
* Tweak: Optimize the functionality on the create_post action setting for firing on the initial post status change
* Tweak: Correct wrong texts all over the plugin
* Tweak: As of the WP 5.5 release, the $user object is sent over with the deleted_user hook: https://developer.wordpress.org/reference/hooks/deleted_user/ - We made the new variable compatible
* Fix: A notice appeared due to a string validation on an object for the deprecated user_meta argument
* Fix: The create_post webhook action in combination with firing on the post status change, caused the post not to be triggered on a post update
* Fix: Correct wrongly assigned nickname variable check on the create_user and update_user webhook actions
* Fix: Correct notice with indefined variable within the webhook action logic
* Fix: PHP warning: SimpleXMLElement::addChild() expects parameter 2 to be string, object given - It occured within the convert_to_xml() function and is now fixed
* Fix: In some cases, the data value, within data mapping templates, was not decoded if the value format was chosen after the JSON Decode setting was activated and saved 
* Fix: On some JSON decode and unserialize excutes within the data mapping template for the "Format value" settings, the data was not unserialized/decoded if there was no mapping keys defined after - it also threw a PHP notice 
* Dev: New helper class to check if a given plugin is installed ( is_plugin_installed() )
* Dev: New helper class to check if a given plugin is active ( is_plugin_active() )

= 3.0.7: September 22, 2020 =
* Tweak: Add import_id to the create_post webhook action
* Tweak: Added permalink to the following triggers: post_create, post_update, post_delete and post_trash
* Fix: Correct naming for data mapping setting
* Dev: Introduced new handler function echo_action_data() to centralize the output of a webhook action
* Dev: Extend the wpwhpro/webhooks/response_response_type filter by a new argument: $args (https://wp-webhooks.com/docs/knowledge-base/filter-response-type/)
* Dev: The echo_response_data() function now returns the validated data as well

= 3.0.6: July 01, 2020 =
* Feature: Fully reworked data mapping engine with tons of new features (It's backwards compatible, of course): https://wp-webhooks.com/docs/knowledge-base/how-to-use-data-mapping/
* Feature: Data Mapping template can now control which keys are send within the payload. This way you can control the size of the outgoing data
* Feature: Data Mapping dynamic tags can now be used for both keys and values
* Feature: New Data Mapping setting to unserialize a serialized array or object
* Feature: Set a Fallback value for Data Mapping Keys and values
* Feature: Data Mapping keys and values can not be formatted to string, integer, bool, float and null
* Feature: Whitelabel feature for WP Webhooks Pro: (https://wp-webhooks.com/docs/knowledge-base/whitelabel-wp-webhooks-pro/)
* Tweak: Optimized layout
* Tweak: Correct comments
* Tweak: Strip slashes from data mapping templates
* Tweak: Optimized licensing error messages
* Tweak: Correct variable naming for map_data_to_template() of $webhook_type 
* Tweak: Revalidate JSON within the JSON string to encode certain characters
* Fix: Attempt to assign property of non-object - is now fixed for the data mapping feature
* Fix: Issue with wrong naming for the "Send Data" setup on the demo user payload for the user_meta key
* Dev: get_response_body() helper now supports manual data entry as a payload and content type
* Dev: New filter: wpwhpro/settings/data_mapping_template_settings (https://wp-webhooks.com/docs/knowledge-base/filter-data-mapping-template-settings/)
* Dev: New filter: wpwhpro/settings/data_mapping_key_settings (https://wp-webhooks.com/docs/knowledge-base/filter-data-mapping-key-settings/)
* Dev: WPWHPRO()->license->update() accepts now custom attributes as well

= 3.0.5: July 01, 2020 =
* Feature: Add user meta to the get_user webhook action
* Feature: Added trigger response data to the log feature and some further data optimizations
* Feature: The custom_action trigger got a rework and now uses apply_filters() instead of do_action - this allows you to also catch the response (the logic is backwards compatible, so you can still continue to use your existing logic)
* Feature: Allow modification of the http arguments within the custom_action webhook action (separate variable for the apply_filters() call)
* Feature: Allow the percentage character within webhook trigger URLs
* Feature: Add separate object and array array serialization to the user/post meta data (Applicable for the following webhook actions: create_user, update_user, create_post, update_post)
* Feature: Add user meta data to the get_users webhook action response
* Feature: New trigger setting to allow unsafe looking URLs (By default, URL's like asiufgasvflhsf.siugsf.com are prevented from being sent for your security)
* Feature: New trigger setting to allow unverified SSL connections (In case you have a self-signed URL, you can prevent the default SSL check for each webhook)
* Feature: New data mapping action for decoding a JSON string within the webhook response to make each entry accessible
* Tweak: Optimized log layout
* Tweak: Optimized data structure for the log feature
* Tweak: Optimize PHPDocs
* Fix: The same webhook names for different triggers broke the settings popup
* Fix: the delete_post webhook action contained a wrongly formatted error message
* Fix: Prevalidate json within the is_json() helper function to prevent notices within the debug.log file
* Dev: Added the trigger group slug to the wpwhpro/admin/settings/webhook/page_capability filter (currently the trigger was only sent by its name which is not unique without the trigger group)
* Dev: Added new handler function for generating the API keys
* Dev: New filter to manipulate the API key: wpwhpro/admin/webhooks/generate_api_key (https://wp-webhooks.com/docs/knowledge-base/filter-generated-api-key-for-action-urls/)

= 3.0.4: May 11, 2020 =
* Feature: New webhook trigger that sends data on trashing a post (custom post types supported)
* Feature: The tax_input argument for create_post/update_post actions now supports JSON formatted strings
* Feature: Added taxnomies as well to post_delete trigger
* Feature: Added full post thumbnail URL to the post_create, post_update and post_delete trigger
* Feature: Extend demo data for post_create, post_update and post_delete trigger
* Feature: Add digest authentication for authentication templates
* Tweak: Added the already existing parameters to the parameter description of the post_delete trigger
* Tweak: Optimized all webhook descriptions and texts
* Tweak: Remove test array from extensions ajax feedback
* Tweak: Optimize layout for the webhook action argument list
* Fix: Taxonomies haven't been sent over on post_create and post_update trigger

= 3.0.3: March 17, 2020 =
* Feature: EXTENSION MANAGEMENT - You can now manage all extensions for WP Webhooks and WP Webhooks Pro within our plugin via a new tab (Install, Activate, Deactivate, Upgrade, Delete)
* Feature: Whitelist webhook actions for single webhooks - This allows you to restrict the access of webhooks actions for single webhook action URL's
* Feature: the arguments post_date and post_date_gmt on the create_post/update_post webhook actions accept now any kind of date format (we automatically convert it to Y-m-d H:i:s)
* Feature: Introducton to a new settings item called "Activate Debug Mode" - It will provide further debug.log information about malformed data structures and more
* Tweak: Repositioning of the logging feature for incoming webhooks AFTER the webhook token authentication
* Tweak: Remove post_modified and post_modified_gmt parameter from the create_post webhook action since they are non-functional (https://core.trac.wordpress.org/ticket/49767)
* Tweak: Support for meta data for attachments on create_post and update_post webhook actions
* Tweak: Reposition fetching of the action parameter for incoming webhook requests
* Tweak: Optimized layout for the plugin admin area
* Tweak: Optimize webhook action response text in case there wasn't any action defined
* Fix: create_if_none bug on update_post webhook cation (In case a post ID was given and there was no post related to it, a post was still created, even without defining the create_if_none argument)
* Dev: Add new helper function to check if a plugin is installed

= 3.0.2: March 29, 2020 =
* Feature: Full reworked webhook descriptions (You WILL love them!)
* Feature: Add user data and user meta as well to the deleted_user trigger
* Tweak: Optimized tab descriptions
* Tweak: Optimized stylings
* Tweak: Add post details + meta as well for attachment deletions
* Fix: Post details + meta haven't been available on the post_delete trigger
* Fix: Prevent custom HTML within the log data from destroying the layout
* Dev: Add the $user variable to the do_action argument for the get_user webhook action
* Dev: Add the $return_args variable to the do_action argument for the create_post webhook action

= 3.0.1: March 08, 2020 =
* Feature: New webhook trigger setting to change the request method. Supported: POST (Default), GET, HEAD, PUT, DELETE, TRACE, OPTIONS, PATCH
* Tweak: Optimize certain layout parts 
* Tweak: Display webhook name and technical name within the Settings popup
* Fix: On reset of WP Webhooks Pro, the authentication data was not removed
* Fix: meta values that should be serialized from a JSON haven't been serialized properly. 
* Dev: Deprecated trigger secret (Can be set only with WordPress hooks) - Why? Due to confusion and too specific usecases

= 3.0.0: February 10, 2020 =
* Feature: THIS VERSION IS FULLY BACKWARDS COMPATIBLE
* Feature: Completely refactored any optimized layout
* Feature: GET parameters are now accepted as well as action arguments (Only in real GET calls)
* Feature: New authentication engine: You can now authenticate every webhook trigger for external APIS using API Key, Bearer Token or Basic Auth
* Feature: New webhook action called "custom_action", which allows you to handle every incoming data within a WordPress add_action() hook
* Feature: Change the webhook URL you want to use for testing actions within the "Receive Data" page
* Feature: Custom tag system to map other Payload fields together to a single string within the Data Mapping Engine
* Tweak: Added the action argument as well the the argument list within the "Receive Data" tab
* Tweak: Added the action argument as well to the testing form for webhook actions within the "Receive Data" tab
* Tweak: Completely refactored settings saving process for a smooth UI experience
* Tweak: PHP Docs have been optimized
* Tweak: Placeholder logic is not integrated with dynamic settings fields for "Send Data" settings
* Tweak: Whitelist has now as well a beautified version for displaying JSON data
* Tweak: The webhook triggers within the "Send Data" tab show now as well the internal webhook name (in brackets)
* Tweak: We changed all checkboxes through neat toggles for a better usability
* Tweak: Optimize layout for Helpers Tags on Data Mapping Page
* Tweak: Rearrange setting items
* Fix: API key field was missing after adding a new action URL
* Fix: Corrected certain typos
* Dev: Added new filter to manipulate post-delayed triggers: wpwhpro/post_delay/post_delay_triggers (Prevent webhook triggers from firing or add your own ones)
* Dev: Add multiple arguments to the post_to_webhook()-functions WordPress actions
* Dev: wpwhpro/admin/webhooks/webhook_http_args has now two more arguments: $webhook, $authentication_data
* Dev: wpwhpro/admin/webhooks/webhook_trigger_sent has now more arguments

= 2.2.0: January 28, 2020 =
* Fix: Throw 403 http error accordingly on authentications
* Tweak: Optimize error messages for authentication

= 2.1.9: January 27, 2020 =
* Feature: Import/Export of Data Mapping templates
* Feature: The webhook authentication process is now also fully JSON ready and returns a JSON as a response
* Tweak: A failed authentication now also returns a 200 error code instead of 403 
* Tweak: Settings layout is now better readable

= 2.1.8: January 17, 2020 =
* Feature: Allow the custom webhook trigger to send data only to certain webhooks using the secondary $webhook_names variable: do_action( 'wp_webhooks_send_to_webhook', $custom_data, $webhook_names );
* Tweak: Add $update variable to "do_action" argument of the update_user endpoint
* Tweak: Optimize webhook descriptions for certain triggers and actions
* Fix: Correct password creation logic for creating a user
* Fix: Triggers didn't fire on creating or updating an attachment
* Fix: Generate password as well on user_update if user does not exist and create_if_none is set to yes
* Fix: The custom action trigger contained a custom action that was fired as well on post deletion

= 2.1.7: December 16, 2019 =
* Feature: Display new table field for only the API key
* Feature: Added new webhook trigger that fires after a user was deleted
* Tweak: Better support for our new Zapier App 2.0.0

= 2.1.6: November 27, 2019 =
* Feature: Send post taxonomies along with post creade and update trigger
* Tweak: Clear input fields after adding new trigger
* Tweak: Update plugin updater class

= 2.1.5: November 15, 2019 =
* Feature: Activate/Deactivate single webbhook triggers
* Feature: Post-delay webhook triggers. (Triggers are fired before PHP shuts down to catch plugin changes)
* Feature: Post-delay setting to deactivate the functionality
* Tweak: Optimize PHPDocs

= 2.1.4: November 06, 2019 =
* Feature: Add webhook name field (slug) to the webhook trigger URL's
* Feature: Add webhook name to the webhook trigger headers
* Tweak: Add additional parameters to the authorization hook
* Tweak: Optimize webhook description for "get_user" action
* Fix: Get user response gave success back if no user was found
* Fix: Create user on update_user webhook doesn't work properly
* Dev: Adjust WordPress hook priority for incoming data from 10 to 100 

= 2.1.2: October 20, 2019 =
* Feature: Introduce exclusive Zapier extension (Early access)
* Feature: Introduce new polling feature for next-level Zapier triggers

= 2.1.1: October 12, 2019 =
* Fix: Bump new version to readme and main files

= 2.1.0: October 12, 2019 =
* Feature: Deactivate and Activate webhook action URL's
* Feature: Add the previous post data to the "Send Data on Post Update" trigger response
* Feature: New webhook actions to search/retrieve post(s) within a third party services
* Feature: New webhook actions to search/retrieve user(s) within a third party services
* Teak: Optimized and simplified backend layout
* Tweak: Add webhook name for action and triggers to the webhook settings as data itself (This allows better targeting of webhook manipulations)
* Tweak: Add webhook name to every single log within out logging feature
* Tweak: Include fallback logic for non-working JSON contructs that include unicode characters
* Tweak: Optimize packend docs and WordPress code standards
* Fix: Remove unncessary var_dump()-calls within our backend tabs

= 2.0.5: August 31, 2019 =
* Feature: Protect your webhook actions using an access token
* Feature: Support Woocommerce post status on default post status features like sending a trigger on post creation with a certain status
* Tweak: Correct management of post and user meta values to also send them within the triggers as existing meta values
* Tweak: Made action_delete_user function public
* Fix: Fixed bug with non-working do_action parameter on create/update user action
* Fix: Issue with non working "Send data on user login" due to wrong interpreted user parameter
* Dev: New filter for webhook trigger data: wpwhpro/admin/webhooks/webhook_data

= 2.0.4: August 09, 2019 =
* Feature: Trigger create_post webhook if the initial status of the post changes
* Tweak: Optimize PHPDocs
* Fix: Non-working action testing forms in case https was active
* Dev: New helper function for safe-redirecting the home url
* Dev: Optimize WordPress coding standards

= 2.0.3: August 01, 2019 =
* Tweak: Added phpdocs for data mapping template
* Tweak: Optimize meta value sanitation for allowing a bigger variety of values
* Fix: Correct description of trigger setting for frontend limitations

= 2.0.2: July 26, 2019 =
* Feature: Allow user deletion from whole multisite network
* Feature: Add post author to create/update post action with the user id OR the email address
* Tweak: Optimize webhook descriptions

= 2.0.1: June 30, 2019 =
* Fix: When using update_user action in combination with create_if_none, the user was not aadded

= 2.0.0: June 30, 2019 =
* Feature: Webhook actions are ajax ready
* Feature: Data Mapping Engine to change data keys for triggers and actions and create new values
* Feature: Settings Engine for webhook actions
* Feature: Complete overhaul for the log functionality
* Feature: Security question before deleting an action or trigger
* Fix: Fix text bugs
* Fix: Debug warning if json data is parsed as an array and not as a string
* Fix: Solve bug with not read user meta data when raw json is given
* Fix: Fix issue with not correctly applied text domain for translation functions
* Fix: Non existent translation within the Send Data Tab for the "Add button"
* Dev: New filter wpwhpro/helpers/request_return_value
* Dev: New filter wpwhpro/settings/required_action_settings
* Dev: New filter wpwhpro/admin/settings/data_mapping_table_data
* Dev: New action wpwhpro/admin/webhooks/webhook_action_after_settings

= 1.6.7: May 31, 2019 =
* Feature: Add and remove multiple user roles while creating or updating a user
* Feature: Define a webhook action directly within the webhook URL by adding &action=MYCUSTOMWEBHOOK. E.g.: https://mydomain.com/?wpwhpro_action=XXX&wpwhpro_api_key=XXX&action=create_post
* Tweak: Optimize PHPDocs
* Fix: While updating a user without defining the email specificly, the email was removed

= 1.6.6: May 25, 2019 =
* Tweak: Remove strip slashes settings item (It's not too common so we use only the filter instead)

= 1.6.5: May 23, 2019 =
* Feature: Send your triggers in different content types. Supported types: JSON (Default), XML, X-WWW-FORM-URLENCODE
* Feature: Create serialized arrays as post meta values for users and posts on the following actions: create_user, create_post, update_user, update_post
* Fix: Correct menu item name from "Receive Data" to "Receive Data"
* Fix: Remove sanitation from parsed user password to not change it at all (create_user and update_user trigger)
* Dev: New filter to strip slashes on responses: wpwhpro/helpers/request_values_stripslashes
* Dev: New filter for the new convert_to_xml function to change the prefix: wpwhpro/helpers/convert_to_xml_int_prefix
* Dev: Filter for manipulating the required webhook trigger settings: wpwhpro/settings/required_trigger_settings
* Dev: Filter to change the simplexml data: wpwhpro/admin/webhooks/simplexml_data

= 1.6.4: April 24, 2019 =
* Feature: Introduce new webhook trigger settings - You can now set custom rules for each of your webhook triggers
* Feature: Confirm action before deleting a trigger webhook
* Feature: Reset WP Webhook data via the settings
* Feature: Added a new webhook trigger that fires after a custom WordPress action hook was called. ( Send Data On Custom Action )
* Feature: Introduce new default settings for the following webhooks: Send Data On New Post, Send Data On Post Update, Send Data On Post Deletion
* Feature: Introduce new settings to fire a trigger only on certain post types for the following webhooks: Send Data On New Post, Send Data On Post Update, Send Data On Post Deletion
* Tweak: Add post data and post meta data to the post_delete trigger
* Tweak: Optimize process for generated webhook trigger id's
* Tweak: Change post_delete trigger from after_delete_post to delete_post
* Tweak: Optimize response for custom action after certain webhooks
* Tweak: Optimize phpDocs
* Tweak: Optimize Send Data tab
* Tweak: Improve the displayed values for single webhook trigger responses
* Fix: Fix issue of not visible whitelist and log tabs after saving the settings
* Dev: Introduce optimized handler for posting data to a webhook. You can now also parse the whole webhook array construct
* Dev: Add new webhook default settings api
* Dev: Add new webhook settings api
* Dev: Introduce new update function for updating webhook data

= 1.6.3: April 13, 2019 =
* Feature: Webhook log - Display send and received requests
* Feature: Optimized headers for "Send Data" triggers
* Feature: Add Signature for "Send Data" triggers through new settings option
* Tweak: Optimize backend layout#
* Dev: Add new filter wpwhpro/admin/webhooks/get_hooks for filtering active webhooks
* Dev: Add new filter wpwhpro/admin/webhooks/webhook_http_args for filtering the "Send Data" http arguments
* Dev: Add new action wpwhpro/admin/webhooks/webhook_trigger_sent after a trigger was sent

= 1.6.2: March 23, 2019 =
* Tweak: Better plugin initialization
* Tweak: Optimize text

= 1.6.1: March 20, 2019 =
* Feature: Test the webhook directly out of the plugin
* Feature: Return the action name in case no webhook is set for a better debugging
* Tweak: Add new license expired notice
* Dev: Add filter for the response body (The data that gets send back to the webhook caller)

= 1.6.0: March 9, 2019 =
* Fix: Home content was displayed when custom settings page was registered
* Fix: Fatal error on some plugin activations: Fatal error: Can't use method return value in write context

= 1.5.9: March 1, 2019 =
* Feature: Add our new Assistant Bot to our plugin. It will help you to solve tons of things directly from your dashboard.
* Feature: New action webhook to test fucntionality.

= 1.5.8: February 28, 2019 =
* Feature: New response field inside of actions to see what data you can expect to come back.
* Feature: New sent data field inside of triggers to see which data gets send after a trigger fires.
* Feature: Allow JSON for custom user meta and post meta for a better handling of complex values.
* Tweak: Optimized Response data for a clearer overview (Please make sure you check your webhooks before updating)
* Tweak: Compatibility handler for not correctly set SERVER_NAME var in $_SERVER while grabbing the current url
* Tweak: Optimized Performance
* Tweak: Optimized PHPDocs for various functions
* Tweak: Add user meta to create and update user response
* Tweak: Add post meta and tax inout to create and update post reponse
* Fix: Correct wording issues
* Fix: force_delete for post delete response returned post id instead of boolean
* Dev: New hook wpwhpro/webhooks/response_json_arguments (https://wp-webhooks.com/docs/knowledge-base/filter-response-arguments/)
* Dev: New hook wpwhpro/webhooks/response_response_type (https://wp-webhooks.com/docs/knowledge-base/filter-response-type/)
* Dev: New hook wpwhpro/admin/webhooks/default_webhook_name (https://wp-webhooks.com/docs/knowledge-base/default-webhook-name/)

= 1.5.7: February 12, 2019 =
* Feature: Add whitelist feature for enhanced security
* Feature: Add support for xml response
* Feature: Add $response_data to user various trigger wp hooks
* Tweak: Optimized license handler
* Tweak: Optimize settings tab
* Tweak: Optimize updater class
* Tweak: Validate action nonce data
* Tweak: Optimize code quality and PHPDocs
* Fix: Issue with displaying changes on main plugin update page
* Fix: Set specified content type header on dynamic request type
* Fix: Plugin updater issue (On multisites the updater didn't work)
* Fix: Undefined notice with certain configurations of plugin data for the delete_post action webhook

= 1.5.6: January 27, 2019 =
* Fix: Correct version values

= 1.5.5: January 27, 2019 =
* Tweak: Introduce new updater handler for extensions
* Tweak: Optimize invalid/inactive license notifications
* Tweak: Change namespace definitions
* Tweak: Optimize PHPDocs

= 1.5.4: January 26, 2019 =
* Feature: Add support for application/json
* Feature: Add support for application/xml
* Feature: Add support for x-www-form-urlencoded (native)
* Feature: Add support for text/html via our new tag engine
* Feature: Add support for text/plain via our new tag engine
* Feature: Add new tag engine for text and html validation


= 1.5.3: January 23, 2019 =
* Feature: Add new taxonomy create/delete framework to create_post and update_post
* Feature: New possibility to create a user on update if it doesn't exist as an attribute
* Feature: Create better response for the create post and update post trigger (Send Data)
* Feature: Create better response for the create post and update post action (Receive Data)
* Feature: Add error data to action response on update_user
* Tweak: Optimize some functions for a better WordPress standard
* Tweak: New handling of various key components for actions ($_GET)
* Tweak: Optimize Rich editing value
* Tweak: Optimize short description of the update_post action (Receive data)
* Tweak: Optimize description of the delete_user action (Receive data)
* Tweak: Optimize short description of the create_post action (Receive data)
* Dev: Add functionality for parsing custom arguments to the post_to_webhook function for wp_remote_post
* Fix: Remove PHP Notice when no webhook is set for some send_data actions
* Fix: Webhook action urls were not deletable
* Fix: Create different response message for updated user action (Receive Data)
* Fix: When a post was set to update_post and create_if_none was true, then it always created a new post
* Fix: Correct and optimize webhook descriptions
* Fix: Fix issue with deleting a user if an email is given instead of a user id

= 1.5.2: January 15, 2019 =
* Tweak: Include fallback response if an action is not set or the action function is missing
* Tweak: Optimize PHPDocs and the documentation in general
* Tweak: Include new UTM Links
* Tweak: Improve way of providing response json (die)
* Fix: Fix issue with deleting posts
* Fix: Clear issue with undefined indec notice for triggers if no trigger is set
* Fix: Undefined index notice after a license was not available anymore
* Fix: corrected wrongly spelled words
* Fix: Response issue on some action requests

= 1.5.1: January 13, 2019 =
* Feature: New function for creating an automated username via the create_user webhook
* Feature: Global handler for action response json (Now we validate the response as a json with multiple data fields.
* Tweak: Optimized PHPDocs and better webhook descriptions
* Tweak: Optimize demo request functions
* Fix: Issue with post_category that just allows a single category to be set
* Fix: Multiple small bugfixes like text bugs and formatting issues

= 1.5: January 13, 2019 =
* Feature: Add new actions for create, update and delete posts
* Tweak: Update WordPress "Tested up to" to 5.0.3
* Fix: Correct naming of PHPDocs

= 1.4: December 26, 2018 =
* Feature: Introduce transients for home screen api calls
* Feature: Include a new tag validation function
* Tweak: Update WordPress "Tested up to" to 5.0.2
* Tweak: Optimize existing action descriptions
* Fix: Fix wrongly loaded home screen api

= 1.3: December 25, 2018 =
* Fix: Clear issue with admin related webhook calls
* Fix: Call actions for validating incoming data directly

= 1.2: November 30, 2018 =
* Fix: Remove issue with webhook unset function
* Fix: Update Code for better WordPress Coding Standards
* Fix: Update various links to our Documentation
* Fix: Globalize translation identifier for single page tabs
* Fix: Display plugin title on admin page
* Tweak: Better layout for an easier usage
* Feature: Introduce Settings page
* Feature: Setting for activating translations engine
* Feature: Webhook Control via Settings page (Activate only the features you want)
* Feature: Add new trigger for creating, updating and deleting a post (Works also for custom post types)

= 1.1: November 18, 2018 =
* Fix: Remove issue with webhook unset function
* Fix: issue with recieving actions from various webhooks
* Fix: issue with the api_key action parameter
* Tweak: Optimize performance for validating default webhooks
* Tweak: Make user_login field optional for new users (If not defined, we use the sanitized user email)

= 1.0: November 10, 2018 =
* Birthday of WP Webhooks Pro
