<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_HS_API;
use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * WooCommerce AvaTax main plugin class.
 *
 * @since 2.7.0
 */
class WC_AvaTax_Elr_Utilities {
	
	/* ECM Subscription names */
	const TYPE_AVATAX_ECMESSENTIALS = 'ECMEssentials';
	const TYPE_AVATAX_ECMPRO = 'ECMPro';
	const TYPE_AVATAX_ECMPREMIUM = 'ECMPremium';
	const TABLE_TYPE_FLAT = 'flat';
    const TABLE_TYPE_EAV = 'eav';
    const TABLE_TYPE_VERTICAL = 'vertical';
    const ARR_ELR_DOCUMENT_TYPE = ['order' => 'ubl-invoice', 'refund' => 'ubl-creditnote'];
	protected $MAIN_MAPPER_TABLE = "";
    protected $query ="";
    
	public function __construct(){
		global $wpdb;
		$this->MAIN_MAPPER_TABLE = $wpdb->prefix . "wc_orders";
	}

    /**
	 * Checks if ELR credentials are set.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @return bool 
	 */
	public function is_elr_enabled() {
		return (wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api());
	}

    /**
	 * Gets the integration API for ELR
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function get_integration_api($generateElrToken = false)
	{
		$api_environment     = get_option('wc_avatax_elr_environment');
		

		return wc_avatax()->get_integration_api("", "", $api_environment, $generateElrToken);
	}

    /**
	 * Disconnects the connection to ELR.
	 * 
	 * @since 3.0.0
	 *
	 */
	public function disconnect_elr($is_update = false) {
        $integration_api = wc_avatax()->wc_avatax_utilities()->get_integration_api(true);
		$integration_api->delete_elr_configuration();

        global $wpdb;
        if(!$is_update) {
            update_option("wc_avatax_elr_environment", "");
            update_option("wc_avatax_elr_client_id", "");
            update_option("wc_avatax_elr_client_secret", "");
            delete_option('wc_avatax_elr_company');

            delete_transient( 'wc_avatax_elr_connection_status' );
            $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_elr_company','wc_avatax_elr_tenant_id','wc_avatax_elr_custom_fields','wc_avatax_elr_environment','wc_avatax_elr_client_id','wc_avatax_elr_client_secret','wc_avatax_Seller_company_ID','wc_avatax_Seller_VAT_Id','wc_avatax_Seller_Peppol_ID','wc_avatax_elr_selected_custom_fields','wc_avatax_elr_company','wc_avatax_Seller_Registration_Name')" );
            
            wc_avatax()->refresh_elr_api();
        }
		else {
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_elr_client_id','wc_avatax_elr_client_secret')" );
		}

		// We will be using this common clear_transient() function for both avatax and elr
		//TODO: need to verify if this is correct with both avatax and elr when we will test both
		// Clear transient data
		$this->clear_transient();
	}

    /**
     * Clears transient data.
     * 
     * @since 3.0.0
     *
     */
	public function clear_transient() {

        delete_transient( 'wc_avatax_elr_connection_status' );
        delete_transient( 'wc_avatax_elr_token' );
        delete_transient( 'wc_avatax_elr_company_response' );
	}
    
	/** Functions for Fields mapper functionality */

	 /**
     * Get Table Refference/Structure Fields
     */
    public function getTableRefferenceFields($tablename, $withType=false, $withAllFields=true) {
        return $this->getTableStructure($tablename, $withType, $withAllFields);
    }

