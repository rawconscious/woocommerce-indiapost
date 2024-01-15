<?php
/**
 * Sends data to India Post through API.
 *
 * @package Rawconscious.
 */

wcip_flg_xml_post();

/**
 * Sends API data to India Post.
 *
 * @param array $data shipment data.
 */
function wcip_flg_xml_post( $data = array() ) {

	// $url                         = 'https://aeotrading.flg360.co.uk/api/APILeadCreateUpdate.php';

	$consignee_address                = array();
	$consignee_address['name']        = 'name';
	$consignee_address['address1']    = 'address1';
	$consignee_address['address2']    = 'address2';
	$consignee_address['address3']    = 'address3';
	$consignee_address['city']        = 'Bangalore';
	$consignee_address['pincode']     = '560085';
	$consignee_address['CountryCode'] = '+91';
	$consignee_address['MobileNo']    = '8762657259';

	$shipment_package_info                            = array();
	$shipment_package_info['articleNumber']           = '14A';
	$shipment_package_info['referenceNumber']         = '45545454';
	$shipment_package_info['ShipmentMethodOfPayment'] = 'Cash';

	$cash_on_delivery_charge                      = array();
	$cash_on_delivery_charge['chargeOrAllowance'] = '30';
	$cash_on_delivery_charge['monetaryAmount']    = '12';

	$actual_gross_wieght                = array();
	$actual_gross_wieght['weightValue'] = '2';

	$shipment_package_info['CashOnDeliveryCharge']             = $cash_on_delivery_charge;
	$shipment_package_info['shipmentPackageActualGrossWeight'] = $actual_gross_wieght;
	$shipment_package_info['insuredValue']                     = '1';
	$shipment_package_info['ProofOfDelivery']                  = 'OTP';

	$manifest_data['consigneeAddress']    = $consignee_address;
	$manifest_data['shipmentPackageInfo'] = $shipment_package_info;

	$xml_data                           = array();
	$booking_manifest                   = array();
	$booking_manifest['manifestDetail'] = $manifest_data;
	$xml_data['BookingManifest']        = $booking_manifest;

	$xml = get_xml_from_array( $xml_data );

	var_dump( $xml );

	// $ch = curl_init();
	// curl_setopt( $ch, CURLOPT_URL, $url );
	// curl_setopt( $ch, CURLOPT_POST, 1 );
	// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	// curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
	// curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: text/xml' ) );
	// $result            = curl_exec( $ch );
	// $output            = array();
	// $output['success'] = true;
	// if ( curl_errno( $ch ) ) {
	// $output['success'] = false;
	// $output['message'] = 'ERROR from curl_errno -> ' . curl_errno( $ch ) . ': ' . curl_error( $ch );
	// } else {
	// $returnCode = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	// switch ( $returnCode ) {
	// case 200:
	// $dom->loadXML( $result );
	// if ( $dom->getElementsByTagName( 'status' )->item( 0 )->textContent == '0' ) {
	// good request
	// $output['message']  = '<p> Response Status: Passed - Message: ' . $dom->getElementsByTagName( 'message' )->item( 0 )->textContent;
	// $output['message'] .= '<p> FLG NUMBER: ' . $dom->getElementsByTagName( 'id' )->item( 0 )->textContent;
	// $output['flgNo']    = $dom->getElementsByTagName( 'id' )->item( 0 )->textContent;
	// } else {
	// $output['success'] = false;
	// $output['message'] = '<p> API Connection: Success - Lead Entry: Failed - Reason: ' . $dom->getElementsByTagName( 'message' )->item( 0 )->textContent;
	// }
	// break;
	// default:
	// $output['success'] = false;
	// $output['message'] = '<p>HTTP ERROR -> ' . $returnCode;
	// break;
	// }
	// }
	// curl_close( $ch );
}

/**
 * Get xml from array.
 *
 * @param array $xml_data .
 */
function get_xml_from_array( $xml_data ) {
	$dom                = new DOMDocument( '1.0', 'UTF-8' );
	list( $dom, $node ) = xml_recursion( $xml_data, $dom );

	return $dom->saveXML();
}

/**
 * Get xml from array.
 *
 * @param array  $xml_data .
 * @param object $dom .
 * @param object $node .
 */
function xml_recursion( $xml_data, $dom, $node = null ) {
	if ( is_array( $xml_data ) ) {
		foreach ( $xml_data as $xml_data_key => $xml_data_value ) {
			if ( is_array( $xml_data_value ) ) {
				$element = $dom->createElement( $xml_data_key );
				if ( null === $node ) {
					$node = $dom->appendChild( $element );
				} else {
					$node->appendChild( $element );
				}
				list( $dom, $node ) = xml_recursion( $xml_data_value, $dom, $element );
			} else {
				$element = $dom->createElement( $xml_data_key, $xml_data_value );
				if ( null === $node ) {
					$node = $dom->appendChild( $element );
				} else {
					$node->appendChild( $element );
				}
			}
		}
	} else {
		// Handle non-array value case.
		$element = $dom->createElement( 'value', $xml_data );
		if ( null === $node ) {
			$node = $dom->appendChild( $element );
		} else {
			$node->appendChild( $element );
		}
	}

	return array( $dom, $node );
}
