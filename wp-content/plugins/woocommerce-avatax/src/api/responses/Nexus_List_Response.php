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

namespace SkyVerge\WooCommerce\AvaTax\API\Responses;

use WC_AvaTax_API_Response;
use WC_AvaTax;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API nexus list response class.
 *
 * @since 1.13.0
 */
class Nexus_List_Response extends WC_AvaTax_API_Response {

	/** @var string Country */
	protected $selected_country;

	/**
	 * Gets the supported country list from Nexus.
	 *
	 * @since 1.13.0
	 *
	 * @return string[]
	 */
	public function get_full_nexus_details() : array {
		$nexus_details =  get_transient( 'wc_avatax_full_nexus_details');
		if(empty( $nexus_details ))
		{
			// initialize an empty result array
			$result = [];
			if(!$this->response_data || !isset($this->response_data->value)) {
				return [];
			}
			
			$api_response = $this->response_data->value;

			$result = array_merge($result, (array) $api_response);
			$nextLink = $this -> get_next_link($this->response_data);

			// If / while nextLink is present in response, enrich the result[]
			while(!empty($nextLink)) {
				$token_array = explode("/v2", $nextLink);
				$paginated_url = end($token_array);
				$paginated_response = WC_AvaTax::instance()->get_api()->get_nexus_list($paginated_url);
				$result = array_merge($result, (array) $paginated_response->response_data->value);
				$nextLink = $this -> get_next_link($paginated_response -> response_data);
			}
			set_transient( 'wc_avatax_full_nexus_details', $result, DAY_IN_SECONDS );
			return $result;
		}
		return $nexus_details;
	}
	/**
	 * Gets the supported country list from Nexus.
	 *
	 * @since 2.6.0
	 *
	 * @return string[]
	 */
	public function get_country_list() : array {
		$nexus_details = $this->get_full_nexus_details();
		$filtered_countries = array_unique( array_map( static function( $location ) {
			return $location->country;
		}, $nexus_details ?? [] ));
		return $filtered_countries;
	}

	/**
	 * Gets the supported state list for selected country from Nexus.
	 *
	 * @since 2.7.0
	 * @param string country
	 * @return string[]
	 */
	public function get_state_list(string $country) : array {
		$this->selected_country =  $country;
		$nexus_details = $this->get_full_nexus_details();
		$filtered_countries = array_unique( array_map( array( $this, 'get_states' ), $nexus_details ?? []));
		return $filtered_countries;
	}
	/**
	 * Get filtered States
	 *
	 * @since 2.7.0
	 * @param string[] $location
	 * @return string
	 */
	private function get_states( $location ) {
		if ($location->jurisdictionTypeId === "State" && ($this->selected_country === 'all' || $location->country === $this->selected_country))
		{
			return $location->region;
		}
	}
	/**
	 * Provides nextLink if available in response, if not then ''
	 *
	 * @param mixed $response_data
	 * @return string
	 */
	private function get_next_link($response_data) {
		if (isset($response_data ->{'@nextLink'})) {
			return $response_data ->{'@nextLink'};
		}
		return '';
	}

}