	public function getTableStructure($tablename, $withType = false, $withAllFields = true) {
		global $wpdb;
        $fieldResults = [];
        $results = [];
        $selected_fields = [];
        //$tablename = $this->getTableName($tablename);
        $existCheckQuery = "SELECT EXISTS (
            SELECT TABLE_NAME
            FROM
            information_schema.TABLES
            WHERE
                TABLE_TYPE LIKE 'BASE TABLE' AND
                TABLE_NAME = '" . $tablename . "'
            );";
        $existCheckResults = $wpdb->query($existCheckQuery);
        // $existCheckResultValue = array_values($existCheckResults[0]);
        if ($existCheckResults > 0) {
            //$query = "DESCRIBE `" . $tablename . "`";
            $query = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tablename."';";            
            $results = $wpdb->get_results($query);
            if ($results && count($results) > 0) {
                $fieldResults = [];
                
                if(!$withAllFields)
                {
                    $queryEavKey= "SELECT selected_fields from avatax_einvoice_mapper where main_table='" . $tablename . "'";
                    $selected_field_results = $wpdb->get_results($queryEavKey);
                    $selected_fields = explode("," , $selected_field_results[0]->selected_fields);
                }
                foreach ($results as $result) {
                    if($withType)
                    {
                        if (!$withAllFields && in_array($result->COLUMN_NAME, $selected_fields)) {
                            $fieldResults[$result->COLUMN_NAME] = $result->DATA_TYPE;
                        }
                        else if ($withAllFields){
                            $fieldResults[$result->COLUMN_NAME] = $result->DATA_TYPE;
                        }
                    }else{
                        if (!$withAllFields && in_array($result->COLUMN_NAME, $selected_fields)) {
                            $fieldResults[] = $result->COLUMN_NAME;
                        }
                        else if ($withAllFields){
                            $fieldResults[] = $result->COLUMN_NAME;
                        }
                        
                    }
                }
            }
        }
        return $fieldResults;

    }
	
	/**
     * Prepare Einvoice Mapper Collection for specific Invoice
     */
    public function getEinvoiceCollectionByInvoiceId($invoiceId = '', $entity_type= '') {
        $elrFormattedSchema =  array();
        if (!empty($invoiceId)) {

            $entityObj = wc_get_order($invoiceId);

            if (! $entityObj || ($entity_type == 'order' && $entityObj->get_parent_id()) || ($entity_type == 'refund' && !$entityObj->get_parent_id()) ) {
                wc_avatax()->log_elr( 'Order or Refund not found' );
                return $elrFormattedSchema;
            }

            $allMapperRecords = $this->getEinvoiceMapperRecords($entity_type);
            $uniqueInvoiceRecords = $this->getUniqueInvoiceRecords($invoiceId, $entity_type);
            $uniqueInvoiceRecords = $this->getApplicableFieldsInArray($allMapperRecords , $uniqueInvoiceRecords,  $entity_type);
        
            $elrFormattedSchema['metadata'] = $this->addMetadataField($entity_type);
            $elrFormattedSchema['conditionPayload'] = $this->addConditionalField($uniqueInvoiceRecords);
            $elrFormattedSchema['payload'] = array_merge(
                [
                    'customerEInvoicingData' => $this->getCustomFieldsSchema('customer', true, true, $invoiceId),
                    'CompanyEInvoicingData' => $this->getCustomFieldsSchema('company', true, true),
                ],
                $uniqueInvoiceRecords
            );
        
            if ($entity_type == 'refund') {
                $order = wc_get_order($invoiceId);
                if ($order->get_parent_id()) {
                    $parent_order = wc_get_order($order->get_parent_id());
                    $elrFormattedSchema['payload'] = array_merge($elrFormattedSchema['payload'],["additionalData"=> ["parentOrderDate"=>$parent_order->get_date_created()->date('Y-m-d H:i:s')]]);
                }
                
            }
        }
        return $elrFormattedSchema;
    }

    /**
     * Prepare Einvoice Mapper Collection for specific Invoice
     */
    public function getUniqueInvoiceRecords($invoiceId = '', $entity_type= '') {
        global $wpdb;
        
        $strMapperFlatTableJoinQuery = '';
        $flatTableInvoiceRecords = [];
        $restructuredInvoiceRecords = [];
        $uniqueInvoiceRecords = [];
        $schemaSkeletonWithData = [];
        if (!empty($invoiceId)) {
            $flatTableRecords = $this->getEinvoiceMapperRecords($entity_type, self::TABLE_TYPE_FLAT);
            $allMapperRecords = $this->getEinvoiceMapperRecords($entity_type);

            if ($flatTableRecords && count($flatTableRecords) > 0) {
                $mapperRecords = $this->prepareMainAndSecondaryTables($flatTableRecords);
                $strColumnsToSelect = $this->prepareColumnsToSelect($flatTableRecords);

                $strMapperFlatTableJoinQuery = $this->prepareMapperJoinQuery($mapperRecords, $strColumnsToSelect, $invoiceId);

                $flatTableInvoiceRecords = $wpdb->get_results($strMapperFlatTableJoinQuery);

                if (count($flatTableInvoiceRecords) > 0) {
                    $restructuredInvoiceRecords = $this->restructureKeys($flatTableInvoiceRecords);
                    $uniqueInvoiceRecords = $this->removeDuplicateEntries($restructuredInvoiceRecords);
                    //$keyValuePairs = $this->buildKeyValuePairWithMapperData($allMapperRecords);
					//wc_avatax()->log_elr("keyValuePairs". json_encode($keyValuePairs));
                    $eavAttributesRecords = $this->prepareEavAttributesRecords($uniqueInvoiceRecords , $invoiceId, $entity_type);
                    if ($eavAttributesRecords && !empty($eavAttributesRecords)) {
                        $uniqueInvoiceRecords = array_merge($uniqueInvoiceRecords, $eavAttributesRecords);
                    }

                    if ($entity_type == 'refund') {
                        $refund = wc_get_order($invoiceId);
        
                        if ($refund && $refund->get_type() === 'shop_order_refund') {
                            // Get the original order ID
                            $parent_order_id = $refund->get_parent_id();
    
                            $uniqueParentOrderRecords = $this->getUniqueInvoiceRecords($parent_order_id, 'order');
                        }
                    }

                    //$schemaSkeletonWithData = $this->prepareSchemaSkeleton($keyValuePairs, false, $uniqueInvoiceRecords);
                }
            }

            /**
             * Update null value from parent order if any
             */
            if (!empty($uniqueParentOrderRecords) && !empty($uniqueInvoiceRecords)) {
                
                foreach ($uniqueInvoiceRecords as $key => $record) {
                    foreach ($record as $field => $value) {
                        if (is_null($value) && 
                            isset($uniqueParentOrderRecords[$key]) && 
                            isset($uniqueParentOrderRecords[$key][$field])) {
                            
                            $uniqueInvoiceRecords[$key][$field] = $uniqueParentOrderRecords[$key][$field];
                        }
                    }
                }
            }
        }
        return $uniqueInvoiceRecords;
    }

    /** Gets the custom field data
     * 
     * @since 3.0.0
     * 
     * $type string to get either customer or company custom fields
     * $with_data to get the schema with data or with data type
     * returns array 
     */
    protected function getCustomFieldsSchema($type, $with_data = false, $selected_only = false, $invoice_id = null){
        $custom_fields = array();
		$field_list =  get_option('wc_avatax_elr_custom_fields', array());
		
		if(isset($field_list)) {
			
            switch ($type) {
                case 'customer':
                    if($with_data && !empty($invoice_id)){
                        $order = wc_get_order( $invoice_id );
                        if (!empty($order->get_parent_id())) {
                            $order = wc_get_order( $order->get_parent_id() );
                        }

                        // Get the WP_User Object instance
                        $user = $order->get_user();
                        $customer_fields = (array)$field_list->customer;
                        foreach($customer_fields as $field){
                            if(!$selected_only || ($selected_only && $field->selected)) {
                                $custom_fields[$field->field_id] = $user->{$field->field_id};
                            }
                        }
                    }
                    else{
                        $customer_fields = (array)$field_list->customer;
                        foreach($customer_fields as $field){
                            if(!$selected_only || ($selected_only && $field->selected)) {
                                $custom_fields[$field->field_id] = $field->data_type ;
                            }
                        }
                    }
                    break;
                case 'company':
                    $company_fieds = (array)$field_list->company;
                    foreach($company_fieds as $field){
                        if(!$selected_only || ($selected_only && $field->selected)) {
                            $custom_fields[$field->field_id] = ($with_data ? get_option($field->field_id, '') : $field->data_type) ;
                        }
                    }
                    break;
            }
        }
            
		return $custom_fields;
    }

    /** Sets the selected custom field
     * 
     * @since 3.0.0
     * 
     */
    protected function setSelectedCustomFieldSchema() {
		$selected_field_list =  explode(',', get_option("wc_avatax_elr_selected_custom_fields", ''));
        $field_list = get_option('wc_avatax_elr_custom_fields', array());
        

		if(isset($selected_field_list)) {
            foreach($field_list->customer as $field){
                if(in_array(("JSON.customerEInvoicingData." .$field->field_id), $selected_field_list )){
                    $field->selected = true;
                }
                else{
                    $field->selected = false;
                }
            }
            foreach($field_list->company as $field){
                if(in_array(("JSON.CompanyEInvoicingData." . $field->field_id), $selected_field_list )){
                    $field->selected = true;
                }
                else{
                    $field->selected = false;
                }
            }
        }
        update_option("wc_avatax_elr_custom_fields", $field_list);
    }

    public function addMetadataField($entity_type) {
        $doctype = '';
        if(isset($entity_type) && !empty($entity_type))
        {
            $doctype = self::ARR_ELR_DOCUMENT_TYPE[$entity_type];
        }
        return array(
            "configId" => get_option('wc_avatax_website_id')."_elr",  // Store ID
            "appId" => wc_avatax()->get_elr_connector_id(), // ELR Connector ID
            "companyId" => get_option("wc_avatax_elr_company"), // ELR Company ID
            "documentType" => $doctype // Doctype for Order/Refund entity e.g ubl-invoice/ubl-creditnote
        );
    }

    public function getApplicableFieldsInArray($allMapperRecords, $uniqueInvoiceRecords, $entity_type) {
        
        foreach ($allMapperRecords as $mapperRecord) {
            //$uniqueInvoiceConvertedRecord = [];
            if ($mapperRecord->table_type == 'vertical') {
                $uniqueInvoiceRecords[$mapperRecord->main_table] = $this->addVerticalTableData($mapperRecord, $entity_type);
                continue;
            }
            if ($mapperRecord->isarray && !empty($uniqueInvoiceRecords[$mapperRecord->main_table]) && !array_is_list($uniqueInvoiceRecords[$mapperRecord->main_table]))
            {
                $uniqueInvoiceRecords[$mapperRecord->main_table] = array($uniqueInvoiceRecords[$mapperRecord->main_table]);
            }
            // if (($mapperRecord->isarray) && gettype($uniqueInvoiceRecords[$mapperRecord->main_table]) != "array")  {
            //     $uniqueInvoiceConvertedRecord += $uniqueInvoiceRecords[$mapperRecord->main_table];
            //     $uniqueInvoiceRecords[$mapperRecord->main_table] = $uniqueInvoiceConvertedRecord;
            // }
        }
        return $uniqueInvoiceRecords;
    }

    public function addVerticalTableData($mapperRecord, $entity_type)
    {
        global $wpdb;
        $vertical_table_data = [];
        $key_array = explode(',', $mapperRecord->selected_fields);
        for ($i=0; $i < count($key_array); $i++) { 
            $query = "select * from " . $mapperRecord->main_table . " where " . $mapperRecord->eav_key_field . "='" . trim($key_array[$i]) . "'"; 
            $res = $wpdb->get_results($query);
		    if(!empty($res)){
                $column_name = $mapperRecord->eav_value_field;
                $vertical_table_data[$key_array[$i]] = $res[0]->$column_name;
		    }
        }
        return $vertical_table_data;
        
    }

    public function addConditionalField($payloadData, $withdata = true)
    {
        $conditionalPayload = [];
        $conditionalPayloadRecords = $this->getConditionalPayloadRecords();
        foreach( $conditionalPayloadRecords as $conditionalPayloadRecord ) {            
            $conditionalPayload[$conditionalPayloadRecord->conditional_param] = "string";
            if ($withdata) {
                if(!$conditionalPayloadRecord->filter_data && !empty($payloadData[$conditionalPayloadRecord->mapper_table]))
                {
                    $filter_inp_data = $payloadData[$conditionalPayloadRecord->mapper_table];
                    $conditionalPayload[$conditionalPayloadRecord->conditional_param] = $filter_inp_data[$conditionalPayloadRecord->mapper_field];
                    continue;
                }
                $filter_data_array = explode(',', $conditionalPayloadRecord->filter_data);
                $filter_field_array = explode(',', $conditionalPayloadRecord->filter_field);
                if (!empty($payloadData[$conditionalPayloadRecord->mapper_table])) {
                    $filter_inp_data = $payloadData[$conditionalPayloadRecord->mapper_table];
                    for ($i=0; $i < count($filter_data_array); $i++) { 
                        $filter_inp_data = array_filter($filter_inp_data, function($value) use ($filter_data_array, $filter_field_array, $i) {
                            // Apply your condition here
                            return $value[trim($filter_field_array[$i])] == trim($filter_data_array[$i]); // This will filter out non-positive values
                        });
                    }
                    $conditionalPayload[$conditionalPayloadRecord->conditional_param] = array_values($filter_inp_data)[0][$conditionalPayloadRecord->mapper_field];
                }
            }
        }
       return $conditionalPayload;
    }

    public function getFilteredRecord($filteredConditionalPayloadRecords, $payloadData) 
    {
        foreach ($filteredConditionalPayloadRecords as $filteredConditionalPayloadRecord ) {
            $filter_field = $filteredConditionalPayloadRecord->filter_field;
            $filter_data = $filteredConditionalPayloadRecord->filter_data;
            if (gettype($payloadData)  == "array") {
                $payloadData = array_filter($payloadData[$filteredConditionalPayloadRecord->mapper_table], function($value) {
                    return $value.$filter_field = $filter_data;
                });
                
            }
            else {
                if($payloadData && $payloadData[$filter_field] == $filter_data)
                {
                    return $payloadData;
                }
                else {
                    return array();
                }
                
            }
            
        }
        return $payloadData;
    }
	/**
     * Save E-Invoice Mapping to DB
     */
    public function saveEinvoiceMapping($post) {
		
		global $wpdb;
        $isarray = $post['main_table_isarray'] == "on" ? "1" : "0";
        if ($this->validateEinvoiceMapperFields($post)) {
            if ($this->validateUniqueEinvoiceMapperRecord($post)) {

                if (isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_FLAT) {
					$query = "INSERT INTO avatax_einvoice_mapper (main_table, main_table_ref_field, secondary_table, secondary_table_ref_field, table_type, entity_type, is_default_table, isarray) VALUES (" . 
					"'" . $post['main_table'] . "'," .
					"'" . $post['main_table_ref_field'] . "'," .
					"'" . $post['secondary_table'] . "'," .
					"'" . $post['secondary_table_ref_field'] . "'," .
					"'" . $post['table_type'] . "'," .
                    "'" . $post['entity_type'] . "'," .
					"0" . "," . 
                    $isarray . " )";

					
                } else if (isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_EAV){

					$query = "INSERT INTO avatax_einvoice_mapper (main_table,main_table_ref_field,  secondary_table, secondary_table_ref_field, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES (" . 
					"'" . $post['main_table'] . "'," .
                    "'" . $post['main_table_ref_field'] . "'," .
					"'" . $post['secondary_table'] . "'," .
					"'" . $post['secondary_table_ref_field'] . "'," .
                    "'" . $post['eav_key_field'] . "'," .
                    "'" . $post['eav_value_field'] . "'," .
					"'" . $post['table_type'] . "'," .
                    "'" . $post['entity_type'] . "'," .
					"0" . "," . 
                    $isarray . " )";
                }
                else {
                    $query = "INSERT INTO avatax_einvoice_mapper (main_table,main_table_ref_field,  secondary_table, secondary_table_ref_field, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES (" . 
					"'" . $post['main_table'] . "'," .
                    "''," .
					"''," .
					"''," .
                    "'" . $post['eav_key_field'] . "'," .
                    "'" . $post['eav_value_field'] . "'," .
					"'" . $post['table_type'] . "'," .
                    "'" . $post['entity_type'] . "'," .
					"0" . "," . 
                    $isarray . " )";
                }
				$res = $wpdb->query($query);

                // $this->messageManager->addSuccessMessage(__('Mapping Saved.'));
                return 'Mapping Saved.';
            } else {
                // $this->messageManager->addErrorMessage(__('Duplicate Mapping Entry. Main Table / EAV Entity should be unique.'));
                return 'Duplicate Mapping Entry. Main Table / EAV Entity should be unique.';
            }
        } else {
            // $this->messageManager->addErrorMessage(__('All inputs are required.'));
            return 'All inputs are required.';
        }
    }

    /**
     * Saves the selected schema fields and sends them to the CCS (Compliance Cloud Service).
     * 
     * This function processes the selected data fields, separates custom fields from standard fields,
     * updates the database with the selected fields, and sends the updated schema to CCS.
     * 
     * @param array  $selectedData Array of selected fields in dot notation (e.g., "JSON.sales_invoice.entity_id")
     * @param string $entityType   Optional. The type of entity for which the schema is being saved, values order and refund
     * 
     * @return boolean Returns true on successful save and send, false on failure
     * 
     * @throws Exception Catches and logs any exceptions that occur during execution
     * 
     * @global wpdb $wpdb WordPress database access object
     */
	public function save_and_send_schema($selectedData, $entityType = '') {
		global $wpdb;
		try
		{
            //Performance log variables
			$execution_start = hrtime(true);
			$api_time = $execution_end = 0.0;

			$main_table = '';
			$selected_fields = [];
            $selected_custom_fields = [];
			foreach($selectedData as $key=>$data)
			{
				$columns = explode('.', $data); // e.g => JSON.sales_invoice.entity_id
				$totalColumns = count($columns);
				if($columns && $totalColumns>1)
				{
					if($columns[1] == "customerEInvoicingData" || $columns[1] == "CompanyEInvoicingData"){
                        array_push($selected_custom_fields, $data);
                        unset($selectedData[$key]);
                    }
                    else{
                        $main_table = $columns[($totalColumns-2)];
                        $column = $columns[($totalColumns-1)];
                        $selected_fields[$main_table][] = $column;
                    }
				}
			}
            //Saving selected custom field data
            update_option('wc_avatax_elr_selected_custom_fields', implode(',',$selected_custom_fields));
            $this->setSelectedCustomFieldSchema();
			if(count($selected_fields)>0)
			{
				$query = "SELECT * 
				FROM avatax_einvoice_mapper";
				$query = $query . " order by mapper_id ASC";

				$mapperRecordsCollection = $wpdb->get_results($query);
				if($mapperRecordsCollection)
				{
					foreach($mapperRecordsCollection as $row)
					{
						$current_main_table = $row->main_table;
						$current_selected_fields = '';
						if(isset($selected_fields[$current_main_table]))
							$current_selected_fields = implode(',', $selected_fields[$current_main_table]);
						
						$update_query = "update avatax_einvoice_mapper set selected_fields='" . $current_selected_fields . "' where main_table='" . $current_main_table . "'";
                        if ($entityType) {
                            $update_query .= " AND entity_type='" . $entityType . "'";
                        }
						$wpdb->query($update_query);
						//$row->setSelectedFields($current_selected_fields)->save(); // If $selected_fields data is set, update it otherwise update empty value.
					}
				}
			}
            $response = $this->send_einvoice_schema_to_ccs('POST', $entityType);
            $api_time = $response->get_response_time();

            $execution_end = hrtime(true);
            $execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
            $connector_time = $execution_time - $api_time;
            wc_avatax()->elr_logger()->log_performance_elr("SaveAndSendToElr", "save_and_send_schema", "Save mapping and send schema to CCS.", "", "", $connector_time, $api_time, 0, "", "");
			
            return true;
		}
		catch(Exception $e){
            wc_avatax()->elr_logger()->log_exception("SaveAndSendToElr", "save_and_send_schema", $e->getMessage(), $e->getTraceAsString());
			return false;
		}
    }

    /**
	 * Sends ELR Schema to CCS
	 *
	 * @internal
	 *
	 * @since 2.9.0
	 *
	 * @return void
	 */
	public function send_einvoice_schema_to_ccs($type, $entityType) {
		if($this->is_elr_enabled()) {
            $doctype = '';
            if(isset($entityType) && !empty($entityType))
            {
                $doctype = self::ARR_ELR_DOCUMENT_TYPE[$entityType];
            }

            $data = $this->getCCSSchema($entityType);
            $generateElrToken = true;
			$integration_api = wc_avatax()->wc_avatax_utilities()->get_integration_api($generateElrToken);
            $parsedData = $this->update_array_structure_for_elr($data);
			return $integration_api->send_elr_schema_to_ccs($type, $parsedData, $doctype);
            
		}
	}

    /**
	 * Formate schema for CCS API.
	 *
	 * @since 2.9.0
	 *
	 * @param array $array Data to covert in CCS accepted formate
	 */
	function update_array_structure_for_elr(&$array) {
		foreach ($array as $key => &$value) {
			if (is_array($value)) {
				$this->update_array_structure_for_elr($value); // Recursive call for nested arrays
			} else {
				// Assuming the current value is a string that should be replaced with a structured array
				$name = ucwords(str_replace('_', ' ', $key)); // Capitalize the key to create a display name
			
                $dataType = $this->getSupportedDataType($value);

				$value = [
					'type' => $dataType,
					'displayName' => $name 
				];
			}
		}

		return $array;
	}

    /**
     * Convert data type into CCS JSON supported data type
     *
     * @param string $datatype
     * @return string
     */
    public function getSupportedDataType($datatype){
        $dataTypeArray = [
            "number"    => ["number","int","float","smallint","decimal","double","bigint","tinyint"],
            "string"    => ["text","string","varchar","select","hidden"],
            "date"      => ["date","datetime","timestamp"],
            "boolean"   => ["boolean"],
        ];
        
        if(!empty($datatype)){
            foreach($dataTypeArray as $key=>$value){
                if(in_array($datatype, $value)){
                    return $key;
                }
            }
        } 
        
        return "string";
    }

    /**
     * Prepare Einvoice Mapper Schema for CCS
     */
    public function getCCSSchema($entityType) {
        $records = $this->getEinvoiceMapperRecords($entityType);
        $schema = [];
        $keyValuePairs = [];
        if ($records && count($records) > 0) {
            $keyValuePairs = $this->buildKeyValuePair($records);

            $schema = $this->prepareSchemaForCCS($keyValuePairs, $entityType);
        }
        $schema['customerEInvoicingData'] = $this->getCustomFieldsSchema('customer', false, true);
        $schema['CompanyEInvoicingData'] = $this->getCustomFieldsSchema('company', false, true);
        if ($entityType == 'refund')
        {
            $schema['additionalData']['parentOrderDate'] = "string";
        }
        
        return $schema;
    }

    /**
     * Prepare mapper schema sckeleton
     */
    public function prepareSchemaForCCS($tree, $entityType, $withFields = true, $data = []) {
        $schemaArray = [];
        foreach ($tree as $item) {
            $parentKey = 'parent-flat';
            $isRecordFlat = true;
            if (isset($item['parent-eav'])) {
                $parentKey = 'parent-eav';
                $isRecordFlat = false;
            }
            
            if (!isset($schemaArray[$item[$parentKey]])) {
                $schemaArray[$item[$parentKey]] = [];
                if ($withFields) {
                    if ($isRecordFlat) {
                        $parentArray = $this->getTableRefferenceFields( $item[$parentKey], true, false);
                    } else {
                        $parentArray = $this->getAllEntityAttributes($item[$parentKey], false, $entityType);
                    }


                    $schemaArray[$item[$parentKey]] = $parentArray;
                } else if (count($data) > 0) {
                    $schemaArray[$item[$parentKey]] = isset($data[$item[$parentKey]]) ? $data[$item[$parentKey]] : [];
                }
            }
            
        }
        return $schemaArray;
    }


	/**
     * Validate all Fields for Mapper
     */
    public function validateEinvoiceMapperFields($post) {
        if (
            (
                isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_FLAT &&
                isset($post['main_table']) && !empty($post['main_table']) &&
                isset($post['main_table_ref_field']) && !empty($post['main_table_ref_field']) &&
                isset($post['secondary_table']) && !empty($post['secondary_table']) &&
                isset($post['secondary_table_ref_field']) && !empty($post['secondary_table_ref_field'])
            ) || (
                isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_EAV &&
                isset($post['main_table']) && !empty($post['main_table']) &&
                isset($post['main_table_ref_field']) && !empty($post['main_table_ref_field']) &&
                isset($post['secondary_table']) && !empty($post['secondary_table']) &&
                isset($post['secondary_table_ref_field']) && !empty($post['secondary_table_ref_field']) &&
                isset($post['eav_key_field']) && !empty($post['eav_key_field']) &&
                isset($post['eav_value_field']) && !empty($post['eav_value_field'])
            ) || (
                isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_VERTICAL &&
                isset($post['main_table']) && !empty($post['main_table']) &&
                isset($post['eav_key_field']) && !empty($post['eav_key_field']) &&
                isset($post['eav_value_field']) && !empty($post['eav_value_field'])
            )

        ) {
            return true;
        } else {
            return false;
        }

    }

	/**
     * Validate if Mapper entry is unique or not
     */
    public function validateUniqueEinvoiceMapperRecord($data) {
		global $wpdb;

		$existCheckQuery = "SELECT 1
            FROM avatax_einvoice_mapper";

			
	    $existCheckQuery = $existCheckQuery . " where main_table='". trim($data['main_table']) . "'";

        if (isset($data['entity_type'])) {
            $existCheckQuery = $existCheckQuery . " and entity_type='" . trim($data['entity_type']) . "'";
        }            
        
		$existCheckResults = $wpdb->query($existCheckQuery);
        if ($existCheckResults == 0) {
            return true;
        } else {
            return false;
        }
	}

	public function flattenJson($json, $prefix = '', $flattenTopLevelOnly = true) {
        $flattenedArray = [];
    
        foreach ($json as $key => $value) {
            $newPrefix = $prefix ? $prefix . '.' . $key : $key;
    
            if (is_array($value)) {
                $flattenedArray = array_merge($flattenedArray, $this->flattenJson($value, $newPrefix));
            } else {
                $flattenedArray[$newPrefix] = $value;
            }
        }
    
        return $flattenedArray;
    }

	public function getEinvoiceMapperRecords($entityType = '', $tableType = '') {
        global $wpdb;

        $query = "SELECT * FROM avatax_einvoice_mapper 
                WHERE mapper_id IN (
                    SELECT MIN(mapper_id)
                    FROM avatax_einvoice_mapper";
        
        if ($tableType && !empty($tableType)) {
            $query .= " WHERE table_type='" . $tableType . "'";
            if ($entityType && !empty($entityType)) {
                $query .= " AND entity_type='" . $entityType . "'";
            }
        } else if ($entityType && !empty($entityType)) {
            $query .= " WHERE entity_type='" . $entityType . "'";
        }

        $query .= " GROUP BY main_table)
                ORDER BY mapper_id ASC";
        
        return $wpdb->get_results($query);
    }

    public function getEinvoiceConditionalMapperRecords() {
        global $wpdb;
        
        $query = "SELECT main_table, 
                CASE 
                    WHEN MAX(isarray) = 1 THEN 1 
                    ELSE 0 
                END AS isarray,
                GROUP_CONCAT(DISTINCT selected_fields ORDER BY mapper_id ASC SEPARATOR ',') AS selected_fields
                    
                FROM avatax_einvoice_mapper
                GROUP BY main_table
                ORDER BY main_table";
        
        return $wpdb->get_results($query);
    }

    public function getConditionalPayloadRecords($inMultipleRow = false) {
        global $wpdb;

		$query = "SELECT conditional_param , MAX(mapper_table) as mapper_table , MAX(mapper_field) as mapper_field, GROUP_CONCAT(filter_data ORDER BY filter_data SEPARATOR ', ') as filter_data, GROUP_CONCAT(filter_field ORDER BY filter_data SEPARATOR ', ') as filter_field FROM avatax_conditional_payload_mapper mp left join avatax_conditional_payload_filter fl on mp.conditional_mapper_id = fl.conditional_mapper_id 
                    group by conditional_param";
        if ($inMultipleRow) {
            $query = "select mp.*, fl.filter_id, fl.filter_field, fl.filter_data  from avatax_conditional_payload_mapper mp left join avatax_conditional_payload_filter fl on mp.conditional_mapper_id = fl.conditional_mapper_id";
        }
        return $wpdb->get_results($query);
    }

	 /**
     * Separate Main and Secondary Tables
     */
    public function prepareMainAndSecondaryTables($records = []) {
        $mapperRecords = [];
        if ($records && count($records) > 0) {
            $i = 0;
            foreach ($records as $record) {
                if ($record->main_table == $this->MAIN_MAPPER_TABLE) {
                    $mapperRecords['main'] = $record;
                } else {
                    $mapperRecords['secondary'][$i] = $record;
                }
                $i++;
            }
        }
        return $mapperRecords;
    }

	 /**
     * Prepare All columns with Alias which needs to select
     */
    public function prepareColumnsToSelect($records = []) {
		$strColumnWithAlias = '';
        if ($records && count($records) > 0) {
            foreach ($records as $record) {
                $j = 1;
                $strFields = $record->selected_fields;
                if(!empty($strFields))
                {
                    $fields = explode(',',$strFields);
                    if($fields && count($fields) > 0)
                    {
                        foreach ($fields as $field) {
                            $strColumnWithAlias .= $record->main_table . "." . $field . " as '" . $record->main_table . "|" . $field . "',";
                        }
                    }   
                }                             
            }
            $strColumnWithAlias = trim($strColumnWithAlias, ',');
        }
        return $strColumnWithAlias;
    }

	/**
     * Prepare Mapper Join Query to select all records
     */
    public function prepareMapperJoinQuery($mapperRecords = [], $strColumnsToSelect = '*', $invoiceId = '') {
		global $wpdb;
        $strMapperJoinQuery = '';
        if (($mapperRecords && count($mapperRecords) > 0) && !empty($invoiceId)) {
            if (isset($mapperRecords["main"])) {
                $strMapperJoinQuery .= "SELECT " . $strColumnsToSelect . " FROM "  . $mapperRecords["main"]->main_table . " ";
				if (isset($mapperRecords["secondary"]) && count($mapperRecords["secondary"]) > 0) {
                    foreach ($mapperRecords["secondary"] as $secondaryMapper) {
                        $strMapperJoinQuery .= "LEFT JOIN "  . $secondaryMapper->main_table. " on "  . $secondaryMapper->main_table . "." . $secondaryMapper->main_table_ref_field . " = " . $secondaryMapper->secondary_table . "." . $secondaryMapper->secondary_table_ref_field . " ";
					}
                }
                $strMapperJoinQuery .= "WHERE " . $mapperRecords["main"]->main_table. "." . $mapperRecords["main"]->main_table_ref_field . " = '" . $invoiceId . "'";
            } else {
                return 'MainTableNotFound';
            }
        }
        return $strMapperJoinQuery;
    }

	/**
     * Get mapper table rows with html
     */
	public function getMapperTableRows(){
		global $wpdb;
		$records = "";
        $entityType = sanitize_text_field( Framework\SV_WC_Helper::get_requested_value( 'entity' ) );
		$results = $this->getEinvoiceMapperRecords($entityType);
		if(!empty($results)){
			
			foreach( $results as $result ) {
				$records = $records . "<tr><td style='display:none;'>" . $result->mapper_id ."</td><td>" .($result->table_type == 'flat' ? "Flat" : ($result->table_type == 'eav' ? 'EAV' : 'Vertical')) . "</td><td>" .$result->main_table. "</td><td> " . $result->main_table_ref_field."</td><td>". $result->secondary_table."</td><td>". $result->secondary_table_ref_field. "</td><td>" . $result->eav_key_field."</td><td>" . $result->eav_value_field."</td><td>" . ($result->isarray ? "Yes" : "No") ."</td><td>" . ($result->is_default_table ? "" : "<a href='javascript:void(0);' class='tbl_mapper_delete' id='tbl_mapper_delete'>Delete</td>") . "</tr>";
			}
		}
		return $records;
	}

    /**
     * Get Conditional mapper table rows with html
     */
	public function getConditionalMapperTableRows(){
		global $wpdb;
		$records = "";
		$results = $this->getConditionalPayloadRecords(true);
		if(!empty($results)){
			
			foreach( $results as $result ) {
				$records = $records . "<tr><td style='display:none;'>" . $result->conditional_mapper_id."</td><td style='display:none;'>" . $result->filter_id."</td><td>" . $result->conditional_param ."</td><td>" .$result->mapper_table. "</td><td>" .$result->mapper_field. "</td><td> " . $result->filter_field."</td><td>". $result->filter_data."</td><td><a href='javascript:void(0);' class='tbl_condition_delete' id='tbl_condition_delete'>Delete</td></tr>";
			}
		}
		return $records;
	}

	/**
     * Prepare Einvoice Mapper Schema for Preview
     */
    public function getMapperSchema($entityType = '') {
        $records = $this->getEinvoiceMapperRecords($entityType);
        $schemaSkeleton = [];
        $keyValuePairs = [];
        if ($records && count($records) > 0) {
            $keyValuePairs = $this->buildKeyValuePair($records);
            $schemaSkeleton = $this->prepareSchemaSkeleton($keyValuePairs);
            $schemaSkeleton['customerEInvoicingData'] = $this->getCustomFieldsSchema('customer', false, false);
            $schemaSkeleton['CompanyEInvoicingData'] = $this->getCustomFieldsSchema('company', false, false);
            //Add this line when sending the schema to CCS for adding Conditional Payload Attribute.
            //$schemaSkeleton['conditionPayload'] = $this->addConditionalField($uniqueInvoiceRecords, false);
        }
        // $this->getEinvoiceCollectionByInvoiceId("145", 'order');
        return $schemaSkeleton;
    }

	/**
     * Get Einvoice Selected Fields Schema
     */
    public function getEinvoiceSelectedFieldsSchema($entityType = '') {
        $records = $this->getEinvoiceMapperRecords($entityType);

        if ($records && count($records) > 0) 
        {
            $resultSelectedFields = [];
            foreach ($records as $record) 
            {
                if(!empty($record->selected_fields))
                {
                    $arrDbSelectedFields = explode(',',$record->selected_fields); // Convert comma-separated string to array.
                    if(count($arrDbSelectedFields)>0) {
                        foreach($arrDbSelectedFields as $dbSelectedField)
                        {
                            $resultSelectedFields[] = 'JSON.'.$record->main_table .'.'.$dbSelectedField;
                        }
                    }
                }
            }
        }
        //Adding cselected custom fields
        array_push($resultSelectedFields, explode(',', get_option("wc_avatax_elr_selected_custom_fields", '')));
        return json_encode($resultSelectedFields);
    }

	/**
     * Prepare key pair value for mapper schema
     */
    public function buildKeyValuePair($records) {
        $keyValuePairs = [];
        foreach ($records as $record) {
            // if ($record->secondary_table)) {
                if ($record->table_type == self::TABLE_TYPE_FLAT) {
                    $keyValuePairs[] = ['parent-flat' => $record->main_table];
                } else {
                    $keyValuePairs[] = ['parent-eav' => $record->main_table];
                }

            // }
        }
        return $keyValuePairs;
    }

	/**
     * Prepare key pair value for Invoice Data
     * This method will be used to separate out parent,child and flat,eav records for Invoice Data method
     */
    public function buildKeyValuePairWithMapperData($records) {
        $keyValuePairs = [];
        foreach ($records as $record) {
            if (!empty($record->secondary_table) && !empty($record->selected_fields)) 
            { // Consider record, only if it is atleast one field selected.
                if ($record->table_type == self::TABLE_TYPE_FLAT) {
                    $keyValuePairs[] = ['parent-flat' => $record->secondary_table, 'main-flat' => $record->main_table];
                } else {
                    $keyValuePairs[] = ['parent-eav' => $record->secondary_table, 'main-eav' => $record->main_table];
                }

            }
        }
        return $keyValuePairs;
    }

	/**
     * Prepare mapper schema sckeleton
     */
    public function prepareSchemaSkeleton($tree, $withFields = true, $data = []) {
        $arrChild = [];
        $arrParent = [];
        $schema = [];
        foreach ($tree as $item) {
            $parentKey = 'parent-flat';
            $mainKey = 'main-flat';
            $isRecordFlat = true;
            if (isset($item['parent-eav'])) {
                $parentKey = 'parent-eav';
                // $mainKey = 'main-eav';
                $isRecordFlat = false;
            }
            // if (!isset($arrChild[$item[$mainKey]])) {
            //     // add child to array of all elements
            //     $arrChild[$item[$mainKey]] = [];
            // }
            if (!isset($arrChild[$item[$parentKey]])) {
                // add parent to array of all elements
                $arrChild[$item[$parentKey]] = [];
                if ($withFields) {
                    if ($isRecordFlat) {
                    $parentArray = $this->getTableRefferenceFields($item[$parentKey], true, true);
                    }
                    else {
                        $parentArray = $this->getAllEntityAttributes($item[$parentKey]);
                    }
                    $arrChild[$item[$parentKey]] = $parentArray;
                } else if (count($data) > 0) {
                    $arrChild[$item[$parentKey]] = isset($data[$item[$parentKey]]) ? $data[$item[$parentKey]] : [];
                }
                $arrParent[$item[$parentKey]] = &$arrChild[$item[$parentKey]];
            }
            // if (!isset($arrChild[$item[$parentKey]][$item[$mainKey]])) {
            //     // add reference to child for this parent
            //     if ($isRecordFlat) {
            //         if ($withFields) {
            //             $nameArray = $this->getTableRefferenceFields($item[$mainKey], true);
            //             $arrChild[$item[$mainKey]] = $nameArray;
            //         } else if (count($data) > 0) {
            //             $arrChild[$item[$mainKey]] = isset($data[$item[$mainKey]]) ? $data[$item[$mainKey]] : [];
            //         }
            //     } else {
            //         if ($withFields) {
            //             $nameArray = $this->getAllEntityAttributes($item[$mainKey]);
            //             $arrChild[$item[$mainKey]] = $nameArray;
            //         } else if (count($data) > 0) {
            //             $arrChild[$item[$mainKey]] = isset($data[$item[$mainKey]]) ? $data[$item[$mainKey]] : [];
            //         }
            //     }
            //     $arrChild[$item[$parentKey]][$item[$mainKey]] = &$arrChild[$item[$mainKey]];
            // }
        }
        return $arrParent;
    }

    /**
     * Get all Attributes for entity_code
     */
    public function getAllEntityAttributes($entity_code, $entity_key="", $withAllFields=true, $entityType = '') {
        global $wpdb;
        $selected_fields = "";
        if ($entity_key == "") {
            $queryEavKey= "SELECT eav_key_field, selected_fields from avatax_einvoice_mapper where main_table='" . $entity_code. "'";
            if ($entityType && !empty($entityType)) {
                $queryEavKey .= " AND entity_type='" . $entityType . "'";
            }
            $results = $wpdb->get_results($queryEavKey);
            $entity_key = $results[0]->eav_key_field;
            $selected_fields = $results[0]->selected_fields;
        }
        
        if ($withAllFields) {
            if($entity_code != $wpdb->prefix . "options")
            {
                
                $queryEavKey= "SELECT distinct " . $entity_key . " from " . $entity_code;
            }
            else
            {
                $queryEavKey= "SELECT distinct " . $entity_key . " from " . $entity_code . " where " . $entity_key . " NOT LIKE '%transient%' ''";
            }
        }
        else {
            $quoted_keys = array_map(function($key) {
                return "\"$key\"";
            }, explode(",", $selected_fields));
            $selected_fields = implode(", ", $quoted_keys);
            $queryEavKey= "SELECT distinct " . $entity_key . " from " . $entity_code . " where " . $entity_key . " in (". $selected_fields .")";
        }
        $results = $wpdb->get_results($queryEavKey);
        $attributeCodes = [];
        foreach ($results as $result) {
            $attributeCodes[$result->$entity_key] = "string";
        }
        
        return $attributeCodes;

    }


	/**
     * Replace Array Values with Null
     */
    public function replaceArrayValueswithNull($array) {
        return array_map(function ($value) {
            return null;
        }, $array); // array_map should walk through $array
    }

	/**
     * Restructure Keys to arrange records as per Original Schema
     */
    public function restructureKeys($invoiceRecords = [])
    {
        $restructuredInvoices = [];
        if (count($invoiceRecords) > 0) {
            $i = 0;
            foreach ($invoiceRecords as $invoiceRecord) {
                foreach ($invoiceRecord as $key => $value) {
                    $arrayKey = explode('|', $key);
                    $restructuredInvoices[$i][$arrayKey[0]][$arrayKey[1]] = $value;
                }
                $i++;
            }
        }
        return $restructuredInvoices;
    }

	/**
     * Remove Duplicate Entries from Collection
     */
    public function removeDuplicateEntries($restructuredInvoiceRecords = []) {
        $uniqueInvoiceRecords = [];
        $swapTableKeys = [];
        if (count($restructuredInvoiceRecords) > 0) {
            foreach ($restructuredInvoiceRecords as $restructuredInvoiceRecord) {
                foreach ($restructuredInvoiceRecord as $key => $record) {
                    $swapTableKeys[$key][] = $record;
                }
            }
            foreach ($swapTableKeys as $key => $value) {
                $uniqueValue = array_unique($value, SORT_REGULAR);
                if (sizeof($uniqueValue) > 1) {
                    $filteredKeyArray = array_values(array_filter($uniqueValue));
                    $uniqueInvoiceRecords[$key] = $this->sanitizeArray($filteredKeyArray);
                } else {
                    $uniqueInvoiceRecords[$key] = $this->sanitizeArray($uniqueValue[0]);
                }
            }
        }
        return $uniqueInvoiceRecords;
    }

	public function sanitizeArray($array) {
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $array[$key][$k] = $v;
                        } else {
                            $array[$key][$k] = $this->sanitizeData($v);
                        }
                    }
                } else {
                    $array[$key] = $this->sanitizeData($value);
                }
            }
        }
        return $array;
    }

	public function sanitizeData($data) {
        if (!empty($data)) {
            if ($this->isJson($data)) {
                $data = (array) json_decode($data);
            }
            if (is_array($data)) {
                return $data;
            } else {
                //$data = $this->sanitizeString($data);
                return $data;
            }
        }
        return $data;
    }

    public function sanitizeString($string = '') {
        if (!empty($string)) {
            // Using str_ireplace() function
            // to replace the word
            $string = str_ireplace(array('\'', '"',
                ',', ';', '<', '>'), ' ', $string);
            // Change thoses 2 variables if needed
            $allowableTags = null;
            $allowHtmlEntities = false;
            // Params are optionnal. If you use default values you can remove it.
            $params = ['allowableTags' => $allowableTags, 'escape' => $allowHtmlEntities];
            return $this->filterManager->stripTags($string, $params);
        }
        return $string;
    }

    function isJson($string) {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

	/**
     * Prepare EAV Attribute Mapping Record
     */
    public function prepareEavAttributesRecords($uniqueFlatTableInvoiceRecords, $invoiceId, $entity_type) {
        $eavAttributesRecords = [];
        $eavTableRecords = $this->getEinvoiceMapperRecords($entity_type, self::TABLE_TYPE_EAV);
        if ($eavTableRecords && count($eavTableRecords) > 0) {
            foreach ($eavTableRecords as $eavTableRecord) {
                $eavAttributesRecords[$eavTableRecord->main_table] = $this->getModuleEAVAttributesRecords($eavTableRecord, $uniqueFlatTableInvoiceRecords, $invoiceId);
            }
        }
        return $eavAttributesRecords;
    }

    /**
     * GET EAV Attribute Record for specific Module
     */
    public function getModuleEAVAttributesRecords($eavTableRecord, $uniqueFlatTableInvoiceRecords, $invoiceId) {
        $moduleEavAttributesRecords = [];
        // Convert comma-separated fields string into array
        $arrSelectedFields = explode(',',$eavTableRecord->selected_fields); 
        // Fetch EAV attributed for given entity
        global $wpdb;
        $this->query = "select * from " . $eavTableRecord->main_table;
        $key_field = $eavTableRecord->eav_key_field;
        $value_field = $eavTableRecord->eav_value_field;
        $queryEavKey= "SELECT * from avatax_einvoice_mapper";
        $results = $wpdb->get_results($queryEavKey);
        $this->prepareEAVQuery($results, $eavTableRecord->secondary_table,$eavTableRecord->main_table, $eavTableRecord->secondary_table_ref_field, $eavTableRecord->main_table_ref_field);
        $this->query = $this->query . $invoiceId . "'";
        $results = $wpdb->get_results($this->query);

        foreach ($results as $result) {
            if(in_array($result->$key_field,$arrSelectedFields)) // check if this field is selected in schema or not
            {
                $moduleEavAttributesRecords[$result->$key_field] = (!array_key_exists($result->$key_field,$moduleEavAttributesRecords)) ?  $this->sanitizeData($result->$value_field) : ($moduleEavAttributesRecords[$result->$key_field] . "," . $this->sanitizeData($result->$value_field));
            }
        }

        return $moduleEavAttributesRecords;
    }	


    public function prepareEAVQuery($results, $secondary_table, $main_table,  $secondary_table_ref_field, $main_table_ref_field){
        
        foreach ($results as $result) {
            if($result->main_table == $secondary_table && $result->main_table){
                $this->query = $this->query . " join " . $result->main_table ." on " . $main_table . "." . $main_table_ref_field . "=" . $result->main_table . "." .$secondary_table_ref_field;
                if (!$result->secondary_table) {
                    $this->query = $this->query . " where " . $result->main_table . "." . $result->main_table_ref_field . "= '";
                    break;
                }
                $this->prepareEAVQuery($results, $result->secondary_table,$result->main_table, $result->secondary_table_ref_field, $result->main_table_ref_field);
            }
        } 
    }

    public function InsertFilterData($filterInfo){
        global $wpdb;
        try {
            $existCheckQuery = "select  1 from avatax_conditional_payload_mapper where conditional_param='" . $filterInfo['cond_param'] . "'";
            $existCheckResults = $wpdb->query($existCheckQuery);
            if ($existCheckResults == 0) {
                $query = "INSERT INTO avatax_conditional_payload_mapper (mapper_table, mapper_field, conditional_param) VALUES (" . 
                        "'" . $filterInfo['mapped_table'] . "'," .
                        "'" . $filterInfo['mapper_table_field'] . "'," .
                        "'" . $filterInfo['cond_param'] . "' )";
                $response = $wpdb->get_results($query);
                $query = "select * from avatax_conditional_payload_mapper where conditional_param='" . $filterInfo['cond_param'] . "'";
                $conditional_response = $wpdb->get_results($query);
                if (!empty($filterInfo['filter_obj'])) {
                    foreach ( $filterInfo['filter_obj'] as $key => $value) {
                        $query = "INSERT INTO avatax_conditional_payload_filter (conditional_mapper_id, filter_field, filter_data) VALUES (" . 
                                "'" . $conditional_response[0]->conditional_mapper_id . "'," .
                                "'" . (($key) ? $key : null) . "'," .
                                "'" . $value . "' )";
    
                        $response = $wpdb->get_results($query);
                    }
                }
                return 'Record Created SuccessFully';
            } 
            else {
                return 'Conditional Param Already Mapped. Delete to continue.';
            }
        } 
        catch(Exception $e){
            if ( wc_avatax()->elr_logging_enabled() ) {
                wc_avatax()->log_elr("Exception ". $e->getMessage());
            }
            return false;
        }
    }

	public function saveElrSchema($columns = []){
		update_option("wc_avatax_elr_schema", $columns);
		return $columns;
	}

	public function getElrSchema(){
		return get_option("wc_avatax_elr_schema", []);
	}

	/**
     * Get get Main Table List
     */
    public function getMainTableList($entityType = '') {
        $records = $this->getEinvoiceMapperRecords($entityType);
        
        return $records;
    }

    /**
     * Delete Mapper Record
     */
    public function deleteMapperRecord($mapperId) {
        global $wpdb;
        $delete_query = "Delete from avatax_einvoice_mapper where mapper_id='" . $mapperId . "'";
        return $wpdb->query($delete_query);
    }

    /**
     * Delete conditional Record
     */
    public function deleteConditionalRecord($conditionalId, $filterId) {
        global $wpdb;
        $delete_query = "Delete from avatax_conditional_payload_filter where filter_id='" . $filterId . "'";
        wc_avatax()->log_elr("Delete from avatax_conditional_payload_filter where filter_id='" . $filterId . "'");
        $wpdb->query($delete_query);
        $delete_query = "Delete from avatax_conditional_payload_mapper where conditional_mapper_id='" . $conditionalId . "'";
        return $wpdb->query($delete_query);
    }

    /**
	 * create table and default data for "avatax_conditional_payload_filter"
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
    public function elrDataCreationForConditionalPayloadFilter(){
        global $wpdb;
		// Create table "avatax_conditional_payload_filter" for payload filter mapping
		$table_name_conditional_payload_filter = 'avatax_conditional_payload_filter';

        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name_conditional_payload_filter'") === $table_name_conditional_payload_filter;

		if(!$table_exists)
		{
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name_conditional_payload_filter (
					filter_id mediumint(9) NOT NULL AUTO_INCREMENT,
					conditional_mapper_id mediumint(9) NOT NULL,
					filter_field varchar(255)  NOT NULL,
                    filter_data text  NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (filter_id)
			) $charset_collate;";

            if ( defined('WC_ABSPATH') ) {
				require_once ABSPATH . '/wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql );

            $conditional_query = "SELECT conditional_mapper_id FROM avatax_conditional_payload_mapper";
            $conditional_mappers = $wpdb->get_results($conditional_query);

            if ($conditional_mappers) {
                // Prepare the insert data
                $values = array(
                    array(
                        'conditional_mapper_id' => $conditional_mappers[0]->conditional_mapper_id,
                        'filter_field' => 'address_type',
                        'filter_data' => 'shipping'
                    ),
                    array(
                        'conditional_mapper_id' => $conditional_mappers[1]->conditional_mapper_id,
                        'filter_field' => 'meta_key',
                        'filter_data' => 'wc_avatax_Buyer_Peppol_ID'
                    )
                );
                
                // Use WordPress's prepare statement for safer SQL
                $placeholders = array();
                $data = array();
                
                foreach ($values as $row) {
                    $placeholders[] = "(%s, %s, %s)";
                    $data[] = $row['conditional_mapper_id'];
                    $data[] = $row['filter_field'];
                    $data[] = $row['filter_data'];
                }
                
                $query = $wpdb->prepare(
                    "INSERT INTO $table_name_conditional_payload_filter 
                    (conditional_mapper_id, filter_field, filter_data) 
                    VALUES " . implode(', ', $placeholders),
                    $data
                );
                
                $wpdb->query($query);
            }           
		}
    }

    /**
	 * create table and default data for "avatax_einvoice_mapper"
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
    public function elrDataCreationForEinvoiceMapper(){
        global $wpdb;
        $table_name = 'avatax_einvoice_mapper';

        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

		if (!$table_exists) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
					mapper_id mediumint(9) NOT NULL AUTO_INCREMENT,
					main_table varchar(255)  NULL,
					main_table_ref_field varchar(255)  NULL,
					secondary_table varchar(255)  NULL,
					secondary_table_ref_field varchar(255)  NULL,
					selected_fields text NULL,
					eav_key_field varchar(255) NULL,
					eav_value_field varchar(255) NULL,
					table_type varchar(255)  NULL,
                    entity_type varchar(255)  NULL,
                    is_default_table int(11) DEFAULT 0,
                    isarray bit(1) NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (mapper_id)
			) $charset_collate;";
			

			if ( defined('WC_ABSPATH') ) {
				require_once ABSPATH . '/wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql );

			$query = "INSERT INTO $table_name (main_table,main_table_ref_field,  secondary_table, secondary_table_ref_field, selected_fields, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES 
            		('". $wpdb->prefix. "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'order', 1, 0),
					('". $wpdb->prefix. "wc_order_stats',	'order_id',	'". $wpdb->prefix. "wc_orders',	'id', 'order_id,parent_id,date_created,date_created_gmt,date_paid,date_completed,num_items_sold,total_sales,tax_total,shipping_total,net_total,returning_customer,status,customer_id', NULL, NULL, 'flat', 'order', 1, 0),
					('". $wpdb->prefix. "wc_order_addresses', 'order_id', '". $wpdb->prefix. "wc_orders', 'id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'order', 1, 1),
                    ('". $wpdb->prefix. "users', 'ID', '". $wpdb->prefix. "wc_orders', 'customer_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'order', 1, 0),
                    ('". $wpdb->prefix. "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on', 'option_name', 'option_value', 'vertical', 'order', 1, 0),
                    ('". $wpdb->prefix. "wc_order_tax_lookup', 'order_id', '". $wpdb->prefix. "wc_orders', 'id', 'order_id,order_tax,shipping_tax,tax_rate_id,total_tax', NULL, NULL, 'flat', 'order', 0, 0),
					('". $wpdb->prefix. "woocommerce_order_items', 'order_id', '". $wpdb->prefix. "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'order',	1, 1),
					('". $wpdb->prefix. "woocommerce_order_itemmeta', 'order_item_id', '". $wpdb->prefix. "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'order', 1, 0),
                    ('". $wpdb->prefix. "usermeta', 'user_id', '". $wpdb->prefix. "users', 'ID', 'meta_key,meta_value', NULL, NULL, 'flat', 'order', 1, 1),

                    ('". $wpdb->prefix. "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'refund', 1, 0),
                    ('". $wpdb->prefix. "wc_order_stats',	'order_id',	'". $wpdb->prefix. "wc_orders',	'parent_order_id', 'order_id,parent_id,date_created,date_created_gmt,date_paid,date_completed,num_items_sold,total_sales,tax_total,shipping_total,net_total,returning_customer,status,customer_id', NULL, NULL, 'flat', 'refund', 1, 0),
                    ('". $wpdb->prefix. "wc_customer_lookup',	'customer_id',	'". $wpdb->prefix. "wc_order_stats',	'customer_id', 'customer_id,user_id', NULL, NULL, 'flat', 'refund', 1, 0),
                    ('". $wpdb->prefix. "wc_order_addresses', 'order_id', '". $wpdb->prefix. "wc_orders', 'parent_order_id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'refund', 1, 1),
                    ('". $wpdb->prefix. "users', 'ID', '". $wpdb->prefix. "wc_customer_lookup', 'user_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'refund', 1, 0),
                    ('". $wpdb->prefix. "usermeta', 'user_id', '". $wpdb->prefix. "users', 'ID', 'meta_key,meta_value', NULL, NULL, 'flat', 'refund', 1, 1),
                    ('". $wpdb->prefix. "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on', 'option_name', 'option_value', 'vertical', 'refund', 1, 0),
                    ('". $wpdb->prefix. "wc_order_tax_lookup', 'order_id', '". $wpdb->prefix. "wc_orders', 'id', 'order_id,order_tax,shipping_tax,tax_rate_id,total_tax', NULL, NULL, 'flat', 'refund', 0, 0),
                    ('". $wpdb->prefix. "woocommerce_order_items', 'order_id', '". $wpdb->prefix. "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'refund', 1, 1),
                    ('". $wpdb->prefix. "woocommerce_order_itemmeta', 'order_item_id', '". $wpdb->prefix. "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'refund', 1, 0)";

            $wpdb->query($query);
		}
    }

    /**
	 * create table and default data for "avatax_conditional_payload_mapper"
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
    public function elrDataCreationForConditionalPayloadMapper(){
        global $wpdb;
        // Code to create and insert data for table "avatax_conditional_payload_mapper"
		$table_name = 'avatax_conditional_payload_mapper';
    
        // Check if the table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            
		if (!$table_exists) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
					conditional_mapper_id mediumint(9) NOT NULL AUTO_INCREMENT,
					mapper_table varchar(255) NOT NULL,
					mapper_field varchar(255) NOT NULL,
					conditional_param varchar(255) NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (conditional_mapper_id)
			) $charset_collate;";
			
			if ( defined('WC_ABSPATH') ) {
				require_once ABSPATH . '/wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql );
            
			$query = "INSERT INTO $table_name (mapper_table,  mapper_field, conditional_param) VALUES 
						('". $wpdb->prefix. "wc_order_addresses', 'country', 'destinationCountry'),
                        ('". $wpdb->prefix. "usermeta', 'meta_value', 'entityType')";
                        
			$wpdb->query($query);
		}
    }

    /**
     * Adds default custom fields in the database
     *
     * @since 3.0.0
     *
     * @return void
     */
    public function add_default_custom_fields(){
        if(empty(get_option( "wc_avatax_elr_custom_fields", '' ))){
        // Add custom fields in schema
            $company_fields = [];
            $company_fields[] = ["field_id" => "wc_avatax_Seller_company_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller company ID", "is_default" => true, "selected" => true];
            $company_fields[] = ["field_id" => "wc_avatax_Seller_VAT_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller VAT ID", "is_default" => true, "selected" => true];
            $company_fields[] = ["field_id" => "wc_avatax_Seller_Peppol_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller PEPPOL ID", "is_default" => true,"selected" => true];
            $company_fields[] = ["field_id" => "wc_avatax_Seller_Registration_Name", "field_type" => "company", "data_type" => "string", "field_name" => "Seller registration name", "is_default" => true, "selected" => true];

            $customer_fields = [];
            $customer_fields[] = ["field_id" => "wc_avatax_Buyer_company_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer company ID", "is_default" => true, "selected" => true ];
            $customer_fields[] = ["field_id" => "wc_avatax_Buyer_VAT_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer VAT ID", "is_default" => true, "selected" => true];
            $customer_fields[] = ["field_id" => "wc_avatax_Buyer_Peppol_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer Peppol ID", "is_default" => true, "selected" => true];

            $fields = ["company" => $company_fields, "customer" => $customer_fields];
            update_option("wc_avatax_elr_custom_fields", json_decode(json_encode($fields)));

            // Add custom fields by default preselected.
            $formatted_fields = [];
            // Process customer fields
            foreach ($customer_fields as $field) {
                $formatted_fields[] = "JSON.customerEInvoicingData." . $field['field_id'];
            }

            // Process company fields
            foreach ($company_fields as $field) {
                $formatted_fields[] = "JSON.CompanyEInvoicingData." . $field['field_id'];
            }

            // Join all fields with commas
            $formatted_result = implode(',', $formatted_fields);
            update_option("wc_avatax_elr_selected_custom_fields", $formatted_result);

            // Output will be like:
            // JSON.customerEInvoicingData.wc_avatax_Buyer_company_ID,JSON.customerEInvoicingData.wc_avatax_Buyer_VAT_ID,JSON.customerEInvoicingData.wc_avatax_Buyer_Peppol_ID
        }
    }

    /**
     * Create elr table with default data and custom fields in the database
     *
     * @since 3.0.0
     *
     * @return void
     */
    public function set_elr_default_schema(){
        //avatax_einvoice_mapper
		$this->elrDataCreationForEinvoiceMapper();
		//avatax_conditional_payload_mapper
		$this->elrDataCreationForConditionalPayloadMapper();
		// utility function call for pre-requisites of ELR data creation
		$this->elrDataCreationForConditionalPayloadFilter();
		//add default custom fields
		$this->add_default_custom_fields();
    }

    /**
	 * Determine if an order has a specific AvaTax status.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|\WC_Order_Refund|int $order The order object or ID.
	 * @param string $status Optional. The AvaTax status to check. If none set, it checks if any
	 *                       status is set.
	 * @return bool Whether the order has the specific status.
	 */
	public function order_has_elr_status( $order, $status = '' ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$current_status = $this->get_order_elr_statuses( $order );
		return $current_status == $status;
	}


	/**
	 * Get the statuses of an order when last posted to AvaTax.
	 *
	 * Orders can have multiple statuses, like `posted` and 'refunded'.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return array The order's AvaTax statuses.
	 */
	public function get_order_elr_statuses( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$current_status = wc_avatax()->wc_avatax_utilities()->get_order_meta( $order->get_id(), '_wc_avatax_elr_status', true);

		return $current_status;
	}

    /**
     * Generates HTML markup for displaying ELR (Electronic Logging Record) status information.
     *
     * Creates a formatted HTML structure containing the ELR status, message, and a download
     * invoice button within a metabox-style container.
     *
     * @since 3.0.0
     *
     * @param string $invoice_status        The current status of the invoice.
     * @param string $invoice_status_messages The messages related to the invoice status.
     * 
     * @return string Formatted HTML string containing the ELR status information.
     */
    function get_elr_status_html($order_id, $invoice_status, $processing_id, $invoice_status_messages) {
        return $invoice_status ? 
            sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box">
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <h4><label for="custom-value">%4$s</label></h4>
                    <span>%5$s</span>
                    <h4><label for="custom-value">%6$s</label></h4>
                    <span>%7$s</span>
                    <div style="text-align:right;">'
                        .($invoice_status === 'Complete' ? '' : '<button type="button" id="refresh-status" class="button actionButton refresh_status" title="Refresh invoice status" data-order_id="%1$s"></button>')
                        //.($invoice_status === 'Complete' ? '<button type="button" id="download-invoice" class="button actionButton download_document" title="Download invoice" data-order_id="%1$s"></button>' : '')
                        .($invoice_status === 'Error' ? '<button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_order" data-order_id="%1$s"></button>' : '')
                    .'</div>
                </div>',
                $order_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                __('Document Id', 'woocommerce-avatax'),
                $processing_id,
                __('Message(s)', 'woocommerce-avatax'),
                $invoice_status_messages
            ) :
            sprintf(
                '<div  id="order-%1$s" data-order_id="%1$s" class="customer-history order-attribution-metabox elr-status-box">
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                </div>',
                $order_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent"
            );
    }

    /**
     * Generates HTML markup for displaying ELR (Electronic Logging Record) status information.
     *
     * Creates a formatted HTML structure containing the ELR status, message, and a download
     * invoice button within a metabox-style container.
     *
     * @since 3.0.0
     *
     * @param string $invoice_status        The current status of the invoice.
     * @param string $invoice_status_messages The messages related to the invoice status.
     * 
     * @return string Formatted HTML string containing the ELR status information.
     */
    function get_elr_refund_status_html($refund_id, $invoice_status, $processing_id, $invoice_status_messages) {
        return $invoice_status ? 
            sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box refund">
                    <h4><label for="custom-value">Refund #%1$s</label></h4> 
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <h4><label for="custom-value">%4$s</label></h4>
                    <span>%5$s</span>
                    <h4><label for="custom-value">%6$s</label></h4>
                    <span>%7$s</span>
                    <div style="text-align:right;">'
                        .($invoice_status === 'Complete' ? '' : '<button type="button" id="refresh-status" class="button actionButton refresh_status" title="Refresh invoice status" data-order_id="%1$s"></button>')
                        //.($invoice_status === 'Complete' ? '<button type="button" id="download-invoice" class="button actionButton download_document" title="Download invoice" data-order_id="%1$s"></button>' : '')
                        .($invoice_status === 'Error' ? '<button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_refund" data-order_id="%1$s"></button>' : '').
                    '</div>
                </div>',
                $refund_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                __('Document Id', 'woocommerce-avatax'),
                $processing_id,
                __('Message(s)', 'woocommerce-avatax'),
                $invoice_status_messages
            ) :
            sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box refund">
                    <h4><label for="custom-value"><u>Refund #%1$s</u></label></h4> 
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <div style="text-align:right;">
                        <button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_refund" data-order_id="%1$s"></button>
                    </div>
                </div>',
                $refund_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent"
            );
    }

    /**
     * Registers or updates the ELR tenant application with the CCS API.
     * 
     * This function handles both registration (POST) and update (PUT) operations for the ELR application.
     * A successful operation is indicated by response codes:
     * - 200: Success
     * - 409: Application already exists
     * 
     * @since 1.0.0
     * 
     * @param string $type The HTTP method type ('POST' for registration or 'PUT' for update)
     * 
     * @return void
     */
    public function register_or_update_elr_tenant($type){
        $integration_api = $this->get_integration_api(true);
		$response_code = $integration_api->register_elr_app_on_ccs($type);

		if(($response_code == "200" || $response_code == "409")){
			wc_avatax()->log_elr("Successfully ". ($type == "POST" ? "registered" : "updated") ." ELR app for sending schema to CCS API");
		}
    }
}
